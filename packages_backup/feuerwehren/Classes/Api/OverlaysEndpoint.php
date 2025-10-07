<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Api;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class OverlaysEndpoint
{
    /**
     * USER_INT entrypoint
     * @param string $content
     * @param array<string,mixed> $conf
     */
    public function render(string $content = '', array $conf = []): string
    {
        $conn = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_feuerwehren_domain_model_gemeinde');

        $qb = $conn->createQueryBuilder();
        $rows = $qb->select('uid','name','gemeindegebiet')
            ->from('tx_feuerwehren_domain_model_gemeinde')
            ->where(
                $qb->expr()->eq('deleted', 0),
                $qb->expr()->eq('hidden', 0)
            )
            ->executeQuery()->fetchAllAssociative();

        $gemeinden = [];
        foreach ($rows as $r) {
            $raw = (string)$r['gemeindegebiet'];
            $geo = null;
            if ($raw !== '') {
                $tmp = json_decode($raw, true);
                if (is_array($tmp)) { $geo = $tmp; }
            }
            if ($geo) {
                $gemeinden[] = [
                    'uid' => (int)$r['uid'],
                    'name' => (string)$r['name'],
                    'geojson' => $geo,
                ];
            }
        }

        // Optional: KBM/KBI-Zuordnung später ergänzen – vorerst leer
        return json_encode(['gemeinden' => $gemeinden, 'kbm' => new \stdClass(), 'kbi' => new \stdClass()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
