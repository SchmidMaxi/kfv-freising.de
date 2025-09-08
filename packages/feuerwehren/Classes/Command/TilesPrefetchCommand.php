<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Schmid\Feuerwehren\Domain\Repository\FeuerwehrRepository;

#[AsCommand(name: 'feuerwehren:tiles:prefetch', description: 'Prefetch raster tiles into a local directory')]
final class TilesPrefetchCommand extends Command
{
    public function __construct(
        private readonly ?FeuerwehrRepository $feuerwehrRepository = null,
        private readonly ?LoggerInterface $logger = null
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('minZoom', null, InputOption::VALUE_REQUIRED, 'Min zoom', '8')
            ->addOption('maxZoom', null, InputOption::VALUE_REQUIRED, 'Max zoom', '12')
            ->addOption('bbox', null, InputOption::VALUE_REQUIRED, 'Bounding box lonW,latS,lonE,latN (e.g. 10.0,47.0,13.9,49.8)')
            ->addOption('fromDb', null, InputOption::VALUE_NONE, 'Derive bbox from all Feuerwehr lat/lon')
            ->addOption('tilesDir', null, InputOption::VALUE_REQUIRED, 'Target directory (webroot-relative)', '')
            ->addOption('tilesUrl', null, InputOption::VALUE_REQUIRED, 'Base URL (not used for fetch)', '')
            ->addOption('remoteUrl', null, InputOption::VALUE_REQUIRED, 'Remote template URL', '')
            ->addOption('dryRun', null, InputOption::VALUE_NONE, 'Only print, do not download');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Load settings via TypoScript @ runtime (CLI has no TSFE; use sane defaults + CLI options)
        $tilesDir = $input->getOption('tilesDir') ?: 'fileadmin/tiles';
        $remote   = $input->getOption('remoteUrl') ?: 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png';
        $minZ     = (int)$input->getOption('minZoom');
        $maxZ     = (int)$input->getOption('maxZoom');
        $dry      = (bool)$input->getOption('dryRun');

        // Resolve bbox
        $bboxOpt = $input->getOption('bbox');
        if ($input->getOption('fromDb')) {
            $bbox = $this->bboxFromDb();
            if (!$bbox) {
                $output->writeln('<error>No coordinates in DB; please pass --bbox.</error>');
                return Command::FAILURE;
            }
        } elseif ($bboxOpt) {
            $bbox = $this->parseBbox((string)$bboxOpt);
            if (!$bbox) {
                $output->writeln('<error>Invalid --bbox format. Use lonW,latS,lonE,latN</error>');
                return Command::FAILURE;
            }
        } else {
            $output->writeln('<error>Provide --bbox or use --fromDb</error>');
            return Command::FAILURE;
        }

        // Build absolute target dir
        $publicPath = Environment::getPublicPath();
        $targetBase = rtrim($publicPath . '/' . ltrim($tilesDir, '/'), '/');
        if (!is_dir($targetBase) && !$dry) {
            GeneralUtility::mkdir_deep($targetBase);
        }

        $output->writeln(sprintf(
            '<info>Prefetch tiles %s → %s (z=%d..%d)</info>',
            $remote, $targetBase, $minZ, $maxZ
        ));
        $output->writeln(sprintf('<info>BBOX: W=%f S=%f E=%f N=%f</info>', $bbox['w'], $bbox['s'], $bbox['e'], $bbox['n']));

        $total = 0;
        for ($z = $minZ; $z <= $maxZ; $z++) {
            [$xMin,$xMax,$yMin,$yMax] = $this->tileRangeForBbox($bbox, $z);
            for ($x = $xMin; $x <= $xMax; $x++) {
                for ($y = $yMin; $y <= $yMax; $y++) {
                    $url  = strtr($remote, ['{z}' => (string)$z, '{x}' => (string)$x, '{y}' => (string)$y]);
                    $dir  = $targetBase . '/' . $z . '/' . $x;
                    $file = $dir . '/' . $y . '.png';
                    $total++;

                    if ($dry) {
                        $output->writeln("DRY: $url -> $file");
                        continue;
                    }

                    if (!is_dir($dir)) {
                        GeneralUtility::mkdir_deep($dir);
                    }
                    if (is_file($file)) {
                        continue; // already cached
                    }

                    $data = @file_get_contents($url);
                    if ($data === false) {
                        $this->logger?->warning('Tile fetch failed', ['url' => $url]);
                        continue;
                    }
                    file_put_contents($file, $data);
                }
            }
            $output->writeln(sprintf('z=%d done (%d x %d tiles)', $z, ($xMax-$xMin+1), ($yMax-$yMin+1)));
        }

        $output->writeln("<info>Done. Total tiles touched: $total</info>");
        return Command::SUCCESS;
    }

