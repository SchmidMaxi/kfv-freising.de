<?php

declare(strict_types=1);

namespace Schmid\Sitepackage\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * One-off, idempotent: Musterseiten Tabs/Slider, Service-Hub (39), Kontakt-Shortcut (63),
 * Einsätze via News-Kategorie (32) und Termine-Ergänzung (13).
 */
final class SeedPhaseDPagesCommand extends Command
{
    private const PID_MUSTERSEITEN = 9;
    private const PID_SERVICE = 39;
    private const PID_EINSAETZE = 32;
    private const PID_TERMINE = 13;
    private const PID_NEWS_STORAGE = 46;
    private const PAGE_DOWNLOADS = 47;
    private const PAGE_FORMULARE = 14;
    private const PAGE_KONTAKT = 17;
    private const PAGE_KONTAKT_HAUPTNAV = 63;
    private const CATEGORY_EINSATZ = 1;

    protected function configure(): void
    {
        $this->setDescription('Seeds Musterseiten Tabs/Slider, Service-Hub, Kontakt-Shortcut, Einsätze (News) und Termine-Ergänzung. Safe to re-run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backendUser = GeneralUtility::makeInstance(CommandLineUserAuthentication::class);
        $backendUser->user['uid'] = 0;
        $backendUser->user['admin'] = 1;
        $backendUser->user['username'] = '_cli_sitepackage';
        $backendUser->workspace = 0;
        $GLOBALS['BE_USER'] = $backendUser;

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $ttContentConnection = $connectionPool->getConnectionForTable('tt_content');
        $pagesConnection = $connectionPool->getConnectionForTable('pages');

        $this->seedMusterseiteTabs($ttContentConnection, $pagesConnection, $output);
        $this->seedMusterseiteSlider($ttContentConnection, $pagesConnection, $output);
        $this->seedServiceHub($ttContentConnection, $output);
        $this->fixKontaktShortcut($pagesConnection, $output);
        $this->seedEinsaetze($ttContentConnection, $connectionPool, $output);
        $this->seedTermineExtra($ttContentConnection, $output);
        $this->seedSuche($ttContentConnection, $pagesConnection, $output);

        return Command::SUCCESS;
    }

    private function pageHasContent(Connection $connection, int $pid): bool
    {
        $count = (int)$connection->count('uid', 'tt_content', ['pid' => $pid, 'deleted' => 0]);
        return $count > 0;
    }

    private function addPidToCollections(array &$dataMap, int $pid): void
    {
        foreach ($dataMap as $table => &$records) {
            if ($table === 'tt_content' || $table === 'pages' || $table === 'tx_news_domain_model_news') {
                continue;
            }
            foreach ($records as &$record) {
                $record['pid'] = $pid;
            }
            unset($record);
        }
        unset($records);
    }

    private function runDataMap(array $dataMap, OutputInterface $output, string $label): DataHandler
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($dataMap, []);
        $dataHandler->process_datamap();
        if (!empty($dataHandler->errorLog)) {
            foreach ($dataHandler->errorLog as $error) {
                $output->writeln('<error>[' . $label . '] ' . $error . '</error>');
            }
        }
        return $dataHandler;
    }

    private function setSorting(Connection $connection, string $table, array $uidsInOrder, int $step = 100): void
    {
        $sorting = $step;
        foreach ($uidsInOrder as $uid) {
            if ((int)$uid === 0) {
                continue;
            }
            $connection->update($table, ['sorting' => $sorting], ['uid' => (int)$uid]);
            $sorting += $step;
        }
    }

