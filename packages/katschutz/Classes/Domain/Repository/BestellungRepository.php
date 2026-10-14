<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Repository;

use Schmid\Katschutz\Domain\Enum\Bestellstatus;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Model\Lage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Bestellung>
 */
final class BestellungRepository extends Repository
{
    protected $defaultOrderings = ['crdate' => QueryInterface::ORDER_DESCENDING];

    /**
     * @return QueryResultInterface<int, Bestellung>
     */
    public function findByBestellerUid(int $feUser): QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching($query->equals('besteller', $feUser))->execute();
    }

    /**
     * @param list<Bestellstatus> $status leer = alle
     * @return QueryResultInterface<int, Bestellung>
     */
    public function findByStatus(array $status): QueryResultInterface
    {
        $query = $this->createQuery();
        if ($status !== []) {
            $query->matching($query->in('status', array_map(static fn(Bestellstatus $s): string => $s->value, $status)));
        }
        return $query->execute();
    }

    /**
     * @return QueryResultInterface<int, Bestellung>
     */
    public function findByLage(Lage $lage): QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching($query->equals('lage', $lage))->execute();
    }
}
