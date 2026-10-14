<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Model\Lage;
use Schmid\Katschutz\Domain\Repository\BestellungRepository;
use Schmid\Katschutz\Domain\Repository\BuchungRepository;
use Schmid\Katschutz\Domain\Repository\LageRepository;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\VerbleibService;
use Schmid\Katschutz\Service\ZugriffService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;

/**
 * Großschadenslage: Während einer aktiven Lage entfällt die Freigabe und alle
 * Bestellungen/Buchungen werden der Lage zugeordnet.
 */
final class LageController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'end'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly LageRepository $lageRepository,
        private readonly BestellungRepository $bestellungRepository,
        private readonly BuchungRepository $buchungRepository,
        private readonly VerbleibService $verbleib,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(): ResponseInterface
    {
        $this->view->assign('lagen', $this->lageRepository->findAll());
        return $this->htmlResponse();
    }

    public function showAction(Lage $lage): ResponseInterface
    {
        $bestellungen = $this->bestellungRepository->findByLage($lage);
        $this->view->assignMultiple([
            'eintrag' => $lage,
            'bestellungen' => $bestellungen,
            'buchungen' => $this->buchungRepository->findByLage($lage),
            'verbleib' => $this->verbleib->nachFeuerwehr($bestellungen),
        ]);
        return $this->htmlResponse();
    }

    public function newAction(#[IgnoreValidation] ?Lage $eintrag = null): ResponseInterface
    {
        if ($this->lageService->aktiveLage() !== null) {
            $this->fehler('Es ist bereits eine Großschadenslage aktiv.');
            return $this->redirect('list');
        }
        $this->view->assign('eintrag', $eintrag ?? new Lage());
        return $this->htmlResponse();
    }

    public function createAction(Lage $eintrag): ResponseInterface
    {
        if ($this->lageService->aktiveLage() !== null) {
            $this->fehler('Es ist bereits eine Großschadenslage aktiv.');
            return $this->redirect('list');
        }
        $eintrag->setPid($this->katschutz->storagePid);
        $eintrag->setAktiv(true);
        $eintrag->setBeginn(new \DateTimeImmutable());
        $this->lageRepository->add($eintrag);
        $this->meldung(
            sprintf('Großschadenslage „%s“ aktiviert. Bestellungen sind ab sofort ohne Freigabe gültig.', $eintrag->getTitle()),
            ContextualFeedbackSeverity::WARNING,
        );
        return $this->redirect('index', 'Dashboard');
    }

    public function endAction(Lage $eintrag): ResponseInterface
    {
        $eintrag->setAktiv(false);
        $eintrag->setEnde(new \DateTimeImmutable());
        $this->lageRepository->update($eintrag);
        $this->meldung(sprintf('Großschadenslage „%s“ beendet. Freigaben gelten wieder wie konfiguriert.', $eintrag->getTitle()));
        return $this->redirect('show', null, null, ['lage' => $eintrag]);
    }
}
