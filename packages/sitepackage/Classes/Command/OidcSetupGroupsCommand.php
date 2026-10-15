<?php

declare(strict_types=1);

namespace Schmid\Sitepackage\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Legt die FE-Gruppen an, die causal/oidc aus den IAM-Rollen (portal_roles) zuweist.
 * Idempotent: vorhandene Gruppen (gleicher Titel im Benutzer-Ordner) erhalten nur das Pattern.
 */
final class OidcSetupGroupsCommand extends Command
{
    /** Gruppentitel => tx_oidc_pattern (Portal-Rollen des IAM-Portals "typo3-kfv") */
    private const GROUPS = [
        'Kommandant' => 'Kommandant',
        'Kreisbrandmeister' => 'Kreisbrandmeister',
    ];

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly ExtensionConfiguration $extensionConfiguration,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('pid', null, InputOption::VALUE_REQUIRED, 'Ordner für die Gruppen (Default: usersStoragePid der OIDC-Konfiguration)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pid = (int)($input->getOption('pid') ?? $this->defaultPid());
        if ($pid <= 0) {
            $io->error('Kein Benutzer-Ordner bekannt. Bitte --pid angeben oder OIDC_USERS_STORAGE_PID setzen.');
            return Command::FAILURE;
        }

        $connection = $this->connectionPool->getConnectionForTable('fe_groups');
        foreach (self::GROUPS as $title => $pattern) {
            $uid = $connection->select(['uid'], 'fe_groups', ['pid' => $pid, 'title' => $title, 'deleted' => 0])->fetchOne();
            if ($uid !== false) {
                $connection->update('fe_groups', ['tx_oidc_pattern' => $pattern, 'tstamp' => time()], ['uid' => (int)$uid]);
                $io->writeln(sprintf('aktualisiert: %s (uid %d)', $title, (int)$uid));
                continue;
            }
            $connection->insert('fe_groups', [
                'pid' => $pid,
                'title' => $title,
                'description' => 'Wird beim Login über den KFV-Login aus der IAM-Rolle zugewiesen.',
                'tx_oidc_pattern' => $pattern,
                'tstamp' => time(),
                'crdate' => time(),
            ]);
            $io->writeln(sprintf('angelegt: %s (uid %d)', $title, (int)$connection->lastInsertId()));
        }

        $io->success('FE-Gruppen für den KFV-Login sind eingerichtet.');
        return Command::SUCCESS;
    }

    private function defaultPid(): int
    {
        $pids = (string)($this->extensionConfiguration->get('oidc', 'usersStoragePid') ?? '');
        return (int)explode(',', $pids)[0];
    }
}
