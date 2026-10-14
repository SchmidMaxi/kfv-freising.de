<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Repository;

use Schmid\Katschutz\Domain\Model\Artikel;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Model\Buchung;
use Schmid\Katschutz\Domain\Model\Lage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Buchung>
 */
final class BuchungRepository extends Repository
{
    protected $defaultOrderings = ['uid' => QueryInterface::ORDER_DESCENDING];

    /**
     * @return QueryResultInterface<int, Buchung>
     */
    public function findJournal(?Artikel $artikel = null, int $limit = 200): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        if ($artikel !== null) {
            $query->matching($query->equals('artikel', $artikel));
        }
        return $query->setLimit($limit)->execute();
    }

    /**
     * @return QueryResultInterface<int, Buchung>
     */
    public function findByBestellung(Bestellung $bestellung): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->setOrderings(['uid' => QueryInterface::ORDER_ASCENDING]);
        return $query->matching($query->equals('bestellung', $bestellung))->execute();
    }

    /**
     * @return QueryResultInterface<int, Buchung>
     */
    public function findByLage(Lage $lage): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->setOrderings(['uid' => QueryInterface::ORDER_ASCENDING]);
        return $query->matching($query->equals('lage', $lage))->execute();
    }
}
