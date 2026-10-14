<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use Schmid\Katschutz\Domain\Dto\BuchungEingabe;
use Schmid\Katschutz\Domain\Enum\Bestellstatus;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Model\Einzelstueck;
use Schmid\Katschutz\Domain\Model\Position;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\BestellungRepository;
use Schmid\Katschutz\Domain\Repository\EinzelstueckRepository;
use Schmid\Katschutz\Domain\Repository\FrontendUserRepository;
use Schmid\Katschutz\Domain\Repository\ModulRepository;
use Schmid\Katschutz\Exception\BuchungUngueltigException;
use Schmid\Katschutz\Exception\EingabeUngueltigException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Ablauf einer Bestellung: anlegen → freigeben/ablehnen → ausgeben → zurücknehmen → abschließen.
 */
final readonly class BestellService
{
    private const MAX_MENGE = 100000;

    public function __construct(
        private BestellungRepository $bestellungRepository,
        private ArtikelRepository $artikelRepository,
        private ModulRepository $modulRepository,
        private EinzelstueckRepository $einzelstueckRepository,
        private FrontendUserRepository $frontendUserRepository,
        private BereichService $bereichService,
        private BuchungService $buchungService,
        private BestandService $bestandService,
        private LageService $lageService,
        private PersistenceManagerInterface $persistenceManager,
    ) {}

    /**
     * @param array<string, mixed> $daten Formulardaten (feuerwehr, anlass, einsatzabschnitt, von, bis, notiz, menge[], modul[])
     *
     * @throws EingabeUngueltigException
     */
    public function erstelle(array $daten, int $feUser, KatschutzSettings $settings): Bestellung
    {
        $fehler = [];
        $feuerwehrUid = (int)($daten['feuerwehr'] ?? 0);
        $alle = $this->bereichService->alleFeuerwehren();
        if (!isset($alle[$feuerwehrUid])) {
            $fehler[] = 'Bitte die anfordernde Feuerwehr auswählen.';
        }
        $bereich = $this->bereichService->feuerwehrenImBereich($feUser);
        $bereichsfremd = $bereich !== null && $bereich !== [] && !in_array($feuerwehrUid, $bereich, true);

        $anlass = trim((string)($daten['anlass'] ?? ''));
        if ($anlass === '') {
            $fehler[] = 'Bitte Anlass bzw. Einsatznummer angeben.';
        }
        $einsatzabschnitt = trim((string)($daten['einsatzabschnitt'] ?? ''));
        if ($bereichsfremd && $einsatzabschnitt === '') {
            $fehler[] = 'Die Feuerwehr liegt außerhalb Ihres Bereichs – bitte Einsatzabschnitt bzw. Begründung angeben.';
        }
        $von = $this->datum($daten['von'] ?? '');
        $bis = $this->datum($daten['bis'] ?? '');
        if ($von !== null && $bis !== null && $bis < $von) {
            $fehler[] = 'Das Rückgabedatum liegt vor dem Bedarfsdatum.';
        }

        $positionen = [];
        foreach ((array)($daten['menge'] ?? []) as $artikelUid => $menge) {
            $menge = $this->menge($menge);
            if ($menge === 0) {
                continue;
            }
            $artikel = $this->artikelRepository->findByUid((int)$artikelUid);
            if ($artikel === null) {
                continue;
            }
            $position = new Position();
            $position->setArtikel($artikel);
            $position->setMenge($menge);
            $positionen[] = $position;
        }
        foreach ((array)($daten['modul'] ?? []) as $modulUid => $anzahl) {
            $anzahl = $this->menge($anzahl);
            $modul = $anzahl > 0 ? $this->modulRepository->findByUid((int)$modulUid) : null;
            if ($modul === null) {
                continue;
            }
            foreach ($modul->getPositionen() as $modulPosition) {
                if ($modulPosition->getArtikel() === null || $modulPosition->getArtikel()->isHidden()) {
                    continue;
                }
                $position = new Position();
                $position->setArtikel($modulPosition->getArtikel());
                $position->setMenge($modulPosition->getMenge() * $anzahl);
                $position->setModul($modul);
                $positionen[] = $position;
            }
        }
        if ($positionen === []) {
            $fehler[] = 'Bitte mindestens einen Artikel oder ein Modul mit Menge auswählen.';
        }
        if ($fehler !== []) {
            throw new EingabeUngueltigException($fehler);
        }

        $bestellung = new Bestellung();
        $bestellung->setPid($settings->storagePid);
        $bestellung->setFeuerwehr($this->persistenceManager->getObjectByIdentifier($feuerwehrUid, Feuerwehr::class));
        $bestellung->setBereichsfremd($bereichsfremd);
        $bestellung->setBesteller($this->frontendUserRepository->findByUidUeberall($feUser));
        $bestellung->setAnlass($anlass);
        $bestellung->setEinsatzabschnitt($einsatzabschnitt);
        $bestellung->setNotiz((string)($daten['notiz'] ?? ''));
        $bestellung->setVon($von);
        $bestellung->setBis($bis);
        $bestellung->setLage($this->lageService->aktiveLage());
        foreach ($positionen as $position) {
            $position->setPid($settings->storagePid);
            $bestellung->addPosition($position);
        }
        if ($this->lageService->freigabeNoetig($settings)) {
            $bestellung->setStatus(Bestellstatus::Neu);
        } else {
            $bestellung->setStatus(Bestellstatus::Freigegeben);
            $bestellung->setFreigegebenAm(new \DateTimeImmutable());
        }

        $this->bestellungRepository->add($bestellung);
        $this->persistenceManager->persistAll();
        $bestellung->setNummer(sprintf('%s-%04d', date('Y'), $bestellung->getUid()));
        $this->bestellungRepository->update($bestellung);
        $this->persistenceManager->persistAll();

        return $bestellung;
    }

    public function storniere(Bestellung $bestellung): void
    {
        if (!$bestellung->getStatus()->isStornierbar()) {
            throw new BuchungUngueltigException('Diese Bestellung kann nicht mehr storniert werden.');
        }
        $bestellung->setStatus(Bestellstatus::Storniert);
        $this->speichere($bestellung);
    }

    public function gebeFrei(Bestellung $bestellung, int $verwalter): void
    {
        if (!$bestellung->getStatus()->isFreigebbar()) {
            throw new BuchungUngueltigException('Nur angefragte Bestellungen können freigegeben werden.');
        }
        $bestellung->setStatus(Bestellstatus::Freigegeben);
        $bestellung->setFreigegebenVon($this->frontendUserRepository->findByUidUeberall($verwalter));
        $bestellung->setFreigegebenAm(new \DateTimeImmutable());
        $this->speichere($bestellung);
    }

    public function lehneAb(Bestellung $bestellung, string $grund): void
    {
        if (!$bestellung->getStatus()->isFreigebbar() && $bestellung->getStatus() !== Bestellstatus::Freigegeben) {
            throw new BuchungUngueltigException('Diese Bestellung kann nicht mehr abgelehnt werden.');
        }
        $bestellung->setStatus(Bestellstatus::Abgelehnt);
        $bestellung->setVerwalterNotiz($grund);
        $this->speichere($bestellung);
    }

    /**
     * @param array<int|string, mixed> $mengen artikel => Menge (Mengenartikel)
     * @param list<int|string> $einzelstuecke UIDs der auszugebenden Einzelstücke
     *
     * @throws BuchungUngueltigException
     */
    public function gebeAus(Bestellung $bestellung, array $mengen, array $einzelstuecke, string $bemerkung, int $verwalter, KatschutzSettings $settings): void
    {
        if (!$bestellung->getStatus()->isAusgabeErlaubt()) {
            throw new BuchungUngueltigException('Ausgabe nur für freigegebene Bestellungen möglich.');
        }
        $feuerwehr = (int)$bestellung->getFeuerwehr()?->getUid();
        $eingaben = [];
        foreach ($mengen as $artikelUid => $menge) {
            $menge = $this->menge($menge);
            if ($menge > 0) {
                $eingaben[] = new BuchungEingabe(Buchungstyp::Ausgabe, (int)$artikelUid, $menge, $feuerwehr, bestellung: (int)$bestellung->getUid(), bemerkung: $bemerkung);
            }
        }
        foreach (array_unique(array_map('intval', $einzelstuecke)) as $uid) {
            $stueck = $this->einzelstueck($uid);
            $eingaben[] = new BuchungEingabe(Buchungstyp::Ausgabe, (int)$stueck->getArtikel()?->getUid(), 1, $feuerwehr, einzelstueck: $uid, bestellung: (int)$bestellung->getUid(), bemerkung: $bemerkung);
        }
        $this->buchungService->buche($eingaben, $verwalter, $this->lageService->aktiveLageUid(), $settings->storagePid);
        $this->aktualisiereStatus($bestellung);
    }

    /**
     * @param array<int|string, array<int|string, mixed>> $zurueck artikel => feuerwehr => Menge
     * @param array<int|string, array<int|string, mixed>> $verlust artikel => feuerwehr => Menge
     * @param array<int|string, string> $stuecke einzelstueck => 'zurueck'|'verlust'|''
     * @param array<int|string, string> $zustand einzelstueck => ok|mangel|defekt (bei Rücknahme)
     *
     * @throws BuchungUngueltigException
     */
    public function nimmZurueck(
        Bestellung $bestellung,
        array $zurueck,
        array $verlust,
        array $stuecke,
        array $zustand,
        string $bemerkung,
        int $verwalter,
        KatschutzSettings $settings,
    ): void {
        $bestand = $this->bestandService->aktuell();
        $bestellUid = (int)$bestellung->getUid();
        $eingaben = [];
        foreach ([[Buchungstyp::Ruecknahme, $zurueck], [Buchungstyp::Verlust, $verlust]] as [$typ, $liste]) {
            foreach ($liste as $artikelUid => $proFeuerwehr) {
                foreach ((array)$proFeuerwehr as $feuerwehr => $menge) {
                    $menge = $this->menge($menge);
                    if ($menge > 0) {
                        $eingaben[] = new BuchungEingabe($typ, (int)$artikelUid, $menge, (int)$feuerwehr, bestellung: $bestellUid, bemerkung: $bemerkung);
                    }
                }
            }
        }
        $zustandsAenderungen = [];
        foreach ($stuecke as $uid => $aktion) {
            if ($aktion !== 'zurueck' && $aktion !== 'verlust') {
                continue;
            }
            $stueck = $this->einzelstueck((int)$uid);
            $ort = (int)$bestand->einzelstueckOrt((int)$uid);
            $typ = $aktion === 'zurueck' ? Buchungstyp::Ruecknahme : Buchungstyp::Verlust;
            $eingaben[] = new BuchungEingabe($typ, (int)$stueck->getArtikel()?->getUid(), 1, $ort, einzelstueck: (int)$uid, bestellung: $bestellUid, bemerkung: $bemerkung);
            if ($aktion === 'zurueck' && isset($zustand[$uid])) {
                $zustandsAenderungen[] = [$stueck, (string)$zustand[$uid]];
            }
        }
        $this->buchungService->buche($eingaben, $verwalter, $this->lageService->aktiveLageUid(), $settings->storagePid);

        foreach ($zustandsAenderungen as [$stueck, $neuerZustand]) {
            $stueck->setZustand($neuerZustand);
            $this->einzelstueckRepository->update($stueck);
        }
        $this->persistenceManager->persistAll();
        $this->aktualisiereStatus($bestellung);
    }

    /**
     * @throws BuchungUngueltigException
     */
    public function bucheUm(Bestellung $bestellung, int $artikel, int $einzelstueck, int $vonFeuerwehr, int $zielFeuerwehr, int $menge, string $bemerkung, int $verwalter, KatschutzSettings $settings): void
    {
        if ($einzelstueck > 0) {
            $artikel = (int)$this->einzelstueck($einzelstueck)->getArtikel()?->getUid();
            $vonFeuerwehr = (int)$this->bestandService->aktuell()->einzelstueckOrt($einzelstueck);
            $menge = 1;
        }
        $this->buchungService->buche(
            [new BuchungEingabe(Buchungstyp::Umbuchung, $artikel, $menge, $vonFeuerwehr, $zielFeuerwehr, $einzelstueck, (int)$bestellung->getUid(), $bemerkung)],
            $verwalter,
            $this->lageService->aktiveLageUid(),
            $settings->storagePid,
        );
    }

    public function schliesseAb(Bestellung $bestellung): void
    {
        if ($this->bestandService->aktuell()->hatDraussen((int)$bestellung->getUid())) {
            throw new BuchungUngueltigException('Es ist noch Leihgut draußen. Bitte erst zurücknehmen oder als Verlust buchen.');
        }
        if (!in_array($bestellung->getStatus(), [Bestellstatus::Freigegeben, Bestellstatus::Ausgegeben], true)) {
            throw new BuchungUngueltigException('Diese Bestellung kann nicht abgeschlossen werden.');
        }
        $bestellung->setStatus(Bestellstatus::Abgeschlossen);
        $this->speichere($bestellung);
    }

    /**
     * Nach Buchungen: „ausgegeben“, sobald etwas raus ist; „abgeschlossen“, wenn alles
     * Bestellte ausgegeben wurde und kein Leihgut mehr draußen ist.
     */
    private function aktualisiereStatus(Bestellung $bestellung): void
    {
        $bestand = $this->bestandService->aktuell();
        $uid = (int)$bestellung->getUid();
        $etwasAusgegeben = false;
        $vollstaendig = true;
        foreach ($bestellung->mengenJeArtikel() as $artikel => $menge) {
            $ausgegeben = $bestand->ausgegeben($uid, $artikel);
            $etwasAusgegeben = $etwasAusgegeben || $ausgegeben > 0;
            $vollstaendig = $vollstaendig && $ausgegeben >= $menge;
        }
        if (!$etwasAusgegeben) {
            return;
        }
        $bestellung->setStatus(
            $vollstaendig && !$bestand->hatDraussen($uid) ? Bestellstatus::Abgeschlossen : Bestellstatus::Ausgegeben,
        );
        $this->speichere($bestellung);
    }

    private function speichere(Bestellung $bestellung): void
    {
        $this->bestellungRepository->update($bestellung);
        $this->persistenceManager->persistAll();
    }

    private function einzelstueck(int $uid): Einzelstueck
    {
        return $this->einzelstueckRepository->findByUidInklusiveArchiv($uid)
            ?? throw new BuchungUngueltigException(sprintf('Einzelstück %d existiert nicht.', $uid));
    }

    private function menge(mixed $wert): int
    {
        $menge = (int)(is_scalar($wert) ? $wert : 0);
        return max(0, min(self::MAX_MENGE, $menge));
    }

    private function datum(mixed $wert): ?\DateTimeImmutable
    {
        if (!is_string($wert) || $wert === '') {
            return null;
        }
        $datum = \DateTimeImmutable::createFromFormat('!Y-m-d', $wert);
        return $datum === false ? null : $datum;
    }
}
