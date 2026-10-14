<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Enum\Bestellstatus;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\BestellungRepository;
use Schmid\Katschutz\Domain\Repository\BuchungRepository;
use Schmid\Katschutz\Domain\Repository\EinzelstueckRepository;
use Schmid\Katschutz\Domain\Repository\ModulRepository;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Exception\EingabeUngueltigException;
use Schmid\Katschutz\Service\BenachrichtigungService;
use Schmid\Katschutz\Service\BereichService;
use Schmid\Katschutz\Service\Bestand\BuchungPruefer;
use Schmid\Katschutz\Service\BestandService;
use Schmid\Katschutz\Service\BestellService;
use Schmid\Katschutz\Service\FeuerwehrOptionen;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\LieferscheinService;
use Schmid\Katschutz\Service\UebersichtService;
use Schmid\Katschutz\Service\ZugriffService;

/**
 * Bestellungen aus Sicht der Verwalter: freigeben, ausgeben, zurücknehmen, umbuchen.
 */
final class BestellverwaltungController extends AbstractVerwaltungController
{
    protected const SCHREIBENDE_ACTIONS = ['create', 'approve', 'reject', 'ausgabeBuchen', 'ruecknahmeBuchen', 'umbuchungBuchen', 'close'];

    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly BestellungRepository $bestellungRepository,
        private readonly BuchungRepository $buchungRepository,
        private readonly ArtikelRepository $artikelRepository,
        private readonly ModulRepository $modulRepository,
        private readonly EinzelstueckRepository $einzelstueckRepository,
        private readonly BestellService $bestellService,
        private readonly BestandService $bestandService,
        private readonly BereichService $bereichService,
        private readonly FeuerwehrOptionen $feuerwehrOptionen,
        private readonly UebersichtService $uebersicht,
        private readonly BenachrichtigungService $benachrichtigung,
        private readonly LieferscheinService $lieferschein,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    public function listAction(string $filter = 'offen'): ResponseInterface
    {
        $status = match ($filter) {
            'neu' => [Bestellstatus::Neu],
            'draussen' => [Bestellstatus::Ausgegeben],
            'alle' => [],
            default => [Bestellstatus::Neu, Bestellstatus::Freigegeben, Bestellstatus::Ausgegeben],
        };
        $this->view->assignMultiple([
            'bestellungen' => $this->bestellungRepository->findByStatus($status),
            'filter' => $filter,
        ]);
        return $this->htmlResponse();
    }

    /**
     * Schnellerfassung durch Verwalter (z.B. telefonische Anforderung in einer Lage).
     */
    public function newAction(): ResponseInterface
    {
        $this->weiseFormularZu(['menge' => [], 'modul' => []]);
        return $this->htmlResponse();
    }

    public function createAction(): ResponseInterface
    {
        $daten = $this->formularDaten('bestellung');
        try {
            $bestellung = $this->bestellService->erstelle($daten, $this->zugriff->benutzerId(), $this->katschutz);
            if ($bestellung->getStatus() === Bestellstatus::Neu) {
                $this->bestellService->gebeFrei($bestellung, $this->zugriff->benutzerId());
            }
        } catch (EingabeUngueltigException $e) {
            $this->weiseFormularZu($daten + ['menge' => [], 'modul' => []]);
            $this->view->assign('fehler', $e->fehler);
            return $this->htmlResponse($this->view->render('Bestellverwaltung/New'));
        }
        $this->meldung(sprintf('Bestellung %s angelegt und freigegeben.', $bestellung->getNummer()));
        return $this->redirect('ausgabe', null, null, ['bestellung' => $bestellung]);
    }

    public function showAction(Bestellung $bestellung): ResponseInterface
    {
        $bestand = $this->bestandService->aktuell();
        $uid = (int)$bestellung->getUid();
        $positionen = [];
        foreach ($bestellung->mengenJeArtikel() as $artikelUid => $menge) {
            $positionen[] = [
                'artikel' => $this->artikelRepository->findByUidInklusiveArchiv($artikelUid),
                'bestellt' => $menge,
                'ausgegeben' => $bestand->ausgegeben($uid, $artikelUid),
                'zurueck' => $bestand->zurueck($uid, $artikelUid),
                'draussen' => array_sum($bestand->draussenFuerBestellung($uid)[$artikelUid] ?? []),
            ];
        }
        $this->view->assignMultiple([
            'bestellung' => $bestellung,
            'positionen' => $positionen,
            'buchungen' => $this->buchungRepository->findByBestellung($bestellung),
            'hatDraussen' => $bestand->hatDraussen($uid),
            'hatAusgaben' => array_sum(array_column($positionen, 'ausgegeben')) > 0,
        ]);
        return $this->htmlResponse();
    }

    public function approveAction(Bestellung $bestellung): ResponseInterface
    {
        try {
            $this->bestellService->gebeFrei($bestellung, $this->zugriff->benutzerId());
            $this->benachrichtigung->statusGeaendert($bestellung, $this->katschutz, $this->request);
            $this->meldung(sprintf('Bestellung %s freigegeben.', $bestellung->getNummer()));
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
        }
        return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
    }

