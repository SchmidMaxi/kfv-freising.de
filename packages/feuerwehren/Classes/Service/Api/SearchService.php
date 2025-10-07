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
        $west  = (float)($queryParams['bboxW'] ?? 11.30);
        $south = (float)($queryParams['bboxS'] ?? 48.30);
        $east  = (float)($queryParams['bboxE'] ?? 12.08);
        $north = (float)($queryParams['bboxN'] ?? 48.70);

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

        // Wir geben direkt das Array zurück, die Middleware erstellt die JsonResponse
        return ['items' => $items];
    }
}