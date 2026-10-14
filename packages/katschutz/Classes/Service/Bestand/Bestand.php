<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service\Bestand;

/**
 * Momentaufnahme des Bestands, berechnet aus dem Buchungsjournal.
 *
 * Feuerwehr-UID 0 steht für die KatS-Halle. Bei Einzelstücken bedeutet Ort
 * EINZELSTUECK_WEG, dass das Stück ausgesondert oder verloren ist.
 */
final class Bestand
{
    public const EINZELSTUECK_WEG = -1;

    /**
     * @param array<int, int> $halle artikel => Menge in der Halle
     * @param array<int, array<int, array<int, int>>> $draussen bestellung => artikel => feuerwehr => Menge (nur Leihgut)
     * @param array<int, array<int, int>> $ausgegeben bestellung => artikel => insgesamt ausgegebene Menge (inkl. Verbrauch)
     * @param array<int, int> $einzelstueckOrt einzelstueck => feuerwehr (0 = Halle, -1 = weg)
     * @param array<int, int> $einzelstueckBestellung einzelstueck => Bestellung, unter der es draußen ist
     * @param array<int, array<int, int>> $zurueck bestellung => artikel => insgesamt zurückgenommene Menge
     */
    public function __construct(
        private array $halle = [],
        private array $draussen = [],
        private array $ausgegeben = [],
        private array $einzelstueckOrt = [],
        private array $einzelstueckBestellung = [],
        private array $zurueck = [],
    ) {}

    public function inHalle(int $artikel): int
    {
        return $this->halle[$artikel] ?? 0;
    }

    /** Leihgut, das aktuell bei Feuerwehren ist. */
    public function draussenGesamt(int $artikel): int
    {
        $summe = 0;
        foreach ($this->draussen as $proArtikel) {
            $summe += array_sum($proArtikel[$artikel] ?? []);
        }
        return $summe;
    }

    /** Eigentum = Halle + draußen (Verbrauchtes zählt nicht mehr). */
    public function gesamt(int $artikel): int
    {
        return $this->inHalle($artikel) + $this->draussenGesamt($artikel);
    }

    /**
     * @return array<int, array<int, int>> feuerwehr => artikel => Menge
     */
    public function draussenNachFeuerwehr(): array
    {
        $ergebnis = [];
        foreach ($this->draussen as $proArtikel) {
            foreach ($proArtikel as $artikel => $proFeuerwehr) {
                foreach ($proFeuerwehr as $feuerwehr => $menge) {
                    if ($menge === 0) {
                        continue;
                    }
                    $ergebnis[$feuerwehr][$artikel] = ($ergebnis[$feuerwehr][$artikel] ?? 0) + $menge;
                }
            }
        }
        return $ergebnis;
    }

    /**
     * @return array<int, array<int, int>> artikel => feuerwehr => Menge
     */
    public function draussenFuerBestellung(int $bestellung): array
    {
        $ergebnis = [];
        foreach ($this->draussen[$bestellung] ?? [] as $artikel => $proFeuerwehr) {
            $proFeuerwehr = array_filter($proFeuerwehr, static fn(int $m): bool => $m !== 0);
            if ($proFeuerwehr !== []) {
                $ergebnis[$artikel] = $proFeuerwehr;
            }
        }
        return $ergebnis;
    }

    public function draussenBei(int $bestellung, int $artikel, int $feuerwehr): int
    {
        return $this->draussen[$bestellung][$artikel][$feuerwehr] ?? 0;
    }

    public function hatDraussen(int $bestellung): bool
    {
        return $this->draussenFuerBestellung($bestellung) !== [];
    }

    public function ausgegeben(int $bestellung, int $artikel): int
    {
        return $this->ausgegeben[$bestellung][$artikel] ?? 0;
    }

    public function zurueck(int $bestellung, int $artikel): int
    {
        return $this->zurueck[$bestellung][$artikel] ?? 0;
    }

    /** null = Einzelstück ist im Journal noch nicht erfasst (wird als Halle behandelt). */
    public function einzelstueckOrt(int $einzelstueck): ?int
    {
        return $this->einzelstueckOrt[$einzelstueck] ?? null;
    }

    public function einzelstueckBestellung(int $einzelstueck): int
    {
        return $this->einzelstueckBestellung[$einzelstueck] ?? 0;
    }

    /**
     * @return list<int> Einzelstücke, die unter dieser Bestellung draußen sind
     */
    public function einzelstueckeDraussen(int $bestellung): array
    {
        $ergebnis = [];
        foreach ($this->einzelstueckBestellung as $einzelstueck => $b) {
            if ($b === $bestellung && ($this->einzelstueckOrt[$einzelstueck] ?? 0) > 0) {
                $ergebnis[] = $einzelstueck;
            }
        }
        return $ergebnis;
    }
}
