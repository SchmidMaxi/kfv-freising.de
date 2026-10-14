<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Model\Modul;
use Schmid\Katschutz\Domain\Model\ModulPosition;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\ModulRepository;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\ZugriffService;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;

/**
 * Module/Sets: Titel per Property-Mapping, Inhalt als Menge je Artikel (inhalt[artikel] = menge).
 */
final class ModulController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'update', 'archive'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly ModulRepository $modulRepository,
        private readonly ArtikelRepository $artikelRepository,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(): ResponseInterface
    {
        $this->view->assign('module', $this->modulRepository->findAlleInklusiveArchiv());
        return $this->htmlResponse();
    }

    public function newAction(#[IgnoreValidation] ?Modul $modul = null): ResponseInterface
    {
        $this->weiseZu($modul ?? new Modul());
        return $this->htmlResponse();
    }

    public function createAction(Modul $modul): ResponseInterface
    {
        $modul->setPid($this->katschutz->storagePid);
        $this->uebernehmeInhalt($modul);
        $this->modulRepository->add($modul);
        $this->meldung(sprintf('Modul „%s“ angelegt.', $modul->getTitle()));
        return $this->redirect('list');
    }

    public function editAction(#[IgnoreValidation] Modul $modul): ResponseInterface
    {
        $this->weiseZu($modul);
        return $this->htmlResponse();
    }

    public function updateAction(Modul $modul): ResponseInterface
    {
        $this->uebernehmeInhalt($modul);
        $this->modulRepository->update($modul);
        $this->meldung(sprintf('Modul „%s“ gespeichert.', $modul->getTitle()));
        return $this->redirect('list');
    }

    public function archiveAction(int $modul): ResponseInterface
    {
        $objekt = $this->modulRepository->findByUidInklusiveArchiv($modul);
        if ($objekt !== null) {
            $objekt->setHidden(!$objekt->isHidden());
            $this->modulRepository->update($objekt);
            $this->meldung($objekt->isHidden() ? 'Modul archiviert.' : 'Modul reaktiviert.');
        }
        return $this->redirect('list');
    }

    private function weiseZu(Modul $modul): void
    {
        $mengen = [];
        foreach ($modul->getPositionen() as $position) {
            $mengen[(int)$position->getArtikel()?->getUid()] = $position->getMenge();
        }
        $this->view->assignMultiple([
            'modul' => $modul,
            'artikelListe' => $this->artikelRepository->findAll(),
            'mengen' => $mengen,
        ]);
    }

    private function uebernehmeInhalt(Modul $modul): void
    {
        $inhalt = $this->formularDaten('inhalt');
        foreach ($modul->getPositionen()->toArray() as $alt) {
            $modul->removePosition($alt);
        }
        foreach ($inhalt as $artikelUid => $menge) {
            $menge = (int)$menge;
            $artikel = $menge > 0 ? $this->artikelRepository->findByUid((int)$artikelUid) : null;
            if ($artikel === null) {
                continue;
            }
            $position = new ModulPosition();
            $position->setPid($this->katschutz->storagePid);
            $position->setArtikel($artikel);
            $position->setMenge($menge);
            $modul->addPosition($position);
        }
    }
}