    // -----------------------------------------------------------------
    // Musterseite: Tabs
    // -----------------------------------------------------------------
    private function seedMusterseiteTabs(Connection $ttContent, Connection $pages, OutputInterface $output): void
    {
        $existing = $pages->select(['uid'], 'pages', ['pid' => self::PID_MUSTERSEITEN, 'slug' => '/tabs'])->fetchOne();
        if ($existing) {
            $output->writeln('Musterseite Tabs already exists (uid ' . $existing . ') — skipping.');
            return;
        }

        $pageHandler = $this->runDataMap([
            'pages' => [
                'NEWtabsPage' => [
                    'pid' => self::PID_MUSTERSEITEN,
                    'title' => 'Tabs',
                    'slug' => '/tabs',
                    'doktype' => 1,
                    'hidden' => 0,
                ],
            ],
        ], $output, 'musterseite-tabs-page');
        $pageUid = (int)($pageHandler->substNEWwithIDs['NEWtabsPage'] ?? 0);
        if ($pageUid === 0) {
            $output->writeln('<error>Musterseite Tabs: Seite konnte nicht angelegt werden.</error>');
            return;
        }

        $containerHandler = $this->runDataMap([
            'tt_content' => [
                'NEWtabsContainer' => [
                    'pid' => $pageUid,
                    'CType' => 'tabs',
                    'header' => '',
                    'frame_class' => 'default',
                ],
            ],
        ], $output, 'musterseite-tabs-container');
        $tabsUid = (int)($containerHandler->substNEWwithIDs['NEWtabsContainer'] ?? 0);
        if ($tabsUid === 0) {
            $output->writeln('<error>Musterseite Tabs: Tabs-Container fehlgeschlagen.</error>');
            return;
        }

        $tabContent = [
            210 => ['Übersicht', 'Demo-Inhalt für Tab 1 — beliebige Content-Elemente können hier platziert werden.'],
            211 => ['Details', 'Demo-Inhalt für Tab 2 — z. B. Fließtext, Karten oder Akkordeons.'],
            212 => ['Downloads', 'Demo-Inhalt für Tab 3 — Verweis auf <a href="/downloads">Downloads</a>.'],
            213 => ['Kontakt', 'Demo-Inhalt für Tab 4 — Verweis auf <a href="/kontakt">Kontakt</a>.'],
        ];
        $dataMap = ['tt_content' => []];
        $i = 0;
        foreach ($tabContent as $colPos => [$header, $text]) {
            $dataMap['tt_content']['NEWtab' . $i] = [
                'pid' => $pageUid,
                'CType' => 'html',
                'header' => $header,
                'bodytext' => '<p class="mb-0">' . $text . '</p>',
                'tx_container_parent' => $tabsUid,
                'colPos' => $colPos,
                'frame_class' => 'default',
            ];
            $i++;
        }
        $this->runDataMap($dataMap, $output, 'musterseite-tabs-content');

        $output->writeln('Musterseite Tabs (uid ' . $pageUid . ') angelegt.');
    }

    // -----------------------------------------------------------------
    // Musterseite: Slider
    // -----------------------------------------------------------------
    private function seedMusterseiteSlider(Connection $ttContent, Connection $pages, OutputInterface $output): void
    {
        $existing = $pages->select(['uid'], 'pages', ['pid' => self::PID_MUSTERSEITEN, 'slug' => '/slider'])->fetchOne();
        if ($existing) {
            $output->writeln('Musterseite Slider already exists (uid ' . $existing . ') — skipping.');
            return;
        }

        $pageHandler = $this->runDataMap([
            'pages' => [
                'NEWsliderPage' => [
                    'pid' => self::PID_MUSTERSEITEN,
                    'title' => 'Slider',
                    'slug' => '/slider',
                    'doktype' => 1,
                    'hidden' => 0,
                ],
            ],
        ], $output, 'musterseite-slider-page');
        $pageUid = (int)($pageHandler->substNEWwithIDs['NEWsliderPage'] ?? 0);
        if ($pageUid === 0) {
            $output->writeln('<error>Musterseite Slider: Seite konnte nicht angelegt werden.</error>');
            return;
        }

        $dataMap = [
            'tt_content' => [
                'NEWheader' => [
                    'pid' => $pageUid,
                    'CType' => 'section-header',
                    'header' => 'Slider-Varianten',
                    'sitepackage_sectionheader_badge_text' => 'Musterseite',
                    'sitepackage_sectionheader_subheadline' => 'Demo der Splide-basierten Card-Slider-Konfigurationen.',
                    'sitepackage_sectionheader_alignment' => 'left',
                    'frame_class' => 'default',
                ],
                'NEWslider1' => [
                    'pid' => $pageUid,
                    'CType' => 'card-slider',
                    'header' => 'Variante 1 — Standard',
                    'color' => 'default',
                    'sitepackage_cardslider_items' => 'NEWs1,NEWs2,NEWs3,NEWs4',
                    'frame_class' => 'default',
                ],
                'NEWslider2' => [
                    'pid' => $pageUid,
                    'CType' => 'card-slider',
                    'header' => 'Variante 2 — Dunkel',
                    'color' => 'dark',
                    'sitepackage_cardslider_items' => 'NEWs5,NEWs6,NEWs7',
                    'frame_class' => 'default',
                ],
            ],
            'sitepackage_cardslider_items' => [
                'NEWs1' => ['header' => 'Freising', 'bodytext' => 'Stützpunktfeuerwehr Freising'],
                'NEWs2' => ['header' => 'Moosburg', 'bodytext' => 'Stützpunktfeuerwehr Moosburg'],
                'NEWs3' => ['header' => 'Neufahrn', 'bodytext' => 'Ortsfeuerwehr Neufahrn'],
                'NEWs4' => ['header' => 'Eching', 'bodytext' => 'Ortsfeuerwehr Eching'],
                'NEWs5' => ['header' => 'Jahreshauptversammlung', 'bodytext' => '15. Januar, Bürgerhaus Freising'],
                'NEWs6' => ['header' => 'Leistungsprüfung THL', 'bodytext' => '22. Februar, FFW Moosburg'],
                'NEWs7' => ['header' => 'Führungskräfte-Fortbildung', 'bodytext' => '8. März, FFW Freising'],
            ],
        ];
        $this->addPidToCollections($dataMap, $pageUid);
        $handler = $this->runDataMap($dataMap, $output, 'musterseite-slider-content');
        $this->setSorting($ttContent, 'tt_content', [
            $handler->substNEWwithIDs['NEWheader'] ?? 0,
            $handler->substNEWwithIDs['NEWslider1'] ?? 0,
            $handler->substNEWwithIDs['NEWslider2'] ?? 0,
        ]);

        $output->writeln('Musterseite Slider (uid ' . $pageUid . ') angelegt.');
    }

