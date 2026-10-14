<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Großschadenslage: solange aktiv, entfällt die Freigabe und alle Bestellungen/
 * Buchungen werden ihr zugeordnet.
 */
class Lage extends AbstractEntity
{
    #[Validate(validator: 'NotEmpty')]
    protected string $title = '';
    protected bool $aktiv = false;
    protected ?\DateTimeImmutable $beginn = null;
    protected ?\DateTimeImmutable $ende = null;
    protected string $bemerkung = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function isAktiv(): bool
    {
        return $this->aktiv;
    }

    public function setAktiv(bool $aktiv): void
    {
        $this->aktiv = $aktiv;
    }

    public function getBeginn(): ?\DateTimeImmutable
    {
        return $this->beginn;
    }

    public function setBeginn(?\DateTimeImmutable $beginn): void
    {
        $this->beginn = $beginn;
    }

    public function getEnde(): ?\DateTimeImmutable
    {
        return $this->ende;
    }

    public function setEnde(?\DateTimeImmutable $ende): void
    {
        $this->ende = $ende;
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
