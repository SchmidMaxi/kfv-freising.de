<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Http\JsonResponse;
use Schmid\Feuerwehren\Domain\Repository\{
    FeuerwehrRepository,
    FahrzeugkategorieRepository,
    GemeindeRepository,
    AreaRepository
};

final class FeuerwehrController extends ActionController
{
    public function __construct(
        protected FeuerwehrRepository $feuerwehrRepository,
        protected FahrzeugkategorieRepository $fahrzeugkategorieRepository,
        protected GemeindeRepository $gemeindeRepository,
        protected AreaRepository $areaRepository
    ) {}

    public function listAction(): ResponseInterface
    {
        $kategorien = $this->fahrzeugkategorieRepository->findAll();

        $mapConfig = [
            'vtUrlTemplate' => (string)($this->settings['vtUrlTemplate'] ?? '/_vt/{z}/{x}/{y}.pbf'),
            'apiSearchUrl'  => (string)($this->settings['apiSearchUrl']  ?? '/?type=171001'),
            'overlaysUrl'   => (string)($this->settings['overlaysUrl']   ?? '/?type=171002'),
        ];

        $this->view->assignMultiple([
            'fahrzeugkategorien' => $kategorien,
            'mapConfig' => $mapConfig,
        ]);
        return $this->htmlResponse();
    }

    public function apiSearchAction(ServerRequestInterface $request): ResponseInterface
    {
        $q = $request->getQueryParams();

        // BBOX (Default = LKR Freising)
        if (!empty($q['bbox'])) {
            [$west, $south, $east, $north] = array_map(static fn($v) => (float)str_replace(',', '.', trim($v)), explode(',', (string)$q['bbox']));
        } else {
            $west  = (float)($this->settings['bboxW'] ?? 11.30);
            $south = (float)($this->settings['bboxS'] ?? 48.30);
            $east  = (float)($this->settings['bboxE'] ?? 12.08);
            $north = (float)($this->settings['bboxN'] ?? 48.70);
        }

        $selected = $q['f'] ?? [];
        $mode = ($q['mode'] ?? 'and') === 'or' ? 'or' : 'and';

        $items = [];
        foreach ($this->feuerwehrRepository->findAll() as $fw) {
            $lat = $this->parseCoord($fw->getLatitude(), -90, 90);
            $lon = $this->parseCoord($fw->getLongitude(), -180, 180);
            if ($lat === null || $lon === null) { continue; }
            if ($lon < $west || $lon > $east || $lat < $south || $lat > $north) { continue; }

            $cats = [];
            foreach ($fw->getFahrzeugkategorien() as $cat) { $cats[] = (string)$cat->getTitle(); }

            if ($selected) {
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

    public function apiOverlaysAction(): ResponseInterface
    {
        $gemeinden = [];
        $gjByGemeinde = [];

        foreach ($this->gemeindeRepository->findAll() as $g) {
            $geom = $this->normalizeGeom($g->getGeojson());
            if (!$geom) { continue; }
            $gid = (int)$g->getUid();
            $gemeinden[] = ['uid' => $gid, 'name' => (string)$g->getName(), 'geojson' => $geom];
            $gjByGemeinde[$gid] = $geom;
        }

        // KBM = MultiPolygon aus zugeordneten Gemeinden
        $kbmFeatures = ['type'=>'FeatureCollection','features'=>[]];
        foreach ($this->areaRepository->findByType('kbm') as $kbm) {
            $gids = [];
            foreach ($kbm->getGemeinden() as $g) { $gids[] = (int)$g->getUid(); }
            $multi = $this->multiFromGemeinden($gids, $gjByGemeinde);
            if ($multi) {
                $kbmFeatures['features'][] = [
                    'type'=>'Feature',
                    'properties'=>[
                        'type'=>'kbm',
                        'areaUid'=>(int)$kbm->getUid(),
                        'title'=>$kbm->getTitle(),
                        'parentUid'=>$kbm->getParent()?->getUid(),
                    ],
                    'geometry'=>$multi
                ];
            }
        }

        // KBI = MultiPolygon aus allen KBM-Kindern
        $kbiFeatures = ['type'=>'FeatureCollection','features'=>[]];
        foreach ($this->areaRepository->findByType('kbi') as $kbi) {
            $gids = [];
            foreach ($this->areaRepository->findKbmsByParent($kbi) as $kbm) {
                foreach ($kbm->getGemeinden() as $g) { $gids[] = (int)$g->getUid(); }
            }
            $multi = $this->multiFromGemeinden($gids, $gjByGemeinde);
            if ($multi) {
                $kbiFeatures['features'][] = [
                    'type'=>'Feature',
                    'properties'=>[
                        'type'=>'kbi',
                        'areaUid'=>(int)$kbi->getUid(),
                        'title'=>$kbi->getTitle(),
                        'parentUid'=>$kbi->getParent()?->getUid(),
                    ],
                    'geometry'=>$multi
                ];
            }
        }

        return new JsonResponse([
            'gemeinden'   => $gemeinden,
            'kbmFeatures' => $kbmFeatures,
            'kbiFeatures' => $kbiFeatures,
            'kbm' => (object)[], 'kbi' => (object)[], // Kompatibilität
        ]);
    }

    private function normalizeGeom($raw): ?array {
        $geom = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($geom)) { return null; }
        $type = $geom['type'] ?? '';
        if ($type === 'Feature' && isset($geom['geometry'])) return $geom['geometry'];
        if ($type === 'FeatureCollection') {
            foreach ($geom['features'] ?? [] as $f) {
                if (($f['type'] ?? '')==='Feature' && is_array($f['geometry'] ?? null)) {
                    return $f['geometry'];
                }
            }
            return null;
        }
        return $geom;
    }

    private function multiFromGemeinden(array $gemeindeUids, array $gjByGemeinde): ?array {
        $polys = [];
        foreach (array_unique($gemeindeUids) as $gid) {
            $g = $gjByGemeinde[$gid] ?? null;
            if (!$g) continue;
            $type = $g['type'] ?? '';
            $coords = $g['coordinates'] ?? null;
            if (!is_array($coords)) continue;
            if ($type === 'Polygon') { $polys[] = $coords; }
            elseif ($type === 'MultiPolygon') { foreach ($coords as $p) { $polys[] = $p; } }
        }
        return $polys ? ['type'=>'MultiPolygon','coordinates'=>$polys] : null;
    }

    private function parseCoord($value, float $min, float $max): ?float {
        if ($value === null) { return null; }
        $s = str_replace(',', '.', trim((string)$value));
        if ($s === '' || !preg_match('/^-?\d+(?:\.\d+)?$/', $s)) { return null; }
        $f = (float)$s;
        return ($f < $min || $f > $max) ? null : $f;
    }
}
