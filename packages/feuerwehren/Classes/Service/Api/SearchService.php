<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Service\Api;

use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use Schmid\Feuerwehren\Domain\Repository\FeuerwehrRepository;

/**
 * Service for searching fire departments via the API.
 *
 * Handles search requests with bounding box filtering and
 * optional vehicle category constraints.
 */
final class SearchService
{
    private const DEFAULT_BBOX = [
        'west' => 11.30,
        'south' => 48.30,
        'east' => 12.08,
        'north' => 48.70,
    ];

    private const MAX_NAME_QUERY_LENGTH = 100;

    public function __construct(
        private readonly FeuerwehrRepository $feuerwehrRepository,
    ) {}

    /**
     * Searches fire departments within a bounding box.
     *
     * @param array<string, mixed> $queryParams Query parameters:
     *   - bbox: string "west,south,east,north" (optional, defaults to Freising region)
     *   - f[]: array<int> Vehicle category UIDs to filter by (optional)
     *   - mode: string 'and'|'or' for combining filters (default: 'and')
     *   - q: string name substring; when given, the bbox is ignored and the whole
     *     district is searched so hits outside the current viewport are found
     * @return array{items: list<array{
     *     uid: int,
     *     name: string,
     *     strasse: string,
     *     plz: string,
     *     ort: string,
     *     lat: float|null,
     *     lon: float|null,
     *     mitglieder: int,
     *     typ: string,
     *     fahrzeuge: list<string>
     * }>}
     */
    public function search(array $queryParams): array
    {
        $nameQuery = $this->parseNameQuery($queryParams['q'] ?? null);

        // Bei einer Namenssuche landkreisweit suchen statt nur im sichtbaren
        // Kartenausschnitt — sonst findet die Suche nichts, was gerade ausserhalb
        // des Viewports liegt.
        $bbox = $nameQuery === ''
            ? $this->parseBoundingBox($queryParams['bbox'] ?? null)
            : self::DEFAULT_BBOX;

        $categoryUids = $this->parseCategoryFilter($queryParams['f'] ?? []);
        $mode = $this->parseFilterMode($queryParams['mode'] ?? null);

        $feuerwehren = $this->feuerwehrRepository->findForMapSearch(
            $bbox['west'],
            $bbox['south'],
            $bbox['east'],
            $bbox['north'],
            $categoryUids,
            $mode,
            $nameQuery
        );

        $items = [];

        /** @var Feuerwehr $feuerwehr */
        foreach ($feuerwehren as $feuerwehr) {
            $items[] = $this->transformToApiResponse($feuerwehr);
        }

        return ['items' => $items];
    }

    /**
     * Parses the bounding box parameter.
     *
     * @return array{west: float, south: float, east: float, north: float}
     */
    private function parseBoundingBox(mixed $bbox): array
    {
        if (!is_string($bbox) || $bbox === '') {
            return self::DEFAULT_BBOX;
        }

        $coords = explode(',', $bbox);

        if (count($coords) !== 4) {
            return self::DEFAULT_BBOX;
        }

        return [
            'west' => (float)$coords[0],
            'south' => (float)$coords[1],
            'east' => (float)$coords[2],
            'north' => (float)$coords[3],
        ];
    }

    /**
     * Parses and validates the category filter.
     *
     * @param mixed $filter Raw filter input
     * @return list<int> Valid category UIDs
     */
    private function parseCategoryFilter(mixed $filter): array
    {
        if (!is_array($filter)) {
            return [];
        }

        return array_values(array_filter(
            array_map('intval', $filter),
            static fn(int $uid): bool => $uid > 0
        ));
    }

    /**
     * Parses the filter mode parameter.
     *
     * @return 'and'|'or'
     */
    private function parseFilterMode(mixed $mode): string
    {
        return $mode === 'or' ? 'or' : 'and';
    }

    /**
     * Parses and normalises the free-text name query.
     *
     * LIKE wildcards are stripped here rather than in the repository so that the
     * "is this a search?" decision (which discards the bbox) and the actual name
     * constraint always agree — otherwise a query of just "%" would drop the bbox
     * but apply no name filter, returning the whole district.
     *
     * Returns an empty string when no usable query was supplied, so callers can
     * distinguish "no search" from "search". Length is capped to keep the LIKE
     * constraint cheap.
     */
    private function parseNameQuery(mixed $query): string
    {
        if (!is_string($query)) {
            return '';
        }

        $normalised = trim(str_replace(['%', '_'], '', $query));

        return mb_substr($normalised, 0, self::MAX_NAME_QUERY_LENGTH);
    }

    /**
     * Transforms a Feuerwehr entity to an API response array.
     *
     * @return array{
     *     uid: int,
     *     name: string,
     *     strasse: string,
     *     plz: string,
     *     ort: string,
     *     lat: float|null,
     *     lon: float|null,
     *     mitglieder: int,
     *     typ: string,
     *     fahrzeuge: list<string>
     * }
     */
    private function transformToApiResponse(Feuerwehr $feuerwehr): array
    {
        $fahrzeuge = [];

        foreach ($feuerwehr->getFahrzeugkategorien() as $kategorie) {
            $fahrzeuge[] = $kategorie->getTitle();
        }

        return [
            'uid' => $feuerwehr->getUid(),
            'name' => $feuerwehr->getName(),
            'strasse' => $feuerwehr->getStrasse(),
            'plz' => $feuerwehr->getPlz(),
            'ort' => $feuerwehr->getOrt(),
            'lat' => $feuerwehr->getLatitude(),
            'lon' => $feuerwehr->getLongitude(),
            'mitglieder' => $feuerwehr->getMitglieder(),
            'typ' => $feuerwehr->getTyp(),
            'fahrzeuge' => $fahrzeuge,
        ];
    }
}