    // -----------------------------------------------------------------
    // Service Hub (39)
    // -----------------------------------------------------------------
    private function seedServiceHub(Connection $connection, OutputInterface $output): void
    {
        $staleUids = $connection->executeQuery(
            'SELECT uid FROM tt_content WHERE pid = ? AND deleted = 0 AND CType IN (\'textpic\', \'text\')',
            [self::PID_SERVICE]
        )->fetchFirstColumn();

        if (!empty($staleUids)) {
            $cmdMap = ['tt_content' => []];
            foreach ($staleUids as $uid) {
                $cmdMap['tt_content'][$uid] = ['delete' => 1];
            }
            $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
            $dataHandler->start([], $cmdMap);
            $dataHandler->process_cmdmap();
            $output->writeln('Service (39): ' . count($staleUids) . ' fachfremde Alt-Inhalte gelöscht.');
        }

        if ($this->pageHasContent($connection, self::PID_SERVICE)) {
            $output->writeln('Service (39) hat bereits Inhalt — Hub-Content übersprungen.');
        } else {
            $dataMap = [
                'tt_content' => [
                    'NEWheader' => [
                        'pid' => self::PID_SERVICE,
                        'CType' => 'section-header',
                        'header' => 'Service',
                        'sitepackage_sectionheader_badge_text' => 'Service',
                        'sitepackage_sectionheader_subheadline' => 'Downloads, Formulare und häufige Fragen rund um den Kreisfeuerwehrverband Freising.',
                        'sitepackage_sectionheader_alignment' => 'left',
                        'frame_class' => 'default',
                    ],
                    'NEWcards' => [
                        'pid' => self::PID_SERVICE,
                        'CType' => 'card-group',
                        'header' => '',
                        'sitepackage_cardgroup_card_layout' => 'feature',
                        'breakpoint_mobile' => 1,
                        'breakpoint_tablet' => 2,
                        'breakpoint_desktop' => 3,
                        'sitepackage_cardgroup_items' => 'NEWc1,NEWc2,NEWc3',
                        'frame_class' => 'default',
                    ],
                    'NEWfaqTeaser' => [
                        'pid' => self::PID_SERVICE,
                        'CType' => 'accordion',
                        'header' => 'Häufige Fragen',
                        'sitepackage_accordion_accordion_layout' => 'card',
                        'sitepackage_accordion_items' => 'NEWfaq1,NEWfaq2,NEWfaq3',
                        'frame_class' => 'default',
                    ],
                ],
                'sitepackage_cardgroup_items' => [
                    'NEWc1' => ['header' => 'Downloads', 'bodytext' => 'Formulare, Satzungen und Einsatzunterlagen', 'link' => 't3://page?uid=' . self::PAGE_DOWNLOADS],
                    'NEWc2' => ['header' => 'Formulare', 'bodytext' => 'Anträge für Lehrgänge, Mitgliedschaft und Zuschüsse', 'link' => 't3://page?uid=' . self::PAGE_FORMULARE],
                    'NEWc3' => ['header' => 'Kontakt', 'bodytext' => 'Direkt Kontakt zur Geschäftsstelle aufnehmen', 'link' => 't3://page?uid=' . self::PAGE_KONTAKT],
                ],
                'sitepackage_accordion_items' => [
                    'NEWfaq1' => ['title' => 'Wie kann ich der Feuerwehr beitreten?', 'bodytext' => 'Jeder ab 12 Jahren kann Mitglied werden. Wende dich an deine örtliche Feuerwehr oder nutze unser Kontaktformular.'],
                    'NEWfaq2' => ['title' => 'Muss ich für die Ausrüstung bezahlen?', 'bodytext' => 'Nein, die persönliche Schutzausrüstung wird von der Gemeinde gestellt.'],
                    'NEWfaq3' => ['title' => 'Was ist der Kreisfeuerwehrverband?', 'bodytext' => 'Der KFV ist der Zusammenschluss aller Feuerwehren im Landkreis Freising.'],
                ],
            ];
            $this->addPidToCollections($dataMap, self::PID_SERVICE);
            $handler = $this->runDataMap($dataMap, $output, 'service-hub');
            $this->setSorting($connection, 'tt_content', [
                $handler->substNEWwithIDs['NEWheader'] ?? 0,
                $handler->substNEWwithIDs['NEWcards'] ?? 0,
                $handler->substNEWwithIDs['NEWfaqTeaser'] ?? 0,
            ]);
            $output->writeln('Service (39) Hub-Content angelegt.');
        }

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('pages')
            ->update('pages', ['hidden' => 0], ['uid' => self::PID_SERVICE]);
        $output->writeln('Service (39) entsperrt (hidden=0).');
    }

