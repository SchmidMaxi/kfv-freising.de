<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Model\Einzelstueck;
use Schmid\Katschutz\Domain\Model\Pruefung;
use Schmid\Katschutz\Domain\Repository\EinzelstueckRepository;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\ZugriffService;

final class PruefungController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly EinzelstueckRepository $einzelstueckRepository,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(): ResponseInterface
    {
        $heute = new \DateTimeImmutable('today');
        $this->view->assignMultiple([
            'faellig' => $this->einzelstueckRepository->findPruefungFaelligBis(
                $heute->modify(sprintf('+%d days', $this->katschutz->pruefungVorlaufTage)),
            ),
            'nichtEinsatzbereit' => $this->einzelstueckRepository->findNichtEinsatzbereit(),
            'heute' => $heute,
            'vorlauf' => $this->katschutz->pruefungVorlaufTage,
        ]);
        return $this->htmlResponse();
    }

    public function newAction(Einzelstueck $einzelstueck): ResponseInterface
    {
        $this->view->assignMultiple([
            'einzelstueck' => $einzelstueck,
            'heute' => new \DateTimeImmutable('today'),
        ]);
        return $this->htmlResponse();
    }

    /**
     * Prüfung erfassen: setzt nächsten Termin (Datum + Intervall) und den Zustand
     * (bestanden → einsatzbereit, Mangel → gesperrt bis zur nächsten bestandenen Prüfung).
     */
    public function createAction(Einzelstueck $einzelstueck): ResponseInterface
    {
        $daten = $this->formularDaten('pruefung');
        $datum = \DateTimeImmutable::createFromFormat('!Y-m-d', (string)($daten['datum'] ?? ''));
        if ($datum === false || $datum > new \DateTimeImmutable('tomorrow')) {
            $this->fehler('Bitte ein gültiges Prüfdatum (nicht in der Zukunft) angeben.');
            return $this->redirect('new', null, null, ['einzelstueck' => $einzelstueck]);
        }
        $pruefung = new Pruefung();
        $pruefung->setPid($this->katschutz->storagePid);
        $pruefung->setDatum($datum);
        $pruefung->setErgebnis((string)($daten['ergebnis'] ?? 'bestanden'));
        $pruefung->setPruefer((string)($daten['pruefer'] ?? ''));
        $pruefung->setBemerkung((string)($daten['bemerkung'] ?? ''));
        $einzelstueck->addPruefung($pruefung);

        $intervall = (int)$einzelstueck->getArtikel()?->getPruefintervall();
        if ($pruefung->isBestanden()) {
            $einzelstueck->setZustand('ok');
            if ($intervall > 0) {
                $einzelstueck->setNaechstePruefung($datum->modify(sprintf('+%d months', $intervall)));
            }
        } else {
            $einzelstueck->setZustand('mangel');
        }
        $this->einzelstueckRepository->update($einzelstueck);
        $this->meldung(sprintf(
            'Prüfung für %s erfasst%s.',
            $einzelstueck->getInventarnummer(),
            $pruefung->isBestanden() ? '' : ' – Stück ist bis zur Mängelbehebung gesperrt',
        ));
        return $this->redirect('show', 'Artikel', null, ['artikel' => $einzelstueck->getArtikel()]);
    }
}
