<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Repository;

use Schmid\Katschutz\Domain\Model\Lage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Lage>
 */
final class LageRepository extends Repository
{
    protected $defaultOrderings = ['beginn' => QueryInterface::ORDER_DESCENDING];

    public function findAktive(): ?Lage
    {
        $query = $this->createQuery();
        return $query->matching($query->equals('aktiv', true))->setLimit(1)->execute()->getFirst();
    }
}
