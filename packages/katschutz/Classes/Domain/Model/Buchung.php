<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Lesemodell für das Journal. Geschrieben wird ausschließlich über den BuchungService.
 */
class Buchung extends AbstractEntity
{
    protected ?Buchungstyp $typ = null;
    protected ?Artikel $artikel = null;
    protected ?Einzelstueck $einzelstueck = null;
    protected int $menge = 0;
    protected ?Feuerwehr $feuerwehr = null;
    protected ?Feuerwehr $zielFeuerwehr = null;
    protected ?Bestellung $bestellung = null;
    protected ?Lage $lage = null;
    protected ?FrontendUser $feUser = null;
    protected string $bemerkung = '';
    protected ?\DateTimeImmutable $crdate = null;

    public function getTyp(): ?Buchungstyp
    {
        return $this->typ;
    }

    public function getArtikel(): ?Artikel
    {
        return $this->artikel;
    }

    public function getEinzelstueck(): ?Einzelstueck
    {
        return $this->einzelstueck;
    }

    public function getMenge(): int
    {
        return $this->menge;
    }

    public function getFeuerwehr(): ?Feuerwehr
    {
        return $this->feuerwehr;
    }

    public function getZielFeuerwehr(): ?Feuerwehr
    {
        return $this->zielFeuerwehr;
    }

    public function getBestellung(): ?Bestellung
    {
        return $this->bestellung;
    }

    public function getLage(): ?Lage
    {
        return $this->lage;
    }

    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }

    public function getBemerkung(): string
    {
        return $this->bemerkung;
    }

    public function getCrdate(): ?\DateTimeImmutable
    {
        return $this->crdate;
    }
}
