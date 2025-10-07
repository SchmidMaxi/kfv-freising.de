<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

final class Person extends AbstractEntity
{
    protected string $title = '';
    protected string $slug = '';

    /**
     * @var \Schmid\Feuerwehren\Domain\Model\FrontendUser|null
     */
    protected $feUser = null;

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

    /**
     * @return \Schmid\Feuerwehren\Domain\Model\FrontendUser|null
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * @param \Schmid\Feuerwehren\Domain\Model\FrontendUser|\TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy|null $feUser
     */
    public function setFeUser($feUser): void
    {
        $this->feUser = $feUser;
    }
}