<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Service;

/**
 * Service for GeoJSON geometry operations.
 *
 * Provides utility methods for normalizing and combining GeoJSON geometries,
 * particularly for handling Polygon and MultiPolygon types from municipality data.
 */
final class GeoJsonService
{
    /**
     * Normalizes various GeoJSON input formats to a geometry object.
     *
     * Handles:
     * - Raw JSON strings
     * - Feature objects (extracts geometry)
     * - FeatureCollection objects (extracts first feature's geometry)
     * - Direct geometry objects (Polygon, MultiPolygon)
     *
     * @param string|array<string, mixed>|null $raw Raw GeoJSON input
     * @return array<string, mixed>|null Normalized geometry or null
     */
    public function normalizeGeometry(string|array|null $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        $geom = is_string($raw) ? json_decode($raw, true) : $raw;

        if (!is_array($geom)) {
            return null;
        }

        $type = $geom['type'] ?? '';

        if ($type === 'Feature' && isset($geom['geometry'])) {
            return $geom['geometry'];
        }

        if ($type === 'FeatureCollection') {
            foreach ($geom['features'] ?? [] as $feature) {
                if (($feature['type'] ?? '') === 'Feature' && is_array($feature['geometry'] ?? null)) {
                    return $feature['geometry'];
                }
            }
            return null;
        }

        if ($type === 'Polygon' || $type === 'MultiPolygon') {
            return $geom;
        }

        return $geom;
    }

    /**
     * Creates a MultiPolygon geometry from multiple municipality geometries.
     *
     * @param array<int, int> $gemeindeUids List of Gemeinde UIDs to include
     * @param array<int, array<string, mixed>> $geometriesByGemeinde Map of UID => geometry
     * @return array<string, mixed>|null MultiPolygon geometry or null if no valid polygons
     */
    public function createMultiPolygonFromGemeinden(array $gemeindeUids, array $geometriesByGemeinde): ?array
    {
        $polygons = [];

        foreach (array_unique($gemeindeUids) as $uid) {
            $geometry = $geometriesByGemeinde[$uid] ?? null;

            if ($geometry === null) {
                continue;
            }

            $type = $geometry['type'] ?? '';
            $coordinates = $geometry['coordinates'] ?? null;

            if (!is_array($coordinates)) {
                continue;
            }

            if ($type === 'Polygon') {
                $polygons[] = $coordinates;
            } elseif ($type === 'MultiPolygon') {
                foreach ($coordinates as $polygon) {
                    $polygons[] = $polygon;
                }
            }
        }

        if ($polygons === []) {
            return null;
        }

        return [
            'type' => 'MultiPolygon',
            'coordinates' => $polygons,
        ];
    }

    /**
     * Validates and parses a coordinate value.
     *
     * @param mixed $value Input value (string or numeric)
     * @param float $min Minimum allowed value
     * @param float $max Maximum allowed value
     * @return float|null Parsed value or null if invalid
     */
    public function parseCoordinate(mixed $value, float $min, float $max): ?float
    {
        if ($value === null) {
            return null;
        }

        $stringValue = str_replace(',', '.', trim((string) $value));

        if ($stringValue === '' || !preg_match('/^-?\d+(?:\.\d+)?$/', $stringValue)) {
            return null;
        }

        $floatValue = (float) $stringValue;

        if ($floatValue < $min || $floatValue > $max) {
            return null;
        }

        return $floatValue;
    }

    /**
     * Creates a GeoJSON Feature from an entity with geometry.
     *
     * @param int $uid Entity UID
     * @param string $type Feature type identifier
     * @param string $title Feature title
     * @param array<string, mixed> $geometry GeoJSON geometry
     * @param array<string, mixed> $additionalProperties Additional properties to include
     * @return array<string, mixed> GeoJSON Feature
     */
    public function createFeature(
        int $uid,
        string $type,
        string $title,
        array $geometry,
        array $additionalProperties = []
    ): array {
        return [
            'type' => 'Feature',
            'properties' => array_merge([
                'uid' => $uid,
                'type' => $type,
                'title' => $title,
            ], $additionalProperties),
            'geometry' => $geometry,
        ];
    }
}
