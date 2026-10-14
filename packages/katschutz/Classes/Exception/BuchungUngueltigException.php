<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Exception;

/**
 * Fachlicher Fehler beim Buchen (z.B. zu wenig Bestand). Die Meldung ist für
 * Verwalter bestimmt und wird als Flash Message angezeigt.
 */
final class BuchungUngueltigException extends \RuntimeException {}
