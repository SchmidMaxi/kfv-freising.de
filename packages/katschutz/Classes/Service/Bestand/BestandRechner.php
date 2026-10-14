<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service\Bestand;

use Schmid\Katschutz\Domain\Enum\Buchungstyp;

/**
 * Rechnet das Buchungsjournal zu einem Bestand zusammen. Reine Funktion ohne
 * Datenbankzugriff, damit sie isoliert testbar ist.
 *
 * Wirkung je Buchungsart (m = Menge, FW = Feuerwehr, 0 = Halle):
 *  zugang/korrektur  Halle += m (Korrektur darf negativ sein)
 *  aussonderung      Halle -= m
 *  ausgabe → FW      Halle -= m, draußen(FW) += m (nur Leihgut)
 *  ruecknahme ← FW   Halle += m, draußen(FW) -= m (nur Leihgut)
 *  umbuchung FW→Ziel draußen(FW) -= m, draußen(Ziel) += m
 *  verlust           bei FW 0: Halle -= m, sonst draußen(FW) -= m
 */
final class BestandRechner
{
    /**
     * @param iterable<array{typ: string, artikel: int, einzelstueck: int, menge: int, feuerwehr: int, ziel_feuerwehr: int, bestellung: int}> $buchungen in Buchungsreihenfolge
     * @param array<int, bool> $verbrauch artikel => ist Verbrauchsmaterial
     */
    public function berechne(iterable $buchungen, array $verbrauch = []): Bestand
    {
        $halle = [];
        $draussen = [];
        $ausgegeben = [];
        $ort = [];
        $ortBestellung = [];
        $zurueck = [];

        foreach ($buchungen as $b) {
            $typ = Buchungstyp::from($b['typ']);
            $artikel = (int)$b['artikel'];
            $menge = (int)$b['menge'];
            $feuerwehr = (int)$b['feuerwehr'];
            $bestellung = (int)$b['bestellung'];
            $einzelstueck = (int)$b['einzelstueck'];
            $istLeihgut = !($verbrauch[$artikel] ?? false);

            switch ($typ) {
                case Buchungstyp::Zugang:
                case Buchungstyp::Korrektur:
                    $halle[$artikel] = ($halle[$artikel] ?? 0) + $menge;
                    if ($einzelstueck > 0) {
                        $ort[$einzelstueck] = 0;
                    }
                    break;
                case Buchungstyp::Aussonderung:
                    $halle[$artikel] = ($halle[$artikel] ?? 0) - $menge;
                    if ($einzelstueck > 0) {
                        $ort[$einzelstueck] = Bestand::EINZELSTUECK_WEG;
                    }
                    break;
                case Buchungstyp::Ausgabe:
                    $halle[$artikel] = ($halle[$artikel] ?? 0) - $menge;
                    $ausgegeben[$bestellung][$artikel] = ($ausgegeben[$bestellung][$artikel] ?? 0) + $menge;
                    if ($istLeihgut) {
                        $draussen[$bestellung][$artikel][$feuerwehr] = ($draussen[$bestellung][$artikel][$feuerwehr] ?? 0) + $menge;
                    }
                    if ($einzelstueck > 0) {
                        $ort[$einzelstueck] = $feuerwehr;
                        $ortBestellung[$einzelstueck] = $bestellung;
                    }
                    break;
                case Buchungstyp::Ruecknahme:
                    $halle[$artikel] = ($halle[$artikel] ?? 0) + $menge;
                    $zurueck[$bestellung][$artikel] = ($zurueck[$bestellung][$artikel] ?? 0) + $menge;
                    if ($istLeihgut) {
                        $draussen[$bestellung][$artikel][$feuerwehr] = ($draussen[$bestellung][$artikel][$feuerwehr] ?? 0) - $menge;
                    }
                    if ($einzelstueck > 0) {
                        $ort[$einzelstueck] = 0;
                        unset($ortBestellung[$einzelstueck]);
                    }
                    break;
                case Buchungstyp::Umbuchung:
                    $ziel = (int)$b['ziel_feuerwehr'];
                    $draussen[$bestellung][$artikel][$feuerwehr] = ($draussen[$bestellung][$artikel][$feuerwehr] ?? 0) - $menge;
                    $draussen[$bestellung][$artikel][$ziel] = ($draussen[$bestellung][$artikel][$ziel] ?? 0) + $menge;
                    if ($einzelstueck > 0) {
                        $ort[$einzelstueck] = $ziel;
                    }
                    break;
                case Buchungstyp::Verlust:
                    if ($feuerwehr === 0) {
                        $halle[$artikel] = ($halle[$artikel] ?? 0) - $menge;
                    } elseif ($istLeihgut) {
                        $draussen[$bestellung][$artikel][$feuerwehr] = ($draussen[$bestellung][$artikel][$feuerwehr] ?? 0) - $menge;
                    }
                    if ($einzelstueck > 0) {
                        $ort[$einzelstueck] = Bestand::EINZELSTUECK_WEG;
                        unset($ortBestellung[$einzelstueck]);
                    }
                    break;
            }
        }

        return new Bestand($halle, $draussen, $ausgegeben, $ort, $ortBestellung, $zurueck);
    }
}
