<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Person extends AbstractEntity
{
    protected string $title = '';
    protected string $slug = '';
    protected int $feUser = 0;
    protected ?Rolle $rolle = null;
    protected ?Gemeinde $gemeinde = null;

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): void { $this->slug = $slug; }

    public function getFeUser(): int { return $this->feUser; }
    public function setFeUser(int $feUser): void { $this->feUser = $feUser; }

    public function getRolle(): ?Rolle { return $this->rolle; }
    public function setRolle(?Rolle $rolle): void { $this->rolle = $rolle; }

    public function getGemeinde(): ?Gemeinde { return $this->gemeinde; }
    public function setGemeinde(?Gemeinde $gemeinde): void { $this->gemeinde = $gemeinde; }
}