    // -----------------------------------------------------------------
    // Kontakt (63) -> Shortcut auf Kontakt (17), statt Dupliziertem Inhalt
    // -----------------------------------------------------------------
    private function fixKontaktShortcut(Connection $pages, OutputInterface $output): void
    {
        $row = $pages->select(['doktype', 'shortcut', 'hidden'], 'pages', ['uid' => self::PAGE_KONTAKT_HAUPTNAV])->fetchAssociative();
        if ($row && (int)$row['doktype'] === 4 && (int)$row['shortcut'] === self::PAGE_KONTAKT && (int)$row['hidden'] === 0) {
            $output->writeln('Kontakt (63) ist bereits als Shortcut auf 17 konfiguriert — skipping.');
            return;
        }

        $pages->update('pages', [
            'doktype' => 4,
            'shortcut' => self::PAGE_KONTAKT,
            'shortcut_mode' => 0,
            'hidden' => 0,
        ], ['uid' => self::PAGE_KONTAKT_HAUPTNAV]);

        $output->writeln('Kontakt (63) als Shortcut auf Kontakt (17) gesetzt und entsperrt.');
    }

    // -----------------------------------------------------------------
    // Einsätze (32) via News-Kategorie "Einsatz"
    // -----------------------------------------------------------------
    private function seedEinsaetze(Connection $ttContent, ConnectionPool $connectionPool, OutputInterface $output): void
    {
        $newsConnection = $connectionPool->getConnectionForTable('tx_news_domain_model_news');

        $einsaetze = [
            ['Zimmerbrand im 2. Obergeschoss', '2024-12-20 14:32:00', 'Freising, Hauptstraße 15', 'Brand gelöscht, keine Verletzten. Eingesetzt: FF Freising, FF Lerchenfeld.'],
            ['Verkehrsunfall mit eingeklemmter Person', '2024-12-19 08:15:00', 'B11 bei Moosburg', 'Technische Hilfeleistung. Eingesetzt: FF Moosburg, FF Langenpreising.'],
            ['Containerbrand auf Firmengelände', '2024-12-18 22:45:00', 'Hallbergmoos, Industriegebiet', 'Brandeinsatz. Eingesetzt: FF Hallbergmoos.'],
            ['Ölspur auf der Fahrbahn', '2024-12-17 16:20:00', 'Neufahrn, Bahnhofstraße', 'Sonstiger Einsatz, Bindemittel aufgebracht. Eingesetzt: FF Neufahrn.'],
            ['Brandmeldeanlage — Fehlalarm', '2024-12-15 11:00:00', 'Eching, Gewerbegebiet', 'Brandeinsatz, Fehlalarm. Eingesetzt: FF Eching.'],
        ];

        $newsExisting = $newsConnection->select(['title'], 'tx_news_domain_model_news', ['pid' => self::PID_NEWS_STORAGE])->fetchFirstColumn();
        $dataMap = ['tt_content' => [], 'tx_news_domain_model_news' => []];
        $created = 0;
        $i = 0;
        foreach ($einsaetze as [$title, $datetime, $ort, $text]) {
            if (in_array($title, $newsExisting, true)) {
                $i++;
                continue;
            }
            $key = 'NEWeinsatz' . $i;
            $dataMap['tx_news_domain_model_news'][$key] = [
                'pid' => self::PID_NEWS_STORAGE,
                'title' => $title,
                'teaser' => $ort . ' — ' . $text,
                'bodytext' => $text,
                'datetime' => (new \DateTimeImmutable($datetime))->getTimestamp(),
                'categories' => self::CATEGORY_EINSATZ,
                'type' => 0,
            ];
            $created++;
            $i++;
        }
        if ($created > 0) {
            $this->runDataMap($dataMap, $output, 'einsaetze-news-records');
            $output->writeln('Einsätze: ' . $created . ' News-Datensätze (Kategorie "Einsatz") angelegt.');
        } else {
            $output->writeln('Einsätze: News-Datensätze bereits vorhanden — skipping.');
        }

        if ($this->pageHasContent($ttContent, self::PID_EINSAETZE)) {
            $output->writeln('Einsätze (32) hat bereits Content-Elemente — skipping.');
            return;
        }

        $flexform = $this->buildNewsListFlexform((string)self::CATEGORY_EINSATZ, 20);
        $pageHandler = $this->runDataMap([
            'tt_content' => [
                'NEWheader' => [
                    'pid' => self::PID_EINSAETZE,
                    'CType' => 'section-header',
                    'header' => 'Aktuelle Einsätze',
                    'sitepackage_sectionheader_badge_text' => 'Einsätze',
                    'sitepackage_sectionheader_subheadline' => 'Übersicht der aktuellen und vergangenen Einsätze der Feuerwehren im Landkreis Freising.',
                    'sitepackage_sectionheader_alignment' => 'left',
                    'frame_class' => 'default',
                ],
                'NEWnewslist' => [
                    'pid' => self::PID_EINSAETZE,
                    'CType' => 'news_pi1',
                    'header' => '',
                    'pi_flexform' => $flexform,
                    'frame_class' => 'default',
                ],
            ],
        ], $output, 'einsaetze-page');
        $this->setSorting($ttContent, 'tt_content', [
            $pageHandler->substNEWwithIDs['NEWheader'] ?? 0,
            $pageHandler->substNEWwithIDs['NEWnewslist'] ?? 0,
        ]);
        $output->writeln('Einsätze (32) Seiteninhalt angelegt (News-Liste, Kategorie "Einsatz").');
    }

