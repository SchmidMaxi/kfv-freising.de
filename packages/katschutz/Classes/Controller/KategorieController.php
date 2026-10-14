<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Model\Kategorie;
use Schmid\Katschutz\Domain\Repository\KategorieRepository;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\ZugriffService;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;

final class KategorieController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'update', 'archive'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly KategorieRepository $repository,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(): ResponseInterface
    {
        $this->view->assign('eintraege', $this->repository->findAlleInklusiveArchiv());
        return $this->htmlResponse();
    }

    public function newAction(#[IgnoreValidation] ?Kategorie $kategorie = null): ResponseInterface
    {
        $this->view->assign('kategorie', $kategorie ?? new Kategorie());
        return $this->htmlResponse();
    }

    public function createAction(Kategorie $kategorie): ResponseInterface
    {
        $kategorie->setPid($this->katschutz->storagePid);
        $this->repository->add($kategorie);
        $this->meldung(sprintf('„%s“ angelegt.', $kategorie->getTitle()));
        return $this->redirect('list');
    }

    public function editAction(#[IgnoreValidation] Kategorie $kategorie): ResponseInterface
    {
        $this->view->assign('kategorie', $kategorie);
        return $this->htmlResponse();
    }

    public function updateAction(Kategorie $kategorie): ResponseInterface
    {
        $this->repository->update($kategorie);
        $this->meldung(sprintf('„%s“ gespeichert.', $kategorie->getTitle()));
        return $this->redirect('list');
    }

    public function archiveAction(int $kategorie): ResponseInterface
    {
        $objekt = $this->repository->findByUidInklusiveArchiv($kategorie);
        if ($objekt !== null) {
            $objekt->setHidden(!$objekt->isHidden());
            $this->repository->update($objekt);
            $this->meldung($objekt->isHidden() ? 'Archiviert.' : 'Reaktiviert.');
        }
        return $this->redirect('list');
    }
}
