<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Position extends AbstractEntity
{
    protected ?Artikel $artikel = null;
    protected int $menge = 1;
    protected ?Modul $modul = null;

    public function getArtikel(): ?Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(?Artikel $artikel): void
    {
        $this->artikel = $artikel;
    }

    public function getMenge(): int
    {
        return $this->menge;
    }

    public function setMenge(int $menge): void
    {
        $this->menge = max(1, $menge);
    }

    public function getModul(): ?Modul
    {
        return $this->modul;
    }

    public function setModul(?Modul $modul): void
    {
        $this->modul = $modul;
    }
}
