<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;

#[AsCommand(name: 'feuerwehren:update-tiles', description: 'Update or prewarm local OSM tiles/MBTiles cache')]
class OsmTilesUpdateCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Example: prewarm a small bbox around all Feuerwehren positions
        // Implement repository lookups via DI if you want advanced prewarming.
        $var = Environment::getVarPath() . '/tiles';
        if (!is_dir($var)) { @mkdir($var, 0775, true); }
        $output->writeln('<info>Tiles cache checked at: ' . $var . '</info>');
        // You can extend this command to download/replace a configured MBTiles file regularly.
        return Command::SUCCESS;
    }
}