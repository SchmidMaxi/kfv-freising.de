<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Tests\Unit\Service\Bestand;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Service\Bestand\Bestand;
use Schmid\Katschutz\Service\Bestand\BestandRechner;

final class BestandRechnerTest extends TestCase
{
    private const PUMPE = 1;
    private const SANDSACK = 2;
    private const FF_A = 10;
    private const FF_B = 11;

    /**
     * @param list<BuchungEingabe> $eingaben
     */
    private function rechne(array $eingaben): Bestand
    {
        return (new BestandRechner())->berechne(
            array_map(static fn(BuchungEingabe $e): array => $e->alsZeile(), $eingaben),
            [self::SANDSACK => true],
        );
    }

    #[Test]
    public function zugangUndKorrekturErhoehenHallenbestand(): void
    {
        $bestand = $this->rechne([
            new BuchungEingabe(Buchungstyp::Zugang, self::SANDSACK, 500),
            new BuchungEingabe(Buchungstyp::Korrektur, self::SANDSACK, -20),
        ]);

        self::assertSame(480, $bestand->inHalle(self::SANDSACK));
        self::assertSame(480, $bestand->gesamt(self::SANDSACK));
    }

    #[Test]
    public function ausgabeVonLeihgutIstDraussenBeiFeuerwehr(): void
    {
        $bestand = $this->rechne([
            new BuchungEingabe(Buchungstyp::Zugang, self::PUMPE, 4),
            new BuchungEingabe(Buchungstyp::Ausgabe, self::PUMPE, 3, self::FF_A, bestellung: 7),
            new BuchungEingabe(Buchungstyp::Ruecknahme, self::PUMPE, 1, self::FF_A, bestellung: 7),
        ]);

        self::assertSame(2, $bestand->inHalle(self::PUMPE));
        self::assertSame(2, $bestand->draussenGesamt(self::PUMPE));
        self::assertSame(4, $bestand->gesamt(self::PUMPE));
        self::assertSame([self::FF_A => [self::PUMPE => 2]], $bestand->draussenNachFeuerwehr());
        self::assertSame(3, $bestand->ausgegeben(7, self::PUMPE));
        self::assertSame(1, $bestand->zurueck(7, self::PUMPE));
    }

    #[Test]
    public function verbrauchsmaterialZaehltNichtAlsDraussen(): void
    {
        $bestand = $this->rechne([
            new BuchungEingabe(Buchungstyp::Zugang, self::SANDSACK, 1000),
            new BuchungEingabe(Buchungstyp::Ausgabe, self::SANDSACK, 300, self::FF_A, bestellung: 7),
            new BuchungEingabe(Buchungstyp::Ruecknahme, self::SANDSACK, 50, self::FF_A, bestellung: 7),
        ]);

        self::assertSame(750, $bestand->inHalle(self::SANDSACK));
        self::assertSame(0, $bestand->draussenGesamt(self::SANDSACK));
        self::assertSame([], $bestand->draussenNachFeuerwehr());
        self::assertFalse($bestand->hatDraussen(7));
    }

    #[Test]
    public function umbuchungVerschiebtZwischenFeuerwehrenOhneHalle(): void
    {
        $bestand = $this->rechne([
            new BuchungEingabe(Buchungstyp::Zugang, self::PUMPE, 2),
            new BuchungEingabe(Buchungstyp::Ausgabe, self::PUMPE, 2, self::FF_A, bestellung: 7),
            new BuchungEingabe(Buchungstyp::Umbuchung, self::PUMPE, 1, self::FF_A, self::FF_B, bestellung: 7),
        ]);

        self::assertSame(0, $bestand->inHalle(self::PUMPE));
        self::assertSame(
            [self::FF_A => [self::PUMPE => 1], self::FF_B => [self::PUMPE => 1]],
            $bestand->draussenNachFeuerwehr(),
        );
        self::assertSame([self::PUMPE => [self::FF_A => 1, self::FF_B => 1]], $bestand->draussenFuerBestellung(7));
    }

    #[Test]
    public function verlustBeiFeuerwehrReduziertEigentum(): void
    {
        $bestand = $this->rechne([
            new BuchungEingabe(Buchungstyp::Zugang, self::PUMPE, 2),
            new BuchungEingabe(Buchungstyp::Ausgabe, self::PUMPE, 2, self::FF_A, bestellung: 7),
            new BuchungEingabe(Buchungstyp::Verlust, self::PUMPE, 1, self::FF_A, bestellung: 7),
            new BuchungEingabe(Buchungstyp::Ruecknahme, self::PUMPE, 1, self::FF_A, bestellung: 7),
        ]);

        self::assertSame(1, $bestand->inHalle(self::PUMPE));
        self::assertSame(1, $bestand->gesamt(self::PUMPE));
        self::assertFalse($bestand->hatDraussen(7));
    }

    #[Test]
    public function einzelstueckOrtFolgtDemJournal(): void
    {
        $bestand = $this->rechne([
            new BuchungEingabe(Buchungstyp::Zugang, self::PUMPE, 1, einzelstueck: 99),
            new BuchungEingabe(Buchungstyp::Ausgabe, self::PUMPE, 1, self::FF_A, einzelstueck: 99, bestellung: 7),
        ]);
        self::assertSame(self::FF_A, $bestand->einzelstueckOrt(99));
        self::assertSame([99], $bestand->einzelstueckeDraussen(7));

        $bestand = $this->rechne([
            new BuchungEingabe(Buchungstyp::Zugang, self::PUMPE, 1, einzelstueck: 99),
            new BuchungEingabe(Buchungstyp::Ausgabe, self::PUMPE, 1, self::FF_A, einzelstueck: 99, bestellung: 7),
            new BuchungEingabe(Buchungstyp::Umbuchung, self::PUMPE, 1, self::FF_A, self::FF_B, 99, 7),
            new BuchungEingabe(Buchungstyp::Ruecknahme, self::PUMPE, 1, self::FF_B, einzelstueck: 99, bestellung: 7),
        ]);
        self::assertSame(0, $bestand->einzelstueckOrt(99));
        self::assertSame([], $bestand->einzelstueckeDraussen(7));
        self::assertNull($bestand->einzelstueckOrt(100));
    }
}
