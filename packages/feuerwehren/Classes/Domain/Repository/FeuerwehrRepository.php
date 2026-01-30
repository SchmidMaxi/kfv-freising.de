<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for Feuerwehr (fire department) domain model.
 *
 * Provides methods for querying fire departments, including
 * geographic bounding box searches with optional vehicle category filtering.
 *
 * @extends Repository<Feuerwehr>
 */
final class FeuerwehrRepository extends Repository
{
    /**
     * Initializes query settings to ignore storage page restrictions.
     *
     * Fire department data should be accessible across all pages.
     */
    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Finds fire departments within a geographic bounding box.
     *
     * Optionally filters by vehicle categories using AND or OR logic.
     *
     * @param float $west Western boundary (minimum longitude)
     * @param float $south Southern boundary (minimum latitude)
     * @param float $east Eastern boundary (maximum longitude)
     * @param float $north Northern boundary (maximum latitude)
     * @param list<int> $fahrzeugKategorieUids Vehicle category UIDs to filter by
     * @param 'and'|'or' $mode How to combine category filters
     * @return QueryResultInterface<Feuerwehr>
     */
    public function findForMapSearch(
        float $west,
        float $south,
        float $east,
        float $north,
        array $fahrzeugKategorieUids = [],
        string $mode = 'and'
    ): QueryResultInterface {
        $query = $this->createQuery();

        $constraints = [
            $query->greaterThanOrEqual('longitude', $west),
            $query->lessThanOrEqual('longitude', $east),
            $query->greaterThanOrEqual('latitude', $south),
            $query->lessThanOrEqual('latitude', $north),
        ];

        if ($fahrzeugKategorieUids !== []) {
            $categoryConstraints = array_map(
                fn(int $uid) => $query->contains('fahrzeugkategorien', $uid),
                $fahrzeugKategorieUids
            );

            $constraints[] = $mode === 'or'
                ? $query->logicalOr(...$categoryConstraints)
                : $query->logicalAnd(...$categoryConstraints);
        }

        $query->matching($query->logicalAnd(...$constraints));

        return $query->execute();
    }
}