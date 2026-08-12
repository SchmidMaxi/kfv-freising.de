<?php

declare(strict_types=1);

namespace Schmid\IcsImporter\Service;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Service for assigning categories to imported calendar events.
 */
final readonly class CategoryAssignmentService
{
    private const TABLE_EVENT = 'tx_calendarize_domain_model_event';
    private const TABLE_MM = 'sys_category_record_mm';

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    /**
     * Sync a category to events identified by their import IDs.
     *
     * - Adds the category to events in the current import set.
     * - Removes the category from ALL other events (across all pages) to prevent
     *   stale assignments from misconfigured runs or feed changes.
     *
     * @param list<string> $importIds ICS UIDs used as import_id
     * @param int $pid Page ID to filter events (0 = no filter)
     * @param int $categoryUid Category UID to assign
     */
    public function assignCategoryToEventsByImportIds(
        array $importIds,
        int $pid,
        int $categoryUid,
    ): void {
        if ($importIds === []) {
            return;
        }

        $currentEventUids = $this->findEventUidsByImportIds($importIds, $pid);

        // Sync: remove category from events that are no longer in this feed.
        // Only sync if we found events to prevent accidental cleanup on first run.
        if ($currentEventUids !== []) {
            $this->removeStaleCategoryAssignments($currentEventUids, $categoryUid);
        }

        if ($currentEventUids === []) {
            return;
        }

        $this->assignCategoryToEvents($currentEventUids, $categoryUid);
    }

    /**
     * @param list<string> $importIds
     * @return list<int>
     */
    private function findEventUidsByImportIds(array $importIds, int $pid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_EVENT);
        $queryBuilder
            ->select('uid')
            ->from(self::TABLE_EVENT)
            ->where(
                $queryBuilder->expr()->in(
                    'import_id',
                    $queryBuilder->createNamedParameter($importIds, ArrayParameterType::STRING)
                )
            );

        if ($pid > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, ParameterType::INTEGER)
                )
            );
        }

        return array_map(
            static fn(mixed $uid): int => (int)$uid,
            $queryBuilder->executeQuery()->fetchFirstColumn()
        );
    }

    /**
     * Remove category from all events that are NOT in the current import set.
     * This ensures each category is exclusively owned by one import task.
     *
     * @param list<int> $currentEventUids Event UIDs currently in the feed
     * @param int $categoryUid Category UID to clean up
     */
    private function removeStaleCategoryAssignments(array $currentEventUids, int $categoryUid): void
    {
        $allEventsWithCategory = $this->findAllEventUidsWithCategory($categoryUid);
        $staleEventUids = array_values(array_diff($allEventsWithCategory, $currentEventUids));

        if ($staleEventUids === []) {
            return;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_MM);
        $queryBuilder
            ->delete(self::TABLE_MM)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_local',
                    $queryBuilder->createNamedParameter($categoryUid, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->in(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter($staleEventUids, ArrayParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter(self::TABLE_EVENT)
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter('categories')
                )
            )
            ->executeStatement();
    }

    /**
     * Find all event UIDs that have the specified category assigned (across all pages).
     *
     * @return list<int>
     */
    private function findAllEventUidsWithCategory(int $categoryUid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_MM);

        return array_map(
            static fn(mixed $uid): int => (int)$uid,
            $queryBuilder
                ->select('uid_foreign')
                ->from(self::TABLE_MM)
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid_local',
                        $queryBuilder->createNamedParameter($categoryUid, ParameterType::INTEGER)
                    ),
                    $queryBuilder->expr()->eq(
                        'tablenames',
                        $queryBuilder->createNamedParameter(self::TABLE_EVENT)
                    ),
                    $queryBuilder->expr()->eq(
                        'fieldname',
                        $queryBuilder->createNamedParameter('categories')
                    )
                )
                ->executeQuery()
                ->fetchFirstColumn()
        );
    }

    /**
     * @param list<int> $eventUids
     */
    private function assignCategoryToEvents(array $eventUids, int $categoryUid): void
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE_MM);

        foreach ($eventUids as $eventUid) {
            if ($this->categoryRelationExists($eventUid, $categoryUid)) {
                continue;
            }

            $connection->insert(self::TABLE_MM, [
                'uid_local' => $categoryUid,
                'uid_foreign' => $eventUid,
                'tablenames' => self::TABLE_EVENT,
                'fieldname' => 'categories',
                'sorting' => 0,
            ]);
        }
    }

    private function categoryRelationExists(int $eventUid, int $categoryUid): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_MM);

        $count = (int)$queryBuilder
            ->count('*')
            ->from(self::TABLE_MM)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_local',
                    $queryBuilder->createNamedParameter($categoryUid, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter($eventUid, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter(self::TABLE_EVENT)
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter('categories')
                )
            )
            ->executeQuery()
            ->fetchOne();

        return $count > 0;
    }
}
