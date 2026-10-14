<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Repository;

use Schmid\Katschutz\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<FrontendUser>
 */
final class FrontendUserRepository extends Repository
{
    public function findByUidUeberall(int $uid): ?FrontendUser
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->matching($query->equals('uid', $uid))->execute()->getFirst();
    }
}
