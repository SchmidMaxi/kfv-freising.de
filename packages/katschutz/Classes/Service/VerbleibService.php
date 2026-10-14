<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Schmid\Katschutz\Domain\Enum\Bestellstatus;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\BestellungRepository;
use Schmid\Katschutz\Domain\Repository\EinzelstueckRepository;

/**
 * „Wo ist was gerade?“ – Leihgut außerhalb der Halle, nach Feuerwehr gruppiert.
 */
final readonly class VerbleibService
{
    public function __construct(
        private BestandService $bestandService,
        private BereichService $bereichService,
        private BestellungRepository $bestellungRepository,
        private ArtikelRepository $artikelRepository,
        private EinzelstueckRepository $einzelstueckRepository,
    ) {}

    /**
     * @param iterable<Bestellung>|null $bestellungen null = alle mit Status „ausgegeben“
     * @return array<string, list<array{artikel: mixed, menge: int, stuecke: list<mixed>, bestellung: Bestellung}>> Feuerwehr-Name => Zeilen
     */
    public function nachFeuerwehr(?iterable $bestellungen = null): array
    {
        $bestand = $this->bestandService->aktuell();
        $namen = $this->bereichService->alleFeuerwehren();
        $bestellungen ??= $this->bestellungRepository->findByStatus([Bestellstatus::Ausgegeben]);

        $ergebnis = [];
        foreach ($bestellungen as $bestellung) {
            $uid = (int)$bestellung->getUid();
            $stueckeNachOrt = [];
            foreach ($bestand->einzelstueckeDraussen($uid) as $stueckUid) {
                $stueck = $this->einzelstueckRepository->findByUidInklusiveArchiv($stueckUid);
                if ($stueck !== null) {
                    $stueckeNachOrt[(int)$bestand->einzelstueckOrt($stueckUid)][(int)$stueck->getArtikel()?->getUid()][] = $stueck;
                }
            }
            foreach ($bestand->draussenFuerBestellung($uid) as $artikelUid => $proFeuerwehr) {
                $artikel = $this->artikelRepository->findByUidInklusiveArchiv($artikelUid);
                foreach ($proFeuerwehr as $feuerwehr => $menge) {
                    $ergebnis[$namen[$feuerwehr] ?? ('Feuerwehr #' . $feuerwehr)][] = [
                        'artikel' => $artikel,
                        'menge' => $menge,
                        'stuecke' => $stueckeNachOrt[$feuerwehr][$artikelUid] ?? [],
                        'bestellung' => $bestellung,
                    ];
                }
            }
        }
        $collator = class_exists(\Collator::class) ? new \Collator('de_DE') : null;
        uksort($ergebnis, static fn(string $a, string $b): int => $collator !== null ? (int)$collator->compare($a, $b) : strnatcasecmp($a, $b));
        return $ergebnis;
    }
}
