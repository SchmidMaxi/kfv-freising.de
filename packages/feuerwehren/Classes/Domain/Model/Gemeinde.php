<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;

class Gemeinde extends AbstractEntity
{
    /** UNIX timestamp (managed by TYPO3) */
    protected int $crdate = 0;

    protected string $name = '';
    protected string $slug = '';

    /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null */
    protected $logo = null;

    /** GeoJSON als String */
    protected string $gemeindegebiet = '';

    protected ?Area $kbmArea = null;

    /** @var ObjectStorage<Feuerwehr> */
    #[Lazy]
    protected ObjectStorage $feuerwehren;

    public function __construct()
    {
        $this->feuerwehren = new ObjectStorage();
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /** @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference|\TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy|null $logo
     */
    public function setLogo($logo): void
    {
        $this->logo = $logo;
    }

    public function getGemeindegebiet(): string
    {
        return $this->gemeindegebiet;
    }
    public function setGemeindegebiet(string $gemeindegebiet): void
    {
        $this->gemeindegebiet = $gemeindegebiet;
    }

    /** @return ObjectStorage<Feuerwehr> */
    public function getFeuerwehren(): ObjectStorage
    {
        return $this->feuerwehren;
    }
    /** @param ObjectStorage<Feuerwehr> $feuerwehren */
    public function setFeuerwehren(ObjectStorage $feuerwehren): void
    {
        $this->feuerwehren = $feuerwehren;
    }
    public function addFeuerwehr(Feuerwehr $feuerwehr): void
    {
        $this->feuerwehren->attach($feuerwehr);
    }
    public function removeFeuerwehr(Feuerwehr $feuerwehr): void
    {
        $this->feuerwehren->detach($feuerwehr);
    }
}
