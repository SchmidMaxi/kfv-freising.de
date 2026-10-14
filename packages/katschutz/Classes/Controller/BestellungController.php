<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\BestellungRepository;
use Schmid\Katschutz\Domain\Repository\BuchungRepository;
use Schmid\Katschutz\Domain\Repository\ModulRepository;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Exception\EingabeUngueltigException;
use Schmid\Katschutz\Service\BenachrichtigungService;
use Schmid\Katschutz\Service\BereichService;
use Schmid\Katschutz\Service\BestellService;
use Schmid\Katschutz\Service\FeuerwehrOptionen;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\LieferscheinService;
use Schmid\Katschutz\Service\UebersichtService;
use Schmid\Katschutz\Service\ZugriffService;

/**
 * Bestellungen aus Sicht der Inspektion: anlegen, eigene Bestellungen ansehen, stornieren.
 */
final class BestellungController extends AbstractKatschutzController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'cancel'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly ArtikelRepository $artikelRepository,
        private readonly ModulRepository $modulRepository,
        private readonly BestellungRepository $bestellungRepository,
        private readonly BuchungRepository $buchungRepository,
        private readonly BereichService $bereichService,
        private readonly FeuerwehrOptionen $feuerwehrOptionen,
        private readonly UebersichtService $uebersicht,
        private readonly BestellService $bestellService,
        private readonly BenachrichtigungService $benachrichtigung,
        private readonly LieferscheinService $lieferschein,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    protected function hatZugriff(): bool
    {
        return $this->zugriff->darfBestellen($this->katschutz);
    }

    public function newAction(): ResponseInterface
    {
        $this->weiseFormularZu(['menge' => [], 'modul' => []]);
        return $this->htmlResponse();
    }

    public function createAction(): ResponseInterface
    {
        $daten = $this->request->hasArgument('bestellung') ? (array)$this->request->getArgument('bestellung') : [];
        try {
            $bestellung = $this->bestellService->erstelle($daten, $this->zugriff->benutzerId(), $this->katschutz);
        } catch (EingabeUngueltigException $e) {
            $this->weiseFormularZu($daten + ['menge' => [], 'modul' => []]);
            $this->view->assign('fehler', $e->fehler);
            return $this->htmlResponse($this->view->render('Bestellung/New'));
        }
        $this->benachrichtigung->neueBestellung($bestellung, $this->katschutz, $this->request);
        $this->meldung(sprintf(
            'Bestellung %s wurde angelegt%s.',
            $bestellung->getNummer(),
            $bestellung->getStatus()->value === 'neu' ? ' und wartet auf Freigabe' : ' und ist freigegeben',
        ));
        return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
    }

    public function listAction(): ResponseInterface
    {
        $this->view->assign('bestellungen', $this->bestellungRepository->findByBestellerUid($this->zugriff->benutzerId()));
        return $this->htmlResponse();
    }

    public function showAction(Bestellung $bestellung): ResponseInterface
    {
        $this->pruefeEigentum($bestellung);
        $this->view->assignMultiple([
            'bestellung' => $bestellung,
            'buchungen' => $this->buchungRepository->findByBestellung($bestellung),
        ]);
        return $this->htmlResponse();
    }

    public function cancelAction(Bestellung $bestellung): ResponseInterface
    {
        $this->pruefeEigentum($bestellung);
        try {
            $this->bestellService->storniere($bestellung);
            $this->meldung(sprintf('Bestellung %s wurde storniert.', $bestellung->getNummer()));
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
        }
        return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
    }

    public function lieferscheinAction(Bestellung $bestellung): ResponseInterface
    {
        $this->pruefeEigentum($bestellung);
        $this->lieferschein->sendePdf($bestellung, 'lieferschein', $this->request);
    }

    /**
     * @param array<string, mixed> $daten
     */
    private function weiseFormularZu(array $daten): void
    {
        $feUser = $this->zugriff->benutzerId();
        $this->view->assignMultiple([
            'feuerwehrGruppen' => $this->feuerwehrOptionen->gruppiere(
                $this->bereichService->alleFeuerwehren(),
                $this->bereichService->feuerwehrenImBereich($feUser),
            ),
            'gruppen' => $this->uebersicht->zeilenNachKategorie($this->artikelRepository->findAll()),
            'module' => $this->modulRepository->findAll(),
            'daten' => $daten,
        ]);
    }

    /** Inspektion sieht nur eigene Bestellungen, Verwalter alle. */
    private function pruefeEigentum(Bestellung $bestellung): void
    {
        if ($this->zugriff->istVerwalter($this->katschutz)) {
            return;
        }
        if ((int)$bestellung->getBesteller()?->getUid() !== $this->zugriff->benutzerId()) {
            throw new \TYPO3\CMS\Core\Http\PropagateResponseException(
                $this->htmlResponse('<div class="alert alert-danger">Keine Berechtigung für diese Bestellung.</div>')->withStatus(403),
                1760439010,
            );
        }
    }
}
