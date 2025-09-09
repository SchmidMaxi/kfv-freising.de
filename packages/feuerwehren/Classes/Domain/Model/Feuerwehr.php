<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use DateTimeImmutable;
use DateTime;
use DateTimeInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;

class Feuerwehr extends AbstractEntity
{
    /** UNIX timestamp (managed by TYPO3) */
    protected int $crdate = 0;

    protected string $name = '';
    protected string $slug = '';
    protected string $strasse = '';
    protected string $plz = '';
    protected string $ort = '';
    protected string $latitude = '';
    protected string $longitude = '';

    /** Gründungsdatum (TCA eval=date -> Domain: DateTimeImmutable) */
    protected ?DateTimeImmutable $gruendungsdatum = null;

    /** @var ObjectStorage<Fahrzeugkategorie> */
    #[Lazy]
    protected ObjectStorage $fahrzeugkategorien;

    /** @var ObjectStorage<Jubilaeum> */
    #[Lazy]
    #[Cascade(['remove'])]
    protected ObjectStorage $jubilaeen;

    public function __construct()
    {
        $this->fahrzeugkategorien = new ObjectStorage();
        $this->jubilaeen = new ObjectStorage();
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

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

    public function getLatitude(): string { return $this->latitude; }
    public function setLatitude(string $latitude): void { $this->latitude = $latitude; }

    public function getLongitude(): string { return $this->longitude; }
    public function setLongitude(string $longitude): void { $this->longitude = $longitude; }

    public function getGruendungsdatum(): ?DateTimeImmutable
    {
        return $this->gruendungsdatum;
    }
    public function setGruendungsdatum($date): void
    {
        if ($date instanceof DateTimeImmutable) {
            $this->gruendungsdatum = $date;
        } elseif ($date instanceof DateTime) {
            $this->gruendungsdatum = DateTimeImmutable::createFromInterface($date);
        } elseif ($date === null) {
            $this->gruendungsdatum = null;
        }
    }

    /** @return ObjectStorage<Fahrzeugkategorie> */
    public function getFahrzeugkategorien(): ObjectStorage { return $this->fahrzeugkategorien; }
    /** @param ObjectStorage<Fahrzeugkategorie> $fahrzeugkategorien */
    public function setFahrzeugkategorien(ObjectStorage $fahrzeugkategorien): void { $this->fahrzeugkategorien = $fahrzeugkategorien; }
    public function addFahrzeugkategorie(Fahrzeugkategorie $fahrzeug): void { $this->fahrzeugkategorien->attach($fahrzeug); }
    public function removeFahrzeugkategorie(Fahrzeugkategorie $fahrzeug): void { $this->fahrzeugkategorien->detach($fahrzeug); }

    /** @return ObjectStorage<Jubilaeum> */
    public function getJubilaeen(): ObjectStorage { return $this->jubilaeen; }
    /** @param ObjectStorage<Jubilaeum> $jubilaeen */
    public function setJubilaeen(ObjectStorage $jubilaeen): void { $this->jubilaeen = $jubilaeen; }
    public function addJubilaeum(Jubilaeum $j): void { $this->jubilaeen->attach($j); }
    public function removeJubilaeum(Jubilaeum $j): void { $this->jubilaeen->detach($j); }
}
