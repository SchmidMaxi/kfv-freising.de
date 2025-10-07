<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

final class PersonRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function initializeObject(): void
    {
        $qs = $this->createQuery()->getQuerySettings();
        $qs->setRespectStoragePage(false);
        $qs->setRespectSysLanguage(false);
        $qs->setIgnoreEnableFields(false);
        $qs->setIncludeDeleted(false);
        $this->setDefaultQuerySettings($qs);
    }
}
