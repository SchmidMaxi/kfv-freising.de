<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Ermittelt die Feuerwehren im Zuständigkeitsbereich eines FE-Users über
 * EXT:feuerwehren: fe_users → Person → Area (KBR/KBI/KBM) → Gemeinden → Feuerwehren.
 */
final readonly class BereichService
{
    private const T_FEUERWEHR = 'tx_feuerwehren_domain_model_feuerwehr';
    private const T_PERSON = 'tx_feuerwehren_domain_model_person';
    private const T_AREA = 'tx_feuerwehren_domain_model_area';
    private const T_GEMEINDE = 'tx_feuerwehren_domain_model_gemeinde';
    private const T_MM = 'tx_feuerwehren_gemeinde_feuerwehr_mm';

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    /**
     * @return array<int, string> uid => Name aller aktiven Feuerwehren
     */
    public function alleFeuerwehren(): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_FEUERWEHR);
        $rows = $qb->select('uid', 'name')
            ->from(self::T_FEUERWEHR)
            ->executeQuery()
            ->fetchAllAssociative();
        $ergebnis = [];
        foreach ($rows as $r) {
            $ergebnis[(int)$r['uid']] = (string)$r['name'];
        }
        return $ergebnis;
    }

    /**
     * @return list<int>|null Feuerwehr-UIDs; null = gesamter Landkreis (KBR); [] = keine Zuordnung
     */
    public function feuerwehrenImBereich(int $feUser): ?array
    {
        if ($feUser <= 0) {
            return [];
        }
        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_AREA);
        $areas = $qb->select('a.uid', 'a.type')
            ->from(self::T_AREA, 'a')
            ->join('a', self::T_PERSON, 'p', 'p.uid = a.person')
            ->where($qb->expr()->eq('p.fe_user', $qb->createNamedParameter($feUser, \Doctrine\DBAL\ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchAllAssociative();

        $areaUids = [];
        foreach ($areas as $area) {
            if ($area['type'] === 'kbr') {
                return null;
            }
            if ($area['type'] === 'kbi' || $area['type'] === 'kbm') {
                $areaUids[] = (int)$area['uid'];
            }
        }
        if ($areaUids === []) {
            return [];
        }

        // KBI: zusätzlich alle KBM-Abschnitte darunter
        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_AREA);
        $kinder = $qb->select('uid')
            ->from(self::T_AREA)
            ->where($qb->expr()->in('parent', $qb->quoteArrayBasedValueListToIntegerList($areaUids)))
            ->executeQuery()
            ->fetchFirstColumn();
        $areaUids = array_values(array_unique([...$areaUids, ...array_map('intval', $kinder)]));

        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_GEMEINDE);
        $feuerwehren = $qb->select('mm.uid_foreign')
            ->from(self::T_GEMEINDE, 'g')
            ->join('g', self::T_MM, 'mm', 'mm.uid_local = g.uid')
            ->where($qb->expr()->in('g.kbm_area', $qb->quoteArrayBasedValueListToIntegerList($areaUids)))
            ->executeQuery()
            ->fetchFirstColumn();

        return array_values(array_unique(array_map('intval', $feuerwehren)));
    }
}
