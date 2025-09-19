<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Api;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class SearchEndpoint
{
    /**
     * USER_INT entrypoint
     * @param string $content
     * @param array<string,mixed> $conf
     */
    public function render(string $content = '', array $conf = []): string
    {
        // Request aus globalem PSR-7 ziehen (TYPO3 12/13)
        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $qp = $request->getQueryParams();

        $bboxParam = (string)($qp['bbox'] ?? '');
        $mode = (string)($qp['mode'] ?? 'and');
        $selected = $qp['f'] ?? []; // f[]=HLF&f[]=DLK
        if (!is_array($selected)) { $selected = [$selected]; }

        // TS-Settings lesen (Defaults für LKR Freising, wenn keine bbox übergeben wurde)
        $ts = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuerwehren.']['settings.'] ?? [];
        $settings = [];
        foreach ($ts as $k => $v) { $settings[rtrim((string)$k, '.')] = is_array($v) ? $v : (string)$v; }

        $bbox = null;
        if ($bboxParam !== '') {
            $parts = array_map('trim', explode(',', $bboxParam));
            if (count($parts) === 4) {
                $bbox = [
                    'w' => (float)str_replace(',', '.', $parts[0]),
                    's' => (float)str_replace(',', '.', $parts[1]),
                    'e' => (float)str_replace(',', '.', $parts[2]),
                    'n' => (float)str_replace(',', '.', $parts[3]),
                ];
            }
        } else {
            // Defaults nur für Test-URL 171001
            $bbox = [
                'w' => (float)($settings['bboxW'] ?? 11.30),
                's' => (float)($settings['bboxS'] ?? 48.30),
                'e' => (float)($settings['bboxE'] ?? 12.08),
                'n' => (float)($settings['bboxN'] ?? 48.70),
            ];
        }

        $conn = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getConnectionForTable('tx_feuerwehren_domain_model_feuerwehr');

        $qb = $conn->createQueryBuilder();
        $rows = $qb->select('uid','name','strasse','plz','ort','latitude','longitude')
            ->from('tx_feuerwehren_domain_model_feuerwehr')
            ->where($qb->expr()->eq('deleted', 0), $qb->expr()->eq('hidden', 0))
            ->executeQuery()->fetchAllAssociative();

        // MM: Fahrzeugkategorien je Feuerwehr
        $conn2 = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getConnectionForTable('tx_feuerwehren_feuerwehr_fahrzeugkategorie_mm');
        $qbm = $conn2->createQueryBuilder();
        $mm = $qbm->select('mm.uid_local AS fw', 'c.title AS title')
            ->from('tx_feuerwehren_feuerwehr_fahrzeugkategorie_mm', 'mm')
            ->leftJoin('mm', 'tx_feuerwehren_domain_model_fahrzeugkategorie', 'c', 'c.uid = mm.uid_foreign AND c.deleted=0 AND c.hidden=0')
            ->executeQuery()->fetchAllAssociative();

        $catsByFw = [];
        foreach ($mm as $r) {
            if (!$r['fw'] || !$r['title']) { continue; }
            $catsByFw[(int)$r['fw']][] = (string)$r['title'];
        }

        $items = [];
        foreach ($rows as $r) {
            $fwUid = (int)$r['uid'];
            $lat = self::parseCoord($r['latitude'], -90, 90);
            $lon = self::parseCoord($r['longitude'], -180, 180);
            if ($lat === null || $lon === null) { continue; }

            if ($bbox && ($lon < $bbox['w'] || $lon > $bbox['e'] || $lat < $bbox['s'] || $lat > $bbox['n'])) {
                continue;
            }

            $cats = $catsByFw[$fwUid] ?? [];
            if (!empty($selected)) {
                $has = array_intersect($selected, $cats);
                $ok = ($mode === 'or') ? (count($has) > 0) : (count($has) === count($selected));
                if (!$ok) { continue; }
            }

            $items[] = [
                'uid' => $fwUid,
                'name' => (string)$r['name'],
                'strasse' => (string)$r['strasse'],
                'plz' => (string)$r['plz'],
                'ort' => (string)$r['ort'],
                'lat' => $lat,
                'lon' => $lon,
                'fahrzeuge' => $cats,
            ];
        }

        return json_encode(['items' => array_values($items)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


    private static function parseCoord($value, float $min, float $max): ?float
    {
        if ($value === null) { return null; }
        $s = trim((string)$value);
        if ($s === '') { return null; }
        $s = str_replace(',', '.', $s);
        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $s)) { return null; }
        $f = (float)$s;
        if ($f < $min || $f > $max) { return null; }
        return $f;
    }
}
