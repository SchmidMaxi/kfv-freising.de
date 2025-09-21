<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

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

    /** JSON: Overlays (Gemeinden-GeoJSON + KBM/KBI-Zuordnung) */
    // Classes/Controller/FeuerwehrController.php

    public function apiOverlaysAction(): \Psr\Http\Message\ResponseInterface
    {
        // 1) Gemeinden einsammeln (wie gehabt)
        $gemeinden = [];
        foreach ($this->gemeindeRepository->findAll() as $g) {
            $gj = $g->getGeojson(); // string|array, je nach Modell
            if (is_string($gj)) {
                $decoded = json_decode($gj, true);
                if (!is_array($decoded)) { continue; }
                $gj = $decoded;
            }
            if (!is_array($gj)) { continue; }

            $gemeinden[] = [
                'uid' => (int)$g->getUid(),
                'name' => (string)$g->getName(),
                'geojson' => $gj,
            ];
        }

        // 2) Aus Personen die Bereiche ableiten
        $kbm = []; // personUid => [gemeindeUid,...]
        $kbi = []; // personUid => [gemeindeUid,...]

        foreach ($this->personRepository->findAll() as $p) {
            $role = strtolower((string)$p->getRolle()); // "kbm", "fach-kbm", "kbi", "kbr", ...
            // Hole die zugeordneten Gemeinden (Multi-Select)
            $gUids = [];
            $gs = $p->getGemeinden();
            if ($gs) {
                foreach ($gs as $g) {
                    $gUids[] = (int)$g->getUid();
                }
            }

            if (in_array($role, ['kbm', 'fach-kbm'], true)) {
                $kbm[(int)$p->getUid()] = $gUids;
            } elseif ($role === 'kbi') {
                $kbi[(int)$p->getUid()] = $gUids;
            }
            // KBR wird hier ignoriert
        }

        // 3) JSON-Antwort
        $payload = [
            'gemeinden' => $gemeinden,
            'kbm' => $kbm,
            'kbi' => $kbi,
        ];

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        return $response;
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
