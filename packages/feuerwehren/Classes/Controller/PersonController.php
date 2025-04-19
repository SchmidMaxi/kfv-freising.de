<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class PersonController extends ActionController {
    protected $personRepository;
    public function injectPersonRepository(\Schmid\Feuerwehren\Domain\Repository\PersonRepository $personRepository) {
        $this->personRepository = $personRepository;
    }
    public function listAction() {
        $personen = $this->personRepository->findAll();
        $this->view->assign('personen', $personen);
    }
    public function showAction(\Schmid\Feuerwehren\Domain\Model\Person $person) {
        $this->view->assign('person', $person);
    }
}