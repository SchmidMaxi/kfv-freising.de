<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

/**
 * Baut die Auswahl der anfordernden Feuerwehr: erst der eigene Bereich, dann alle
 * übrigen Feuerwehren (für Einsatzabschnitte im Katastrophenfall), je alphabetisch.
 */
final class FeuerwehrOptionen
{
    public const GRUPPE_BEREICH = 'Mein Bereich';
    public const GRUPPE_WEITERE = 'Weitere Feuerwehren';
    public const GRUPPE_ALLE = 'Alle Feuerwehren';

    /**
     * @param array<int, string> $feuerwehren uid => Name
     * @param list<int>|null $bereich Feuerwehr-UIDs des eigenen Bereichs, null = gesamter Landkreis
     * @return array<string, array<int, string>> Optgroup-Label => (uid => Name)
     */
    public function gruppiere(array $feuerwehren, ?array $bereich): array
    {
        if ($bereich === null || $bereich === []) {
            return [self::GRUPPE_ALLE => $this->sortiere($feuerwehren)];
        }
        $eigene = array_intersect_key($feuerwehren, array_flip($bereich));
        $weitere = array_diff_key($feuerwehren, $eigene);

        return array_filter([
            self::GRUPPE_BEREICH => $this->sortiere($eigene),
            self::GRUPPE_WEITERE => $this->sortiere($weitere),
        ]);
    }

    /**
     * @param array<int, string> $feuerwehren
     * @return array<int, string>
     */
    private function sortiere(array $feuerwehren): array
    {
        if (class_exists(\Collator::class)) {
            $collator = new \Collator('de_DE');
            uasort($feuerwehren, static fn(string $a, string $b): int => (int)$collator->compare($a, $b));
        } else {
            uasort($feuerwehren, 'strnatcasecmp');
        }
        return $feuerwehren;
    }
}
