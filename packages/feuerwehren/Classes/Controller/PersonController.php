<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Feuerwehren\Domain\Model\Area;
use Schmid\Feuerwehren\Domain\Model\Person;
use Schmid\Feuerwehren\Domain\Repository\AreaRepository;
use Schmid\Feuerwehren\Domain\Repository\PersonRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class PersonController extends ActionController
{
    private PersonRepository $personRepository;
    private AreaRepository $areaRepository;

    public function __construct(PersonRepository $personRepository, AreaRepository $areaRepository)
    {
        $this->personRepository = $personRepository;
        $this->areaRepository = $areaRepository;
    }

    public function listAction(): ResponseInterface
    {
        $kbr = $this->areaRepository->findOneBy(['type' => 'kbr']);
        $fachKbms = $this->areaRepository->findBy(['type' => 'fach-kbm']);
        $kbis = $this->areaRepository->findBy(['type' => 'kbi']);

        // Für jeden KBI die untergeordneten KBMs finden
        $kbisWithChildren = [];
        /** @var Area $kbi */
        foreach ($kbis as $kbi) {
            $kbms = $this->areaRepository->findBy(['parent' => $kbi, 'type' => 'kbm']);
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

    public function showAction(Person $person): ResponseInterface
    {
        $this->view->assign('person', $person);
        return $this->htmlResponse();
    }
}