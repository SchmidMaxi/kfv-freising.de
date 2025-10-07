<?php
// EXT:feuerwehren/Classes/Service/Api/OverlaysService.php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Service\Api;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class OverlaysService
{
    private const TABLE_AREA = 'tx_feuerwehren_domain_model_area'; // anpassen falls anders

    /** @return array{overlays:array<int,array<string,mixed>>} */
    public function overlays(): array
    {
        // Beispiel: wir lesen Polygone/GeoJSON, falls vorhanden
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE_AREA);
        $qb->select('uid','title','geojson') // Spaltennamen ggf. anpassen
        ->from(self::TABLE_AREA)
            ->where($qb->expr()->eq('deleted', 0), $qb->expr()->eq('hidden', 0))
            ->orderBy('title', 'ASC');

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $ol = [];
        foreach ($rows as $r) {
            $ol[] = [
                'type' => 'geojson',
                'id'   => (int)$r['uid'],
                'name' => (string)$r['title'],
                'data' => json_decode((string)($r['geojson'] ?? 'null'), true) ?? null,
            ];
        }

        return ['overlays' => $ol];
    }
}