    private function buildNewsListFlexform(string $categories, int $limit): string
    {
        return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'
            . '<T3FlexForms><data>'
            . '<sheet index="sDEF"><language index="lDEF">'
            . '<field index="settings.categories"><value index="vDEF">' . $categories . '</value></field>'
            . '<field index="settings.categoryConjunction"><value index="vDEF">or</value></field>'
            . '<field index="settings.orderBy"><value index="vDEF">datetime</value></field>'
            . '<field index="settings.orderDirection"><value index="vDEF">desc</value></field>'
            . '</language></sheet>'
            . '<sheet index="additional"><language index="lDEF">'
            . '<field index="settings.limit"><value index="vDEF">' . $limit . '</value></field>'
            . '</language></sheet>'
            . '</data></T3FlexForms>';
    }

    // -----------------------------------------------------------------
    // Termine (13) — zusätzliche vollständige Terminliste (Jubiläen-Plugin bereits vorhanden)
    // -----------------------------------------------------------------
    private function seedTermineExtra(Connection $connection, OutputInterface $output): void
    {
        $already = (int)$connection->count('uid', 'tt_content', ['pid' => self::PID_TERMINE, 'deleted' => 0, 'header' => 'Termine']);
        if ($already > 0) {
            $output->writeln('Termine (13): erweiterte Terminliste bereits vorhanden — skipping.');
            return;
        }

        $flexform = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'
            . '<T3FlexForms><data>'
            . '<sheet index="main"><language index="lDEF">'
            . '<field index="settings.limit"><value index="vDEF">20</value></field>'
            . '<field index="settings.hidePagination"><value index="vDEF">0</value></field>'
            . '</language></sheet>'
            . '<sheet index="general"><language index="lDEF">'
            . '<field index="settings.configuration"><value index="vDEF">Event</value></field>'
            . '<field index="settings.categoryConjunction"><value index="vDEF">or</value></field>'
            . '<field index="settings.sortBy"><value index="vDEF">start</value></field>'
            . '</language></sheet>'
            . '</data></T3FlexForms>';

        $dataMap = [
            'tt_content' => [
                'NEWterminHeader' => [
                    'pid' => self::PID_TERMINE,
                    'CType' => 'header',
                    'header' => 'Termine',
                    'header_layout' => 2,
                    'frame_class' => 'default',
                ],
                'NEWterminListe' => [
                    'pid' => self::PID_TERMINE,
                    'CType' => 'list',
                    'list_type' => 'calendarize_list',
                    'header' => '',
                    'pi_flexform' => $flexform,
                    'frame_class' => 'default',
                ],
            ],
        ];
        $handler = $this->runDataMap($dataMap, $output, 'termine-extra');
        $this->setSorting($connection, 'tt_content', [
            $handler->substNEWwithIDs['NEWterminHeader'] ?? 0,
            $handler->substNEWwithIDs['NEWterminListe'] ?? 0,
        ], 5);
        $output->writeln('Termine (13): vollständige Terminliste ergänzt.');
    }

