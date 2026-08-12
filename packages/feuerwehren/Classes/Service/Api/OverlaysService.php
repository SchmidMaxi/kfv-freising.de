<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Service\Api;

use Schmid\Feuerwehren\Domain\Model\Area;
use Schmid\Feuerwehren\Domain\Model\Gemeinde;
use Schmid\Feuerwehren\Domain\Repository\AreaRepository;
use Schmid\Feuerwehren\Domain\Repository\GemeindeRepository;
use Schmid\Feuerwehren\Service\GeoJsonService;

/**
 * Service for generating GeoJSON overlay data for the fire department map.
 *
 * Provides municipality boundaries, KBM (Kreisbrandmeister) areas,
 * and KBI (Kreisbrandinspektor) areas as GeoJSON FeatureCollections.
 */
final class OverlaysService
{
    public function __construct(
        private readonly GemeindeRepository $gemeindeRepository,
        private readonly AreaRepository $areaRepository,
        private readonly GeoJsonService $geoJsonService,
    ) {}

    /**
     * Generates all overlay data for the fire department map.
     *
     * @return array{
     *     gemeinden: list<array{uid: int, name: string, geojson: array<string, mixed>}>,
     *     kbmFeatures: array{type: string, features: list<array<string, mixed>>},
     *     kbiFeatures: array{type: string, features: list<array<string, mixed>>}
     * }
     */
    public function overlays(): array
    {
        $geometriesByGemeinde = [];
        $gemeindenResult = [];

        /** @var Gemeinde $gemeinde */
        foreach ($this->gemeindeRepository->findAll() as $gemeinde) {
            $geometry = $this->geoJsonService->normalizeGeometry($gemeinde->getGemeindegebiet());

            if ($geometry === null) {
                continue;
            }

            $uid = $gemeinde->getUid();
            $gemeindenResult[] = [
                'uid' => $uid,
                'name' => $gemeinde->getName(),
                'geojson' => $geometry,
            ];
            $geometriesByGemeinde[$uid] = $geometry;
        }

        $kbmFeatures = $this->buildKbmFeatures($geometriesByGemeinde);
        $kbiFeatures = $this->buildKbiFeatures($geometriesByGemeinde);

        return [
            'gemeinden' => $gemeindenResult,
            'kbmFeatures' => $kbmFeatures,
            'kbiFeatures' => $kbiFeatures,
        ];
    }

    /**
     * Builds KBM (Kreisbrandmeister) area features.
     *
     * @param array<int, array<string, mixed>> $geometriesByGemeinde
     * @return array{type: string, features: list<array<string, mixed>>}
     */
    private function buildKbmFeatures(array $geometriesByGemeinde): array
    {
        $features = [];

        /** @var Area $kbm */
        foreach ($this->areaRepository->findByType('kbm') as $kbm) {
            $gemeindeUids = [];

            foreach ($kbm->getGemeinden() as $gemeinde) {
                $gemeindeUids[] = $gemeinde->getUid();
            }

            $multiPolygon = $this->geoJsonService->createMultiPolygonFromGemeinden(
                $gemeindeUids,
                $geometriesByGemeinde
            );

            if ($multiPolygon !== null) {
                $features[] = $this->createAreaFeature($kbm, $multiPolygon, 'kbm');
            }
        }

        return ['type' => 'FeatureCollection', 'features' => $features];
    }

    /**
     * Builds KBI (Kreisbrandinspektor) area features.
     *
     * @param array<int, array<string, mixed>> $geometriesByGemeinde
     * @return array{type: string, features: list<array<string, mixed>>}
     */
    private function buildKbiFeatures(array $geometriesByGemeinde): array
    {
        $features = [];

        /** @var Area $kbi */
        foreach ($this->areaRepository->findByType('kbi') as $kbi) {
            $gemeindeUids = [];

            foreach ($this->areaRepository->findKbmsByParent($kbi) as $kbm) {
                foreach ($kbm->getGemeinden() as $gemeinde) {
                    $gemeindeUids[] = $gemeinde->getUid();
                }
            }

            $multiPolygon = $this->geoJsonService->createMultiPolygonFromGemeinden(
                $gemeindeUids,
                $geometriesByGemeinde
            );

            if ($multiPolygon !== null) {
                $features[] = $this->createAreaFeature($kbi, $multiPolygon, 'kbi');
            }
        }

        return ['type' => 'FeatureCollection', 'features' => $features];
    }

    /**
     * Creates a GeoJSON Feature from an Area entity.
     *
     * @param Area $area The area entity
     * @param array<string, mixed> $geometry The geometry
     * @param string $type The feature type ('kbm' or 'kbi')
     * @return array<string, mixed>
     */
    private function createAreaFeature(Area $area, array $geometry, string $type): array
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
}