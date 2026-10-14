<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Lagerort extends AbstractEntity
{
    #[Validate(validator: 'NotEmpty')]
    protected string $title = '';
    protected string $beschreibung = '';
    protected bool $hidden = false;

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
}
