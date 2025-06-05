<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class JubilaeumController extends ActionController {
    protected $jubilaeumRepository;
    public function injectJubilaeumRepository(\Schmid\Feuerwehren\Domain\Repository\JubilaeumRepository $jubilaeumRepository) {
        $this->jubilaeumRepository = $jubilaeumRepository;
    }
    public function listAction() {
        $jubilaeen = $this->jubilaeumRepository->findAll();
        $this->view->assign('jubilaeen', $jubilaeen);
    }
    public function showAction(\Schmid\Feuerwehren\Domain\Model\Jubilaeum $jubilaeum) {
        $this->view->assign('jubilaeum', $jubilaeum);
    }
}