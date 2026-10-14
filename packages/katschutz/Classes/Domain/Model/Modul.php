<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\ORM\Cascade;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Vordefiniertes Set (z.B. „Hochwasser-Modul 1“), das als Ganzes bestellt werden kann.
 */
class Modul extends AbstractEntity
{
    #[Validate(validator: 'NotEmpty')]
    protected string $title = '';
    protected string $beschreibung = '';
    protected bool $hidden = false;

    /** @var ObjectStorage<ModulPosition> */
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

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * @return ObjectStorage<ModulPosition>
     */
    public function getPositionen(): ObjectStorage
    {
        return $this->positionen;
    }

    /**
     * @param ObjectStorage<ModulPosition> $positionen
     */
    public function setPositionen(ObjectStorage $positionen): void
    {
        $this->positionen = $positionen;
    }

    public function addPosition(ModulPosition $position): void
    {
        $this->positionen->attach($position);
    }

    public function removePosition(ModulPosition $position): void
    {
        $this->positionen->detach($position);
    }
}
