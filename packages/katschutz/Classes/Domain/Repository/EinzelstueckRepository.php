<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Repository;

use Schmid\Katschutz\Domain\Model\Artikel;
use Schmid\Katschutz\Domain\Model\Einzelstueck;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Einzelstueck>
 */
final class EinzelstueckRepository extends Repository
{
    protected $defaultOrderings = ['inventarnummer' => QueryInterface::ORDER_ASCENDING];

    /**
     * @return QueryResultInterface<int, Einzelstueck>
     */
    public function findByArtikelInklusiveArchiv(Artikel $artikel): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true)->setEnableFieldsToBeIgnored(['disabled']);
        return $query->matching($query->equals('artikel', $artikel))->execute();
    }

    public function findByUidInklusiveArchiv(int $uid): ?Einzelstueck
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['disabled'])
            ->setRespectStoragePage(false);
        return $query->matching($query->equals('uid', $uid))->execute()->getFirst();
    }

    /**
     * Prüfpflichtige Stücke, deren Prüfung bis zum Stichtag fällig ist (inkl. ohne Termin).
     *
     * @return QueryResultInterface<int, Einzelstueck>
     */
    public function findPruefungFaelligBis(\DateTimeImmutable $stichtag): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setOrderings(['naechstePruefung' => QueryInterface::ORDER_ASCENDING]);
        return $query->matching($query->logicalAnd(
            $query->greaterThan('artikel.pruefintervall', 0),
            $query->logicalOr(
                $query->equals('naechstePruefung', null),
                $query->lessThanOrEqual('naechstePruefung', $stichtag->format('Y-m-d')),
            ),
        ))->execute();
    }

    /**
     * @return QueryResultInterface<int, Einzelstueck>
     */
    public function findNichtEinsatzbereit(): QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching($query->in('zustand', ['mangel', 'defekt']))->execute();
    }
}
