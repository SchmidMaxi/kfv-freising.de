<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Service\Api;

use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use Schmid\Feuerwehren\Domain\Repository\FeuerwehrRepository;

final class SearchService
{
    public function __construct(private readonly FeuerwehrRepository $feuerwehrRepository)
    {
    }

    public function search(array $queryParams): array
    {
        // KORREKTUR: Variablen initialisieren und den 'bbox'-Parameter robust verarbeiten
        $west = null;
        $south = null;
        $east = null;
        $north = null;

        if (!empty($queryParams['bbox']) && is_string($queryParams['bbox'])) {
            $coords = explode(',', $queryParams['bbox']);
            if (count($coords) === 4) {
                // Sicherstellen, dass alle Werte als float interpretiert werden
                [$west, $south, $east, $north] = array_map('floatval', $coords);
            }
        }

        // Fallback auf die Standardwerte, falls bbox nicht vorhanden oder ungültig war
        $west  ??= 11.30;
        $south ??= 48.30;
        $east  ??= 12.08;
        $north ??= 48.70;

        $selectedCategories = array_filter(
            array_map('intval', $queryParams['f'] ?? []),
            static fn(int $uid) => $uid > 0
        );
        $mode = ($queryParams['mode'] ?? 'and') === 'or' ? 'or' : 'and';

        $feuerwehren = $this->feuerwehrRepository->findForMapSearch(
            $west, $south, $east, $north, $selectedCategories, $mode
        );

        $items = [];
        /** @var Feuerwehr $fw */
        foreach ($feuerwehren as $fw) {
            $cats = [];
            foreach ($fw->getFahrzeugkategorien() as $cat) {
                $cats[] = (string)$cat->getTitle();
            }
            $items[] = [
                'uid'       => $fw->getUid(),
                'name'      => $fw->getName(),
                'strasse'   => $fw->getStrasse(),
                'plz'       => $fw->getPlz(),
                'ort'       => $fw->getOrt(),
                'lat'       => $fw->getLatitude(),
                'lon'       => $fw->getLongitude(),
                'fahrzeuge' => $cats,
            ];
        }

        return ['items' => $items];
    }
}