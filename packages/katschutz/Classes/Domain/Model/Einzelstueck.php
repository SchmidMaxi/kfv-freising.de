<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\ORM\Lazy;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Einzelstueck extends AbstractEntity
{
    protected ?Artikel $artikel = null;
    #[Validate(validator: 'NotEmpty')]
    protected string $inventarnummer = '';
    protected string $seriennummer = '';
    protected ?Lagerort $lagerort = null;
    protected string $zustand = 'ok';
    protected ?\DateTimeImmutable $naechstePruefung = null;
    protected string $bemerkung = '';
    protected bool $hidden = false;

    /** @var ObjectStorage<Pruefung> */
    #[Lazy]
    protected ObjectStorage $pruefungen;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->pruefungen ??= new ObjectStorage();
    }

    public function getArtikel(): ?Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(?Artikel $artikel): void
    {
        $this->artikel = $artikel;
    }

    public function getInventarnummer(): string
    {
        return $this->inventarnummer;
    }

    public function setInventarnummer(string $inventarnummer): void
    {
        $this->inventarnummer = trim($inventarnummer);
    }

    public function getSeriennummer(): string
    {
        return $this->seriennummer;
    }

    public function setSeriennummer(string $seriennummer): void
    {
        $this->seriennummer = trim($seriennummer);
    }

    public function getLagerort(): ?Lagerort
    {
        return $this->lagerort;
    }

    public function setLagerort(?Lagerort $lagerort): void
    {
        $this->lagerort = $lagerort;
    }

    /** Eigener Lagerort oder der des Artikels. */
    public function getEffektiverLagerort(): ?Lagerort
    {
        return $this->lagerort ?? $this->artikel?->getLagerort();
    }

    public function getZustand(): string
    {
        return $this->zustand;
    }

    public function setZustand(string $zustand): void
    {
        $this->zustand = in_array($zustand, ['ok', 'mangel', 'defekt'], true) ? $zustand : 'ok';
    }

    public function getNaechstePruefung(): ?\DateTimeImmutable
    {
        return $this->naechstePruefung;
    }

    public function setNaechstePruefung(?\DateTimeImmutable $naechstePruefung): void
    {
        $this->naechstePruefung = $naechstePruefung;
    }

    public function isPruefungUeberfaellig(): bool
    {
        return $this->naechstePruefung !== null && $this->naechstePruefung < new \DateTimeImmutable('today');
    }

    public function getBemerkung(): string
    {
        return $this->bemerkung;
    }

    public function setBemerkung(string $bemerkung): void
    {
        $this->bemerkung = $bemerkung;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * @return ObjectStorage<Pruefung>
     */
    public function getPruefungen(): ObjectStorage
    {
        return $this->pruefungen;
    }

    public function addPruefung(Pruefung $pruefung): void
    {
        $this->pruefungen->attach($pruefung);
    }

    /**
     * @return array{artikel: int, inventarnummer: string, zustand: string, naechste_pruefung: ?\DateTimeImmutable, hidden: bool}
     */
    public function alsPruefdaten(): array
    {
        return [
            'artikel' => (int)$this->artikel?->getUid(),
            'inventarnummer' => $this->inventarnummer,
            'zustand' => $this->zustand,
            'naechste_pruefung' => $this->naechstePruefung,
            'hidden' => $this->hidden,
        ];
    }
}