    // -----------------------------------------------------------------
    // Suche (neue Seite unter Hauptnavigation, pid 2)
    // -----------------------------------------------------------------
    private const PID_HAUPTNAV = 2;

    private function seedSuche(Connection $ttContent, Connection $pages, OutputInterface $output): void
    {
        $existing = $pages->select(['uid'], 'pages', ['pid' => self::PID_HAUPTNAV, 'slug' => '/suche'])->fetchOne();
        if ($existing) {
            $output->writeln('Suche already exists (uid ' . $existing . ') — skipping.');
            return;
        }

        $pageHandler = $this->runDataMap([
            'pages' => [
                'NEWsuchePage' => [
                    'pid' => self::PID_HAUPTNAV,
                    'title' => 'Suche',
                    'slug' => '/suche',
                    'doktype' => 1,
                    'nav_hide' => 1,
                    'hidden' => 0,
                ],
            ],
        ], $output, 'suche-page');
        $pageUid = (int)($pageHandler->substNEWwithIDs['NEWsuchePage'] ?? 0);
        if ($pageUid === 0) {
            $output->writeln('<error>Suche: Seite konnte nicht angelegt werden.</error>');
            return;
        }

        $handler = $this->runDataMap([
            'tt_content' => [
                'NEWsearchPlugin' => [
                    'pid' => $pageUid,
                    'CType' => 'indexedsearch_pi2',
                    'header' => 'Suche',
                    'frame_class' => 'default',
                ],
            ],
        ], $output, 'suche-plugin');
        $this->setSorting($ttContent, 'tt_content', [
            $handler->substNEWwithIDs['NEWsearchPlugin'] ?? 0,
        ]);

        $output->writeln('Suche (uid ' . $pageUid . ') angelegt, nav_hide=1 (Aufruf nur über Header-Suchfeld, wie im Lovable-Prototyp).');
    }
}
