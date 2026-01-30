<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SQLite3;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * Controller for serving vector tiles from MBTiles database.
 *
 * Serves Protocol Buffer Format (PBF) tiles for MapLibre GL JS.
 * Tiles are read from a SQLite MBTiles database and served with
 * appropriate caching headers.
 */
final class VectorTileController
{
    private const DEFAULT_MBTILES_PATH = 'fileadmin/tiles/osm.mbtiles';
    private const CACHE_MAX_AGE = 31536000; // 1 year
    private const GZIP_MAGIC_BYTE_1 = 0x1f;
    private const GZIP_MAGIC_BYTE_2 = 0x8b;

    /**
     * Serves a single vector tile.
     *
     * @param ServerRequestInterface $request The current request
     * @param int $z Zoom level
     * @param int $x Tile column
     * @param int $y Tile row (XYZ scheme, will be converted to TMS)
     */
    public function pbfAction(ServerRequestInterface $request, int $z, int $x, int $y): ResponseInterface
    {
        $this->disableOutputBuffering();

        $mbtilesPath = $this->resolveMbtilesPath($request);
        $response = new Response();

        if (!is_file($mbtilesPath)) {
            return $response->withStatus(404);
        }

        $tileData = $this->readTileFromMbtiles($mbtilesPath, $z, $x, $y);

        if ($tileData === null) {
            return $response->withStatus(204);
        }

        $response->getBody()->write($tileData);
        $response = $response
            ->withHeader('Content-Type', 'application/x-protobuf')
            ->withHeader('Cache-Control', sprintf('public, max-age=%d, immutable', self::CACHE_MAX_AGE));

        if ($this->isGzipCompressed($tileData)) {
            $response = $response->withHeader('Content-Encoding', 'gzip');
        }

        return $response;
    }

    /**
     * Resolves the MBTiles file path from site configuration.
     */
    private function resolveMbtilesPath(ServerRequestInterface $request): string
    {
        $relativePath = self::DEFAULT_MBTILES_PATH;

        /** @var Site|null $site */
        $site = $request->getAttribute('site');

        if ($site instanceof Site) {
            $settings = $site->getConfiguration()['settings'] ?? [];
            $relativePath = $settings['feuerwehren']['vectorTiles']['mbtilesPath']
                ?? $settings['feuerwehren.vectorTiles.mbtilesPath']
                ?? self::DEFAULT_MBTILES_PATH;
        }

        // Handle both absolute and relative paths
        if (str_starts_with($relativePath, '/')) {
            return $relativePath;
        }

        return Environment::getPublicPath() . '/' . $relativePath;
    }

    /**
     * Reads a tile from the MBTiles SQLite database.
     *
     * MBTiles uses TMS (Tile Map Service) coordinate scheme where Y is inverted
     * compared to the XYZ/Slippy Map scheme used by web maps.
     *
     * @param string $path Path to MBTiles file
     * @param int $z Zoom level
     * @param int $x Tile column
     * @param int $y Tile row (XYZ scheme)
     * @return string|null Raw tile data or null if not found
     */
    private function readTileFromMbtiles(string $path, int $z, int $x, int $y): ?string
    {
        $db = new SQLite3($path, SQLITE3_OPEN_READONLY);

        // Convert XYZ to TMS: Y is inverted
        $tmsY = (1 << $z) - 1 - $y;

        $stmt = $db->prepare(
            'SELECT tile_data FROM tiles WHERE zoom_level = :z AND tile_column = :x AND tile_row = :y LIMIT 1'
        );
        $stmt->bindValue(':z', $z, SQLITE3_INTEGER);
        $stmt->bindValue(':x', $x, SQLITE3_INTEGER);
        $stmt->bindValue(':y', $tmsY, SQLITE3_INTEGER);

        $result = $stmt->execute();
        $row = $result?->fetchArray(SQLITE3_ASSOC);

        $db->close();

        return $row['tile_data'] ?? null;
    }

    /**
     * Checks if the tile data is gzip compressed by inspecting magic bytes.
     */
    private function isGzipCompressed(string $data): bool
    {
        return strlen($data) >= 2
            && ord($data[0]) === self::GZIP_MAGIC_BYTE_1
            && ord($data[1]) === self::GZIP_MAGIC_BYTE_2;
    }

    /**
     * Disables output buffering to ensure raw tile data is sent.
     */
    private function disableOutputBuffering(): void
    {
        @ini_set('zlib.output_compression', '0');

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }
    }
}
