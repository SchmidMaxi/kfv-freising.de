<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Area extends AbstractEntity
{
    protected string $title = '';
    protected string $type = '';
    protected ?Area $parent = null;
    protected ?Person $person = null;

    /** @var ObjectStorage<Area> */
    protected ObjectStorage $children;

    /** @var ObjectStorage<Gemeinde> */
    protected ObjectStorage $gemeinden;

    public function __construct()
    {
        $this->children = new ObjectStorage();
        $this->gemeinden = new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getParent(): ?Area
    {
        return $this->parent;
    }

    public function setParent(?Area $parent): void
    {
        $this->parent = $parent;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): void
    {
        $this->person = $person;
    }

    /**
     * @return ObjectStorage<Area>
     */
    public function getChildren(): ObjectStorage
    {
        return $this->children;
    }

    /**
     * @param ObjectStorage<Area> $children
     */
    public function setChildren(ObjectStorage $children): void
    {
        $this->children = $children;
    }

    public function addChild(Area $child): void
    {
        $this->children->attach($child);
    }

    public function removeChild(Area $child): void
    {
        $this->children->detach($child);
    }

    /**
     * @return ObjectStorage<Gemeinde>
     */
    public function getGemeinden(): ObjectStorage
    {
        return $this->gemeinden;
    }

    /**
     * @param ObjectStorage<Gemeinde> $gemeinden
     */
    public function setGemeinden(ObjectStorage $gemeinden): void
    {
        $this->gemeinden = $gemeinden;
    }

    public function addGemeinde(Gemeinde $gemeinde): void
    {
        $this->gemeinden->attach($gemeinde);
    }

    public function removeGemeinde(Gemeinde $gemeinde): void
    {
        $this->gemeinden->detach($gemeinde);
    }
}