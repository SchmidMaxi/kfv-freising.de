<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Tests\Unit\Service\Bestand;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Service\Bestand\Bestand;
use Schmid\Katschutz\Service\Bestand\BestandRechner;
use Schmid\Katschutz\Service\Bestand\BuchungPruefer;

final class BuchungPrueferTest extends TestCase
{
    private const MENGE = ['title' => 'Sandsack', 'typ' => 'menge', 'verbrauch' => true];
    private const LEIHGUT = ['title' => 'Schlauch B', 'typ' => 'menge', 'verbrauch' => false];
    private const EINZELN = ['title' => 'Tauchpumpe', 'typ' => 'einzeln', 'verbrauch' => false];

    private \DateTimeImmutable $heute;

    protected function setUp(): void
    {
        $this->heute = new \DateTimeImmutable('2026-10-14');
    }

    /**
     * @param list<BuchungEingabe> $journal
     */
    private function bestand(array $journal): Bestand
    {
        return (new BestandRechner())->berechne(
            array_map(static fn(BuchungEingabe $e): array => $e->alsZeile(), $journal),
            [1 => true],
        );
    }

    /**
     * @return array{artikel: int, inventarnummer: string, zustand: string, naechste_pruefung: ?\DateTimeImmutable, hidden: bool}
     */
    private function stueck(string $zustand = 'ok', ?string $pruefung = null, bool $hidden = false): array
    {
        return [
            'artikel' => 3,
            'inventarnummer' => 'P-01',
            'zustand' => $zustand,
            'naechste_pruefung' => $pruefung !== null ? new \DateTimeImmutable($pruefung) : null,
            'hidden' => $hidden,
        ];
    }

    #[Test]
    public function ausgabeUeberHallenbestandWirdAbgewiesen(): void
    {
        $this->expectException(BuchungUngueltigException::class);
        $this->expectExceptionMessage('Nur 10 in der Halle');

        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Ausgabe, 1, 11, 5, bestellung: 1),
            $this->bestand([new BuchungEingabe(Buchungstyp::Zugang, 1, 10)]),
            self::MENGE,
            null,
            $this->heute,
        );
    }

    #[Test]
    public function ausgabeImRahmenDesBestandsIstErlaubt(): void
    {
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Ausgabe, 1, 10, 5, bestellung: 1),
            $this->bestand([new BuchungEingabe(Buchungstyp::Zugang, 1, 10)]),
            self::MENGE,
            null,
            $this->heute,
        );
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function ausgabeOhneBestellungWirdAbgewiesen(): void
    {
        $this->expectException(BuchungUngueltigException::class);
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Ausgabe, 1, 1, 5),
            $this->bestand([new BuchungEingabe(Buchungstyp::Zugang, 1, 10)]),
            self::MENGE,
            null,
            $this->heute,
        );
    }

    #[Test]
    public function ruecknahmeVonLeihgutNurBisZurDraussenMenge(): void
    {
        $journal = [
            new BuchungEingabe(Buchungstyp::Zugang, 2, 10),
            new BuchungEingabe(Buchungstyp::Ausgabe, 2, 4, 5, bestellung: 1),
        ];
        $this->expectException(BuchungUngueltigException::class);
        $this->expectExceptionMessage('nur 4');
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Ruecknahme, 2, 5, 5, bestellung: 1),
            $this->bestand($journal),
            self::LEIHGUT,
            null,
            $this->heute,
        );
    }

    #[Test]
    public function ruecknahmeVonVerbrauchsmaterialNurBisAusgegeben(): void
    {
        $journal = [
            new BuchungEingabe(Buchungstyp::Zugang, 1, 100),
            new BuchungEingabe(Buchungstyp::Ausgabe, 1, 40, 5, bestellung: 1),
            new BuchungEingabe(Buchungstyp::Ruecknahme, 1, 30, 5, bestellung: 1),
        ];
        $this->expectException(BuchungUngueltigException::class);
        $this->expectExceptionMessage('höchstens 10');
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Ruecknahme, 1, 11, 5, bestellung: 1),
            $this->bestand($journal),
            self::MENGE,
            null,
            $this->heute,
        );
    }

    #[Test]
    public function korrekturDarfHalleNichtNegativMachen(): void
    {
        $this->expectException(BuchungUngueltigException::class);
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Korrektur, 2, -11),
            $this->bestand([new BuchungEingabe(Buchungstyp::Zugang, 2, 10)]),
            self::LEIHGUT,
            null,
            $this->heute,
        );
    }

    #[Test]
    public function umbuchungAufGleicheFeuerwehrWirdAbgewiesen(): void
    {
        $journal = [
            new BuchungEingabe(Buchungstyp::Zugang, 2, 10),
            new BuchungEingabe(Buchungstyp::Ausgabe, 2, 4, 5, bestellung: 1),
        ];
        $this->expectException(BuchungUngueltigException::class);
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Umbuchung, 2, 1, 5, 5, bestellung: 1),
            $this->bestand($journal),
            self::LEIHGUT,
            null,
            $this->heute,
        );
    }

    #[Test]
    public function einzelstueckMitUeberfaelligerPruefungIstGesperrt(): void
    {
        $this->expectException(BuchungUngueltigException::class);
        $this->expectExceptionMessage('Prüfung überfällig seit 01.10.2026');
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Ausgabe, 3, 1, 5, einzelstueck: 9, bestellung: 1),
            $this->bestand([new BuchungEingabe(Buchungstyp::Zugang, 3, 1, einzelstueck: 9)]),
            self::EINZELN,
            $this->stueck(pruefung: '2026-10-01'),
            $this->heute,
        );
    }

    #[Test]
    public function einzelstueckDasDraussenIstKannNichtNochmalAusgegebenWerden(): void
    {
        $journal = [
            new BuchungEingabe(Buchungstyp::Zugang, 3, 1, einzelstueck: 9),
            new BuchungEingabe(Buchungstyp::Ausgabe, 3, 1, 5, einzelstueck: 9, bestellung: 1),
        ];
        $this->expectException(BuchungUngueltigException::class);
        $this->expectExceptionMessage('nicht in der Halle');
        // Hallenbestand-Check greift vorher nicht, da wir einen weiteren Zugang simulieren
        $journal[] = new BuchungEingabe(Buchungstyp::Zugang, 3, 1, einzelstueck: 10);
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Ausgabe, 3, 1, 6, einzelstueck: 9, bestellung: 2),
            $this->bestand($journal),
            self::EINZELN,
            $this->stueck(pruefung: '2027-01-01'),
            $this->heute,
        );
    }

    #[Test]
    public function einzelstueckArtikelVerlangtGenauEinStueck(): void
    {
        $this->expectException(BuchungUngueltigException::class);
        (new BuchungPruefer())->pruefe(
            new BuchungEingabe(Buchungstyp::Zugang, 3, 2),
            $this->bestand([]),
            self::EINZELN,
            null,
            $this->heute,
        );
    }

    #[Test]
    public function sperrgrundLiefertNullFuerEinsatzbereitesStueck(): void
    {
        self::assertNull(BuchungPruefer::sperrgrund($this->stueck(pruefung: '2026-10-14'), $this->heute));
        self::assertSame('defekt', BuchungPruefer::sperrgrund($this->stueck('defekt'), $this->heute));
        self::assertSame('archiviert', BuchungPruefer::sperrgrund($this->stueck(hidden: true), $this->heute));
    }
}