    public function rejectAction(Bestellung $bestellung, string $grund = ''): ResponseInterface
    {
        if (trim($grund) === '') {
            $this->fehler('Bitte einen Ablehnungsgrund angeben.');
            return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
        }
        try {
            $this->bestellService->lehneAb($bestellung, $grund);
            $this->benachrichtigung->statusGeaendert($bestellung, $this->katschutz, $this->request);
            $this->meldung(sprintf('Bestellung %s abgelehnt.', $bestellung->getNummer()));
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
        }
        return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
    }

    public function ausgabeAction(Bestellung $bestellung): ResponseInterface
    {
        $bestand = $this->bestandService->aktuell();
        $uid = (int)$bestellung->getUid();
        $heute = new \DateTimeImmutable('today');
        $zeilen = [];
        foreach ($bestellung->mengenJeArtikel() as $artikelUid => $menge) {
            $artikel = $this->artikelRepository->findByUidInklusiveArchiv($artikelUid);
            if ($artikel === null) {
                continue;
            }
            $offen = max(0, $menge - $bestand->ausgegeben($uid, $artikelUid));
            $stuecke = [];
            if ($artikel->isEinzeln()) {
                foreach ($this->einzelstueckRepository->findByArtikelInklusiveArchiv($artikel) as $stueck) {
                    if ($bestand->einzelstueckOrt((int)$stueck->getUid()) === 0) {
                        $stuecke[] = [
                            'stueck' => $stueck,
                            'sperrgrund' => BuchungPruefer::sperrgrund($stueck->alsPruefdaten(), $heute),
                        ];
                    }
                }
            }
            $zeilen[] = [
                'artikel' => $artikel,
                'bestellt' => $menge,
                'ausgegeben' => $bestand->ausgegeben($uid, $artikelUid),
                'offen' => $offen,
                'halle' => $bestand->inHalle($artikelUid),
                'vorschlag' => min($offen, $bestand->inHalle($artikelUid)),
                'stuecke' => $stuecke,
            ];
        }
        $this->view->assignMultiple(['bestellung' => $bestellung, 'zeilen' => $zeilen]);
        return $this->htmlResponse();
    }

    public function ausgabeBuchenAction(Bestellung $bestellung): ResponseInterface
    {
        $daten = $this->formularDaten('ausgabe');
        try {
            $this->bestellService->gebeAus(
                $bestellung,
                (array)($daten['menge'] ?? []),
                array_values((array)($daten['stueck'] ?? [])),
                (string)($daten['bemerkung'] ?? ''),
                $this->zugriff->benutzerId(),
                $this->katschutz,
            );
            $this->meldung('Ausgabe gebucht. Lieferschein kann jetzt gedruckt werden.');
            return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
            return $this->redirect('ausgabe', null, null, ['bestellung' => $bestellung]);
        }
    }

    public function ruecknahmeAction(Bestellung $bestellung): ResponseInterface
    {
        $bestand = $this->bestandService->aktuell();
        $uid = (int)$bestellung->getUid();
        $namen = $this->bereichService->alleFeuerwehren();
        $leihgut = [];
        foreach ($bestand->draussenFuerBestellung($uid) as $artikelUid => $proFeuerwehr) {
            $artikel = $this->artikelRepository->findByUidInklusiveArchiv($artikelUid);
            if ($artikel === null || $artikel->isEinzeln()) {
                continue;
            }
            foreach ($proFeuerwehr as $feuerwehr => $menge) {
                $leihgut[] = ['artikel' => $artikel, 'feuerwehr' => $feuerwehr, 'feuerwehrName' => $namen[$feuerwehr] ?? '?', 'menge' => $menge];
            }
        }
        $stuecke = [];
        foreach ($bestand->einzelstueckeDraussen($uid) as $stueckUid) {
            $stueck = $this->einzelstueckRepository->findByUidInklusiveArchiv($stueckUid);
            if ($stueck !== null) {
                $stuecke[] = ['stueck' => $stueck, 'feuerwehrName' => $namen[(int)$bestand->einzelstueckOrt($stueckUid)] ?? '?'];
            }
        }
        $verbrauch = [];
        foreach ($bestellung->mengenJeArtikel() as $artikelUid => $menge) {
            $artikel = $this->artikelRepository->findByUidInklusiveArchiv($artikelUid);
            $rest = $bestand->ausgegeben($uid, $artikelUid) - $bestand->zurueck($uid, $artikelUid);
            if ($artikel !== null && $artikel->isVerbrauch() && $rest > 0) {
                $verbrauch[] = ['artikel' => $artikel, 'menge' => $rest];
            }
        }
        $this->view->assignMultiple([
            'bestellung' => $bestellung,
            'leihgut' => $leihgut,
            'stuecke' => $stuecke,
            'verbrauch' => $verbrauch,
        ]);
        return $this->htmlResponse();
    }

