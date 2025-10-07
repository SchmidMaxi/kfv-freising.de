<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Service\Api;

use Schmid\Feuerwehren\Domain\Model\Area;
use Schmid\Feuerwehren\Domain\Model\Gemeinde;
use Schmid\Feuerwehren\Domain\Repository\AreaRepository;
use Schmid\Feuerwehren\Domain\Repository\GemeindeRepository;

final class OverlaysService
{
    public function __construct(
        private readonly GemeindeRepository $gemeindeRepository,
        private readonly AreaRepository $areaRepository
    ) {
    }

    public function overlays(): array
    {
        $gjByGemeinde = [];
        $gemeindenResult = [];
        /** @var Gemeinde $g */
        foreach ($this->gemeindeRepository->findAll() as $g) {
            $geom = $this->normalizeGeom($g->getGemeindegebiet());
            if (!$geom) {
                continue;
            }
            $gid = $g->getUid();
            $gemeindenResult[] = ['uid' => $gid, 'name' => (string)$g->getName(), 'geojson' => $geom];
            $gjByGemeinde[$gid] = $geom;
        }

        // KBM = MultiPolygon aus zugeordneten Gemeinden
        $kbmFeatures = ['type' => 'FeatureCollection', 'features' => []];
        /** @var Area $kbm */
        foreach ($this->areaRepository->findByType('kbm') as $kbm) {
            $gids = [];
            foreach ($kbm->getGemeinden() as $g) {
                $gids[] = $g->getUid();
            }
            $multi = $this->multiFromGemeinden($gids, $gjByGemeinde);
            if ($multi) {
                $kbmFeatures['features'][] = $this->createFeature($kbm, $multi, 'kbm');
            }
        }

        // KBI = MultiPolygon aus allen KBM-Kindern
        $kbiFeatures = ['type' => 'FeatureCollection', 'features' => []];
        /** @var Area $kbi */
        foreach ($this->areaRepository->findByType('kbi') as $kbi) {
            $gids = [];
            foreach ($this->areaRepository->findKbmsByParent($kbi) as $kbm) {
                foreach ($kbm->getGemeinden() as $g) {
                    $gids[] = $g->getUid();
                }
            }
            $multi = $this->multiFromGemeinden($gids, $gjByGemeinde);
            if ($multi) {
                $kbiFeatures['features'][] = $this->createFeature($kbi, $multi, 'kbi');
            }
        }

        return [
            'gemeinden' => $gemeindenResult,
            'kbmFeatures' => $kbmFeatures,
            'kbiFeatures' => $kbiFeatures,
        ];
    }

    private function createFeature(Area $area, array $geometry, string $type): array
    {
        return [
            'type' => 'Feature',
            'properties' => [
                'type' => $type,
                'areaUid' => $area->getUid(),
                'title' => $area->getTitle(),
                'parentUid' => $area->getParent()?->getUid(),
            ],
            'geometry' => $geometry,
        ];
    }

    private function normalizeGeom(?string $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        $geom = json_decode($raw, true);
        if (!is_array($geom)) {
            return null;
        }
        $type = $geom['type'] ?? '';
        if ($type === 'Feature' && isset($geom['geometry'])) {
            return $geom['geometry'];
        }
        if ($type === 'FeatureCollection') {
            foreach ($geom['features'] ?? [] as $f) {
                if (($f['type'] ?? '') === 'Feature' && is_array($f['geometry'] ?? null)) {
                    return $f['geometry'];
                }
            }
            return null;
        }
        return ($type === 'Polygon' || $type === 'MultiPolygon') ? $geom : null;
    }

    private function multiFromGemeinden(array $gemeindeUids, array $gjByGemeinde): ?array
    {
        $polys = [];
        foreach (array_unique($gemeindeUids) as $gid) {
            $g = $gjByGemeinde[$gid] ?? null;
            if (!$g) {
                continue;
            }
            $type = $g['type'] ?? '';
            $coords = $g['coordinates'] ?? null;
            if (!is_array($coords)) {
                continue;
            }
            if ($type === 'Polygon') {
                $polys[] = $coords;
            } elseif ($type === 'MultiPolygon') {
                foreach ($coords as $p) {
                    $polys[] = $p;
                }
            }
        }
        return $polys ? ['type' => 'MultiPolygon', 'coordinates' => $polys] : null;
    }
}