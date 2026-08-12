<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Feuerwehren\Domain\Model\Area;
use Schmid\Feuerwehren\Domain\Model\Person;
use Schmid\Feuerwehren\Domain\Repository\AreaRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for the organizational chart plugin.
 *
 * Displays the hierarchical structure of fire department leadership:
 * KBR (Kreisbrandrat) -> KBI (Kreisbrandinspektor) -> KBM (Kreisbrandmeister)
 */
final class PersonController extends ActionController
{
    public function __construct(
        private readonly AreaRepository $areaRepository,
    ) {}

    /**
     * Displays the organizational chart.
     *
     * Renders the hierarchy of fire department leadership positions:
     * - KBR at the top
     * - Fach-KBMs (specialist positions)
     * - KBIs with their subordinate KBMs
     */
    public function listAction(): ResponseInterface
    {
        $kbr = $this->areaRepository->findOneBy(['type' => 'kbr']);
        $fachKbms = $this->areaRepository->findBy(['type' => 'fach-kbm']);
        $kbis = $this->areaRepository->findBy(['type' => 'kbi']);

        $kbisWithChildren = [];

        /** @var Area $kbi */
        foreach ($kbis as $kbi) {
            $kbms = $this->areaRepository->findKbmsByParent($kbi);
            $kbisWithChildren[] = [
                'kbi' => $kbi,
                'kbms' => $kbms,
            ];
        }

        $this->view->assignMultiple([
            'kbr' => $kbr,
            'fachKbms' => $fachKbms,
            'kbisWithChildren' => $kbisWithChildren,
        ]);

        return $this->htmlResponse();
    }

    /**
     * Displays details for a single person.
     */
    public function showAction(Person $person): ResponseInterface
    {
        $this->view->assign('person', $person);

        return $this->htmlResponse();
    }
}