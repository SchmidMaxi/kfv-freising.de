<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Schmid\Feuerwehren\Domain\Repository\JubilaeumRepository;

final class JubilaeumController extends ActionController
{
    public function __construct(protected JubilaeumRepository $jubilaeumRepository) {}

    public function listAction(): ResponseInterface
    {
        $this->view->assign('jubilaeen', $this->jubilaeumRepository->findAll());
        return $this->htmlResponse();
    }

    public function showAction(\Schmid\Feuerwehren\Domain\Model\Jubilaeum $jubilaeum): ResponseInterface
    {
        $this->view->assign('jubilaeum', $jubilaeum);
        return $this->htmlResponse();
    }
}
