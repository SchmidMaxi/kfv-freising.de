<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;

class Jubilaeum extends AbstractEntity
{
    /** UNIX timestamp (managed by TYPO3) */
    protected int $crdate = 0;

    #[Lazy]
    protected ?Feuerwehr $feuerwehr = null;

    protected int $jahr = 0;
    protected string $titel = '';
    protected string $beschreibung = '';

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function getFeuerwehr(): ?Feuerwehr
    {
        return $this->feuerwehr;
    }
    public function setFeuerwehr(?Feuerwehr $feuerwehr): void
    {
        $this->feuerwehr = $feuerwehr;
    }

    public function getJahr(): int
    {
        return $this->jahr;
    }
    public function setJahr(int $jahr): void
    {
        $this->jahr = $jahr;
    }

    public function getTitel(): string
    {
        return $this->titel;
    }
    public function setTitel(string $titel): void
    {
        $this->titel = $titel;
    }

    public function getBeschreibung(): string
    {
        return $this->beschreibung;
    }
    public function setBeschreibung(string $beschreibung): void
    {
        $this->beschreibung = $beschreibung;
    }
}
