<?php
declare(strict_types=1);

namespace Schmid\IcsImporter\Task;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use HDNET\Calendarize\Event\ImportSingleIcalEvent;
use HDNET\Calendarize\Exception\UnableToGetFileForUrlException;
use HDNET\Calendarize\Service\Ical\ICalUrlService;
use HDNET\Calendarize\Service\Ical\VObjectICalService;
use HDNET\Calendarize\Service\IndexerService;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

final class ImportWithCategoryTask extends AbstractTask
{
    /** @var string */
    public $icsUrl = '';

    /** @var int */
    public $pid = 0;

    /** @var int */
    public $categoryUid = 0;

    /** @var string|null e.g. "2024-01-01" or "-10 days" */
    public $since;

    public function execute(): bool
    {
        if ($this->pid <= 0 || $this->categoryUid <= 0 || empty($this->icsUrl)) {
            throw new \RuntimeException('Bitte ICS-URL, PID und Kategorie-UID konfigurieren.');
        }

        /** @var ICalUrlService $iCalUrlService */
        $iCalUrlService = GeneralUtility::makeInstance(ICalUrlService::class);
        /** @var VObjectICalService $iCalService */
        $iCalService    = GeneralUtility::makeInstance(VObjectICalService::class);
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher     = GeneralUtility::makeInstance(EventDispatcher::class);
        /** @var IndexerService $indexer */
        $indexer        = GeneralUtility::makeInstance(IndexerService::class);

        // optionaler Filter
        $ignoreBeforeDate = null;
        if ($this->since !== null && $this->since !== '') {
            $ignoreBeforeDate = new \DateTime($this->since);
        }

        // 1) ICS laden → Temp-Datei
        try {
            $icalFile = $iCalUrlService->getOrCreateLocalFileForUrl($this->icsUrl);
        } catch (UnableToGetFileForUrlException $e) {
            throw new \RuntimeException('ICS-Quelle ungültig: ' . $e->getMessage(), 0, $e);
        }

        // 2) Events parsen
        try {
            $events = $iCalService->getEvents($icalFile);
        } finally {
            GeneralUtility::unlink_tempfile($icalFile);
        }

        // 3) Event‑UIDs (iCal UID) sammeln, um hinterher über import_id zu kategorisieren
        $icsUids = [];
        foreach ($events as $e) {
            $uid = null;
            if (method_exists($e, 'getUid')) { $uid = (string)$e->getUid(); }
            elseif (method_exists($e, 'getId')) { $uid = (string)$e->getId(); }
            if ($uid !== null && $uid !== '') { $icsUids[$uid] = true; }
        }
        $icsUids = array_keys($icsUids);

        // 4) Import per Event-Dispatch (Calendarize-Standard)
        foreach ($events as $event) {
            $endOrStart = $event->getEndDate() ?? $event->getStartDate();
            if ($ignoreBeforeDate instanceof \DateTimeInterface && $endOrStart < $ignoreBeforeDate) {
                continue;
            }
            $dispatcher->dispatch(new ImportSingleIcalEvent($event, (int)$this->pid));
        }

        // 5) Kategorie an alle Events dieses Feeds hängen (import_id IN (<iCal-UIDs>)), optional auf PID beschränkt
        $this->assignCategoryByImportIds($icsUids, (int)$this->pid, (int)$this->categoryUid);

        // 6) Reindex
        $indexer->reindexAll();

        return true;
    }

    private function assignCategoryByImportIds(array $icsUids, int $pid, int $categoryUid): void
    {
        if (!$icsUids) {
            return;
        }
        $pool = GeneralUtility::makeInstance(ConnectionPool::class);

        $qb = $pool->getQueryBuilderForTable('tx_calendarize_domain_model_event');
        $qb->select('uid')
            ->from('tx_calendarize_domain_model_event')
            ->where(
                $qb->expr()->in(
                    'import_id',
                    $qb->createNamedParameter($icsUids, ArrayParameterType::STRING)
                )
            );
        if ($pid > 0) {
            $qb->andWhere($qb->expr()->eq('pid', $qb->createNamedParameter($pid, ParameterType::INTEGER)));
        }
        $eventUids = $qb->executeQuery()->fetchFirstColumn();
        if (!$eventUids) {
            return;
        }

        $mm = $pool->getConnectionForTable('sys_category_record_mm');

        foreach ($eventUids as $eventUid) {
            $qb2 = $pool->getQueryBuilderForTable('sys_category_record_mm');
            $exists = (int)$qb2->count('*')
                ->from('sys_category_record_mm')
                ->where(
                    $qb2->expr()->eq('uid_local',   $qb2->createNamedParameter($categoryUid, ParameterType::INTEGER)),
                    $qb2->expr()->eq('uid_foreign', $qb2->createNamedParameter((int)$eventUid, ParameterType::INTEGER)),
                    $qb2->expr()->eq('tablenames',  $qb2->createNamedParameter('tx_calendarize_domain_model_event')),
                    $qb2->expr()->eq('fieldname',   $qb2->createNamedParameter('categories'))
                )
                ->executeQuery()
                ->fetchOne();

            if ($exists === 0) {
                $mm->insert('sys_category_record_mm', [
                    'uid_local'   => $categoryUid,
                    'uid_foreign' => (int)$eventUid,
                    'tablenames'  => 'tx_calendarize_domain_model_event',
                    'fieldname'   => 'categories',
                    'sorting'     => 0,
                ]);
            }
        }
    }
}

