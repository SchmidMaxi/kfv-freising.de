<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Dto;

use Schmid\Katschutz\Domain\Enum\Buchungstyp;

/**
 * Eine zu buchende Bewegung. Feuerwehr 0 = KatS-Halle.
 */
final readonly class BuchungEingabe
{
    public function __construct(
        public Buchungstyp $typ,
        public int $artikel,
        public int $menge,
        public int $feuerwehr = 0,
        public int $zielFeuerwehr = 0,
        public int $einzelstueck = 0,
        public int $bestellung = 0,
        public string $bemerkung = '',
    ) {}

    /**
     * @return array{typ: string, artikel: int, einzelstueck: int, menge: int, feuerwehr: int, ziel_feuerwehr: int, bestellung: int}
     */
    public function alsZeile(): array
    {
        return [
            'typ' => $this->typ->value,
            'artikel' => $this->artikel,
            'einzelstueck' => $this->einzelstueck,
            'menge' => $this->menge,
            'feuerwehr' => $this->feuerwehr,
            'ziel_feuerwehr' => $this->zielFeuerwehr,
            'bestellung' => $this->bestellung,
        ];
    }
}
