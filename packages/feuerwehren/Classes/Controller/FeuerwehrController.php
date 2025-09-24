<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Schmid\Feuerwehren\Domain\Repository\{
    FeuerwehrRepository,
    FahrzeugkategorieRepository,
    GemeindeRepository,
    PersonRepository
};

final class FeuerwehrController extends ActionController
{
    public function __construct(
        protected FeuerwehrRepository $feuerwehrRepository,
        protected FahrzeugkategorieRepository $fahrzeugkategorieRepository,
        protected GemeindeRepository $gemeindeRepository,
        protected PersonRepository $personRepository
    ) {}


    public function injectGemeindeRepository(GemeindeRepository $r): void {
        $this->gemeindeRepository = $r;
    }
    public function injectPersonRepository(PersonRepository $r): void {
        $this->personRepository = $r;
    }

    // ⬇️ WICHTIG: ResponseInterface zurückgeben
    public function listAction(): ResponseInterface
    {
        // Kategorien für die Checkboxen
        $kategorien = $this->fahrzeugkategorieRepository->findAll();

        // Settings → JS-Konfiguration
        $vtUrlTemplate = (string)($this->settings['vtUrlTemplate'] ?? '/_vt/{z}/{x}/{y}.pbf');
        $apiSearchUrl  = (string)($this->settings['apiSearchUrl']  ?? '/?type=171001');
        $overlaysUrl   = (string)($this->settings['overlaysUrl']   ?? '/?type=171002');

        $mapConfig = [
            'vtUrlTemplate' => $vtUrlTemplate,
            'apiSearchUrl'  => $apiSearchUrl,
            'overlaysUrl'   => $overlaysUrl,
        ];

        $this->view->assignMultiple([
            'fahrzeugkategorien' => $kategorien,
            'mapConfig' => $mapConfig,
        ]);

        return $this->htmlResponse();
    }

    public function showAction(\Schmid\Feuerwehren\Domain\Model\Feuerwehr $feuerwehr): ResponseInterface
    {
        $this->view->assign('feuerwehr', $feuerwehr);
        return $this->htmlResponse();
    }

    /** JSON: sichtbare Feuerwehren (+Filter), Default-BBOX = LKR Freising wenn keine bbox übergeben */
    public function apiSearchAction(ServerRequestInterface $request): ResponseInterface
    {
        $q = $request->getQueryParams();

        // bbox parsen oder Defaults (Landkreis Freising)
        $west = $south = $east = $north = null;
        if (!empty($q['bbox'])) {
            $bbox = array_map('trim', explode(',', (string)$q['bbox']));
            if (count($bbox) === 4) {
                $west  = (float)str_replace(',', '.', $bbox[0]);
                $south = (float)str_replace(',', '.', $bbox[1]);
                $east  = (float)str_replace(',', '.', $bbox[2]);
                $north = (float)str_replace(',', '.', $bbox[3]);
            }
        } else {
            $west  = (float)($this->settings['bboxW'] ?? 11.30);
            $south = (float)($this->settings['bboxS'] ?? 48.30);
            $east  = (float)($this->settings['bboxE'] ?? 12.08);
            $north = (float)($this->settings['bboxN'] ?? 48.70);
        }

        $selected = $q['f'] ?? []; // ?f[]=HLF&f[]=DLK
        $mode = ($q['mode'] ?? 'and') === 'or' ? 'or' : 'and';

        $items = [];
        foreach ($this->feuerwehrRepository->findAll() as $fw) {
            $lat = $this->parseCoord($fw->getLatitude(), -90, 90);
            $lon = $this->parseCoord($fw->getLongitude(), -180, 180);
            if ($lat === null || $lon === null) { continue; }

            if ($west !== null && ($lon < $west || $lon > $east || $lat < $south || $lat > $north)) {
                continue;
            }

            $cats = [];
            foreach ($fw->getFahrzeugkategorien() as $cat) {
                $cats[] = (string)$cat->getTitle();
            }

            if (!empty($selected)) {
                $has = array_intersect($selected, $cats);
                $ok = $mode === 'and' ? count($has) === count($selected) : count($has) > 0;
                if (!$ok) { continue; }
            }

            $items[] = [
                'uid' => (int)$fw->getUid(),
                'name' => (string)$fw->getName(),
                'strasse' => (string)$fw->getStrasse(),
                'plz' => (string)$fw->getPlz(),
                'ort' => (string)$fw->getOrt(),
                'lat' => $lat,
                'lon' => $lon,
                'fahrzeuge' => $cats,
            ];
        }

        return new JsonResponse(['items' => $items]);
    }

