<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Feuerwehr extends AbstractEntity
{
    protected string $name = '';
    protected string $slug = '';
    protected string $strasse = '';
    protected string $plz = '';
    protected string $ort = '';
    protected float $latitude = 0.0;
    protected float $longitude = 0.0;
    protected int $gruendungsdatum = 0;

    protected ?Person $kommandant = null;
    protected ?Person $stellvKommandant = null;
    protected ?Person $jugendwart = null;
    protected ?Person $stellvJugendwart = null;
    protected ?Person $kinderwart = null;
    protected ?Person $stellvKinderwart = null;

    // Getter/Setter

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): void { $this->slug = $slug; }

    public function getStrasse(): string { return $this->strasse; }
    public function setStrasse(string $strasse): void { $this->strasse = $strasse; }

    public function getPlz(): string { return $this->plz; }
    public function setPlz(string $plz): void { $this->plz = $plz; }

    public function getOrt(): string { return $this->ort; }
    public function setOrt(string $ort): void { $this->ort = $ort; }

    public function getLatitude(): float { return $this->latitude; }
    public function setLatitude(float $latitude): void { $this->latitude = $latitude; }

    public function getLongitude(): float { return $this->longitude; }
    public function setLongitude(float $longitude): void { $this->longitude = $longitude; }

    public function getGruendungsdatum(): int { return $this->gruendungsdatum; }
    public function setGruendungsdatum(int $datum): void { $this->gruendungsdatum = $datum; }

    // Kommandanten etc. Getter/Setter analog...
}
