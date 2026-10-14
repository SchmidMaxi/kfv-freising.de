<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service\Bestand;

use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Exception\BuchungUngueltigException;

/**
 * Prüft eine Buchung gegen den aktuellen Bestand, bevor sie ins Journal geht.
 * Reine Logik ohne Datenbank; Artikel- und Einzelstückdaten werden übergeben.
 */
final class BuchungPruefer
{
    /**
     * @param array{title: string, typ: string, verbrauch: bool} $artikel
     * @param array{artikel: int, inventarnummer: string, zustand: string, naechste_pruefung: ?\DateTimeImmutable, hidden: bool}|null $einzelstueck
     *
     * @throws BuchungUngueltigException
     */
    public function pruefe(
        BuchungEingabe $eingabe,
        Bestand $bestand,
        array $artikel,
        ?array $einzelstueck,
        \DateTimeImmutable $heute,
    ): void {
        $titel = $artikel['title'];
        $typ = $eingabe->typ;

        if ($typ === Buchungstyp::Korrektur) {
            if ($eingabe->menge === 0) {
                throw new BuchungUngueltigException(sprintf('%s: Korrektur um 0 ist nicht sinnvoll.', $titel));
            }
        } elseif ($eingabe->menge <= 0) {
            throw new BuchungUngueltigException(sprintf('%s: Menge muss größer als 0 sein.', $titel));
        }

        $this->pruefeEinzelstueckBezug($eingabe, $artikel, $einzelstueck);
        $bezeichnung = $einzelstueck !== null ? sprintf('%s (%s)', $titel, $einzelstueck['inventarnummer']) : $titel;
        $ort = $eingabe->einzelstueck > 0 ? $bestand->einzelstueckOrt($eingabe->einzelstueck) : null;

        switch ($typ) {
            case Buchungstyp::Zugang:
                if ($einzelstueck !== null && $ort !== null && $ort !== Bestand::EINZELSTUECK_WEG) {
                    throw new BuchungUngueltigException(sprintf('%s ist bereits im Bestand.', $bezeichnung));
                }
                break;

            case Buchungstyp::Korrektur:
                if ($bestand->inHalle($eingabe->artikel) + $eingabe->menge < 0) {
                    throw new BuchungUngueltigException(sprintf(
                        '%s: Korrektur würde den Hallenbestand negativ machen (aktuell %d).',
                        $titel,
                        $bestand->inHalle($eingabe->artikel),
                    ));
                }
                break;

            case Buchungstyp::Aussonderung:
                $this->pruefeHallenbestand($eingabe, $bestand, $titel);
                $this->pruefeInHalle($einzelstueck, $ort, $bezeichnung);
                break;

            case Buchungstyp::Ausgabe:
                $this->pruefeFeuerwehrUndBestellung($eingabe);
                $this->pruefeHallenbestand($eingabe, $bestand, $titel);
                if ($einzelstueck !== null) {
                    $this->pruefeInHalle($einzelstueck, $ort, $bezeichnung);
                    $this->pruefeEinsatzbereit($einzelstueck, $bezeichnung, $heute);
                }
                break;

            case Buchungstyp::Ruecknahme:
                $this->pruefeFeuerwehrUndBestellung($eingabe);
                if ($artikel['verbrauch']) {
                    $offen = $bestand->ausgegeben($eingabe->bestellung, $eingabe->artikel)
                        - $bestand->zurueck($eingabe->bestellung, $eingabe->artikel);
                    if ($eingabe->menge > $offen) {
                        throw new BuchungUngueltigException(sprintf(
                            '%s: Es können höchstens %d zurückgenommen werden.',
                            $titel,
                            $offen,
                        ));
                    }
                } else {
                    $this->pruefeDraussen($eingabe, $bestand, $einzelstueck, $ort, $bezeichnung);
                }
                break;

            case Buchungstyp::Umbuchung:
                $this->pruefeFeuerwehrUndBestellung($eingabe);
                if ($artikel['verbrauch']) {
                    throw new BuchungUngueltigException(sprintf('%s: Verbrauchsmaterial kann nicht umgebucht werden.', $titel));
                }
                if ($eingabe->zielFeuerwehr <= 0 || $eingabe->zielFeuerwehr === $eingabe->feuerwehr) {
                    throw new BuchungUngueltigException(sprintf('%s: Bitte eine andere Ziel-Feuerwehr wählen.', $titel));
                }
                $this->pruefeDraussen($eingabe, $bestand, $einzelstueck, $ort, $bezeichnung);
                break;

            case Buchungstyp::Verlust:
                if ($eingabe->feuerwehr === 0) {
                    $this->pruefeHallenbestand($eingabe, $bestand, $titel);
                    $this->pruefeInHalle($einzelstueck, $ort, $bezeichnung);
                } elseif (!$artikel['verbrauch']) {
                    $this->pruefeDraussen($eingabe, $bestand, $einzelstueck, $ort, $bezeichnung);
                }
                break;
        }
    }

