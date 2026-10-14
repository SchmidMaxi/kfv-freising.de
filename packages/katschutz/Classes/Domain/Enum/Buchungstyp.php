<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Enum;

enum Buchungstyp: string
{
    case Zugang = 'zugang';
    case Korrektur = 'korrektur';
    case Aussonderung = 'aussonderung';
    case Ausgabe = 'ausgabe';
    case Ruecknahme = 'ruecknahme';
    case Umbuchung = 'umbuchung';
    case Verlust = 'verlust';

    public function getLabel(): string
    {
        return match ($this) {
            self::Zugang => 'Zugang',
            self::Korrektur => 'Inventur-Korrektur',
            self::Aussonderung => 'Aussonderung',
            self::Ausgabe => 'Ausgabe',
            self::Ruecknahme => 'Rücknahme',
            self::Umbuchung => 'Umbuchung',
            self::Verlust => 'Verlust',
        };
    }

    /**
     * Buchungsarten, die ein Verwalter direkt (ohne Bestellung) im Lager buchen darf.
     *
     * @return list<self>
     */
    public static function lagerbuchungen(): array
    {
        return [self::Zugang, self::Korrektur, self::Aussonderung, self::Verlust];
    }
}
