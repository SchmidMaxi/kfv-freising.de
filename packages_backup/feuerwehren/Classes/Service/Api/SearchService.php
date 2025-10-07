<?php
// EXT:feuerwehren/Classes/Service/Api/SearchService.php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Service\Api;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class SearchService
{
    private const TABLE_FW = 'tx_feuerwehren_domain_model_feuerwehr'; // anpassen falls anders

    /**
     * @param array{bbox?:string,mode?:string,f?:array<int,string>} $query
     * @return array{items:array<int,array<string,mixed>>,meta:array<string,mixed>}
     */
    public function search(array $query): array
    {
        [$west,$south,$east,$north] = array_pad(explode(',', (string)($query['bbox'] ?? '')), 4, null);
        $mode = strtolower((string)($query['mode'] ?? 'or'));
        $filters = $query['f'] ?? []; // Fahrzeugkategorien etc.

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE_FW);
        $qb->select('uid','title','latitude','longitude') // Felder ergänzen
        ->from(self::TABLE_FW)
            ->where($qb->expr()->eq('deleted', 0), $qb->expr()->eq('hidden', 0))
            ->orderBy('title', 'ASC')
            ->setMaxResults(1000);

        $rows = $qb->executeQuery()->fetchAllAssociative();

        // BBox & Filter im PHP (oder via SQL, wenn Spalten vorhanden sind)
        $items = [];
        foreach ($rows as $r) {
            $lat = (float)($r['latitude'] ?? 0);
            $lon = (float)($r['longitude'] ?? 0);
            if ($west !== null) {
                if ($lon < (float)$west || $lon > (float)$east || $lat < (float)$south || $lat > (float)$north) {
                    continue;
                }
            }
            // TODO: Filter f[] gegen Kategorien prüfen (wenn Relation/Join bekannt)
            $items[] = [
                'uid'  => (int)$r['uid'],
                'title'=> (string)$r['title'],
                'lat'  => $lat,
                'lon'  => $lon,
            ];
        }

        return [
            'items' => $items,
            'meta'  => [
                'count' => count($items),
                'bbox'  => [$west,$south,$east,$north],
                'mode'  => $mode,
                'filters' => $filters,
            ],
        ];
    }
}
