<?php

declare(strict_types=1);

namespace Schmid\Sitepackage\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Applies a JSON file of the form {"datamap": {...}, "cmdmap": {...}} via DataHandler
 * in the live workspace and prints the resolved NEW-id mapping as JSON.
 *
 * The datamap is processed before the cmdmap, so cmdmap entries cannot reference
 * NEW ids from the same file — split such changes into two runs.
 */
final class DataHandlerApplyCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to JSON file with "datamap" and/or "cmdmap"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string)$input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln('<error>File not found: ' . $file . '</error>');
            return Command::FAILURE;
        }
        $payload = json_decode((string)file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

        $backendUser = GeneralUtility::makeInstance(CommandLineUserAuthentication::class);
        $backendUser->user['uid'] = 0;
        $backendUser->user['admin'] = 1;
        $backendUser->user['username'] = '_cli_sitepackage';
        $backendUser->workspace = 0;
        $GLOBALS['BE_USER'] = $backendUser;

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($payload['datamap'] ?? [], $payload['cmdmap'] ?? []);
        $dataHandler->process_datamap();
        $dataHandler->process_cmdmap();

        $output->writeln(json_encode([
            'ids' => $dataHandler->substNEWwithIDs,
            'errors' => $dataHandler->errorLog,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $dataHandler->errorLog === [] ? Command::SUCCESS : Command::FAILURE;
    }
}
