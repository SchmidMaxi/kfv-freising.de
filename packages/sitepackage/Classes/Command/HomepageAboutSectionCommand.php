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
 * One-off, idempotent homepage fix: wraps the "Über uns" 2cols (uid 93) in a bg-muted
 * section container (mirroring the existing "Aktuelles" section 85/87 pattern), adds the
 * missing section-header and Feuerwehren-Karte plugin, removes the double-container
 * frame_class on the nested textmedia/feature-list, and sets the quick-actions card links.
 */
final class HomepageAboutSectionCommand extends Command
{
    private const HOME_PID = 1;
    private const ABOUT_TWOCOLS_UID = 93;
    private const TEXTMEDIA_UID = 94;
    private const FEATURELIST_UID = 95;

    protected function configure(): void
    {
        $this->setDescription('Restructures the homepage About section and fixes quick-actions links. Safe to re-run.');
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

        $dataMap = [];

        // --- Quick-actions card links (plain field update, always safe to re-apply) ---
        $links = [
            5 => 't3://page?uid=32', // Aktuelle Einsätze
            6 => 't3://page?uid=13', // Termine & Events
            7 => 't3://page?uid=45', // Feuerwehr finden
            8 => 't3://page?uid=47', // Service & Downloads
        ];
        foreach ($links as $uid => $link) {
            $dataMap['sitepackage_quickactions_items'][$uid] = ['link' => $link];
        }

        // --- About-section restructure (guarded, only runs once) ---
        $aboutRow = $connection->select(['uid', 'tx_container_parent'], 'tt_content', ['uid' => self::ABOUT_TWOCOLS_UID])
            ->fetchAssociative();

        if ($aboutRow === false) {
            $output->writeln('<error>tt_content uid ' . self::ABOUT_TWOCOLS_UID . ' not found — skipping About-section restructure.</error>');
        } elseif ((int)$aboutRow['tx_container_parent'] > 0) {
            $output->writeln('About-section already restructured (tx_container_parent=' . $aboutRow['tx_container_parent'] . ') — skipping.');
        } else {
            $result = $this->restructureAboutSection($connection, $output);
            if ($result !== Command::SUCCESS) {
                return $result;
            }
        }

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($dataMap, []);
        $dataHandler->process_datamap();

        if (!empty($dataHandler->errorLog)) {
            foreach ($dataHandler->errorLog as $error) {
                $output->writeln('<error>' . $error . '</error>');
            }
            return Command::FAILURE;
        }

        $output->writeln('Quick-actions card links set.');

        return Command::SUCCESS;
    }

    /**
     * tx_container_parent (added by b13/container) is a plain passthrough field, not a TCA
     * relation type — DataHandler's "NEWxxx"-placeholder substitution does not reach it, so
     * the new section must be created and committed in its own pass before anything can
     * reference its real uid.
     */
    private function restructureAboutSection(\TYPO3\CMS\Core\Database\Connection $connection, OutputInterface $output): int
    {
        $sectionHandler = GeneralUtility::makeInstance(DataHandler::class);
        $sectionHandler->start([
            'tt_content' => [
                'NEWsection1' => [
                    'pid' => self::HOME_PID,
                    'CType' => 'section',
                    'header' => '',
                    'color' => 'bg-muted',
                    'frame_class' => 'default',
                ],
            ],
        ], []);
        $sectionHandler->process_datamap();
        if (!empty($sectionHandler->errorLog)) {
            foreach ($sectionHandler->errorLog as $error) {
                $output->writeln('<error>' . $error . '</error>');
            }
            return Command::FAILURE;
        }
        $newSectionUid = (int)($sectionHandler->substNEWwithIDs['NEWsection1'] ?? 0);
        if ($newSectionUid === 0) {
            $output->writeln('<error>Failed to resolve new section uid.</error>');
            return Command::FAILURE;
        }

        $restHandler = GeneralUtility::makeInstance(DataHandler::class);
        $restHandler->start([
            'tt_content' => [
                'NEWheader1' => [
                    'pid' => self::HOME_PID,
                    'CType' => 'section-header',
                    'header' => 'Wir im Landkreis',
                    'sitepackage_sectionheader_header_highlight' => 'Freising',
                    'sitepackage_sectionheader_badge_text' => 'Über uns',
                    'sitepackage_sectionheader_alignment' => 'left',
                    'tx_container_parent' => $newSectionUid,
                    'colPos' => 200,
                    'frame_class' => 'none',
                ],
                'NEWmap1' => [
                    'pid' => self::HOME_PID,
                    'CType' => 'list',
                    'list_type' => 'feuerwehren_karte',
                    'header' => 'Feuerwehren-Karte',
                    // hidden: Lovable's reference shows the map with no heading above it
                    'header_layout' => 100,
                    'tx_container_parent' => self::ABOUT_TWOCOLS_UID,
                    'colPos' => 201,
                    'frame_class' => 'none',
                ],
                self::ABOUT_TWOCOLS_UID => [
                    'tx_container_parent' => $newSectionUid,
                    'colPos' => 200,
                    'frame_class' => 'none',
                ],
                // header_layout=100 (hidden) suppresses the textmedia's own H2/H3 — the new
                // section-header (NEWheader1) now owns the "Über uns" badge + H2, and the
                // Lovable reference has no separate heading on the paragraph itself.
                self::TEXTMEDIA_UID => ['frame_class' => 'none', 'header_layout' => 100],
                self::FEATURELIST_UID => ['frame_class' => 'none'],
            ],
        ], []);
        $restHandler->process_datamap();
        if (!empty($restHandler->errorLog)) {
            foreach ($restHandler->errorLog as $error) {
                $output->writeln('<error>' . $error . '</error>');
            }
            return Command::FAILURE;
        }

        $newHeaderUid = $restHandler->substNEWwithIDs['NEWheader1'] ?? null;
        if ($newHeaderUid !== null) {
            // Fix visual order within the column: header, then textmedia, then feature-list.
            $connection->update('tt_content', ['sorting' => 100], ['uid' => (int)$newHeaderUid]);
            $connection->update('tt_content', ['sorting' => 200], ['uid' => self::TEXTMEDIA_UID]);
            $connection->update('tt_content', ['sorting' => 300], ['uid' => self::FEATURELIST_UID]);
            $output->writeln(sprintf(
                'Created section (uid %d), section-header (uid %s), map plugin (uid %s); wrapped uid %d.',
                $newSectionUid,
                $newHeaderUid,
                $restHandler->substNEWwithIDs['NEWmap1'] ?? '?',
                self::ABOUT_TWOCOLS_UID
            ));
        }

        return Command::SUCCESS;
    }
}
