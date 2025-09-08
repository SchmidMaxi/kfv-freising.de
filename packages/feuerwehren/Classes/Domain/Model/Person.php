<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;

class Person extends AbstractEntity
{
    /** UNIX timestamp (managed by TYPO3) */
    protected int $crdate = 0;

    protected string $title = '';
    protected string $slug = '';

    #[Lazy]
    protected ?FrontendUser $feUser = null;

    #[Lazy]
    protected ?Rolle $rolle = null;

    #[Lazy]
    protected ?Gemeinde $gemeinde = null;

    /** @var ObjectStorage<Person> */
    #[Lazy]
    protected ObjectStorage $untergeordnet;

    public function __construct()
    {
        $this->untergeordnet = new ObjectStorage();
    }

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

    public function getSlug(): string
    {
        return $this->slug;
    }
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }
    public function setFeUser(?FrontendUser $feUser): void
    {
        $this->feUser = $feUser;
    }

    public function getRolle(): ?Rolle
    {
        return $this->rolle;
    }
    public function setRolle(?Rolle $rolle): void
    {
        $this->rolle = $rolle;
    }

    public function getGemeinde(): ?Gemeinde
    {
        return $this->gemeinde;
    }
    public function setGemeinde(?Gemeinde $gemeinde): void
    {
        $this->gemeinde = $gemeinde;
    }

    /** @return ObjectStorage<Person> */
    public function getUntergeordnet(): ObjectStorage
    {
        return $this->untergeordnet;
    }
    /** @param ObjectStorage<Person> $untergeordnet */
    public function setUntergeordnet(ObjectStorage $untergeordnet): void
    {
        $this->untergeordnet = $untergeordnet;
    }
    public function addUntergeordnet(Person $person): void
    {
        $this->untergeordnet->attach($person);
    }
    public function removeUntergeordnet(Person $person): void
    {
        $this->untergeordnet->detach($person);
    }
}
