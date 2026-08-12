<?php

declare(strict_types=1);

namespace Schmid\Sitepackage\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * One-off, idempotent: builds out the remaining Lovable-Prototyp pages (Ausbildung 58,
 * Downloads 47, Formulare 14, Kontakt 17) with Content Blocks / b13-container structures.
 * Each page is only seeded if it currently has no tt_content rows — safe to re-run.
 */
final class SeedPhaseCPagesCommand extends Command
{
    private const PID_AUSBILDUNG = 58;
    private const PID_DOWNLOADS = 47;
    private const PID_FORMULARE = 14;
    private const PID_KONTAKT = 17;

    private const PAGE_TERMINE = 13;
    private const PAGE_DOWNLOADS = 47;
    private const PAGE_KONTAKT = 17;
    private const PAGE_JUGENDFEUERWEHR = 45;

    protected function configure(): void
    {
        $this->setDescription('Seeds content for Ausbildung, Downloads, Formulare and Kontakt pages. Safe to re-run (skips pages that already have content).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backendUser = GeneralUtility::makeInstance(CommandLineUserAuthentication::class);
        $backendUser->user['uid'] = 0;
        $backendUser->user['admin'] = 1;
        $backendUser->user['username'] = '_cli_sitepackage';
        $backendUser->workspace = 0;
        $GLOBALS['BE_USER'] = $backendUser;

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');

        $this->seedAusbildung($connection, $output);
        $this->seedDownloads($connection, $output);
        $this->seedFormulare($connection, $output);
        $this->seedKontakt($connection, $output);

        return Command::SUCCESS;
    }

    private function pageHasContent(\TYPO3\CMS\Core\Database\Connection $connection, int $pid): bool
    {
        $count = (int)$connection->count('uid', 'tt_content', ['pid' => $pid, 'deleted' => 0]);
        return $count > 0;
    }

    /**
     * IRRE child records (Content Blocks collection tables) need an explicit 'pid' key —
     * DataHandler does not infer it from the parent NEW placeholder alone.
     */
    private function addPidToCollections(array &$dataMap, int $pid): void
    {
        foreach ($dataMap as $table => &$records) {
            if ($table === 'tt_content') {
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

    private function setSorting(\TYPO3\CMS\Core\Database\Connection $connection, array $uidsInOrder, int $step = 100): void
    {
        $sorting = $step;
        foreach ($uidsInOrder as $uid) {
            $connection->update('tt_content', ['sorting' => $sorting], ['uid' => (int)$uid]);
            $sorting += $step;
        }
    }

    // -----------------------------------------------------------------
    // Ausbildung (58)
    // -----------------------------------------------------------------
    private function seedAusbildung(\TYPO3\CMS\Core\Database\Connection $connection, OutputInterface $output): void
    {
        if ($this->pageHasContent($connection, self::PID_AUSBILDUNG)) {
            $output->writeln('Ausbildung (58) already has content — skipping.');
            return;
        }

        // Pass 1: create the 3cols container (tx_container_parent needs a real uid).
        $containerHandler = $this->runDataMap([
            'tt_content' => [
                'NEW3cols' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => '3cols',
                    'header' => '',
                    'frame_class' => 'default',
                ],
            ],
        ], $output, 'ausbildung-container');
        $threeColsUid = (int)($containerHandler->substNEWwithIDs['NEW3cols'] ?? 0);
        if ($threeColsUid === 0) {
            $output->writeln('<error>Ausbildung: failed to create 3cols container.</error>');
            return;
        }

        // Pass 2: everything else.
        $dataMap = [
            'tt_content' => [
                'NEWheader' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'section-header',
                    'header' => 'Ausbildung',
                    'sitepackage_sectionheader_badge_text' => 'Weiterbildung',
                    'sitepackage_sectionheader_subheadline' => 'Lehrgänge, Fortbildungen und Informationen zur Ausbildung im Kreisfeuerwehrverband Freising.',
                    'sitepackage_sectionheader_alignment' => 'left',
                    'frame_class' => 'default',
                ],
                'NEWcardTermine' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'card',
                    'header' => 'Termine',
                    'bodytext' => 'Alle Ausbildungstermine',
                    'header_link' => 't3://page?uid=' . self::PAGE_TERMINE,
                    'color' => 'default',
                    'tx_container_parent' => $threeColsUid,
                    'colPos' => 200,
                    'frame_class' => 'default',
                ],
                'NEWcardDownloads' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'card',
                    'header' => 'Downloads',
                    'bodytext' => 'Formulare & Unterlagen',
                    'header_link' => 't3://page?uid=' . self::PAGE_DOWNLOADS,
                    'color' => 'default',
                    'tx_container_parent' => $threeColsUid,
                    'colPos' => 201,
                    'frame_class' => 'default',
                ],
                'NEWcardKontakt' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'card',
                    'header' => 'Ansprechpartner',
                    'bodytext' => 'Kreisausbilder kontaktieren',
                    'header_link' => 't3://page?uid=' . self::PAGE_KONTAKT,
                    'color' => 'default',
                    'tx_container_parent' => $threeColsUid,
                    'colPos' => 202,
                    'frame_class' => 'default',
                ],
                'NEWlehrgaenge' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'card-group',
                    'header' => 'Lehrgänge auf Kreisebene',
                    'sitepackage_cardgroup_card_layout' => 'feature',
                    'breakpoint_mobile' => 1,
                    'breakpoint_tablet' => 1,
                    'breakpoint_desktop' => 1,
                    'sitepackage_cardgroup_items' => 'NEWlg1,NEWlg2,NEWlg3,NEWlg4,NEWlg5',
                    'frame_class' => 'default',
                ],
                'NEWjugend' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'card-group',
                    'header' => 'Jugend- & Kinderfeuerwehr',
                    'sitepackage_cardgroup_card_layout' => 'stat',
                    'breakpoint_mobile' => 2,
                    'breakpoint_tablet' => 3,
                    'breakpoint_desktop' => 3,
                    'sitepackage_cardgroup_items' => 'NEWj1,NEWj2,NEWj3',
                    'frame_class' => 'default',
                ],
                'NEWjugendCta' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'card',
                    'header' => 'Interesse an der Jugendfeuerwehr?',
                    'bodytext' => 'Die Jugendfeuerwehr bietet Kindern und Jugendlichen die Möglichkeit, spielerisch die Arbeit der Feuerwehr kennenzulernen und wichtige Werte wie Teamarbeit und Hilfsbereitschaft zu erlernen.',
                    'header_link' => 't3://page?uid=' . self::PAGE_JUGENDFEUERWEHR,
                    'color' => 'light',
                    'frame_class' => 'default',
                ],
                'NEWweiterfuehrend' => [
                    'pid' => self::PID_AUSBILDUNG,
                    'CType' => 'card-group',
                    'header' => 'Weiterführende Ausbildung',
                    'sitepackage_cardgroup_card_layout' => 'default',
                    'breakpoint_mobile' => 1,
                    'breakpoint_tablet' => 2,
                    'breakpoint_desktop' => 2,
                    'sitepackage_cardgroup_items' => 'NEWwf1,NEWwf2',
                    'frame_class' => 'default',
                ],
            ],
            'sitepackage_cardgroup_items' => [
                'NEWlg1' => ['header' => 'Truppmann Teil 1', 'bodytext' => 'Grundausbildung für alle Feuerwehrangehörigen · 70 Stunden · max. 24 Teilnehmer · Kreisausbildungszentrum Freising', 'badge' => 'Nächster Termin: März 2026'],
                'NEWlg2' => ['header' => 'Truppmann Teil 2', 'bodytext' => 'Weiterführende Ausbildung nach Truppmann Teil 1 · 80 Stunden · max. 24 Teilnehmer · Kreisausbildungszentrum Freising', 'badge' => 'Nächster Termin: April 2026'],
                'NEWlg3' => ['header' => 'Sprechfunker', 'bodytext' => 'Ausbildung im Bereich Funk und Kommunikation · 16 Stunden · max. 20 Teilnehmer · Feuerwehrhaus Moosburg', 'badge' => 'Nächster Termin: Februar 2026'],
                'NEWlg4' => ['header' => 'Atemschutzgeräteträger', 'bodytext' => 'Ausbildung zum Tragen von Atemschutzgeräten · 25 Stunden · max. 16 Teilnehmer · Atemschutzzentrum Freising', 'badge' => 'Nächster Termin: Mai 2026'],
                'NEWlg5' => ['header' => 'Maschinisten', 'bodytext' => 'Ausbildung für Maschinisten Löschfahrzeuge · 35 Stunden · max. 12 Teilnehmer · Kreisausbildungszentrum Freising', 'badge' => 'Nächster Termin: Juni 2026'],
                'NEWj1' => ['header' => 'Jugendfeuerwehren', 'bodytext' => 'Aktive Jugendfeuerwehren im Landkreis', 'icon' => 'people', 'stat_value' => '24'],
                'NEWj2' => ['header' => 'Mitglieder', 'bodytext' => 'Jugendliche zwischen 12 und 18 Jahren', 'icon' => 'star', 'stat_value' => '480+'],
                'NEWj3' => ['header' => 'Kinderfeuerwehren', 'bodytext' => 'Für Kinder zwischen 6 und 12 Jahren', 'icon' => 'people', 'stat_value' => '8'],
                'NEWwf1' => ['header' => 'Staatliche Feuerwehrschulen', 'bodytext' => 'Lehrgänge auf Landesebene wie Gruppenführer, Zugführer und Spezialausbildungen. <a href="https://www.sfs-w.de" target="_blank" rel="noopener noreferrer">SFS Würzburg &rarr;</a>'],
                'NEWwf2' => ['header' => 'LFV Bayern', 'bodytext' => 'Informationen und Fortbildungsangebote des Landesfeuerwehrverbands Bayern. <a href="https://www.lfv-bayern.de" target="_blank" rel="noopener noreferrer">LFV Bayern &rarr;</a>'],
            ],
        ];

        $this->addPidToCollections($dataMap, self::PID_AUSBILDUNG);
        $handler = $this->runDataMap($dataMap, $output, 'ausbildung');

        $this->setSorting($connection, [
            $handler->substNEWwithIDs['NEWheader'] ?? 0,
            $threeColsUid,
            $handler->substNEWwithIDs['NEWlehrgaenge'] ?? 0,
            $handler->substNEWwithIDs['NEWjugend'] ?? 0,
            $handler->substNEWwithIDs['NEWjugendCta'] ?? 0,
            $handler->substNEWwithIDs['NEWweiterfuehrend'] ?? 0,
        ]);
        $this->setSorting($connection, [
            $handler->substNEWwithIDs['NEWcardTermine'] ?? 0,
            $handler->substNEWwithIDs['NEWcardDownloads'] ?? 0,
            $handler->substNEWwithIDs['NEWcardKontakt'] ?? 0,
        ], 10);

        $output->writeln('Ausbildung (58) seeded.');
    }

    // -----------------------------------------------------------------
    // Downloads (47)
    // -----------------------------------------------------------------
    private function seedDownloads(\TYPO3\CMS\Core\Database\Connection $connection, OutputInterface $output): void
    {
        if ($this->pageHasContent($connection, self::PID_DOWNLOADS)) {
            $output->writeln('Downloads (47) already has content — skipping.');
            return;
        }

        $categories = [
            'ausbildung' => [
                'title' => 'Ausbildung & Lehrgänge',
                'items' => [
                    ['Lehrgangsanmeldung MTA', 'Anmeldung zur Modularen Truppausbildung', 'PDF · 245 KB'],
                    ['Prüfungsordnung THL', 'Prüfungsordnung Technische Hilfeleistung', 'PDF · 1.2 MB'],
                    ['Ausbildungsplan 2025', 'Jahresausbildungsplan mit allen Terminen', 'PDF · 890 KB'],
                    ['Lehrgang Atemschutz - Antrag', 'Anmeldung zum Atemschutzlehrgang', 'PDF · 312 KB'],
                    ['Maschinisten-Lehrgang Unterlagen', 'Begleitmaterial für Maschinisten-Ausbildung', 'PDF · 2.4 MB'],
                    ['Gruppenführer-Lehrgang Info', 'Informationen zum Gruppenführer-Lehrgang', 'PDF · 567 KB'],
                ],
            ],
            'verwaltung' => [
                'title' => 'Verwaltung & Satzung',
                'items' => [
                    ['Mitgliedsantrag KFV', 'Antrag auf Mitgliedschaft im Verband', 'PDF · 156 KB'],
                    ['Satzung KFV Freising', 'Aktuelle Verbandssatzung', 'PDF · 2.1 MB'],
                    ['Beitragsordnung', 'Regelung der Mitgliedsbeiträge', 'PDF · 98 KB'],
                    ['Geschäftsordnung', 'Geschäftsordnung des Verbands', 'PDF · 445 KB'],
                    ['Ehrungsordnung', 'Regelungen zu Ehrungen und Auszeichnungen', 'PDF · 234 KB'],
                    ['Datenschutzerklärung', 'Informationen zum Datenschutz', 'PDF · 189 KB'],
                ],
            ],
            'einsatz' => [
                'title' => 'Einsatzunterlagen',
                'items' => [
                    ['Alarmierungsübersicht Landkreis', 'Übersicht aller Alarmierungsstufen', 'PDF · 445 KB'],
                    ['Funkrufnamen Landkreis Freising', 'Aktuelle Funkrufnamenliste', 'PDF · 312 KB'],
                    ['Einsatzbericht Vorlage', 'Vorlage für Einsatzberichte', 'DOCX · 67 KB'],
                    ['AAO Landkreis Freising', 'Alarm- und Ausrückeordnung', 'PDF · 1.8 MB'],
                    ['Gefahrgut-Merkblätter', 'Wichtige Gefahrgut-Informationen', 'PDF · 3.2 MB'],
                    ['Einsatzleiter-Checkliste', 'Checkliste für Einsatzleiter', 'PDF · 234 KB'],
                ],
            ],
            'formulare' => [
                'title' => 'Anträge & Formulare',
                'items' => [
                    ['Zuschussantrag Ausrüstung', 'Antrag auf Bezuschussung von Ausrüstung', 'PDF · 289 KB'],
                    ['Zuschussantrag Fahrzeuge', 'Antrag für Fahrzeugbeschaffung', 'PDF · 345 KB'],
                    ['Veranstaltungsanmeldung', 'Anmeldung zu Verbandsveranstaltungen', 'PDF · 178 KB'],
                    ['Freistellungsantrag', 'Antrag auf Arbeitsfreistellung', 'PDF · 134 KB'],
                    ['Unfallanzeige', 'Formular für Unfallmeldungen', 'PDF · 267 KB'],
                    ['Kostenerstattung', 'Antrag auf Kostenerstattung', 'PDF · 198 KB'],
                ],
            ],
            'jugend' => [
                'title' => 'Jugendfeuerwehr',
                'items' => [
                    ['Aufnahmeantrag Jugendfeuerwehr', 'Mitgliedsantrag für Jugendliche', 'PDF · 234 KB'],
                    ['Einverständniserklärung Eltern', 'Einverständnis für Veranstaltungen', 'PDF · 145 KB'],
                    ['Jugendleistungsprüfung Ordnung', 'Prüfungsordnung JF', 'PDF · 567 KB'],
                    ['Wissenstest Unterlagen', 'Lernmaterial für den Wissenstest', 'PDF · 1.1 MB'],
                    ['Zeltlager Anmeldung', 'Anmeldung zum JF-Zeltlager', 'PDF · 198 KB'],
                    ['Jugendordnung', 'Ordnung der Jugendfeuerwehr', 'PDF · 345 KB'],
                ],
            ],
        ];

        $faqs = [
            ['Wie kann ich der Feuerwehr beitreten?', 'Jeder ab 12 Jahren kann Mitglied werden. Wende dich an deine örtliche Feuerwehr oder nutze unser Kontaktformular. Die Jugendfeuerwehr nimmt Mitglieder ab 12 Jahren auf, der aktive Dienst beginnt mit 18 Jahren.'],
            ['Welche Ausbildung erhalte ich?', 'Die Grundausbildung (Modulare Truppausbildung) dauert ca. 100 Stunden und umfasst Theorie und Praxis. Danach gibt es zahlreiche Weiterbildungsmöglichkeiten wie Atemschutz, Maschinisten-Ausbildung oder Führungslehrgänge.'],
            ['Muss ich für die Ausrüstung bezahlen?', 'Nein, die persönliche Schutzausrüstung wird von der Gemeinde gestellt. Dazu gehören Einsatzkleidung, Helm, Handschuhe und Stiefel.'],
            ['Wie oft finden Übungen statt?', 'In der Regel übt jede Feuerwehr ein- bis zweimal im Monat. Die genauen Termine werden von der jeweiligen Feuerwehr festgelegt.'],
            ['Kann ich Feuerwehr und Beruf vereinbaren?', 'Ja! Die Freiwillige Feuerwehr ist ehrenamtlich. Arbeitgeber sind gesetzlich verpflichtet, Feuerwehrleute für Einsätze freizustellen. Die meisten Übungen finden abends oder am Wochenende statt.'],
            ['Was ist der Kreisfeuerwehrverband?', 'Der KFV ist der Zusammenschluss aller Feuerwehren im Landkreis Freising. Wir koordinieren überörtliche Ausbildung, vertreten gemeinsame Interessen und fördern die Zusammenarbeit der Wehren.'],
        ];

        $dataMap = [
            'tt_content' => [
                'NEWheader' => [
                    'pid' => self::PID_DOWNLOADS,
                    'CType' => 'section-header',
                    'header' => 'Downloads & Formulare',
                    'sitepackage_sectionheader_badge_text' => 'Service',
                    'sitepackage_sectionheader_subheadline' => 'Hier finden Sie alle wichtigen Dokumente, Formulare und Unterlagen für die Feuerwehren im Landkreis Freising.',
                    'sitepackage_sectionheader_alignment' => 'left',
                    'frame_class' => 'default',
                ],
            ],
            'sitepackage_cardgroup_items' => [],
        ];

        $catUidPlaceholders = [];
        foreach ($categories as $key => $category) {
            $catCode = ucfirst($key);
            $ceKey = 'NEWcat' . $catCode;
            $itemKeys = [];
            foreach ($category['items'] as $i => $item) {
                $itemKey = 'NEWi' . $catCode . $i;
                $itemKeys[] = $itemKey;
                $dataMap['sitepackage_cardgroup_items'][$itemKey] = [
                    'header' => $item[0],
                    'bodytext' => $item[1],
                    'badge' => $item[2],
                ];
            }
            $dataMap['tt_content'][$ceKey] = [
                'pid' => self::PID_DOWNLOADS,
                'CType' => 'card-group',
                'header' => $category['title'],
                'sitepackage_cardgroup_card_layout' => 'feature',
                'breakpoint_mobile' => 1,
                'breakpoint_tablet' => 2,
                'breakpoint_desktop' => 3,
                'sitepackage_cardgroup_items' => implode(',', $itemKeys),
                'frame_class' => 'default',
            ];
            $catUidPlaceholders[] = $ceKey;
        }

        $dataMap['tt_content']['NEWcta'] = [
            'pid' => self::PID_DOWNLOADS,
            'CType' => 'card-group',
            'header' => '',
            'sitepackage_cardgroup_card_layout' => 'feature',
            'breakpoint_mobile' => 1,
            'breakpoint_tablet' => 1,
            'breakpoint_desktop' => 1,
            'sitepackage_cardgroup_items' => 'NEWctaItem',
            'frame_class' => 'default',
        ];
        $dataMap['sitepackage_cardgroup_items']['NEWctaItem'] = [
            'header' => 'Dokument nicht gefunden?',
            'bodytext' => 'Sollten Sie ein bestimmtes Formular oder Dokument benötigen, das hier nicht aufgeführt ist, kontaktieren Sie uns bitte direkt. Wir helfen Ihnen gerne weiter.',
            'link' => 't3://page?uid=' . self::PAGE_KONTAKT,
        ];

        $faqItemKeys = [];
        $dataMap['sitepackage_accordion_items'] = [];
        foreach ($faqs as $i => $faq) {
            $key = 'NEWfaq' . $i;
            $faqItemKeys[] = $key;
            $dataMap['sitepackage_accordion_items'][$key] = [
                'title' => $faq[0],
                'bodytext' => $faq[1],
            ];
        }
        $dataMap['tt_content']['NEWfaqAccordion'] = [
            'pid' => self::PID_DOWNLOADS,
            'CType' => 'accordion',
            'header' => 'Häufige Fragen',
            'sitepackage_accordion_accordion_layout' => 'card',
            'sitepackage_accordion_items' => implode(',', $faqItemKeys),
            'frame_class' => 'default',
        ];

        $this->addPidToCollections($dataMap, self::PID_DOWNLOADS);
        $handler = $this->runDataMap($dataMap, $output, 'downloads');

        $order = [$handler->substNEWwithIDs['NEWheader'] ?? 0];
        foreach ($catUidPlaceholders as $ceKey) {
            $order[] = $handler->substNEWwithIDs[$ceKey] ?? 0;
        }
        $order[] = $handler->substNEWwithIDs['NEWcta'] ?? 0;
        $order[] = $handler->substNEWwithIDs['NEWfaqAccordion'] ?? 0;
        $this->setSorting($connection, $order);

        $output->writeln('Downloads (47) seeded (Download-Links sind Platzhalter ohne echte Dateien).');
    }

    // -----------------------------------------------------------------
    // Formulare (14)
    // -----------------------------------------------------------------
    private function seedFormulare(\TYPO3\CMS\Core\Database\Connection $connection, OutputInterface $output): void
    {
        if ($this->pageHasContent($connection, self::PID_FORMULARE)) {
            $output->writeln('Formulare (14) already has content — skipping.');
            return;
        }

        $forms = [
            ['Lehrgangsanmeldung', 'Anmeldung zu Lehrgängen an der Staatlichen Feuerwehrschule'],
            ['Mitgliedsantrag', 'Antrag auf Mitgliedschaft im Kreisfeuerwehrverband'],
            ['Zuschussantrag', 'Antrag auf Bezuschussung von Ausrüstung und Fahrzeugen'],
            ['Veranstaltungsanmeldung', 'Anmeldung zu Veranstaltungen des Verbandes'],
        ];

        $dataMap = [
            'tt_content' => [
                'NEWheader' => [
                    'pid' => self::PID_FORMULARE,
                    'CType' => 'section-header',
                    'header' => 'Formulare',
                    'sitepackage_sectionheader_badge_text' => 'Service',
                    'sitepackage_sectionheader_subheadline' => 'Alle wichtigen Antragsformulare zum Download.',
                    'sitepackage_sectionheader_alignment' => 'left',
                    'frame_class' => 'default',
                ],
                'NEWforms' => [
                    'pid' => self::PID_FORMULARE,
                    'CType' => 'card-group',
                    'header' => '',
                    'sitepackage_cardgroup_card_layout' => 'feature',
                    'breakpoint_mobile' => 1,
                    'breakpoint_tablet' => 2,
                    'breakpoint_desktop' => 4,
                    'sitepackage_cardgroup_items' => 'NEWf0,NEWf1,NEWf2,NEWf3',
                    'frame_class' => 'default',
                ],
            ],
            'sitepackage_cardgroup_items' => [],
        ];
        foreach ($forms as $i => $form) {
            $dataMap['sitepackage_cardgroup_items']['NEWf' . $i] = [
                'header' => $form[0],
                'bodytext' => $form[1],
            ];
        }

        $this->addPidToCollections($dataMap, self::PID_FORMULARE);
        $handler = $this->runDataMap($dataMap, $output, 'formulare');
        $this->setSorting($connection, [
            $handler->substNEWwithIDs['NEWheader'] ?? 0,
            $handler->substNEWwithIDs['NEWforms'] ?? 0,
        ]);

        $output->writeln('Formulare (14) seeded.');
    }

    // -----------------------------------------------------------------
    // Kontakt (17)
    // -----------------------------------------------------------------
    private function seedKontakt(\TYPO3\CMS\Core\Database\Connection $connection, OutputInterface $output): void
    {
        if ($this->pageHasContent($connection, self::PID_KONTAKT)) {
            $output->writeln('Kontakt (17) already has content — skipping.');
            return;
        }

        // Pass 1: create the 2cols container.
        $containerHandler = $this->runDataMap([
            'tt_content' => [
                'NEW2cols' => [
                    'pid' => self::PID_KONTAKT,
                    'CType' => '2cols',
                    'header' => '',
                    'frame_class' => 'default',
                ],
            ],
        ], $output, 'kontakt-container');
        $twoColsUid = (int)($containerHandler->substNEWwithIDs['NEW2cols'] ?? 0);
        if ($twoColsUid === 0) {
            $output->writeln('<error>Kontakt: failed to create 2cols container.</error>');
            return;
        }

        $formFlexform = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'
            . '<T3FlexForms><data><sheet index="sDEF"><language index="lDEF">'
            . '<field index="settings.persistenceIdentifier"><value index="vDEF">EXT:sitepackage/Resources/Private/Form/Definitions/kontaktformular.form.yaml</value></field>'
            . '</language></sheet></data></T3FlexForms>';

        $notfallHtml = '<div class="bg-danger-subtle border border-danger rounded-3 p-4">'
            . '<h3 class="h5 fw-bold mb-3">Im Notfall</h3>'
            . '<p class="mb-3">Bei Feuer, Unfällen oder akuten Notfällen wählen Sie bitte sofort den Notruf.</p>'
            . '<p class="mb-0"><span class="display-6 fw-bold text-danger">112</span> '
            . '<span class="text-body-secondary ms-2">Notruf Feuerwehr &amp; Rettungsdienst</span></p>'
            . '</div>';

        $dataMap = [
            'tt_content' => [
                'NEWheader' => [
                    'pid' => self::PID_KONTAKT,
                    'CType' => 'section-header',
                    'header' => 'Kontakt',
                    'sitepackage_sectionheader_badge_text' => 'Kontakt',
                    'sitepackage_sectionheader_subheadline' => 'Haben Sie Fragen oder Anliegen? Wir freuen uns auf Ihre Nachricht.',
                    'sitepackage_sectionheader_alignment' => 'left',
                    'frame_class' => 'default',
                ],
                'NEWform' => [
                    'pid' => self::PID_KONTAKT,
                    'CType' => 'form_formframework',
                    'header' => 'Schreiben Sie uns',
                    'pi_flexform' => $formFlexform,
                    'tx_container_parent' => $twoColsUid,
                    'colPos' => 200,
                    'frame_class' => 'default',
                ],
                'NEWkontaktdatenHeader' => [
                    'pid' => self::PID_KONTAKT,
                    'CType' => 'header',
                    'header' => 'Kontaktdaten',
                    'header_layout' => 3,
                    'tx_container_parent' => $twoColsUid,
                    'colPos' => 201,
                    'frame_class' => 'default',
                ],
                'NEWfeaturelist' => [
                    'pid' => self::PID_KONTAKT,
                    'CType' => 'feature-list',
                    'header' => '',
                    'sitepackage_featurelist_features' => 'NEWfeat1,NEWfeat2,NEWfeat3,NEWfeat4',
                    'tx_container_parent' => $twoColsUid,
                    'colPos' => 201,
                    'frame_class' => 'default',
                ],
                'NEWnotfall' => [
                    'pid' => self::PID_KONTAKT,
                    'CType' => 'html',
                    'header' => '',
                    'bodytext' => $notfallHtml,
                    'tx_container_parent' => $twoColsUid,
                    'colPos' => 201,
                    'frame_class' => 'default',
                ],
            ],
            'sitepackage_featurelist_features' => [
                'NEWfeat1' => ['icon' => 'telephone', 'title' => 'Telefon', 'description' => '08161 / 123 456'],
                'NEWfeat2' => ['icon' => 'envelope', 'title' => 'E-Mail', 'description' => 'info@kfv-freising.de'],
                'NEWfeat3' => ['icon' => 'geo-alt', 'title' => 'Adresse', 'description' => 'Landratsamt Freising, Landshuter Str. 31, 85356 Freising'],
                'NEWfeat4' => ['icon' => 'clock', 'title' => 'Öffnungszeiten', 'description' => 'Mo - Fr: 08:00 - 16:00 Uhr'],
            ],
        ];

        $this->addPidToCollections($dataMap, self::PID_KONTAKT);
        $handler = $this->runDataMap($dataMap, $output, 'kontakt');

        $this->setSorting($connection, [
            $handler->substNEWwithIDs['NEWheader'] ?? 0,
            $twoColsUid,
        ]);
        $this->setSorting($connection, [
            $handler->substNEWwithIDs['NEWkontaktdatenHeader'] ?? 0,
            $handler->substNEWwithIDs['NEWfeaturelist'] ?? 0,
            $handler->substNEWwithIDs['NEWnotfall'] ?? 0,
        ], 10);

        $output->writeln('Kontakt (17) seeded.');
    }
}
