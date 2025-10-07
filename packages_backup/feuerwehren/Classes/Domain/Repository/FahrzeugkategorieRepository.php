<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

final class FahrzeugkategorieRepository extends Repository
{
    public function initializeObject(): void
    {
        // QuerySettings über createQuery() holen – kein $objectManager nötig
        $qs = $this->createQuery()->getQuerySettings();
        $qs->setRespectStoragePage(false);
        $qs->setRespectSysLanguage(false);

        $this->setDefaultQuerySettings($qs);
    }
}
