<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Schmid\Feuerwehren\Domain\Repository\FeuerwehrRepository;
use Schmid\Feuerwehren\Domain\Repository\FahrzeugkategorieRepository;
use Schmid\Feuerwehren\Domain\Repository\GemeindeRepository;

final class FeuerwehrController extends ActionController
{
    public function __construct(
        private readonly FeuerwehrRepository $feuerwehrRepository,
        private readonly FahrzeugkategorieRepository $fahrzeugkategorieRepository,
        private readonly GemeindeRepository $gemeindeRepository
    ) {}

    public function listAction(): ResponseInterface
    {
        $this->view->assignMultiple([
            'feuerwehren'         => $this->feuerwehrRepository->findAll(),
            'fahrzeugkategorien'  => $this->fahrzeugkategorieRepository->findAll(),
            'gemeinden'           => $this->gemeindeRepository->findAll(),
        ]);
        return $this->htmlResponse();
    }

    public function showAction(\Schmid\Feuerwehren\Domain\Model\Feuerwehr $feuerwehr): ResponseInterface
    {
        $this->view->assign('feuerwehr', $feuerwehr);
        return $this->htmlResponse();
    }
}
