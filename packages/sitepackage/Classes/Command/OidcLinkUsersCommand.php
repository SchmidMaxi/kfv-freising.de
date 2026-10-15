<?php

declare(strict_types=1);

namespace Schmid\Sitepackage\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Verknüpft bestehende fe_users einmalig mit ihrer KFV-ID (fe_users.tx_oidc).
 *
 * Ohne Verknüpfung legt causal/oidc beim ersten Login einen neuen fe_user an – Zuordnungen wie
 * tx_feuerwehren_domain_model_person.fe_user (KatS-Zuständigkeitsbereiche) gingen dann verloren.
 *
 * CSV aus dem IAM (`php artisan iam:export-kfv-ids`), Kopfzeile: email,kfv_id
 */
final class OidcLinkUsersCommand extends Command
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('csv', InputArgument::REQUIRED, 'Pfad zur CSV-Datei (email,kfv_id)')
            ->addOption('apply', null, InputOption::VALUE_NONE, 'Änderungen schreiben (ohne: nur Bericht)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $apply = (bool)$input->getOption('apply');

        $rows = $this->readCsv((string)$input->getArgument('csv'), $io);
        if ($rows === null) {
            return Command::FAILURE;
        }

        $linkedLabel = $apply ? 'verknüpft' : 'würde verknüpft';
        $result = [$linkedLabel => [], 'bereits verknüpft' => [], 'kein Treffer' => [], 'mehrdeutig' => [], 'Konflikt' => []];
        $connection = $this->connectionPool->getConnectionForTable('fe_users');

        foreach ($rows as [$email, $kfvId]) {
            $qb = $this->connectionPool->getQueryBuilderForTable('fe_users');
            $qb->getRestrictions()->removeAll();
            $users = $qb->select('uid', 'username', 'tx_oidc')
                ->from('fe_users')
                ->where(
                    $qb->expr()->eq('deleted', 0),
                    $qb->expr()->comparison('LOWER(' . $qb->quoteIdentifier('email') . ')', '=', $qb->createNamedParameter($email))
                )
                ->executeQuery()
                ->fetchAllAssociative();

            if ($users === []) {
                $result['kein Treffer'][] = $email;
                continue;
            }
            if (count($users) > 1) {
                $result['mehrdeutig'][] = sprintf('%s (uids %s)', $email, implode(', ', array_column($users, 'uid')));
                continue;
            }

            $user = $users[0];
            if ((string)$user['tx_oidc'] === $kfvId) {
                $result['bereits verknüpft'][] = $email;
                continue;
            }
            if ((string)$user['tx_oidc'] !== '') {
                $result['Konflikt'][] = sprintf('%s (uid %d hat bereits andere KFV-ID)', $email, (int)$user['uid']);
                continue;
            }
            if ($this->kfvIdInUse($kfvId)) {
                $result['Konflikt'][] = sprintf('%s (KFV-ID bereits an anderem fe_user)', $email);
                continue;
            }

            if ($apply) {
                $connection->update('fe_users', ['tx_oidc' => $kfvId, 'tstamp' => time()], ['uid' => (int)$user['uid']]);
            }
            $result[$linkedLabel][] = sprintf('%s → uid %d', $email, (int)$user['uid']);
        }

        foreach ($result as $label => $entries) {
            $io->section(sprintf('%s: %d', ucfirst($label), count($entries)));
            if ($output->isVerbose() || $label !== 'kein Treffer') {
                $io->listing($entries);
            }
        }

        $apply
            ? $io->success('Verknüpfungen geschrieben. CSV-Datei jetzt löschen.')
            : $io->note('Dry-Run – nichts geschrieben. Mit --apply ausführen.');

        return Command::SUCCESS;
    }

    /**
     * @return list<array{0: string, 1: string}>|null
     */
    private function readCsv(string $path, SymfonyStyle $io): ?array
    {
        if (!is_readable($path)) {
            $io->error('CSV nicht lesbar: ' . $path);
            return null;
        }
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            $io->error('CSV nicht lesbar: ' . $path);
            return null;
        }

        $header = array_map(static fn(?string $v): string => strtolower(trim((string)$v)), fgetcsv($handle, null, ',', '"', '') ?: []);
        $emailIndex = array_search('email', $header, true);
        $idIndex = array_search('kfv_id', $header, true);
        if ($emailIndex === false || $idIndex === false) {
            fclose($handle);
            $io->error('Kopfzeile muss "email,kfv_id" enthalten.');
            return null;
        }

        $rows = [];
        while (($line = fgetcsv($handle, null, ',', '"', '')) !== false) {
            $email = strtolower(trim((string)($line[$emailIndex] ?? '')));
            $kfvId = strtolower(trim((string)($line[$idIndex] ?? '')));
            if ($email === '' || !preg_match(self::UUID_PATTERN, $kfvId)) {
                continue;
            }
            $rows[] = [$email, $kfvId];
        }
        fclose($handle);

        return $rows;
    }

    private function kfvIdInUse(string $kfvId): bool
    {
        return $this->connectionPool->getConnectionForTable('fe_users')
            ->count('uid', 'fe_users', ['tx_oidc' => $kfvId, 'deleted' => 0]) > 0;
    }
}
