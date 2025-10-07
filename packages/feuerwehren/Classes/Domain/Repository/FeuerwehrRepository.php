<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

final class FeuerwehrRepository extends Repository
{
    /**
     * Stellt sicher, dass alle Datensätze gefunden werden,
     * unabhängig von der Seitenspeicherung (PID).
     */
    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

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
        $mainConstraints = [];

        // 1. Bounding Box Constraints (immer AND)
        // KORREKTUR: Variablen statt Strings verwenden
        $mainConstraints[] = $query->greaterThanOrEqual('longitude', $west);
        $mainConstraints[] = $query->lessThanOrEqual('longitude', $east);
        $mainConstraints[] = $query->greaterThanOrEqual('latitude', $south);
        $mainConstraints[] = $query->lessThanOrEqual('latitude', $north);

        // 2. Fahrzeugkategorien verarbeiten
        if (!empty($fahrzeugKategorieUids)) {
            $categoryConstraints = [];
            foreach ($fahrzeugKategorieUids as $uid) {
                $categoryConstraints[] = $query->contains('fahrzeugkategorien', $uid);
            }

            if (count($categoryConstraints) > 0) {
                if ($mode === 'or') {
                    $combinedCategoryConstraint = $query->logicalOr(...$categoryConstraints);
                } else {
                    $combinedCategoryConstraint = $query->logicalAnd(...$categoryConstraints);
                }
                $mainConstraints[] = $combinedCategoryConstraint;
            }
        }

        if (empty($mainConstraints)) {
            return $query->execute();
        }

        $query->matching($query->logicalAnd(...$mainConstraints));

        return $query->execute();
    }
}