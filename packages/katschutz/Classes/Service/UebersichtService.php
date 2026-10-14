<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Schmid\Katschutz\Domain\Model\Artikel;
use Schmid\Katschutz\Service\Bestand\Bestand;
use Schmid\Katschutz\Service\Bestand\BuchungPruefer;

/**
 * Bereitet Bestandszahlen je Artikel für Katalog, Bestellformular und Dashboard auf.
 */
final readonly class UebersichtService
{
    public function __construct(
        private BestandService $bestandService,
    ) {}

    /**
     * @param iterable<Artikel> $artikelListe
     * @return list<array{artikel: Artikel, halle: int, draussen: int, gesamt: int, reserviert: int, angefragt: int, verfuegbar: int, gesperrt: int, unterMindestbestand: bool}>
     */
    public function zeilen(iterable $artikelListe): array
    {
        $bestand = $this->bestandService->aktuell();
        $offen = $this->bestandService->offeneMengen();
        $heute = new \DateTimeImmutable('today');
        $zeilen = [];
        foreach ($artikelListe as $artikel) {
            $uid = (int)$artikel->getUid();
            $halle = $bestand->inHalle($uid);
            $gesperrt = $artikel->isEinzeln() ? $this->gesperrteInHalle($artikel, $bestand, $heute) : 0;
            $reserviert = $offen['reserviert'][$uid] ?? 0;
            $zeilen[] = [
                'artikel' => $artikel,
                'halle' => $halle,
                'draussen' => $bestand->draussenGesamt($uid),
                'gesamt' => $bestand->gesamt($uid),
                'reserviert' => $reserviert,
                'angefragt' => $offen['angefragt'][$uid] ?? 0,
                'verfuegbar' => max(0, $halle - $gesperrt - $reserviert),
                'gesperrt' => $gesperrt,
                'unterMindestbestand' => $artikel->getMindestbestand() > 0 && $halle < $artikel->getMindestbestand(),
            ];
        }
        return $zeilen;
    }

    /**
     * Zeilen nach Kategorie gruppiert (Kategorie-Titel => Zeilen), Kategorien ohne Titel unter „Sonstiges“.
     *
     * @param iterable<Artikel> $artikelListe
     * @return array<string, list<array<string, mixed>>>
     */
    public function zeilenNachKategorie(iterable $artikelListe): array
    {
        $gruppen = [];
        foreach ($this->zeilen($artikelListe) as $zeile) {
            $gruppen[$zeile['artikel']->getKategorie()?->getTitle() ?? 'Sonstiges'][] = $zeile;
        }
        ksort($gruppen, SORT_NATURAL | SORT_FLAG_CASE);
        return $gruppen;
    }

    private function gesperrteInHalle(Artikel $artikel, Bestand $bestand, \DateTimeImmutable $heute): int
    {
        $gesperrt = 0;
        foreach ($artikel->getEinzelstuecke() as $stueck) {
            $ort = $bestand->einzelstueckOrt((int)$stueck->getUid());
            if ($ort === 0 && BuchungPruefer::sperrgrund($stueck->alsPruefdaten(), $heute) !== null) {
                $gesperrt++;
            }
        }
        return $gesperrt;
    }
}