    // JSON: Overlays (Gemeinden-GeoJSON + KBM/KBI-Zuordnung)
    // Repositories per DI:
    // private GemeindeRepository $gemeindeRepository;
    // private PersonRepository   $personRepository;

    public function apiOverlaysAction(): ResponseInterface
    {
        $q = $this->personRepository->createQuery();
        $q->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setRespectSysLanguage(false)
            ->setIgnoreEnableFields(false)
            ->setIncludeDeleted(false);
        $persons = $q->execute();

        // --- 1) GME sammeln + Index by UID ---
        $gemeinden = [];
        $gjById = []; // uid => normalized geometry array

        foreach ($this->gemeindeRepository->findAll() as $g) {
            $raw = $g->getGeojson();
            $geom = is_string($raw) ? json_decode($raw, true) : $raw;
            if (!is_array($geom)) { continue; }

            // Auf reine Geometry herunterbrechen (Feature/FC -> geometry)
            $geom = $this->extractGeometry($geom);
            if (!$geom) { continue; }

            $uid = (int)$g->getUid();
            $name = (string)$g->getName();

            $gemeinden[] = ['uid' => $uid, 'name' => $name, 'geojson' => $geom];
            $gjById[$uid] = $geom;
        }

        // --- 2) Personen -> Rollen + Gemeinde-UIDs (nur getGemeinde) ---
        $kbmMap = []; // personUid => [gemeindeUid,...]
        $kbiMap = [];

        $dbg = [
            'persons_seen' => 0,
            'role_hits'    => [],
            'skipped'      => [],
        ];

        foreach ($persons as $p) {
            $dbg['persons_seen']++;

            $role = $this->resolveRoleKey($p);         // kbm | fach-kbm | kbi | kbr | null
            if (!$role) { $dbg['skipped'][] = ['uid'=>(int)$p->getUid(),'reason'=>'no-role']; continue; }

            $uids = $this->extractGemeindeUidsSingle($p); // nutzt getGemeinde()
            if (!$uids) { $dbg['skipped'][] = ['uid'=>(int)$p->getUid(),'reason'=>'no-gemeinde']; continue; }

            $dbg['role_hits'][$role] = ($dbg['role_hits'][$role] ?? 0) + 1;

            if ($role === 'kbi') {
                $kbiMap[(int)$p->getUid()] = $uids;
            } elseif ($role === 'kbm' || $role === 'fach-kbm') {
                $kbmMap[(int)$p->getUid()] = $uids;
            } // kbr ignorieren
        }

        // --- 3) Serverseitig "vereinigen": als MultiPolygon (kein topologisches dissolve, aber Frontend-fertig) ---
        $kbmFeatures = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];
        foreach ($kbmMap as $personUid => $gemeindeUids) {
            $multi = $this->multiPolygonFromGemeinden($gemeindeUids, $gjById);
            if ($multi) {
                $kbmFeatures['features'][] = [
                    'type' => 'Feature',
                    'properties' => [
                        'type' => 'kbm',
                        'personUid' => $personUid,
                        'gemeindeUids' => array_values($gemeindeUids),
                    ],
                    'geometry' => $multi,
                ];
            }
        }

