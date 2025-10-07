<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\JsonResponse;

final class HealthController
{
    public function healthAction(): ResponseInterface
    {
        $public = Environment::getPublicPath();
        $defaultMbtiles = $public . '/fileadmin/tiles/osm.mbtiles';

        $sqliteLoaded = extension_loaded('sqlite3');
        $mbtilesPath = $defaultMbtiles; // Default; ggf. aus TypoScript lesen, wenn TSFE verfügbar

        $exists = is_file($mbtilesPath);
        $minzoom = $maxzoom = null;
        $tileCount = null;

        if ($sqliteLoaded && $exists) {
            try {
                $db = new \SQLite3($mbtilesPath, \SQLITE3_OPEN_READONLY);
                // metadata
                $res = $db->query('SELECT name, value FROM metadata');
                while ($row = $res->fetchArray(\SQLITE3_ASSOC)) {
                    if ($row['name'] === 'minzoom') { $minzoom = (int)$row['value']; }
                    if ($row['name'] === 'maxzoom') { $maxzoom = (int)$row['value']; }
                }
                // einfache Kachel-Anzahl (kann groß sein; LIMIT für Schnelligkeit)
                $res2 = $db->query('SELECT COUNT(1) AS c FROM tiles');
                $row2 = $res2->fetchArray(\SQLITE3_ASSOC);
                $tileCount = (int)($row2['c'] ?? 0);
                $db->close();
            } catch (\Throwable $e) {
                // ignorieren, wird unten gemeldet
            }
        }

        return new JsonResponse([
            'sqlite3_loaded' => $sqliteLoaded,
            'mbtiles_path'   => $mbtilesPath,
            'mbtiles_exists' => $exists,
            'minzoom'        => $minzoom,
            'maxzoom'        => $maxzoom,
            'tiles_count'    => $tileCount,
        ]);
    }
}
