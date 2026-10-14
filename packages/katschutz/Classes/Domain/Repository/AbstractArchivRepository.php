<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository für Datensätze, die statt gelöscht nur archiviert (hidden) werden.
 *
 * @template T of \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface
 * @extends Repository<T>
 */
abstract class AbstractArchivRepository extends Repository
{
    protected $defaultOrderings = ['title' => QueryInterface::ORDER_ASCENDING];

    /**
     * Inklusive archivierter Datensätze (für die Verwaltung).
     *
     * @return QueryResultInterface<int, T>
     */
    public function findAlleInklusiveArchiv(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true)->setEnableFieldsToBeIgnored(['disabled']);
        return $query->execute();
    }

    /**
     * @return T|null
     */
    public function findByUidInklusiveArchiv(int $uid): ?object
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['disabled'])
            ->setRespectStoragePage(false);
        return $query->matching($query->equals('uid', $uid))->execute()->getFirst();
    }
}