        $kbiFeatures = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];
        foreach ($kbiMap as $personUid => $gemeindeUids) {
            $multi = $this->multiPolygonFromGemeinden($gemeindeUids, $gjById);
            if ($multi) {
                $kbiFeatures['features'][] = [
                    'type' => 'Feature',
                    'properties' => [
                        'type' => 'kbi',
                        'personUid' => $personUid,
                        'gemeindeUids' => array_values($gemeindeUids),
                    ],
                    'geometry' => $multi,
                ];
            }
        }

        // --- 4) Payload + optional Debug (via &debug=1) ---
        $qp = $this->request->getQueryParams();
        $wantDebug = !empty($qp['debug']);

        $payload = [
            'gemeinden'    => $gemeinden,
            'kbm'          => (object)$kbmMap,         // Mapping zur Kompatibilität
            'kbi'          => (object)$kbiMap,
            'kbmFeatures'  => $kbmFeatures,            // für direkte Darstellung
            'kbiFeatures'  => $kbiFeatures,
        ];
        if ($wantDebug) {
            $payload['_debug'] = $dbg;
        }

        $res = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('X-Overlays', 'hit'); // damit du sicher siehst, dass diese Action hier läuft

        $res->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $res;
    }

    /** Rolle robust ermitteln – passe hier ggf. deinen Getter an */
    private function resolveRoleKey(object $person): ?string
    {
        // Versuche typische Getter. Falls du einen festen Feldnamen hast, trag ihn an erster Stelle ein.
        foreach (['getRolle', 'getRole', 'getRoleKey', 'getFunktion', 'getTaetigkeit'] as $m) {
            if (method_exists($person, $m)) {
                $val = $person->{$m}();
                if (is_string($val) && $val !== '') {
                    $k = strtolower(trim($val));
                    // Schreibweisen harmonisieren
                    $k = str_replace(['fach kbm','fach- kbm','fachkbm'], 'fach-kbm', $k);
                    return $k;
                }
                if (is_int($val)) {
                    $map = [1=>'kbr', 2=>'kbi', 3=>'kbm', 4=>'fach-kbm'];
                    return $map[$val] ?? null;
                }
            }
        }
        return null;
    }

    /** Holt eine (oder 0) Gemeinde-UID aus getGemeinde() */
    private function extractGemeindeUidsSingle(object $person): array
    {
        if (method_exists($person, 'getGemeinde')) {
            $g = $person->getGemeinde();
            if (is_object($g) && method_exists($g, 'getUid')) {
                return [(int)$g->getUid()];
            }
        }
        return [];
    }

    /** Feature/FeatureCollection -> reine Geometry; andernfalls unverändert. */
    private function extractGeometry(array $in): ?array
    {
        if (($in['type'] ?? '') === 'Feature') {
            return isset($in['geometry']) && is_array($in['geometry']) ? $in['geometry'] : null;
        }
        if (($in['type'] ?? '') === 'FeatureCollection') {
            // nimm die ersten validen Geometrien
            foreach ($in['features'] ?? [] as $f) {
                if (is_array($f) && ($f['type'] ?? '') === 'Feature' && is_array($f['geometry'] ?? null)) {
                    return $f['geometry'];
                }
            }
            return null;
        }
        // already Geometry
        return $in;
    }

    /** Polygon/MultiPolygon -> Liste von Polygon-Ringen */
    private function flattenPolygons(array $geom): array
    {
        $type = $geom['type'] ?? '';
        $coords = $geom['coordinates'] ?? null;
        if (!is_array($coords)) { return []; }

        if ($type === 'Polygon') {
            // [[ring1],[ring2],...]
            return [$coords];
        }
        if ($type === 'MultiPolygon') {
            // [ [[ring...]], [[ring...]], ... ]
            return $coords;
        }
        // (andere Typen ignorieren)
        return [];
    }

    /** Erzeugt aus mehreren Gemeinden ein MultiPolygon (ohne echte topologische Vereinigung) */
    private function multiPolygonFromGemeinden(array $gemeindeUids, array $gjById): ?array
    {
        $polys = [];
        foreach ($gemeindeUids as $uid) {
            $g = $gjById[$uid] ?? null;
            if (!$g) { continue; }
            foreach ($this->flattenPolygons($g) as $poly) {
                $polys[] = $poly; // each $poly ist [[ring...]]
            }
        }
        if (!$polys) { return null; }

        return [
            'type' => 'MultiPolygon',
            'coordinates' => $polys,
        ];
    }


    // Helper
    private function parseCoord($value, float $min, float $max): ?float
    {
        if ($value === null) { return null; }
        $s = trim((string)$value);
        if ($s === '') { return null; }
        $s = str_replace(',', '.', $s);
        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $s)) { return null; }
        $f = (float)$s;
        if ($f < $min || $f > $max) { return null; }
        return $f;
    }
}
