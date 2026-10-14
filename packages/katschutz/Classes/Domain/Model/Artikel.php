<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\FileUpload;
use TYPO3\CMS\Extbase\Attribute\ORM\Lazy;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Artikel extends AbstractEntity
{
    #[Validate(validator: 'NotEmpty')]
    protected string $title = '';
    protected string $beschreibung = '';
    protected ?Kategorie $kategorie = null;
    protected string $typ = 'menge';
    protected bool $verbrauch = false;
    protected string $einheit = 'Stk.';
    protected int $mindestbestand = 0;
    protected ?Lagerort $lagerort = null;
    protected string $lagerfach = '';
    protected int $pruefintervall = 0;
    protected bool $hidden = false;

    /** @var ObjectStorage<FileReference> */
    #[Lazy]
    #[FileUpload(
        validation: [
            'required' => false,
            'maxFiles' => 5,
            'fileSize' => ['minimum' => '0K', 'maximum' => '10M'],
            'mimeType' => ['allowedMimeTypes' => ['image/jpeg', 'image/png', 'image/webp']],
        ],
        uploadFolder: '1:/user_upload/katschutz/',
    )]
    protected ObjectStorage $bilder;

    /** @var ObjectStorage<Einzelstueck> */
    #[Lazy]
    protected ObjectStorage $einzelstuecke;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->bilder ??= new ObjectStorage();
        $this->einzelstuecke ??= new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function getBeschreibung(): string
    {
        return $this->beschreibung;
    }

    public function setBeschreibung(string $beschreibung): void
    {
        $this->beschreibung = $beschreibung;
    }

    public function getKategorie(): ?Kategorie
    {
        return $this->kategorie;
    }

    public function setKategorie(?Kategorie $kategorie): void
    {
        $this->kategorie = $kategorie;
    }

    public function getTyp(): string
    {
        return $this->typ;
    }

    public function setTyp(string $typ): void
    {
        $this->typ = $typ === 'einzeln' ? 'einzeln' : 'menge';
    }

    public function isEinzeln(): bool
    {
        return $this->typ === 'einzeln';
    }

    public function isVerbrauch(): bool
    {
        return $this->verbrauch;
    }

    public function setVerbrauch(bool $verbrauch): void
    {
        $this->verbrauch = $verbrauch;
    }

    public function getEinheit(): string
    {
        return $this->einheit;
    }

    public function setEinheit(string $einheit): void
    {
        $this->einheit = trim($einheit);
    }

    public function getMindestbestand(): int
    {
        return $this->mindestbestand;
    }

    public function setMindestbestand(int $mindestbestand): void
    {
        $this->mindestbestand = max(0, $mindestbestand);
    }

    public function getLagerort(): ?Lagerort
    {
        return $this->lagerort;
    }

    public function setLagerort(?Lagerort $lagerort): void
    {
        $this->lagerort = $lagerort;
    }

    public function getLagerfach(): string
    {
        return $this->lagerfach;
    }

    public function setLagerfach(string $lagerfach): void
    {
        $this->lagerfach = trim($lagerfach);
    }

    public function getPruefintervall(): int
    {
        return $this->pruefintervall;
    }

    public function setPruefintervall(int $pruefintervall): void
    {
        $this->pruefintervall = max(0, $pruefintervall);
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
     * @return ObjectStorage<FileReference>
     */
    public function getBilder(): ObjectStorage
    {
        return $this->bilder;
    }

    public function getErstesBild(): ?FileReference
    {
        foreach ($this->bilder as $bild) {
            return $bild;
        }
        return null;
    }

    /**
     * @param ObjectStorage<FileReference> $bilder
     */
    public function setBilder(ObjectStorage $bilder): void
    {
        $this->bilder = $bilder;
    }

    public function addBild(FileReference $bild): void
    {
        $this->bilder->attach($bild);
    }

    /**
     * @return ObjectStorage<Einzelstueck>
     */
    public function getEinzelstuecke(): ObjectStorage
    {
        return $this->einzelstuecke;
    }
}
