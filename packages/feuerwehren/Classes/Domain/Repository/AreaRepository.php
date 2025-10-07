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
     * HINZUGEFÜGT: Stellt sicher, dass alle Datensätze gefunden werden,
     * unabhängig von der Seitenspeicherung (PID).
     */
    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

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