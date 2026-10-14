<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\BuchungRepository;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Service\BuchungService;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\ZugriffService;

/**
 * Lagerbuchungen ohne Bestellung (Zugang, Inventur-Korrektur, Aussonderung, Verlust in der Halle)
 * und das Journal. Einzelstücke werden über Einzelstück-Erfassung/-Aussonderung gebucht.
 */
final class BuchungController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly BuchungRepository $buchungRepository,
        private readonly ArtikelRepository $artikelRepository,
        private readonly BuchungService $buchungService,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(int $artikel = 0): ResponseInterface
    {
        $artikelObjekt = $artikel > 0 ? $this->artikelRepository->findByUidInklusiveArchiv($artikel) : null;
        $this->view->assignMultiple([
            'buchungen' => $this->buchungRepository->findJournal($artikelObjekt),
            'artikel' => $artikelObjekt,
        ]);
        return $this->htmlResponse();
    }

    public function newAction(int $artikel = 0): ResponseInterface
    {
        $mengenartikel = [];
        foreach ($this->artikelRepository->findAll() as $a) {
            if (!$a->isEinzeln()) {
                $mengenartikel[] = $a;
            }
        }
        $typen = [];
        foreach (Buchungstyp::lagerbuchungen() as $typ) {
            $typen[$typ->value] = $typ->getLabel();
        }
        $this->view->assignMultiple([
            'artikelListe' => $mengenartikel,
            'typen' => $typen,
            'artikel' => $artikel,
        ]);
        return $this->htmlResponse();
    }

    public function createAction(): ResponseInterface
    {
        $daten = $this->formularDaten('buchung');
        $typ = Buchungstyp::tryFrom((string)($daten['typ'] ?? ''));
        $artikel = (int)($daten['artikel'] ?? 0);
        try {
            if ($typ === null || !in_array($typ, Buchungstyp::lagerbuchungen(), true)) {
                throw new BuchungUngueltigException('Bitte eine Buchungsart wählen.');
            }
            $bemerkung = trim((string)($daten['bemerkung'] ?? ''));
            if ($typ !== Buchungstyp::Zugang && $bemerkung === '') {
                throw new BuchungUngueltigException('Bitte bei Korrektur, Aussonderung und Verlust eine Begründung angeben.');
            }
            $this->buchungService->buche(
                [new BuchungEingabe($typ, $artikel, (int)($daten['menge'] ?? 0), bemerkung: $bemerkung)],
                $this->zugriff->benutzerId(),
                $this->lageService->aktiveLageUid(),
                $this->katschutz->storagePid,
            );
            $this->meldung(sprintf('%s gebucht.', $typ->getLabel()));
            return $this->redirect('list');
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
            return $this->redirect('new', null, null, ['artikel' => $artikel]);
        }
    }
}
