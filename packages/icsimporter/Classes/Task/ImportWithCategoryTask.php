<?php

declare(strict_types=1);

namespace Schmid\IcsImporter\Task;

use HDNET\Calendarize\Event\ImportSingleIcalEvent;
use HDNET\Calendarize\Exception\UnableToGetFileForUrlException;
use HDNET\Calendarize\Ical\ICalEvent;
use HDNET\Calendarize\Service\Ical\ICalUrlService;
use HDNET\Calendarize\Service\IndexerService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Schmid\IcsImporter\Service\CategoryAssignmentService;
use Schmid\IcsImporter\Service\FixedTimezoneICalService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Scheduler task for importing ICS feeds with automatic category assignment.
 */
final class ImportWithCategoryTask extends AbstractTask
{
    public string $icsUrl = '';
    public int $pid = 0;
    public int $categoryUid = 0;
    public ?string $since = null;

    public function execute(): bool
    {
        $this->validateConfiguration();

        $icalFile = $this->downloadIcsFile();

        try {
            $events = $this->parseEvents($icalFile);
            $importIds = $this->extractImportIds($events);

            $this->importEvents($events);
            $this->assignCategories($importIds);
            $this->reindexEvents();
        } finally {
            GeneralUtility::unlink_tempfile($icalFile);
        }

        return true;
    }

    private function validateConfiguration(): void
    {
        if ($this->pid <= 0 || $this->categoryUid <= 0 || $this->icsUrl === '') {
            throw new \InvalidArgumentException(
                'ICS-URL, PID und Kategorie-UID müssen konfiguriert sein.',
                1700000001
            );
        }
    }

    private function downloadIcsFile(): string
    {
        try {
            return $this->getICalUrlService()->getOrCreateLocalFileForUrl($this->icsUrl);
        } catch (UnableToGetFileForUrlException $e) {
            throw new \RuntimeException(
                'ICS-Quelle ungültig: ' . $e->getMessage(),
                1700000002,
                $e
            );
        }
    }

    /**
     * @return list<ICalEvent>
     */
    private function parseEvents(string $icalFile): array
    {
        return $this->getICalService()->getEvents($icalFile);
    }

    /**
     * @param list<ICalEvent> $events
     * @return list<string>
     */
    private function extractImportIds(array $events): array
    {
        $ids = [];

        foreach ($events as $event) {
            $uid = $this->getEventUid($event);
            if ($uid !== null && $uid !== '') {
                $ids[$uid] = true;
            }
        }

        return array_keys($ids);
    }

    private function getEventUid(ICalEvent $event): ?string
    {
        if (method_exists($event, 'getUid')) {
            return (string)$event->getUid();
        }
        if (method_exists($event, 'getId')) {
            return (string)$event->getId();
        }

        return null;
    }

    /**
     * @param list<ICalEvent> $events
     */
    private function importEvents(array $events): void
    {
        $ignoreBeforeDate = $this->getIgnoreBeforeDate();
        $dispatcher = $this->getEventDispatcher();

        foreach ($events as $event) {
            if ($this->shouldSkipEvent($event, $ignoreBeforeDate)) {
                continue;
            }

            $dispatcher->dispatch(new ImportSingleIcalEvent($event, $this->pid));
        }
    }

    private function getIgnoreBeforeDate(): ?\DateTimeInterface
    {
        if ($this->since === null || $this->since === '') {
            return null;
        }

        return new \DateTime($this->since);
    }

    private function shouldSkipEvent(ICalEvent $event, ?\DateTimeInterface $ignoreBeforeDate): bool
    {
        if ($ignoreBeforeDate === null) {
            return false;
        }

        $eventDate = $event->getEndDate() ?? $event->getStartDate();

        return $eventDate !== null && $eventDate < $ignoreBeforeDate;
    }

    /**
     * @param list<string> $importIds
     */
    private function assignCategories(array $importIds): void
    {
        $this->getCategoryAssignmentService()->assignCategoryToEventsByImportIds(
            $importIds,
            $this->pid,
            $this->categoryUid
        );
    }

    private function reindexEvents(): void
    {
        $this->getIndexerService()->reindexAll();
    }

    // Service getters (scheduler tasks don't support constructor injection due to serialization)

    private function getICalUrlService(): ICalUrlService
    {
        return GeneralUtility::makeInstance(ICalUrlService::class);
    }

    private function getICalService(): FixedTimezoneICalService
    {
        return GeneralUtility::makeInstance(FixedTimezoneICalService::class);
    }

    private function getEventDispatcher(): EventDispatcherInterface
    {
        return GeneralUtility::makeInstance(EventDispatcherInterface::class);
    }

    private function getIndexerService(): IndexerService
    {
        return GeneralUtility::makeInstance(IndexerService::class);
    }

    private function getCategoryAssignmentService(): CategoryAssignmentService
    {
        return GeneralUtility::makeInstance(CategoryAssignmentService::class);
    }
}
