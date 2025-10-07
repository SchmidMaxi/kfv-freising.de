<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use DateTime;
use DateTimeImmutable;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Jubilaeum extends AbstractEntity
{
    /** UNIX timestamp (managed by TYPO3) */
    protected int $crdate = 0;

    #[Lazy]
    protected ?Feuerwehr $feuerwehr = null;

    /** Das ist die Art des Jubiläums, z.B. 150 */
    protected int $titel = 0;

    /** Das ist das exakte Datum der Feier */
    protected ?DateTimeImmutable $datum = null;

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

    public function getTitel(): int
    {
        return $this->titel;
    }
    public function setTitel(int $titel): void
    {
        $this->titel = $titel;
    }

    public function getDatum(): ?DateTimeImmutable
    {
        return $this->datum;
    }
    public function setDatum($date): void
    {
        if ($date instanceof DateTimeImmutable) {
            $this->datum = $date;
        } elseif ($date instanceof DateTime) {
            $this->datum = DateTimeImmutable::createFromInterface($date);
        } elseif ($date === null) {
            $this->datum = null;
        }
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