<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Enum;

enum Bestellstatus: string
{
    case Neu = 'neu';
    case Freigegeben = 'freigegeben';
    case Ausgegeben = 'ausgegeben';
    case Abgeschlossen = 'abgeschlossen';
    case Abgelehnt = 'abgelehnt';
    case Storniert = 'storniert';

    public function getLabel(): string
    {
        return match ($this) {
            self::Neu => 'angefragt',
            self::Freigegeben => 'freigegeben',
            self::Ausgegeben => 'ausgegeben',
            self::Abgeschlossen => 'abgeschlossen',
            self::Abgelehnt => 'abgelehnt',
            self::Storniert => 'storniert',
        };
    }

    /** Bootstrap-Farbe für das Status-Badge */
    public function getFarbe(): string
    {
        return match ($this) {
            self::Neu => 'warning',
            self::Freigegeben => 'info',
            self::Ausgegeben => 'primary',
            self::Abgeschlossen => 'success',
            self::Abgelehnt, self::Storniert => 'secondary',
        };
    }

    /** Besteller darf nur stornieren, solange noch nichts ausgegeben wurde. */
    public function isStornierbar(): bool
    {
        return $this === self::Neu || $this === self::Freigegeben;
    }

    public function isFreigebbar(): bool
    {
        return $this === self::Neu;
    }

    public function isAusgabeErlaubt(): bool
    {
        return $this === self::Freigegeben || $this === self::Ausgegeben;
    }

    /** Offene Bestellungen reservieren Bestand bzw. haben Material draußen. */
    public function isOffen(): bool
    {
        return in_array($this, [self::Neu, self::Freigegeben, self::Ausgegeben], true);
    }
}