    private function parseBbox(string $s): ?array
    {
        $p = array_map('trim', explode(',', $s));
        if (count($p) !== 4) { return null; }
        return ['w' => (float)$p[0], 's' => (float)$p[1], 'e' => (float)$p[2], 'n' => (float)$p[3]];
    }

    /** @return array{w:float,s:float,e:float,n:float}|null */
// NEU: robust gegen VARCHAR, Leerstring, Komma-Dezimal usw.
    private function bboxFromDb(): ?array
    {
        if (!$this->feuerwehrRepository) {
            return null;
        }

        $minLat =  90.0;  $maxLat = -90.0;
        $minLon = 180.0;  $maxLon = -180.0;

        foreach ($this->feuerwehrRepository->findAll() as $fw) {
            $lat = $this->parseCoord($fw->getLatitude(),  -90.0,  90.0);
            $lon = $this->parseCoord($fw->getLongitude(), -180.0, 180.0);
            if ($lat === null || $lon === null) {
                continue; // ungültige/fehlende Koordinate
            }
            $minLat = min($minLat, $lat);
            $maxLat = max($maxLat, $lat);
            $minLon = min($minLon, $lon);
            $maxLon = max($maxLon, $lon);
        }

        if ($minLat > $maxLat || $minLon > $maxLon) {
            return null; // keine gültigen Koordinaten gefunden
        }

        // kleiner Puffer um die Punkte
        $pad = 0.02;
        return [
            'w' => $minLon - $pad,
            's' => $minLat - $pad,
            'e' => $maxLon + $pad,
            'n' => $maxLat + $pad,
        ];
    }

    /**
     * Konvertiert eine VARCHAR-Koordinate zu float.
     * - trimmt
     * - wandelt Komma in Punkt
     * - validiert Format und Wertebereich
     * - gibt null bei Ungültigkeit zurück
     */
    private function parseCoord(?string $value, float $min, float $max): ?float
    {
        if ($value === null) {
            return null;
        }
        $s = trim($value);
        if ($s === '') {
            return null;
        }
        $s = str_replace(',', '.', $s);

        // nur - . und Ziffern erlauben (einfach & schnell)
        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $s)) {
            return null;
        }

        $f = (float)$s;
        if ($f < $min || $f > $max) {
            return null;
        }
        return $f;
    }


    /** @return array{0:int,1:int,2:int,3:int} */
    private function tileRangeForBbox(array $bbox, int $z): array
    {
        $xMin = $this->lon2tile($bbox['w'], $z);
        $xMax = $this->lon2tile($bbox['e'], $z);
        $yMin = $this->lat2tile($bbox['n'], $z); // north is smaller y in XYZ
        $yMax = $this->lat2tile($bbox['s'], $z);
        return [max(0,$xMin), max(0,$xMax), max(0,$yMin), max(0,$yMax)];
    }
    private function lon2tile(float $lon, int $z): int
    {
        return (int)floor(($lon + 180.0) / 360.0 * (1 << $z));
    }
    private function lat2tile(float $lat, int $z): int
    {
        $latRad = deg2rad($lat);
        return (int)floor((1.0 - log(tan($latRad) + 1.0 / cos($latRad)) / M_PI) / 2.0 * (1 << $z));
    }
}
