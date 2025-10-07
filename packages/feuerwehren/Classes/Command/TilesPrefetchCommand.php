<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsCommand(name: 'feuerwehren:tiles:prefetch', description: 'Prefetch vector tiles into public/_vt (XYZ scheme)')]
final class TilesPrefetchCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('bbox', null, InputOption::VALUE_REQUIRED, 'west,south,east,north (lon,lat,lon,lat) in WGS84')
            ->addOption('zooms', null, InputOption::VALUE_REQUIRED, 'zoom or range like 8-14')
            ->addOption('preset', null, InputOption::VALUE_OPTIONAL, 'bbox preset: freising | oberbayern')
            ->addOption('source', null, InputOption::VALUE_OPTIONAL, 'remote source template with {z}/{x}/{y}, e.g. https://api.maptiler.com/tiles/v3/{z}/{x}/{y}.pbf?key=YOUR_KEY', '')
            ->addOption('concurrency', null, InputOption::VALUE_OPTIONAL, 'parallel downloads', '6')
            ->addOption('timeout', null, InputOption::VALUE_OPTIONAL, 'HTTP timeout (s)', '20');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // --- 1) BBOX bestimmen (preset > bbox) ---
        $bbox = $this->resolveBbox(
            (string)$input->getOption('bbox'),
            (string)$input->getOption('preset')
        );
        if ($bbox === null) {
            $output->writeln('<error>Provide --bbox="lonW,latS,lonE,latN" or --preset=freising|oberbayern</error>');
            return Command::INVALID;
        }
        [$w,$s,$e,$n] = $bbox;

        // --- 2) Zooms parsen ---
        $zooms = $this->parseZooms((string)$input->getOption('zooms'));
        if (empty($zooms)) {
            $output->writeln('<error>Provide --zooms, e.g. 9-14</error>');
            return Command::INVALID;
        }

        $source = (string)$input->getOption('source');
        if ($source === '') {
            // Sinnvolle Default-Quelle (MapTiler v3). Erfordert API-Key! (oder eigene Quelle angeben)
            $output->writeln('<comment>No --source given; example (needs key): --source="https://api.maptiler.com/tiles/v3/{z}/{x}/{y}.pbf?key=YOUR_KEY"</comment>');
            return Command::INVALID;
        }

        $baseDir = GeneralUtility::getFileAbsFileName('_vt/');
        if (!is_dir($baseDir) && !@mkdir($baseDir, 0775, true)) {
            $output->writeln('<error>Cannot create directory: '.$baseDir.'</error>');
            return Command::FAILURE;
        }

        $client = new Client([
            'timeout' => (float)$input->getOption('timeout'),
            'headers' => [
                // korrekter MIME-Type für Vektor-Tiles
                'Accept' => 'application/vnd.mapbox-vector-tile,application/x-protobuf;q=0.9,*/*;q=0.8',
            ],
        ]);

        // --- 3) Alle XYZ-Kacheln innerhalb der BBOX für die Zooms berechnen ---
        $jobs = [];
        foreach ($zooms as $z) {
            [$minX, $minY, $maxX, $maxY] = $this->tileBoundsXYZ($w, $s, $e, $n, $z);
            for ($x = $minX; $x <= $maxX; $x++) {
                for ($y = $minY; $y <= $maxY; $y++) {
                    $local = $baseDir . $z . '/' . $x . '/' . $y . '.pbf';
                    if (is_file($local)) { continue; }
                    $url = strtr($source, [
                        '{z}' => (string)$z,
                        '{x}' => (string)$x,
                        '{y}' => (string)$y,
                    ]);
                    $jobs[] = [$url, $local];
                }
            }
            $output->writeln(sprintf(
                '<info>z=%d</info> tiles: x[%d..%d], y[%d..%d]  (center approx %d/%d/%d)',
                $z, $minX, $maxX, $minY, $maxY, $z,
                (int)floor(($minX+$maxX)/2),
                (int)floor(($minY+$maxY)/2)
            ));
        }

        // --- 4) Laden (einfacher, serieller Downloader mit kleinem Parallelismus) ---
        $concurrency = max(1, (int)$input->getOption('concurrency'));
        $total = count($jobs);
        $done = 0;
        $output->writeln('<comment>Jobs: '.$total.' (concurrency '.$concurrency.')</comment>');

        $queue = $jobs;
        $workers = [];
        while ($done < $total) {
            while (count($workers) < $concurrency && !empty($queue)) {
                [$url, $local] = array_shift($queue);
                $workers[] = $this->spawnDownload($client, $url, $local);
            }
            // auf abgeschlossene warten
            foreach ($workers as $i => $state) {
                if ($state['done']()) {
                    $done++;
                    unset($workers[$i]);
                }
            }
            usleep(10000); // 10ms
        }

        $output->writeln('<info>Finished.</info>');
        $output->writeln('<info>Directory:</info> ' . $baseDir);
        $output->writeln('<info>Example tile:</info> /_vt/10/545/353.pbf');
        return Command::SUCCESS;
    }

    /**
     * Richtige XYZ-Formel (WebMercator): y = floor((1 - ln(tan(lat)+sec(lat))/π)/2 * 2^z)
     * Erwartet (lon, lat) in Grad.
     */
    private function lonLatToTileXYZ(float $lon, float $lat, int $z): array
    {
        $lat = max(min($lat, 85.05112878), -85.05112878);
        $n = 2 ** $z;
        $x = (int)floor(($lon + 180.0) / 360.0 * $n);

        $latRad = deg2rad($lat);
        $yFloat = (1.0 - log(tan($latRad) + 1.0 / cos($latRad)) / M_PI) / 2.0 * $n;
        $y = (int)floor($yFloat);

        return [$x, $y];
    }

    /**
     * Berechnet die min/max-Tile-Indizes für BBOX (west,south,east,north) im **XYZ**-Schema.
     */
    private function tileBoundsXYZ(float $w, float $s, float $e, float $n, int $z): array
    {
        // clamp longitudes
        $w = max(-180.0, min(180.0, $w));
        $e = max(-180.0, min(180.0, $e));
        // clamp latitudes
        $s = max(-85.05112878, min(85.05112878, $s));
        $n = max(-85.05112878, min(85.05112878, $n));

        // West/North und East/South in XYZ umrechnen
        [$x1, $y1] = $this->lonLatToTileXYZ($w, $n, $z); // NW
        [$x2, $y2] = $this->lonLatToTileXYZ($e, $s, $z); // SE

        $minX = min($x1, $x2);
        $maxX = max($x1, $x2);
        $minY = min($y1, $y2);
        $maxY = max($y1, $y2);

        return [$minX, $minY, $maxX, $maxY];
    }

    private function parseZooms(string $zooms): array
    {
        $zooms = trim($zooms);
        if ($zooms === '') { return []; }
        if (str_contains($zooms, '-')) {
            [$a,$b] = array_map('intval', explode('-', $zooms, 2));
            if ($a > $b) { [$a,$b] = [$b,$a]; }
            return range($a, $b);
        }
        return [ (int)$zooms ];
    }

    /**
     * --preset in BBOX auflösen; ansonsten --bbox parsen.
     */
    private function resolveBbox(string $bboxOpt, ?string $preset): ?array
    {
        $presets = [
            // grob genug für die Region:
            'freising'   => [11.40, 48.25, 12.00, 48.60],
            'oberbayern' => [10.50, 47.25, 12.80, 48.90],
        ];
        if ($preset && isset($presets[$preset])) {
            return $presets[$preset];
        }
        $bboxOpt = trim($bboxOpt);
        if ($bboxOpt !== '') {
            $p = array_map('trim', explode(',', $bboxOpt));
            if (count($p) === 4) {
                return [ (float)$p[0], (float)$p[1], (float)$p[2], (float)$p[3] ];
            }
        }
        return null;
    }

    /**
     * Mini "Future": Startet einen Download (synchron im Closure, aber wir poll’en).
     */
    private function spawnDownload(Client $client, string $url, string $local): array
    {
        $tmp = $local . '.part';
        $dir = dirname($local);
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $done = false;

        // Sofort ausführen; der "Worker" ist nur ein Status-Closure.
        try {
            $resp = $client->get($url, ['http_errors' => false]);
            if ($resp->getStatusCode() === 200) {
                file_put_contents($tmp, $resp->getBody()->getContents());
                @rename($tmp, $local);
            } else {
                // 204 / 404 / 5xx etc. => keine Datei anlegen
                if (is_file($tmp)) { @unlink($tmp); }
            }
        } catch (\Throwable $e) {
            if (is_file($tmp)) { @unlink($tmp); }
        } finally {
            $done = true;
        }

        return [
            'done' => static function() use (&$done): bool { return $done; }
        ];
    }
}
