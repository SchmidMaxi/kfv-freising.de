<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Http\JsonResponse;
use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
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
