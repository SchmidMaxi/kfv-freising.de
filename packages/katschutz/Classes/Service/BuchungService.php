<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Doctrine\DBAL\ArrayParameterType;
use Psr\Log\LoggerInterface;
use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Service\Bestand\BestandRechner;
use Schmid\Katschutz\Service\Bestand\BuchungPruefer;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Einziger Schreibzugang zum Buchungsjournal. Eine Liste von Buchungen wird
 * atomar gebucht: Artikelzeilen werden gesperrt, jede Buchung gegen den bis
 * dahin fortgeschriebenen Bestand geprüft, erst dann wird alles eingefügt.
 */
final class BuchungService
{
    private const T_EINZELSTUECK = 'tx_katschutz_domain_model_einzelstueck';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly BestandService $bestandService,
        private readonly BestandRechner $rechner,
        private readonly BuchungPruefer $pruefer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @param list<BuchungEingabe> $eingaben
     *
     * @throws BuchungUngueltigException
     */
    public function buche(array $eingaben, int $feUser, int $lage, int $pid): void
    {
        if ($eingaben === []) {
            throw new BuchungUngueltigException('Keine Buchung angegeben.');
        }
        $artikelUids = array_values(array_unique(array_map(static fn(BuchungEingabe $e): int => $e->artikel, $eingaben)));
        $connection = $this->connectionPool->getConnectionForTable(BestandService::T_BUCHUNG);

        $connection->transactional(function (Connection $connection) use ($eingaben, $artikelUids, $feUser, $lage, $pid): void {
            // Sperre gegen parallele Buchungen auf dieselben Artikel bis zum Commit
            $connection->executeQuery(
                'SELECT uid FROM ' . $connection->quoteIdentifier(BestandService::T_ARTIKEL) . ' WHERE uid IN (?) FOR UPDATE',
                [$artikelUids],
                [ArrayParameterType::INTEGER],
            );
            $artikel = $this->ladeArtikel($artikelUids);
            $einzelstuecke = $this->ladeEinzelstuecke($eingaben);
            $verbrauch = array_map(static fn(array $a): bool => $a['verbrauch'], $artikel);
            $journal = $this->bestandService->journal($artikelUids);
            $heute = new \DateTimeImmutable('today');

            foreach ($eingaben as $eingabe) {
                if (!isset($artikel[$eingabe->artikel])) {
                    throw new BuchungUngueltigException(sprintf('Artikel %d existiert nicht.', $eingabe->artikel));
                }
                $bestand = $this->rechner->berechne($journal, $verbrauch);
                $this->pruefer->pruefe(
                    $eingabe,
                    $bestand,
                    $artikel[$eingabe->artikel],
                    $eingabe->einzelstueck > 0 ? ($einzelstuecke[$eingabe->einzelstueck] ?? throw new BuchungUngueltigException('Einzelstück existiert nicht.')) : null,
                    $heute,
                );
                $journal[] = $eingabe->alsZeile();
            }

            $jetzt = time();
            foreach ($eingaben as $eingabe) {
                $connection->insert(BestandService::T_BUCHUNG, [
                    'pid' => $pid,
                    'crdate' => $jetzt,
                    'typ' => $eingabe->typ->value,
                    'artikel' => $eingabe->artikel,
                    'einzelstueck' => $eingabe->einzelstueck,
                    'menge' => $eingabe->menge,
                    'feuerwehr' => $eingabe->feuerwehr,
                    'ziel_feuerwehr' => $eingabe->zielFeuerwehr,
                    'bestellung' => $eingabe->bestellung,
                    'lage' => $lage,
                    'fe_user' => $feUser,
                    'bemerkung' => $eingabe->bemerkung,
                ]);
            }
        });

        $this->bestandService->vergessen();
        $this->logger->info('KatS-Buchung', [
            'fe_user' => $feUser,
            'buchungen' => array_map(static fn(BuchungEingabe $e): array => $e->alsZeile(), $eingaben),
        ]);
    }

    /**
     * @param list<int> $uids
     * @return array<int, array{title: string, typ: string, verbrauch: bool}>
     */
    private function ladeArtikel(array $uids): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable(BestandService::T_ARTIKEL);
        $qb->getRestrictions()->removeByType(\TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction::class);
        $rows = $qb->select('uid', 'title', 'typ', 'verbrauch')
            ->from(BestandService::T_ARTIKEL)
            ->where($qb->expr()->in('uid', $qb->quoteArrayBasedValueListToIntegerList($uids)))
            ->executeQuery()
            ->fetchAllAssociative();
        $artikel = [];
        foreach ($rows as $r) {
            $artikel[(int)$r['uid']] = [
                'title' => (string)$r['title'],
                'typ' => (string)$r['typ'],
                'verbrauch' => (bool)$r['verbrauch'],
            ];
        }
        return $artikel;
    }

    /**
     * @param list<BuchungEingabe> $eingaben
     * @return array<int, array{artikel: int, inventarnummer: string, zustand: string, naechste_pruefung: ?\DateTimeImmutable, hidden: bool}>
     */
    private function ladeEinzelstuecke(array $eingaben): array
    {
        $uids = array_values(array_filter(array_map(static fn(BuchungEingabe $e): int => $e->einzelstueck, $eingaben)));
        if ($uids === []) {
            return [];
        }
        $qb = $this->connectionPool->getQueryBuilderForTable(self::T_EINZELSTUECK);
        $qb->getRestrictions()->removeByType(\TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction::class);
        $rows = $qb->select('uid', 'artikel', 'inventarnummer', 'zustand', 'naechste_pruefung', 'hidden')
            ->from(self::T_EINZELSTUECK)
            ->where($qb->expr()->in('uid', $qb->quoteArrayBasedValueListToIntegerList($uids)))
            ->executeQuery()
            ->fetchAllAssociative();
        $stuecke = [];
        foreach ($rows as $r) {
            $stuecke[(int)$r['uid']] = self::einzelstueckAusZeile($r);
        }
        return $stuecke;
    }

    /**
     * @param array<string, mixed> $r
     * @return array{artikel: int, inventarnummer: string, zustand: string, naechste_pruefung: ?\DateTimeImmutable, hidden: bool}
     */
    public static function einzelstueckAusZeile(array $r): array
    {
        $pruefung = $r['naechste_pruefung'] ?? null;
        return [
            'artikel' => (int)$r['artikel'],
            'inventarnummer' => (string)$r['inventarnummer'],
            'zustand' => (string)$r['zustand'],
            'naechste_pruefung' => is_string($pruefung) && $pruefung !== '' && $pruefung !== '0000-00-00'
                ? new \DateTimeImmutable($pruefung)
                : null,
            'hidden' => (bool)$r['hidden'],
        ];
    }
}
