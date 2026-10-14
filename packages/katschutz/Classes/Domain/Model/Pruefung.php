<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Pruefung extends AbstractEntity
{
    protected ?\DateTimeImmutable $datum = null;
    protected string $ergebnis = 'bestanden';
    protected string $pruefer = '';
    protected string $bemerkung = '';

    public function getDatum(): ?\DateTimeImmutable
    {
        return $this->datum;
    }

    public function setDatum(?\DateTimeImmutable $datum): void
    {
        $this->datum = $datum;
    }

    public function getErgebnis(): string
    {
        return $this->ergebnis;
    }

    public function setErgebnis(string $ergebnis): void
    {
        $this->ergebnis = $ergebnis === 'mangel' ? 'mangel' : 'bestanden';
    }

    public function isBestanden(): bool
    {
        return $this->ergebnis === 'bestanden';
    }

    public function getPruefer(): string
    {
        return $this->pruefer;
    }

    public function setPruefer(string $pruefer): void
    {
        $this->pruefer = trim($pruefer);
    }

    public function getBemerkung(): string
    {
        return $this->bemerkung;
    }

    public function setBemerkung(string $bemerkung): void
    {
        $this->bemerkung = $bemerkung;
    }
}
