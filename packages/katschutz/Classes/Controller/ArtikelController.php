<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Domain\Model\Artikel;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\BuchungRepository;
use Schmid\Katschutz\Domain\Repository\EinzelstueckRepository;
use Schmid\Katschutz\Domain\Repository\KategorieRepository;
use Schmid\Katschutz\Domain\Repository\LagerortRepository;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Service\BereichService;
use Schmid\Katschutz\Service\Bestand\BuchungPruefer;
use Schmid\Katschutz\Service\BestandService;
use Schmid\Katschutz\Service\BuchungService;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\UebersichtService;
use Schmid\Katschutz\Service\ZugriffService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class ArtikelController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'update', 'archive'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly ArtikelRepository $artikelRepository,
        private readonly KategorieRepository $kategorieRepository,
        private readonly LagerortRepository $lagerortRepository,
        private readonly EinzelstueckRepository $einzelstueckRepository,
        private readonly BuchungRepository $buchungRepository,
        private readonly UebersichtService $uebersicht,
        private readonly BestandService $bestandService,
        private readonly BereichService $bereichService,
        private readonly BuchungService $buchungService,
        private readonly PersistenceManagerInterface $persistenceManager,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(bool $archiv = false): ResponseInterface
    {
        $this->view->assignMultiple([
            'zeilen' => $this->uebersicht->zeilen($archiv ? $this->artikelRepository->findAlleInklusiveArchiv() : $this->artikelRepository->findAll()),
            'archiv' => $archiv,
        ]);
        return $this->htmlResponse();
    }

    public function showAction(Artikel $artikel): ResponseInterface
    {
        $bestand = $this->bestandService->aktuell();
        $namen = $this->bereichService->alleFeuerwehren();
        $heute = new \DateTimeImmutable('today');
        $stuecke = [];
        foreach ($this->einzelstueckRepository->findByArtikelInklusiveArchiv($artikel) as $stueck) {
            $ort = $bestand->einzelstueckOrt((int)$stueck->getUid());
            $stuecke[] = [
                'stueck' => $stueck,
                'ort' => match (true) {
                    $ort === null => 'nicht gebucht',
                    $ort === 0 => 'Halle',
                    $ort < 0 => 'ausgesondert / verloren',
                    default => $namen[$ort] ?? '?',
                },
                'draussen' => $ort !== null && $ort > 0,
                'sperrgrund' => BuchungPruefer::sperrgrund($stueck->alsPruefdaten(), $heute),
            ];
        }
        $this->view->assignMultiple([
            'zeile' => $this->uebersicht->zeilen([$artikel])[0],
            'stuecke' => $stuecke,
            'buchungen' => $this->buchungRepository->findJournal($artikel, 50),
        ]);
        return $this->htmlResponse();
    }

    public function newAction(#[IgnoreValidation] ?Artikel $artikel = null): ResponseInterface
    {
        $this->weiseAuswahlZu();
        $this->view->assign('artikel', $artikel ?? new Artikel());
        return $this->htmlResponse();
    }

    public function createAction(Artikel $artikel, int $anfangsbestand = 0): ResponseInterface
    {
        $artikel->setPid($this->katschutz->storagePid);
        $this->artikelRepository->add($artikel);
        $this->persistenceManager->persistAll();
        if ($anfangsbestand > 0 && !$artikel->isEinzeln()) {
            try {
                $this->buchungService->buche(
                    [new BuchungEingabe(Buchungstyp::Zugang, (int)$artikel->getUid(), $anfangsbestand, bemerkung: 'Anfangsbestand')],
                    $this->zugriff->benutzerId(),
                    $this->lageService->aktiveLageUid(),
                    $this->katschutz->storagePid,
                );
            } catch (BuchungUngueltigException $e) {
                $this->fehler($e->getMessage());
            }
        }
        $this->meldung(sprintf('Artikel „%s“ angelegt.', $artikel->getTitle()));
        if ($artikel->isEinzeln()) {
            $this->meldung('Jetzt die Einzelstücke mit Inventarnummer erfassen.', ContextualFeedbackSeverity::INFO);
        }
        return $this->redirect('show', null, null, ['artikel' => $artikel]);
    }

    public function editAction(#[IgnoreValidation] Artikel $artikel): ResponseInterface
    {
        $this->weiseAuswahlZu();
        $this->view->assign('artikel', $artikel);
        return $this->htmlResponse();
    }

    public function updateAction(Artikel $artikel): ResponseInterface
    {
        $this->artikelRepository->update($artikel);
        $this->meldung(sprintf('Artikel „%s“ gespeichert.', $artikel->getTitle()));
        return $this->redirect('show', null, null, ['artikel' => $artikel]);
    }

    /**
     * Archivieren/Reaktivieren statt Löschen – Buchungen behalten ihren Bezug.
     */
    public function archiveAction(int $artikel): ResponseInterface
    {
        $objekt = $this->artikelRepository->findByUidInklusiveArchiv($artikel);
        if ($objekt === null) {
            return $this->redirect('list');
        }
        if (!$objekt->isHidden() && $this->bestandService->aktuell()->draussenGesamt($artikel) > 0) {
            $this->fehler('Artikel ist noch bei Feuerwehren ausgegeben und kann nicht archiviert werden.');
            return $this->redirect('show', null, null, ['artikel' => $objekt]);
        }
        $objekt->setHidden(!$objekt->isHidden());
        $this->artikelRepository->update($objekt);
        $this->meldung($objekt->isHidden() ? 'Artikel archiviert.' : 'Artikel reaktiviert.');
        return $this->redirect('list', null, null, ['archiv' => $objekt->isHidden()]);
    }

    private function weiseAuswahlZu(): void
    {
        $this->view->assignMultiple([
            'kategorien' => $this->kategorieRepository->findAll(),
            'lagerorte' => $this->lagerortRepository->findAll(),
        ]);
    }
}