    /**
     * @param array{typ: string} $artikel
     * @param array{artikel: int}|null $einzelstueck
     */
    private function pruefeEinzelstueckBezug(BuchungEingabe $eingabe, array $artikel, ?array $einzelstueck): void
    {
        if ($artikel['typ'] === 'einzeln') {
            if ($eingabe->typ === Buchungstyp::Korrektur) {
                throw new BuchungUngueltigException('Einzelstück-Artikel werden über Zugang/Aussonderung je Stück korrigiert.');
            }
            if ($einzelstueck === null || $eingabe->menge !== 1) {
                throw new BuchungUngueltigException('Bei Einzelstück-Artikeln muss genau ein Einzelstück gebucht werden.');
            }
            if ($einzelstueck['artikel'] !== $eingabe->artikel) {
                throw new BuchungUngueltigException('Das Einzelstück gehört nicht zu diesem Artikel.');
            }
        } elseif ($eingabe->einzelstueck > 0) {
            throw new BuchungUngueltigException('Mengenartikel haben keine Einzelstücke.');
        }
    }

    private function pruefeFeuerwehrUndBestellung(BuchungEingabe $eingabe): void
    {
        if ($eingabe->feuerwehr <= 0 || $eingabe->bestellung <= 0) {
            throw new BuchungUngueltigException('Ausgaben und Rücknahmen sind nur zu einer Bestellung mit Feuerwehr möglich.');
        }
    }

    private function pruefeHallenbestand(BuchungEingabe $eingabe, Bestand $bestand, string $titel): void
    {
        $verfuegbar = $bestand->inHalle($eingabe->artikel);
        if ($eingabe->menge > $verfuegbar) {
            throw new BuchungUngueltigException(sprintf(
                '%s: Nur %d in der Halle vorhanden, %d angefordert.',
                $titel,
                $verfuegbar,
                $eingabe->menge,
            ));
        }
    }

    /**
     * @param array<string, mixed>|null $einzelstueck
     */
    private function pruefeInHalle(?array $einzelstueck, ?int $ort, string $bezeichnung): void
    {
        if ($einzelstueck !== null && $ort !== 0) {
            throw new BuchungUngueltigException(sprintf('%s ist nicht in der Halle.', $bezeichnung));
        }
    }

    /**
     * @param array{zustand: string, naechste_pruefung: ?\DateTimeImmutable, hidden: bool} $einzelstueck
     */
    private function pruefeEinsatzbereit(array $einzelstueck, string $bezeichnung, \DateTimeImmutable $heute): void
    {
        $grund = self::sperrgrund($einzelstueck, $heute);
        if ($grund !== null) {
            throw new BuchungUngueltigException(sprintf('%s kann nicht ausgegeben werden: %s.', $bezeichnung, $grund));
        }
    }

    /**
     * @param array<string, mixed>|null $einzelstueck
     */
    private function pruefeDraussen(BuchungEingabe $eingabe, Bestand $bestand, ?array $einzelstueck, ?int $ort, string $bezeichnung): void
    {
        if ($einzelstueck !== null) {
            if ($ort !== $eingabe->feuerwehr || $bestand->einzelstueckBestellung($eingabe->einzelstueck) !== $eingabe->bestellung) {
                throw new BuchungUngueltigException(sprintf('%s ist laut Journal nicht bei dieser Feuerwehr.', $bezeichnung));
            }
            return;
        }
        $draussen = $bestand->draussenBei($eingabe->bestellung, $eingabe->artikel, $eingabe->feuerwehr);
        if ($eingabe->menge > $draussen) {
            throw new BuchungUngueltigException(sprintf(
                '%s: Bei dieser Feuerwehr sind laut Journal nur %d.',
                $bezeichnung,
                $draussen,
            ));
        }
    }

    /**
     * Warum ein Einzelstück nicht ausgegeben werden darf, oder null.
     *
     * @param array{zustand: string, naechste_pruefung: ?\DateTimeImmutable, hidden: bool} $einzelstueck
     */
    public static function sperrgrund(array $einzelstueck, \DateTimeImmutable $heute): ?string
    {
        if ($einzelstueck['hidden']) {
            return 'archiviert';
        }
        if ($einzelstueck['zustand'] === 'defekt') {
            return 'defekt';
        }
        if ($einzelstueck['zustand'] === 'mangel') {
            return 'Mangel offen';
        }
        $pruefung = $einzelstueck['naechste_pruefung'];
        if ($pruefung !== null && $pruefung < $heute->setTime(0, 0)) {
            return 'Prüfung überfällig seit ' . $pruefung->format('d.m.Y');
        }
        return null;
    }
}
