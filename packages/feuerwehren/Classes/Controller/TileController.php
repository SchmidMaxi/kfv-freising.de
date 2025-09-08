<?php
declare(strict_types=1);


namespace Schmid\Feuerwehren\Controller;


use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\Response;

class TileController
{
    public function __construct(private readonly ?\SQLite3 $sqlite = null) {}


    public function tileAction(int $z, int $x, int $y, string $format = 'png'): ResponseInterface
    {
        $settings = $this->getSettings();
        $response = new Response();


        // Serve from MBTiles if configured
        if (($settings['tileSource'] ?? 'mbtiles') === 'mbtiles' && !empty($settings['mbtilesPath'])) {
            $tile = $this->readMbtilesTile($settings['mbtilesPath'], $z, $x, $y);
            if ($tile !== null) {
                $response->getBody()->write($tile);
                return $response->withHeader('Content-Type', $this->mimeFromFormat($format));
            }
        }
        // Fallback: proxy + cache
        $cacheDir = Environment::getVarPath() . '/tiles/' . $z . '/' . $x;
        GeneralUtility::mkdir_deep($cacheDir);
        $file = $cacheDir . '/' . $y . '.' . $format;
        if (!is_file($file)) {
            $remote = str_replace(['{z}','{x}','{y}'], [$z,$x,$y], $settings['proxyUrl'] ?? 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png');
            $data = @file_get_contents($remote);
            if ($data === false) {
                return $response->withStatus(404);
            }
            file_put_contents($file, $data);
        }
        $response->getBody()->write((string)file_get_contents($file));
        return $response->withHeader('Content-Type', $this->mimeFromFormat($format));
    }


    private function readMbtilesTile(string $path, int $z, int $x, int $y): ?string
    {
        if (!is_file($path)) { return null; }
        $db = new \SQLite3($path, \SQLITE3_OPEN_READONLY);
        // MBTiles uses TMS scheme; convert XYZ->TMS
        $tmsY = (int)((1 << $z) - 1 - $y);
        $stmt = $db->prepare('SELECT tile_data FROM tiles WHERE zoom_level = :z AND tile_column = :x AND tile_row = :y LIMIT 1');
        $stmt->bindValue(':z', $z, \SQLITE3_INTEGER);
        $stmt->bindValue(':x', $x, \SQLITE3_INTEGER);
        $stmt->bindValue(':y', $tmsY, \SQLITE3_INTEGER);
        $res = $stmt->execute();
        $row = $res?->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $row['tile_data'] ?? null;
    }


    private function mimeFromFormat(string $format): string
    {
        return match (strtolower($format)) { 'jpg','jpeg' => 'image/jpeg', 'webp' => 'image/webp', default => 'image/png' };
    }


    private function getSettings(): array
    {
        // Read TypoScript settings (plugin.tx_feuerwehren.settings)
        $ts = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuerwehren.']['settings.'] ?? [];
        // Flatten dot-notation
        $out = [];
        foreach ($ts as $k=>$v) { $out[rtrim($k,'.')] = is_array($v)?$v: (string)$v; }
        return $out;
    }
}