<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Api;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\Environment;

final class TileEndpoint
{
    /**
     * USER_INT entrypoint for TypoScript PAGE (type=171003)
     * @param string $content
     * @param array<string,mixed> $conf
     */
    public function render(string $content = '', array $conf = []): string
    {
        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $q = $request->getQueryParams();

        $z = isset($q['z']) ? (int)$q['z'] : null;
        $x = isset($q['x']) ? (int)$q['x'] : null;
        $y = isset($q['y']) ? (int)$q['y'] : null;
        $format = isset($q['format']) ? (string)$q['format'] : 'png';
        if ($z === null || $x === null || $y === null) {
            header('HTTP/1.1 400 Bad Request');
            return '';
        }

        // TypoScript settings (optional)
        $ts = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuerwehren.']['settings.'] ?? [];
        $settings = [];
        foreach ($ts as $k => $v) { $settings[rtrim((string)$k, '.')] = is_array($v) ? $v : (string)$v; }
        $src = $settings['tileSource'] ?? 'mbtiles';
        $mbtilesPath = $settings['mbtilesPath'] ?? 'fileadmin/tiles/osm.mbtiles';
        if ($mbtilesPath !== '' && $mbtilesPath[0] !== '/') {
            $mbtilesPath = Environment::getPublicPath() . '/' . ltrim($mbtilesPath, '/');
        }
        $proxyUrl = $settings['proxyUrl'] ?? 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png';

        // 1) MBTiles
        if ($src === 'mbtiles' && is_file($mbtilesPath)) {
            $blob = self::readMbtiles($mbtilesPath, $z, $x, $y);
            if ($blob !== null) {
                header('Content-Type: ' . self::mime($format));
                return $blob;
            }
        }

        // 2) Proxy + Cache ins var/
        $dir = Environment::getVarPath() . '/tiles/' . $z . '/' . $x;
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        $file = $dir . '/' . $y . '.' . $format;
        if (!is_file($file)) {
            $url = str_replace(['{z}','{x}','{y}'], [$z,$x,$y], $proxyUrl);
            $data = @file_get_contents($url);
            if ($data === false) {
                header('HTTP/1.1 404 Not Found');
                return '';
            }
            file_put_contents($file, $data);
        }
        header('Content-Type: ' . self::mime($format));
        return (string)file_get_contents($file);
    }

    private static function readMbtiles(string $path, int $z, int $x, int $y): ?string
    {
        if (!class_exists(\SQLite3::class)) { return null; } // PHP SQLite fehlt
        $db = new \SQLite3($path, \SQLITE3_OPEN_READONLY);
        // XYZ -> TMS
        $tmsY = (int)((1 << $z) - 1 - $y);
        $stmt = $db->prepare('SELECT tile_data FROM tiles WHERE zoom_level=:z AND tile_column=:x AND tile_row=:y LIMIT 1');
        $stmt->bindValue(':z', $z, \SQLITE3_INTEGER);
        $stmt->bindValue(':x', $x, \SQLITE3_INTEGER);
        $stmt->bindValue(':y', $tmsY, \SQLITE3_INTEGER);
        $res = $stmt->execute();
        $row = $res?->fetchArray(\SQLITE3_ASSOC);
        $db->close();
        return $row['tile_data'] ?? null;
    }

    private static function mime(string $format): string
    {
        return match (strtolower($format)) {
            'jpg','jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }
}
