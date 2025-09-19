<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;

#[AsCommand(
    name: 'feuerwehren:mbtiles:fetch',
    description: 'Download a MBTiles file and atomically replace the configured mbtilesPath'
)]
final class MbtilesFetchCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('url', null, InputOption::VALUE_REQUIRED, 'Source URL (http/https/file) for MBTiles')
            ->addOption('dest', null, InputOption::VALUE_REQUIRED, 'Destination path for MBTiles (absolute or relative to public/)')
            ->addOption('sha256', null, InputOption::VALUE_OPTIONAL, 'Expected SHA256 checksum (optional)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite even if destination exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Read from CLI options OR fall back to TypoScript constants via ENV if you expose them.
        $url  = (string)($input->getOption('url') ?? '');
        $dest = (string)($input->getOption('dest') ?? '');
        $sum  = (string)($input->getOption('sha256') ?? '');
        $force = (bool)$input->getOption('force');

        if ($url === '') {
            $output->writeln('<error>--url is required</error>');
            return Command::INVALID;
        }
        if ($dest === '') {
            // Default to public/fileadmin/tiles/osm.mbtiles
            $dest = Environment::getPublicPath() . '/fileadmin/tiles/osm.mbtiles';
        } elseif ($dest[0] !== '/') {
            $dest = Environment::getPublicPath() . '/' . ltrim($dest, '/');
        }

        $tmpDir = Environment::getVarPath() . '/mbtiles';
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
        $tmpFile = $tmpDir . '/download_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.mbtiles';

        $output->writeln('<info>Downloading:</info> ' . $url);
        $output->writeln('<info>Destination:</info> ' . $dest);

        // Stream download (handles big files)
        $in = @fopen($url, 'rb');
        if ($in === false) {
            $output->writeln('<error>Could not open URL for reading</error>');
            return Command::FAILURE;
        }
        if (!is_dir(dirname($tmpFile))) { @mkdir(dirname($tmpFile), 0775, true); }
        $out = @fopen($tmpFile, 'wb');
        if ($out === false) {
            fclose($in);
            $output->writeln('<error>Could not open temp file for writing</error>');
            return Command::FAILURE;
        }

        $bytes = stream_copy_to_stream($in, $out);
        fclose($in);
        fclose($out);

        if ($bytes === false || $bytes === 0) {
            @unlink($tmpFile);
            $output->writeln('<error>Download produced no data</error>');
            return Command::FAILURE;
        }
        $output->writeln('<info>Downloaded bytes:</info> ' . number_format((float)$bytes));

        // Optional checksum
        if ($sum !== '') {
            $calc = hash_file('sha256', $tmpFile);
            if (!hash_equals($sum, $calc)) {
                @unlink($tmpFile);
                $output->writeln('<error>SHA256 mismatch</error>');
                $output->writeln('<comment>Expected:</comment> ' . $sum);
                $output->writeln('<comment>Actual:</comment>   ' . $calc);
                return Command::FAILURE;
            }
            $output->writeln('<info>SHA256 verified</info>');
        }

        // Ensure destination dir exists
        $destDir = dirname($dest);
        if (!is_dir($destDir)) { @mkdir($destDir, 0775, true); }

        if (is_file($dest) && !$force) {
            $output->writeln('<comment>Destination exists; use --force to overwrite</comment>');
            @unlink($tmpFile);
            return Command::SUCCESS;
        }

        // Atomic replace: write to *.new then rename
        $destNew = $dest . '.new';
        if (is_file($destNew)) { @unlink($destNew); }
        if (!@rename($tmpFile, $destNew)) {
            @unlink($tmpFile);
            $output->writeln('<error>Could not move temp to destination .new</error>');
            return Command::FAILURE;
        }
        if (is_file($dest)) {
            if (!@rename($dest, $dest . '.bak_' . date('Ymd_His'))) {
                $output->writeln('<comment>Could not rotate old file; continuing</comment>');
            }
        }
        if (!@rename($destNew, $dest)) {
            $output->writeln('<error>Could not finalize replacement</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>MBTiles installed at:</info> ' . $dest);

        // Optional: read min/max zoom from metadata (best-effort)
        try {
            $db = new \SQLite3($dest, \SQLITE3_OPEN_READONLY);
            $meta = $db->query('SELECT name, value FROM metadata');
            $info = [];
            while ($row = $meta->fetchArray(\SQLITE3_ASSOC)) {
                $info[$row['name']] = $row['value'];
            }
            $db->close();
            if ($info) {
                $output->writeln('<comment>metadata:</comment> ' . json_encode($info, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
            }
        } catch (\Throwable $e) {
            $output->writeln('<comment>(metadata read skipped: '.$e->getMessage().')</comment>');
        }

        return Command::SUCCESS;
    }
}
