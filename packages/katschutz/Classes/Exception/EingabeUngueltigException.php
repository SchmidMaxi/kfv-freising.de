<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Exception;

/**
 * Formulareingaben sind unvollständig/ungültig. Trägt alle Meldungen, damit das
 * Formular sie gesammelt anzeigen kann.
 */
final class EingabeUngueltigException extends \RuntimeException
{
    /**
     * @param list<string> $fehler
     */
    public function __construct(public readonly array $fehler)
    {
        parent::__construct(implode(' ', $fehler), 1760439001);
    }
}
