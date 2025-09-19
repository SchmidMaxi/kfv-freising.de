<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Command;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class TilesPrefetchCommand extends Command
{
    protected static $defaultName = 'feuerwehren:tiles:prefetch';
    protected static $defaultDescription = 'Prefetch Vector Tiles (.pbf) into var/tiles for given bbox and zooms';

    protected function configure(): void
    {
        $this
            ->addOption(
                'bbox',
                null,
                InputOption::VALUE_REQUIRED,
                'Bounding box as "minLon,minLat,maxLon,maxLat" (W,S,E,N)',
                '11.30,48.30,12.08,48.70'
            )
            ->addOption(
                'zooms',
                null,
                InputOption::VALUE_REQUIRED,
                'Zooms "10-14" or csv "10,11,12" (WebMercator)',
                '10-14'
            )
            ->addOption(
                'source',
                null,
                InputOption::VALUE_OPTIONAL,
                'Upstream template URL for PBF tiles (with {z}/{x}/{y}); falls back to EXT conf vtProxy.baseUrl',
                ''
            )
            ->addOption(
                'overwrite',
                'f',
                InputOption::VALUE_NONE,
                'Overwrite existing tiles'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io    = new SymfonyStyle($input, $output);
        $bbox  = (string)$input->getOption('bbox');
        $zooms = (string)$input->getOption('zooms');
        $src   = (string)$input->getOption('source');
        $force = (bool)$input->getOption('overwrite');

        // Upstream aus EXT-Konfig übernehmen, wenn --source fehlt
        if ($src === '') {
            $conf = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['feuerwehren']['vtProxy'] ?? [];
            $src  = (string)($conf['baseUrl'] ?? '');
        }
        if ($src === '' || !str_contains($src, '{z}') || !str_contains($src, '{x}') || !str_contains($src, '{y}')) {
            $io->error('Upstream "source" fehlt oder enthält keine {z}/{x}/{y}-Platzhalter.');
            $io->writeln('Beispiel: --source="https://tiles.example.com/data/v3/{z}/{x}/{y}.pbf?key=APIKEY"');
            return Command::INVALID;
        }

        [$w, $s, $e, $n] = $this->parseBbox($bbox);
        $zoomList = $this->parseZooms($zooms);
        if ($zoomList === []) {
            $io->error('Ungültige Zoom-Angabe.');
            return Command::INVALID;
        }

        $baseVar = Environment::getVarPath();
        $destRoot = $baseVar . '/tiles';

        /** @var RequestFactory $rf */
        $rf = GeneralUtility::makeInstance(RequestFactory::class);

        $total = 0;
        foreach ($zoomList as $z) {
            [$minX, $maxX, $minY, $maxY] = $this->tileBounds($w, $s, $e, $n, $z);
            $countThisZoom = max(0, ($maxX - $minX + 1)) * max(0, ($maxY - $minY + 1));
            $io->section(sprintf('Zoom %d: %d Tiles (%d..%d x %d..%d)', $z, $countThisZoom, $minX, $maxX, $minY, $maxY));
            $total += $countThisZoom;
            $bar = $io->createProgressBar($countThisZoom);
            $bar->start();

            for ($x = $minX; $x <= $maxX; $x++) {
                for ($y = $minY; $y <= $maxY; $y++) {
                    $destDir = sprintf('%s/%d/%d', $destRoot, $z, $x);
                    $dest = sprintf('%s/%d/%d/%d.pbf', $destRoot, $z, $x, $y);

                    if (!$force && is_file($dest) && filesize($dest) > 0) {
                        $bar->advance();
                        continue;
                    }
                    if (!is_dir($destDir) && !@mkdir($destDir, 0775, true) && !is_dir($destDir)) {
                        $io->warning('Konnte Verzeichnis nicht anlegen: ' . $destDir);
                        $bar->advance();
                        continue;
                    }

                    $url = strtr($src, ['{z}' => (string)$z, '{x}' => (string)$x, '{y}' => (string)$y]);
                    try {
                        $res = $rf->request($url, 'GET', [
                            'headers' => [
                                'Accept' => 'application/x-protobuf, */*',
                                'User-Agent' => 'FeuerwehrenTilesPrefetch/1.0 (+TYPO3)',
                            ],
                            'http_errors' => false,
                            'timeout' => 20,
                            'verify' => false,
                        ]);
                        if ($res->getStatusCode() === 200) {
                            file_put_contents($dest, (string)$res->getBody());
                        } else {
                            // leere Datei nicht schreiben, um „defekte“ Tiles zu vermeiden
                        }
                    } catch (\Throwable $e) {
                        // still: skip
                    }
                    $bar->advance();
                }
            }
            $bar->finish();
            $io->newLine(2);
        }

        $io->success(sprintf('Fertig. %d Tiles in %s gespeichert.', $total, $destRoot));
        $io->writeln('Hinweis: Stelle sicher, dass die Middleware auf storage=var und fileBase=tiles zeigt.');
        return Command::SUCCESS;
    }

    /** @return array{0:float,1:float,2:float,3:float} */
    private function parseBbox(string $bbox): array
    {
        $p = array_map('trim', explode(',', $bbox));
        if (count($p) !== 4) {
            throw new \InvalidArgumentException('bbox erwartet "minLon,minLat,maxLon,maxLat"');
        }
        return [ (float)$p[0], (float)$p[1], (float)$p[2], (float)$p[3] ];
    }

    /** @return int[] */
    private function parseZooms(string $zooms): array
    {
        $zooms = trim($zooms);
        if ($zooms === '') { return []; }
        if (preg_match('~^(\d+)\s*-\s*(\d+)$~', $zooms, $m)) {
            $a = (int)$m[1]; $b = (int)$m[2];
            if ($a > $b) { [$a, $b] = [$b, $a]; }
            return range($a, $b);
        }
        $list = [];
        foreach (explode(',', $zooms) as $z) {
            $z = trim($z);
            if ($z !== '' && ctype_digit($z)) {
                $list[] = (int)$z;
            }
        }
        sort($list);
        return array_values(array_unique($list));
    }

    /** @return array{0:int,1:int,2:int,3:int} */
    private function tileBounds(float $w, float $s, float $e, float $n, int $z): array
    {
        $tile = static function (float $lon, float $lat, int $z): array {
            $lat = max(min($lat, 85.05112878), -85.05112878); // WebMercator clamp
            $x = (int)floor(($lon + 180.0) / 360.0 * (1 << $z));
            $sin = sin(deg2rad($lat));
            $y = (int)floor((1.0 - log((1.0 + $sin) / (1.0 - $sin)) / M_PI) / 2.0 * (1 << $z));
            return [$x, $y];
        };

        [$x1, $y1] = $tile($w, $n, $z);
        [$x2, $y2] = $tile($e, $s, $z);

        $minX = min($x1, $x2);
        $maxX = max($x1, $x2);
        $minY = min($y1, $y2);
        $maxY = max($y1, $y2);

        // Wrap um Antimeridian ignorieren (nicht notwendig für Lkr. Freising)
        return [$minX, $maxX, $minY, $maxY];
    }
}