    public function ruecknahmeBuchenAction(Bestellung $bestellung): ResponseInterface
    {
        $daten = $this->formularDaten('ruecknahme');
        $zurueck = (array)($daten['zurueck'] ?? []);
        // Restmengen Verbrauchsmaterial kommen von der anfordernden Feuerwehr zurück
        foreach ((array)($daten['verbrauch'] ?? []) as $artikelUid => $menge) {
            $zurueck[$artikelUid][(int)$bestellung->getFeuerwehr()?->getUid()] = $menge;
        }
        try {
            $this->bestellService->nimmZurueck(
                $bestellung,
                $zurueck,
                (array)($daten['verlust'] ?? []),
                (array)($daten['stueck'] ?? []),
                (array)($daten['zustand'] ?? []),
                (string)($daten['bemerkung'] ?? ''),
                $this->zugriff->benutzerId(),
                $this->katschutz,
            );
            $this->meldung('Rücknahme gebucht.');
            return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
            return $this->redirect('ruecknahme', null, null, ['bestellung' => $bestellung]);
        }
    }

    public function umbuchungAction(Bestellung $bestellung): ResponseInterface
    {
        $bestand = $this->bestandService->aktuell();
        $uid = (int)$bestellung->getUid();
        $namen = $this->bereichService->alleFeuerwehren();
        $quellen = [];
        foreach ($bestand->draussenFuerBestellung($uid) as $artikelUid => $proFeuerwehr) {
            $artikel = $this->artikelRepository->findByUidInklusiveArchiv($artikelUid);
            if ($artikel === null || $artikel->isEinzeln()) {
                continue;
            }
            foreach ($proFeuerwehr as $feuerwehr => $menge) {
                $quellen['m-' . $artikelUid . '-' . $feuerwehr] = sprintf('%s – %d %s bei %s', $artikel->getTitle(), $menge, $artikel->getEinheit(), $namen[$feuerwehr] ?? '?');
            }
        }
        foreach ($bestand->einzelstueckeDraussen($uid) as $stueckUid) {
            $stueck = $this->einzelstueckRepository->findByUidInklusiveArchiv($stueckUid);
            if ($stueck !== null) {
                $quellen['e-' . $stueckUid] = sprintf('%s %s bei %s', $stueck->getArtikel()?->getTitle(), $stueck->getInventarnummer(), $namen[(int)$bestand->einzelstueckOrt($stueckUid)] ?? '?');
            }
        }
        $this->view->assignMultiple([
            'bestellung' => $bestellung,
            'quellen' => $quellen,
            'feuerwehrGruppen' => $this->feuerwehrOptionen->gruppiere($namen, null),
        ]);
        return $this->htmlResponse();
    }

    public function umbuchungBuchenAction(Bestellung $bestellung): ResponseInterface
    {
        $daten = $this->formularDaten('umbuchung');
        $quelle = explode('-', (string)($daten['quelle'] ?? ''));
        try {
            if ($quelle[0] === 'e' && isset($quelle[1])) {
                $this->bestellService->bucheUm($bestellung, 0, (int)$quelle[1], 0, (int)($daten['ziel'] ?? 0), 1, (string)($daten['bemerkung'] ?? ''), $this->zugriff->benutzerId(), $this->katschutz);
            } elseif ($quelle[0] === 'm' && isset($quelle[2])) {
                $this->bestellService->bucheUm($bestellung, (int)$quelle[1], 0, (int)$quelle[2], (int)($daten['ziel'] ?? 0), (int)($daten['menge'] ?? 0), (string)($daten['bemerkung'] ?? ''), $this->zugriff->benutzerId(), $this->katschutz);
            } else {
                throw new BuchungUngueltigException('Bitte auswählen, was umgebucht werden soll.');
            }
            $this->meldung('Umbuchung gebucht.');
            return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
            return $this->redirect('umbuchung', null, null, ['bestellung' => $bestellung]);
        }
    }

    public function closeAction(Bestellung $bestellung): ResponseInterface
    {
        try {
            $this->bestellService->schliesseAb($bestellung);
            $this->meldung(sprintf('Bestellung %s abgeschlossen.', $bestellung->getNummer()));
        } catch (BuchungUngueltigException $e) {
            $this->fehler($e->getMessage());
        }
        return $this->redirect('show', null, null, ['bestellung' => $bestellung]);
    }

    public function lieferscheinAction(Bestellung $bestellung, string $art = 'lieferschein'): ResponseInterface
    {
        $this->lieferschein->sendePdf($bestellung, $art === 'ruecknahme' ? 'ruecknahme' : 'lieferschein', $this->request);
    }

    /**
     * @param array<string, mixed> $daten
     */
    private function weiseFormularZu(array $daten): void
    {
        $this->view->assignMultiple([
            'feuerwehrGruppen' => $this->feuerwehrOptionen->gruppiere($this->bereichService->alleFeuerwehren(), null),
            'gruppen' => $this->uebersicht->zeilenNachKategorie($this->artikelRepository->findAll()),
            'module' => $this->modulRepository->findAll(),
            'daten' => $daten,
        ]);
    }
}
