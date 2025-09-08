<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Rolle extends AbstractEntity
{
    /** UNIX timestamp (managed by TYPO3) */
    protected int $crdate = 0;

    protected string $title = '';

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
