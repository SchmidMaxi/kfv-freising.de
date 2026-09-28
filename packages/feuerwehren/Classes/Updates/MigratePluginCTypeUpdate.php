<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Updates;

use Doctrine\DBAL\ArrayParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Migrates feuerwehren's plugin content elements from the classic CType=list +
 * list_type convention to their own dedicated CType (see
 * Configuration/TCA/Overrides/tt_content.php for why: v14's FlexForm resolution
 * keys strictly off CType, so a shared CType=list can no longer carry a
 * per-plugin data structure).
 */
#[UpgradeWizard('feuerwehrenMigratePluginCType')]
final class MigratePluginCTypeUpdate implements UpgradeWizardInterface
{
    private const TABLE = 'tt_content';

    private const LIST_TYPES = [
        'feuerwehren_karte',
        'feuerwehren_organigramm',
        'feuerwehren_jubilaeen',
    ];

    public function __construct(private readonly ConnectionPool $connectionPool) {}

    public function getIdentifier(): string
    {
        return 'feuerwehrenMigratePluginCType';
    }

    public function getTitle(): string
    {
        return 'EXT:feuerwehren: Migrate plugin content elements from list_type to CType';
    }

    public function getDescription(): string
    {
        return 'Feuerwehren plugin content elements (Karte, Organigramm, Jubiläen) used the classic '
            . 'CType=list + list_type convention. TYPO3 v14 resolves FlexForms strictly by CType, so '
            . 'these records are migrated to their own dedicated CType (list_type is cleared).';
    }

    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    public function updateNecessary(): bool
    {
        return $this->getAffectedRecords() !== [];
    }

    public function executeUpdate(): bool
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE);

        foreach ($this->getAffectedRecords() as $record) {
            $connection->update(
                self::TABLE,
                [
                    'CType' => $record['list_type'],
                    'list_type' => '',
                ],
                ['uid' => (int)$record['uid']]
            );
        }

        return true;
    }

    /**
     * @return list<array{uid: int, list_type: string}>
     */
    private function getAffectedRecords(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder
            ->select('uid', 'list_type')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list')),
                $queryBuilder->expr()->in(
                    'list_type',
                    $queryBuilder->createNamedParameter(self::LIST_TYPES, ArrayParameterType::STRING)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
