<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Schmid\Katschutz\Service\FeuerwehrOptionen;

final class FeuerwehrOptionenTest extends TestCase
{
    private const FEUERWEHREN = [
        1 => 'Zolling',
        2 => 'Au i. d. Hallertau',
        3 => 'Öxing',
        4 => 'Attenkirchen',
        5 => 'Moosburg',
        6 => 'Oberhummel',
    ];

    #[Test]
    public function eigenerBereichKommtZuerstUndBeideGruppenSindAlphabetisch(): void
    {
        $gruppen = (new FeuerwehrOptionen())->gruppiere(self::FEUERWEHREN, [5, 1, 4]);

        self::assertSame([FeuerwehrOptionen::GRUPPE_BEREICH, FeuerwehrOptionen::GRUPPE_WEITERE], array_keys($gruppen));
        self::assertSame(['Attenkirchen', 'Moosburg', 'Zolling'], array_values($gruppen[FeuerwehrOptionen::GRUPPE_BEREICH]));
        // Umlaut wird wie O einsortiert, nicht ans Ende
        self::assertSame(['Au i. d. Hallertau', 'Oberhummel', 'Öxing'], array_values($gruppen[FeuerwehrOptionen::GRUPPE_WEITERE]));
        self::assertSame(4, array_key_first($gruppen[FeuerwehrOptionen::GRUPPE_BEREICH]));
    }

    #[Test]
    public function ohneBereichGibtEsNurEineGruppe(): void
    {
        $gruppen = (new FeuerwehrOptionen())->gruppiere(self::FEUERWEHREN, []);
        self::assertSame([FeuerwehrOptionen::GRUPPE_ALLE], array_keys($gruppen));
        self::assertCount(6, $gruppen[FeuerwehrOptionen::GRUPPE_ALLE]);

        $gruppen = (new FeuerwehrOptionen())->gruppiere(self::FEUERWEHREN, null);
        self::assertSame([FeuerwehrOptionen::GRUPPE_ALLE], array_keys($gruppen));
    }

    #[Test]
    public function bereichMitAllenFeuerwehrenLaesstLeereGruppeWeg(): void
    {
        $gruppen = (new FeuerwehrOptionen())->gruppiere([1 => 'Zolling', 2 => 'Au'], [1, 2]);
        self::assertSame([FeuerwehrOptionen::GRUPPE_BEREICH], array_keys($gruppen));
    }
}
