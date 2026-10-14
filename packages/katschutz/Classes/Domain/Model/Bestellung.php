<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use Schmid\Katschutz\Domain\Enum\Bestellstatus;
use TYPO3\CMS\Extbase\Attribute\ORM\Cascade;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Bestellung extends AbstractEntity
{
    protected string $nummer = '';
    protected Bestellstatus $status = Bestellstatus::Neu;
    protected ?Feuerwehr $feuerwehr = null;
    protected bool $bereichsfremd = false;
    protected ?FrontendUser $besteller = null;
    protected ?\DateTimeImmutable $von = null;
    protected ?\DateTimeImmutable $bis = null;
    protected string $anlass = '';
    protected string $einsatzabschnitt = '';
    protected string $notiz = '';
    protected ?Lage $lage = null;
    protected string $verwalterNotiz = '';
    protected ?FrontendUser $freigegebenVon = null;
    protected ?\DateTimeImmutable $freigegebenAm = null;
    protected ?\DateTimeImmutable $crdate = null;

    /** @var ObjectStorage<Position> */
    #[Cascade(value: 'remove')]
    protected ObjectStorage $positionen;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->positionen ??= new ObjectStorage();
    }

    public function getNummer(): string
    {
        return $this->nummer;
    }

    public function setNummer(string $nummer): void
    {
        $this->nummer = $nummer;
    }

    public function getStatus(): Bestellstatus
    {
        return $this->status;
    }

    public function setStatus(Bestellstatus $status): void
    {
        $this->status = $status;
    }

    public function getFeuerwehr(): ?Feuerwehr
    {
        return $this->feuerwehr;
    }

    public function setFeuerwehr(?Feuerwehr $feuerwehr): void
    {
        $this->feuerwehr = $feuerwehr;
    }

    public function isBereichsfremd(): bool
    {
        return $this->bereichsfremd;
    }

    public function setBereichsfremd(bool $bereichsfremd): void
    {
        $this->bereichsfremd = $bereichsfremd;
    }

    public function getBesteller(): ?FrontendUser
    {
        return $this->besteller;
    }

    public function setBesteller(?FrontendUser $besteller): void
    {
        $this->besteller = $besteller;
    }

    public function getVon(): ?\DateTimeImmutable
    {
        return $this->von;
    }

    public function setVon(?\DateTimeImmutable $von): void
    {
        $this->von = $von;
    }

    public function getBis(): ?\DateTimeImmutable
    {
        return $this->bis;
    }

    public function setBis(?\DateTimeImmutable $bis): void
    {
        $this->bis = $bis;
    }

    public function getAnlass(): string
    {
        return $this->anlass;
    }

    public function setAnlass(string $anlass): void
    {
        $this->anlass = trim($anlass);
    }

    public function getEinsatzabschnitt(): string
    {
        return $this->einsatzabschnitt;
    }

    public function setEinsatzabschnitt(string $einsatzabschnitt): void
    {
        $this->einsatzabschnitt = trim($einsatzabschnitt);
    }

    public function getNotiz(): string
    {
        return $this->notiz;
    }

    public function setNotiz(string $notiz): void
    {
        $this->notiz = trim($notiz);
    }

    public function getLage(): ?Lage
    {
        return $this->lage;
    }

    public function setLage(?Lage $lage): void
    {
        $this->lage = $lage;
    }

    public function getVerwalterNotiz(): string
    {
        return $this->verwalterNotiz;
    }

    public function setVerwalterNotiz(string $verwalterNotiz): void
    {
        $this->verwalterNotiz = trim($verwalterNotiz);
    }

    public function getFreigegebenVon(): ?FrontendUser
    {
        return $this->freigegebenVon;
    }

    public function setFreigegebenVon(?FrontendUser $freigegebenVon): void
    {
        $this->freigegebenVon = $freigegebenVon;
    }

    public function getFreigegebenAm(): ?\DateTimeImmutable
    {
        return $this->freigegebenAm;
    }

    public function setFreigegebenAm(?\DateTimeImmutable $freigegebenAm): void
    {
        $this->freigegebenAm = $freigegebenAm;
    }

    public function getCrdate(): ?\DateTimeImmutable
    {
        return $this->crdate;
    }

    /**
     * @return ObjectStorage<Position>
     */
    public function getPositionen(): ObjectStorage
    {
        return $this->positionen;
    }

    public function addPosition(Position $position): void
    {
        $this->positionen->attach($position);
    }

    /**
     * Bestellte Menge je Artikel (Positionen aus Modulen zusammengefasst).
     *
     * @return array<int, int>
     */
    public function mengenJeArtikel(): array
    {
        $mengen = [];
        foreach ($this->positionen as $position) {
            $uid = (int)$position->getArtikel()?->getUid();
            $mengen[$uid] = ($mengen[$uid] ?? 0) + $position->getMenge();
        }
        return $mengen;
    }

    /** Rückgabetermin überschritten (nur relevant, solange Leihgut draußen ist). */
    public function isRueckgabeUeberfaellig(): bool
    {
        return $this->bis !== null && $this->bis < new \DateTimeImmutable('today');
    }
}
