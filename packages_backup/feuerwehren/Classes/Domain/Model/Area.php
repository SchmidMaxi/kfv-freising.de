<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Area extends AbstractEntity {
    protected string $title = '';
    protected string $type = '';
    protected ?Area $parent = null;
    protected ?Person $person = null;

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Area> */
    protected $children;

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Gemeinde> */
    protected $gemeinden;
}