<?php

declare(strict_types=1);

namespace Schmid\Sitepackage\Controller;

use HDNET\Calendarize\Domain\Repository\IndexRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Combines Jubiläen (EXT:feuerwehren) and, optionally, Calendarize-Termine
 * in a single plugin. The two sources are shown as separate sections/tables,
 * never merged into one interleaved list (matches the Lovable reference design).
 *
 * Jubiläen are read via a plain QueryBuilder (not Extbase ORM): the
 * `Jubilaeum.feuerwehr` TCA field is a bare `passthrough` int, not a
 * relation TCA config, so Extbase's DataMapper cannot hydrate the entity
 * when queried as its own root (only works via Feuerwehr's IRRE-side
 * `jubilaeen` relation, which this plugin does not use).
 */
final class TermineController extends ActionController
{
    private const TABLE_JUBILAEUM = 'tx_feuerwehren_domain_model_jubilaeum';
    private const TABLE_FEUERWEHR = 'tx_feuerwehren_domain_model_feuerwehr';
    private const TABLE_CALENDARIZE_INDEX = 'tx_calendarize_domain_model_index';
    private const TABLE_CALENDARIZE_EVENT = 'tx_calendarize_domain_model_event';
    private const FALLBACK_CATEGORY = 'Termin';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly IndexRepository $indexRepository,
    ) {}

    public function listAction(): ResponseInterface
    {
        $layout = (string)($this->settings['layout'] ?? 'full');
        $showPastJubilaeen = (bool)($this->settings['showPastJubilaeen'] ?? false);
        $showCalendarizeEvents = (bool)($this->settings['showCalendarizeEvents'] ?? false);
        $limit = (int)($this->settings['limit'] ?? 0);

        $jubilaeen = $this->findJubilaeen($showPastJubilaeen, 'compact' === $layout ? $limit : 0);

        $calendarizeEvents = [];
        $calendarizeEventsFull = [];
        $calendarizeCategories = [];
        if ($showCalendarizeEvents) {
            if ('compact' === $layout) {
                $calendarizeEvents = $this->findCalendarizeEvents($limit);
            } else {
                $calendarizeEventsFull = $this->findCalendarizeEventsFull();
                $categoryNames = array_values(array_unique(array_column($calendarizeEventsFull, 'category')));
                sort($categoryNames);
                $calendarizeCategories = array_map(
                    fn (string $name): array => ['name' => $name, 'slug' => $this->slugify($name)],
                    $categoryNames
                );
            }
        }

        $jubilaeumYears = array_values(array_unique(array_map(
            static fn (array $j): int => (int)substr((string)$j['datum'], 0, 4),
            $jubilaeen
        )));
        sort($jubilaeumYears);

        $jubilaeumArten = array_values(array_unique(array_map(
            static fn (array $j): int => (int)$j['titel'],
            $jubilaeen
        )));
        sort($jubilaeumArten);

        $this->view->assignMultiple([
            'layout' => $layout,
            'jubilaeen' => $jubilaeen,
            'jubilaeumYears' => $jubilaeumYears,
            'jubilaeumArten' => $jubilaeumArten,
            'showCalendarizeEvents' => $showCalendarizeEvents,
            'calendarizeEvents' => $calendarizeEvents,
            'calendarizeEventsFull' => $calendarizeEventsFull,
            'calendarizeEventsJson' => json_encode($calendarizeEventsFull),
            'calendarizeCategories' => $calendarizeCategories,
        ]);

        return $this->htmlResponse();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function findJubilaeen(bool $showPast, int $limit): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_JUBILAEUM);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(new DeletedRestriction())
            ->add(new HiddenRestriction());

        $queryBuilder
            ->select(
                'j.uid',
                'j.titel',
                'j.datum',
                'j.beschreibung',
                'f.name AS feuerwehr_name',
                'f.ort AS feuerwehr_ort',
                'f.gruendungsdatum AS feuerwehr_gruendungsdatum',
            )
            ->from(self::TABLE_JUBILAEUM, 'j')
            ->join(
                'j',
                self::TABLE_FEUERWEHR,
                'f',
                (string)$queryBuilder->expr()->eq('f.uid', $queryBuilder->quoteIdentifier('j.feuerwehr'))
            )
            ->orderBy('j.datum', 'ASC');

        if (!$showPast) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->gte(
                    'j.datum',
                    $queryBuilder->createNamedParameter((new \DateTimeImmutable('today'))->format('Y-m-d'))
                )
            );
        }

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * Full, unpaginated upcoming-events list with category names attached — used by the
     * Kalender/Liste tabs of the 'full' layout, which filter/sort client-side.
     *
     * @return array<int, array<string, mixed>>
     */
    private function findCalendarizeEventsFull(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_CALENDARIZE_INDEX);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(new DeletedRestriction())
            ->add(new HiddenRestriction());

        $queryBuilder
            ->select(
                'i.uid',
                'i.foreign_uid AS event_uid',
                'i.start_date',
                'i.start_time',
                'i.all_day',
                'e.title',
                'e.location',
            )
            ->from(self::TABLE_CALENDARIZE_INDEX, 'i')
            ->join(
                'i',
                self::TABLE_CALENDARIZE_EVENT,
                'e',
                (string)$queryBuilder->expr()->eq('e.uid', $queryBuilder->quoteIdentifier('i.foreign_uid'))
            )
            ->andWhere(
                $queryBuilder->expr()->eq(
                    'i.foreign_table',
                    $queryBuilder->createNamedParameter(self::TABLE_CALENDARIZE_EVENT)
                ),
                $queryBuilder->expr()->gte(
                    'i.start_date',
                    $queryBuilder->createNamedParameter((new \DateTimeImmutable('today'))->format('Y-m-d'))
                )
            )
            ->orderBy('i.start_date', 'ASC')
            ->addOrderBy('i.start_time', 'ASC');

        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();

        $eventUids = array_values(array_unique(array_map(static fn (array $r): int => (int)$r['event_uid'], $rows)));
        $categoriesByEvent = $this->findCategoriesForEvents($eventUids);

        return array_map(static function (array $row) use ($categoriesByEvent): array {
            $eventUid = (int)$row['event_uid'];
            $categories = $categoriesByEvent[$eventUid] ?? [];

            $category = $categories[0] ?? self::FALLBACK_CATEGORY;

            return [
                'uid' => (int)$row['uid'],
                'date' => $row['start_date'],
                'time' => (bool)$row['all_day'] ? null : gmdate('H:i', (int)$row['start_time']),
                'title' => (string)$row['title'],
                'location' => (string)$row['location'],
                'categories' => $categories,
                'category' => $category,
                'categorySlug' => self::slugify($category),
            ];
        }, $rows);
    }

    /**
     * Turns a category name into a CSS-class-safe slug (e.g. "Leistungsabzeichen" -> "leistungsabzeichen"),
     * used to look up its hex color in Scss/extensions/_termine.scss.
     */
    private static function slugify(string $name): string
    {
        $transliterated = str_replace(
            ['ä', 'ö', 'ü', 'ß', 'Ä', 'Ö', 'Ü'],
            ['ae', 'oe', 'ue', 'ss', 'ae', 'oe', 'ue'],
            $name
        );
        $slug = strtolower((string)preg_replace('/[^a-zA-Z0-9]+/', '-', $transliterated));

        return trim($slug, '-') ?: 'termin';
    }

    /**
     * @param array<int, int> $eventUids
     * @return array<int, array<int, string>>
     */
    private function findCategoriesForEvents(array $eventUids): array
    {
        if ($eventUids === []) {
            return [];
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category_record_mm');
        $queryBuilder->getRestrictions()->removeAll();

        $rows = $queryBuilder
            ->select('mm.uid_foreign AS event_uid', 'c.title AS category_title')
            ->from('sys_category_record_mm', 'mm')
            ->join(
                'mm',
                'sys_category',
                'c',
                (string)$queryBuilder->expr()->eq('c.uid', $queryBuilder->quoteIdentifier('mm.uid_local'))
            )
            ->where(
                $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter(self::TABLE_CALENDARIZE_EVENT)),
                $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('categories')),
                $queryBuilder->expr()->in('mm.uid_foreign', $queryBuilder->createNamedParameter($eventUids, \Doctrine\DBAL\ArrayParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $map = [];
        foreach ($rows as $row) {
            $map[(int)$row['event_uid']][] = (string)$row['category_title'];
        }

        return $map;
    }

    /**
     * @return array<int, \HDNET\Calendarize\Domain\Model\Index>
     */
    private function findCalendarizeEvents(int $limit): array
    {
        $storagePids = array_values(array_filter(array_map(
            'intval',
            explode(',', (string)($this->settings['calendarizeStoragePid'] ?? ''))
        )));

        if ($storagePids !== []) {
            $this->indexRepository->setOverridePageIds($storagePids);
        }

        $result = $this->indexRepository->findList($limit > 0 ? $limit : 0);

        return \is_array($result) ? $result : $result->toArray();
    }
}
