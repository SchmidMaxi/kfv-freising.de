<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

final class FeuerwehrRepository extends Repository
{
    /**
     * Findet Feuerwehren basierend auf Bounding Box und Fahrzeugkategorien.
     *
     * @param float $west
     * @param float $south
     * @param float $east
     * @param float $north
     * @param int[] $fahrzeugKategorieUids
     * @param string $mode ('and' | 'or')
     * @return QueryResultInterface
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
        $constraints = [];

        // 1. Bounding Box Constraint
        $constraints[] = $query->logicalAnd([
            $query->greaterThanOrEqual('longitude', $west),
            $query->lessThanOrEqual('longitude', $east),
            $query->greaterThanOrEqual('latitude', $south),
            $query->lessThanOrEqual('latitude', $north),
        ]);

        // 2. Fahrzeugkategorien Constraint
        if (!empty($fahrzeugKategorieUids)) {
            $categoryConstraints = [];
            foreach ($fahrzeugKategorieUids as $uid) {
                // 'fahrzeugkategorien' ist der Property-Name im Model
                $categoryConstraints[] = $query->contains('fahrzeugkategorien', $uid);
            }

            if ($mode === 'or') {
                $constraints[] = $query->logicalOr($categoryConstraints);
            } else {
                // 'AND' ist der Standard bei logicalAnd
                $constraints[] = $query->logicalAnd($categoryConstraints);
            }
        }

        if (empty($constraints)) {
            return $query->execute();
        }

        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }
}