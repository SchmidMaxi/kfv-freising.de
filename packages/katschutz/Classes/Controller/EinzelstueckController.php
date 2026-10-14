<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Domain\Model\Artikel;
use Schmid\Katschutz\Domain\Model\Einzelstueck;
use Schmid\Katschutz\Domain\Repository\EinzelstueckRepository;
use Schmid\Katschutz\Domain\Repository\LagerortRepository;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Service\BestandService;
use Schmid\Katschutz\Service\BuchungService;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\ZugriffService;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class EinzelstueckController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'update', 'archive'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly EinzelstueckRepository $einzelstueckRepository,
        private readonly LagerortRepository $lagerortRepository,
        private readonly BuchungService $buchungService,
        private readonly BestandService $bestandService,
        private readonly PersistenceManagerInterface $persistenceManager,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function newAction(Artikel $artikel, #[IgnoreValidation] ?Einzelstueck $einzelstueck = null): ResponseInterface
    {
        $this->view->assignMultiple([
            'artikel' => $artikel,
            'einzelstueck' => $einzelstueck ?? new Einzelstueck(),
            'lagerorte' => $this->lagerortRepository->findAll(),
        ]);
        return $this->htmlResponse();
    }

    /**
     * Legt das Stück an und bucht es als Zugang in die Halle.
     */
    public function createAction(Artikel $artikel, Einzelstueck $einzelstueck): ResponseInterface
    {
        if (!$artikel->isEinzeln()) {
            $this->fehler('Dieser Artikel wird nur als Menge geführt.');
            return $this->redirect('show', 'Artikel', null, ['artikel' => $artikel]);
        }
        $einzelstueck->setArtikel($artikel);
        $einzelstueck->setPid($this->katschutz->storagePid);
        if ($einzelstueck->getNaechstePruefung() === null && $artikel->getPruefintervall() > 0) {
            $einzelstueck->setNaechstePruefung(new \DateTimeImmutable('today'));
        }
        $this->einzelstueckRepository->add($einzelstueck);
        $this->persistenceManager->persistAll();
        try {
            $this->buchungService->buche(
                [new BuchungEingabe(Buchungstyp::Zugang, (int)$artikel->getUid(), 1, einzelstueck: (int)$einzelstueck->getUid(), bemerkung: 'Erfassung')],
                $this->zugriff->benutzerId(),
                $this->lageService->aktiveLageUid(),
                $this->katschutz->storagePid,
            );
            $this->meldung(sprintf('Einzelstück %s erfasst.', $einzelstueck->getInventarnummer()));
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
        }
        return $this->redirect('show', 'Artikel', null, ['artikel' => $artikel]);
    }

    public function editAction(#[IgnoreValidation] Einzelstueck $einzelstueck): ResponseInterface
    {
        $this->view->assignMultiple([
            'einzelstueck' => $einzelstueck,
            'lagerorte' => $this->lagerortRepository->findAll(),
        ]);
        return $this->htmlResponse();
    }

    public function updateAction(Einzelstueck $einzelstueck): ResponseInterface
    {
        $this->einzelstueckRepository->update($einzelstueck);
        $this->meldung(sprintf('Einzelstück %s gespeichert.', $einzelstueck->getInventarnummer()));
        return $this->redirect('show', 'Artikel', null, ['artikel' => $einzelstueck->getArtikel()]);
    }

    /**
     * Aussondern: bucht das Stück aus der Halle aus und archiviert es.
     */
    public function archiveAction(int $einzelstueck, string $grund = ''): ResponseInterface
    {
        $stueck = $this->einzelstueckRepository->findByUidInklusiveArchiv($einzelstueck);
        if ($stueck === null || $stueck->isHidden()) {
            return $this->redirect('list', 'Artikel');
        }
        try {
            if ($this->bestandService->aktuell()->einzelstueckOrt($einzelstueck) === 0) {
                $this->buchungService->buche(
                    [new BuchungEingabe(Buchungstyp::Aussonderung, (int)$stueck->getArtikel()?->getUid(), 1, einzelstueck: $einzelstueck, bemerkung: $grund)],
                    $this->zugriff->benutzerId(),
                    $this->lageService->aktiveLageUid(),
                    $this->katschutz->storagePid,
                );
            } elseif ((int)$this->bestandService->aktuell()->einzelstueckOrt($einzelstueck) > 0) {
                throw new BuchungUngueltigException('Das Stück ist ausgegeben – bitte erst zurücknehmen oder als Verlust buchen.');
            }
            $stueck->setHidden(true);
            $this->einzelstueckRepository->update($stueck);
            $this->meldung(sprintf('Einzelstück %s ausgesondert.', $stueck->getInventarnummer()));
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
        }
        return $this->redirect('show', 'Artikel', null, ['artikel' => $stueck->getArtikel()]);
    }
}
