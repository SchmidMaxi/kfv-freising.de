<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Model\Lagerort;
use Schmid\Katschutz\Domain\Repository\LagerortRepository;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\ZugriffService;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;

final class LagerortController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'update', 'archive'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly LagerortRepository $repository,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(): ResponseInterface
    {
        $this->view->assign('eintraege', $this->repository->findAlleInklusiveArchiv());
        return $this->htmlResponse();
    }

    public function newAction(#[IgnoreValidation] ?Lagerort $lagerort = null): ResponseInterface
    {
        $this->view->assign('lagerort', $lagerort ?? new Lagerort());
        return $this->htmlResponse();
    }

    public function createAction(Lagerort $lagerort): ResponseInterface
    {
        $lagerort->setPid($this->katschutz->storagePid);
        $this->repository->add($lagerort);
        $this->meldung(sprintf('„%s“ angelegt.', $lagerort->getTitle()));
        return $this->redirect('list');
    }

    public function editAction(#[IgnoreValidation] Lagerort $lagerort): ResponseInterface
    {
        $this->view->assign('lagerort', $lagerort);
        return $this->htmlResponse();
    }

    public function updateAction(Lagerort $lagerort): ResponseInterface
    {
        $this->repository->update($lagerort);
        $this->meldung(sprintf('„%s“ gespeichert.', $lagerort->getTitle()));
        return $this->redirect('list');
    }

    public function archiveAction(int $lagerort): ResponseInterface
    {
        $objekt = $this->repository->findByUidInklusiveArchiv($lagerort);
        if ($objekt !== null) {
            $objekt->setHidden(!$objekt->isHidden());
            $this->repository->update($objekt);
            $this->meldung($objekt->isHidden() ? 'Archiviert.' : 'Reaktiviert.');
        }
        return $this->redirect('list');
    }
}
