<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use Schmid\Feuerwehren\Domain\Model\FrontendUser;
use Schmid\Feuerwehren\Domain\Model\Gemeinde;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;

class Person extends AbstractEntity
{
    /** UNIX timestamp (managed by TYPO3) */
    protected int $crdate = 0;
    protected string $title = '';
    protected string $slug = '';
    protected string $rolle = '';

    /** @var \Schmid\Feuerwehren\Domain\Model\FrontendUser|null */
    protected $feUser = null;

    /** @var ObjectStorage<Gemeinde> */
    protected ObjectStorage $gemeinde;

    /** @var ObjectStorage<Person> */
    #[Lazy]
    protected ObjectStorage $untergeordnet;

    public function __construct()
    {
        $this->untergeordnet = new ObjectStorage();
        $this->gemeinde = new ObjectStorage();
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

    /** @return \Schmid\Feuerwehren\Domain\Model\FrontendUser|null */
    public function getFeUser() {
        return $this->feUser;
    }
    /** @param \Schmid\Feuerwehren\Domain\Model\FrontendUser|\TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy|null $feUser */
    public function setFeUser($feUser): void {
        $this->feUser = $feUser;
    }

    public function getRolle(): string
    {
        return $this->rolle;
    }
    public function setRolle(string $rolle): void
    {
        $this->rolle = $rolle;
    }

    /** @return ObjectStorage<Gemeinde> */
    public function getGemeinde(): ObjectStorage
    {
        return $this->gemeinde;
    }

    /** @param ObjectStorage<Gemeinde> $gemeinde */
    public function setGemeinde(ObjectStorage $gemeinde): void
    {
        $this->gemeinde = $gemeinde;
    }

    public function addGemeinde(Gemeinde $g): void
    {
        $this->gemeinde->attach($g);
    }

    public function removeGemeinde(Gemeinde $g): void
    {
        $this->gemeinde->detach($g);
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
