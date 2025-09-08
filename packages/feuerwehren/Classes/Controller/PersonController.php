<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Schmid\Feuerwehren\Domain\Repository\PersonRepository;

final class PersonController extends ActionController
{
    public function __construct(
        private readonly PersonRepository $personRepository
    ) {}

    public function listAction(): ResponseInterface
    {
        $this->view->assign('personen', $this->personRepository->findAll());
        return $this->htmlResponse();
    }

    public function showAction(\Schmid\Feuerwehren\Domain\Model\Person $person): ResponseInterface
    {
        $this->view->assign('person', $person);
        return $this->htmlResponse();
    }
}
