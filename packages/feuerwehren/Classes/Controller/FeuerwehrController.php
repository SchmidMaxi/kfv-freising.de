<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Schmid\Feuerwehren\Domain\Repository\FeuerwehrRepository;
use Schmid\Feuerwehren\Domain\Repository\FahrzeugkategorieRepository;
use Schmid\Feuerwehren\Domain\Repository\GemeindeRepository;

class FeuerwehrController extends ActionController
{
    protected FeuerwehrRepository $feuerwehrRepository;
    protected FahrzeugkategorieRepository $fahrzeugkategorieRepository;
    protected GemeindeRepository $gemeindeRepository;

    public function injectFeuerwehrRepository(FeuerwehrRepository $feuerwehrRepository): void
    {
        $this->feuerwehrRepository = $feuerwehrRepository;
    }

    public function injectFahrzeugkategorieRepository(FahrzeugkategorieRepository $fahrzeugkategorieRepository): void
    {
        $this->fahrzeugkategorieRepository = $fahrzeugkategorieRepository;
    }

    public function injectGemeindeRepository(GemeindeRepository $gemeindeRepository): void
    {
        $this->gemeindeRepository = $gemeindeRepository;
    }

    public function listAction(): void
    {
        $feuerwehren = $this->feuerwehrRepository->findAll();
        $fahrzeugkategorien = $this->fahrzeugkategorieRepository->findAll();
        $gemeinden = $this->gemeindeRepository->findAll();

        $this->view->assignMultiple([
            'feuerwehren' => $feuerwehren,
            'fahrzeugkategorien' => $fahrzeugkategorien,
            'gemeinden' => $gemeinden
        ]);
    }

    public function showAction(\Schmid\Feuerwehren\Domain\Model\Feuerwehr $feuerwehr): void
    {
        $this->view->assign('feuerwehr', $feuerwehr);
    }
}
