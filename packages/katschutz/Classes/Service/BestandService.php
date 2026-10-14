<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Schmid\Katschutz\Domain\Enum\Bestellstatus;
use Schmid\Katschutz\Service\Bestand\Bestand;
use Schmid\Katschutz\Service\Bestand\BestandRechner;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Lädt Journal und offene Bestellungen aus der Datenbank und liefert daraus
 * Bestand, Reservierungen und Anfragen.
 */
final class BestandService
{
    public const T_BUCHUNG = 'tx_katschutz_domain_model_buchung';
    public const T_ARTIKEL = 'tx_katschutz_domain_model_artikel';
    public const T_BESTELLUNG = 'tx_katschutz_domain_model_bestellung';
    public const T_POSITION = 'tx_katschutz_domain_model_position';

    private ?Bestand $cache = null;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly BestandRechner $rechner,
    ) {}

    public function aktuell(): Bestand
    {
        return $this->cache ??= $this->rechner->berechne($this->journal(), $this->verbrauchsartikel());
    }

    public function vergessen(): void
    {
        $this->cache = null;
    }

    /**
     * @param list<int> $artikel leer = alle
     * @return list<array{typ: string, artikel: int, einzelstueck: int, menge: int, feuerwehr: int, ziel_feuerwehr: int, bestellung: int}>
     */
    public function journal(array $artikel = []): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_BUCHUNG);
        $qb->select('typ', 'artikel', 'einzelstueck', 'menge', 'feuerwehr', 'ziel_feuerwehr', 'bestellung')
            ->from(self::T_BUCHUNG)
            ->orderBy('uid');
        if ($artikel !== []) {
            $qb->where($qb->expr()->in('artikel', $qb->quoteArrayBasedValueListToIntegerList($artikel)));
        }
        $zeilen = [];
        foreach ($qb->executeQuery()->iterateAssociative() as $z) {
            $zeilen[] = [
                'typ' => (string)$z['typ'],
                'artikel' => (int)$z['artikel'],
                'einzelstueck' => (int)$z['einzelstueck'],
                'menge' => (int)$z['menge'],
                'feuerwehr' => (int)$z['feuerwehr'],
                'ziel_feuerwehr' => (int)$z['ziel_feuerwehr'],
                'bestellung' => (int)$z['bestellung'],
            ];
        }
        return $zeilen;
    }

    /**
     * @return array<int, bool>
     */
    public function verbrauchsartikel(): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_ARTIKEL);
        $qb->getRestrictions()->removeAll();
        $rows = $qb->select('uid')
            ->from(self::T_ARTIKEL)
            ->where($qb->expr()->eq('verbrauch', 1))
            ->executeQuery()
            ->fetchFirstColumn();
        return array_fill_keys(array_map('intval', $rows), true);
    }

    /**
     * Bestellte Mengen offener Bestellungen je Artikel.
     *
     * @return array{reserviert: array<int, int>, angefragt: array<int, int>}
     *         reserviert: freigegeben, aber noch nicht ausgegeben; angefragt: noch nicht freigegeben
     */
    public function offeneMengen(): array
    {
        $bestand = $this->aktuell();
        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_POSITION);
        $rows = $qb->select('p.artikel', 'p.menge', 'p.bestellung', 'b.status')
            ->from(self::T_POSITION, 'p')
            ->join('p', self::T_BESTELLUNG, 'b', 'b.uid = p.bestellung')
            ->where($qb->expr()->in('b.status', $qb->createNamedParameter(
                [Bestellstatus::Neu->value, Bestellstatus::Freigegeben->value, Bestellstatus::Ausgegeben->value],
                \Doctrine\DBAL\ArrayParameterType::STRING,
            )))
            ->andWhere($qb->expr()->eq('b.deleted', 0))
            ->executeQuery()
            ->fetchAllAssociative();

        // Mehrere Positionen desselben Artikels (z.B. aus Modulen) zusammenfassen
        $bestellt = [];
        $status = [];
        foreach ($rows as $r) {
            $bestellt[(int)$r['bestellung']][(int)$r['artikel']] = ($bestellt[(int)$r['bestellung']][(int)$r['artikel']] ?? 0) + (int)$r['menge'];
            $status[(int)$r['bestellung']] = (string)$r['status'];
        }

        $reserviert = [];
        $angefragt = [];
        foreach ($bestellt as $bestellung => $proArtikel) {
            foreach ($proArtikel as $artikel => $menge) {
                if ($status[$bestellung] === Bestellstatus::Neu->value) {
                    $angefragt[$artikel] = ($angefragt[$artikel] ?? 0) + $menge;
                    continue;
                }
                $rest = max(0, $menge - $bestand->ausgegeben($bestellung, $artikel));
                if ($rest > 0) {
                    $reserviert[$artikel] = ($reserviert[$artikel] ?? 0) + $rest;
                }
            }
        }
        return ['reserviert' => $reserviert, 'angefragt' => $angefragt];
    }
}
