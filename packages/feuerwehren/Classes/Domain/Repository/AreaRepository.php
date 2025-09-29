<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Schmid\Feuerwehren\Domain\Model\Area;

final class AreaRepository extends Repository
{
    /**
     * @var array<string,int>
     */
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|Area[]
     */
    public function findByType(string $type)
    {
        $q = $this->createQuery();
        $q->matching($q->equals('type', $type));
        return $q->execute();
    }

    /**
     * Alle KBM unterhalb einer KBI.
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|Area[]
     */
    public function findKbmsByParent(Area $parent)
    {
        $q = $this->createQuery();
        $q->matching(
            $q->logicalAnd(
                $q->equals('type', 'kbm'),
                $q->equals('parent', $parent)
            )
        );
        return $q->execute();
    }
}
