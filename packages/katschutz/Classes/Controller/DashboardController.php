<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Enum\Bestellstatus;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\BestellungRepository;
use Schmid\Katschutz\Domain\Repository\EinzelstueckRepository;
use Schmid\Katschutz\Service\BestandService;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\UebersichtService;
use Schmid\Katschutz\Service\VerbleibService;
use Schmid\Katschutz\Service\ZugriffService;

final class DashboardController extends AbstractVerwaltungController
{
    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly ArtikelRepository $artikelRepository,
        private readonly BestellungRepository $bestellungRepository,
        private readonly EinzelstueckRepository $einzelstueckRepository,
        private readonly UebersichtService $uebersicht,
        private readonly VerbleibService $verbleib,
        private readonly BestandService $bestandService,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function indexAction(): ResponseInterface
    {
        $gruppen = $this->uebersicht->zeilenNachKategorie($this->artikelRepository->findAll());
        $unterMindestbestand = [];
        foreach ($gruppen as $zeilen) {
            foreach ($zeilen as $zeile) {
                if ($zeile['unterMindestbestand']) {
                    $unterMindestbestand[] = $zeile;
                }
            }
        }

        $bestand = $this->bestandService->aktuell();
        $ueberfaellig = [];
        foreach ($this->bestellungRepository->findByStatus([Bestellstatus::Ausgegeben]) as $bestellung) {
            if ($bestellung->isRueckgabeUeberfaellig() && $bestand->hatDraussen((int)$bestellung->getUid())) {
                $ueberfaellig[] = $bestellung;
            }
        }

        $stichtag = (new \DateTimeImmutable('today'))->modify(sprintf('+%d days', $this->katschutz->pruefungVorlaufTage));
        $verbleib = $this->verbleib->nachFeuerwehr();

        $this->view->assignMultiple([
            'gruppen' => $gruppen,
            'anfragen' => $this->bestellungRepository->findByStatus([Bestellstatus::Neu]),
            'bereit' => $this->bestellungRepository->findByStatus([Bestellstatus::Freigegeben]),
            'verbleib' => $verbleib,
            'anzahlFeuerwehrenMitMaterial' => count($verbleib),
            'unterMindestbestand' => $unterMindestbestand,
            'ueberfaellig' => $ueberfaellig,
            'pruefungFaellig' => $this->einzelstueckRepository->findPruefungFaelligBis($stichtag),
            'nichtEinsatzbereit' => $this->einzelstueckRepository->findNichtEinsatzbereit(),
            'heute' => new \DateTimeImmutable('today'),
        ]);
        return $this->htmlResponse();
    }
}
