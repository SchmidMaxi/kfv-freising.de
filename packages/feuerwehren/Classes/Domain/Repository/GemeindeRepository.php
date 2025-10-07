<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

final class GemeindeRepository extends Repository
{
    public function initializeObject(): void
    {
        // QuerySettings anpassen, um alle Datensätze zu finden
        $querySettings = $this->createQuery()->getQuerySettings();
        // WICHTIG: Storage Page Beschränkungen ignorieren
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }
}