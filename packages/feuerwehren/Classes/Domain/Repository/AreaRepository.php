<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use Schmid\Feuerwehren\Domain\Model\Area;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for Area domain model.
 *
 * Provides methods to query organizational areas (KBR, KBI, KBM, Fach-KBM)
 * with their hierarchical relationships.
 *
 * @extends Repository<Area>
 */
final class AreaRepository extends Repository
{
    /** @var array<string, string> */
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
    ];

    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Finds all root areas (areas without a parent).
     *
     * @return QueryResultInterface<Area>
     */
    public function findRootNodes(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('parent', null));

        return $query->execute();
    }

    /**
     * Finds all areas of a specific type.
     *
     * @param string $type Area type ('kbr', 'kbi', 'kbm', 'fach-kbm')
     * @return QueryResultInterface<Area>
     */
    public function findByType(string $type): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('type', $type));

        return $query->execute();
    }

    /**
     * Finds all KBM areas under a specific parent (typically a KBI).
     *
     * @param Area $parent The parent area (KBI)
     * @return QueryResultInterface<Area>
     */
    public function findKbmsByParent(Area $parent): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('type', 'kbm'),
                $query->equals('parent', $parent)
            )
        );

        return $query->execute();
    }
}