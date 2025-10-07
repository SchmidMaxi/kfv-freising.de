<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\Response;

final class VectorTileController
{
    public function pbfAction(int $z, int $x, int $y): ResponseInterface
    {
        // Keine (zlib-)Kompression / kein HTML-Overhead
        @ini_set('zlib.output_compression', '0');
        while (function_exists('ob_get_level') && ob_get_level() > 0) { @ob_end_clean(); }

        $settings = $this->getSettings();
        $mbtiles = $settings['vectorTiles']['mbtilesPath'] ?? $settings['vectorTiles.mbtilesPath'] ?? '';

        $res = new Response();
        if (!is_file($mbtiles)) {
            return $res->withStatus(404);
        }

        $tile = $this->readMbtilesTile($mbtiles, $z, $x, $y); // raw PBF, oft gzip-komprimiert gespeichert
        if ($tile === null) {
            return $res->withStatus(204); // no content
        }

        $res->getBody()->write($tile);
        $res = $res
            ->withHeader('Content-Type', 'application/x-protobuf')
            ->withHeader('Cache-Control', 'public, max-age=31536000, immutable');

        // Wenn in der MBTiles-Datei gzip-komprimiert abgelegt (üblich), setze den Header korrekt:
        if (strlen($tile) >= 2 && ord($tile[0]) === 0x1f && ord($tile[1]) === 0x8b) {
            $res = $res->withHeader('Content-Encoding', 'gzip');
        } else {
            $res = $res->withoutHeader('Content-Encoding');
        }
        return $res;
    }

    private function readMbtilesTile(string $path, int $z, int $x, int $y): ?string
    {
        $db = new \SQLite3($path, \SQLITE3_OPEN_READONLY);
        // MBTiles nutzt TMS → Y invertieren
        $tmsY = (1 << $z) - 1 - $y;
        $stmt = $db->prepare('SELECT tile_data FROM tiles WHERE zoom_level = :z AND tile_column = :x AND tile_row = :y LIMIT 1');
        $stmt->bindValue(':z', $z, \SQLITE3_INTEGER);
        $stmt->bindValue(':x', $x, \SQLITE3_INTEGER);
        $stmt->bindValue(':y', $tmsY, \SQLITE3_INTEGER);
        $row = $stmt->execute()?->fetchArray(\SQLITE3_ASSOC);
        $db->close();
        return $row['tile_data'] ?? null;
    }

    private function getSettings(): array
    {
        $ts = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuerwehren.']['settings.'] ?? [];
        $out = [];
        foreach ($ts as $k=>$v) {
            $key = rtrim($k, '.'); $out[$key] = is_array($v) ? $v : (string)$v;
        }
        return $out;
    }
}
