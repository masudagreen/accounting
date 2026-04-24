<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;
use Rucaro\Infrastructure\Auth\PasswordHasher;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * `php scripts/import/legacy_to_v2.php` — end-to-end legacy -> new schema
 * migration CLI.
 *
 * Connects to the legacy DB (`--source-db`) and the new DB (`--target-db`),
 * then drives {@see ImportOrchestrator} through the configured stages.
 * Exactly one of `--dry-run` or `--apply` must be set.
 */
#[AsCommand(name: 'legacy:import', description: 'Import legacy Rucaro data into the new schema')]
final class LegacyToV2Command extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('source-db', null, InputOption::VALUE_REQUIRED, 'Legacy DB name', 'rucaro_legacy')
            ->addOption('target-db', null, InputOption::VALUE_REQUIRED, 'New (target) DB name', 'rucaro')
            ->addOption('db-host', null, InputOption::VALUE_REQUIRED, 'DB host', '127.0.0.1')
            ->addOption('db-port', null, InputOption::VALUE_REQUIRED, 'DB port', '3307')
            ->addOption('db-user', null, InputOption::VALUE_REQUIRED, 'DB user', 'root')
            ->addOption('db-password', null, InputOption::VALUE_REQUIRED, 'DB password', 'root')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Read-only preview; does not write to target')
            ->addOption('apply', null, InputOption::VALUE_NONE, 'Actually apply the migration')
            ->addOption(
                'stage',
                null,
                InputOption::VALUE_REQUIRED,
                'all | users | entities | terms | account-titles | sub-accounts | journals | fixed-assets | fs-mappings',
                'all'
            )
            ->addOption('truncate-target', null, InputOption::VALUE_NONE, 'Delete previously-migrated rows before re-import')
            ->addOption(
                'placeholder-password',
                null,
                InputOption::VALUE_REQUIRED,
                'Argon2id placeholder password assigned to every migrated user (required)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dryRun = (bool) $input->getOption('dry-run');
        $apply = (bool) $input->getOption('apply');
        if ($dryRun === $apply) {
            $io->error('Exactly one of --dry-run or --apply must be set.');
            return Command::INVALID;
        }

        $placeholder = (string) $input->getOption('placeholder-password');
        if (strlen($placeholder) < 8) {
            $io->error('--placeholder-password is required (min 8 chars).');
            return Command::INVALID;
        }

        $sourceDb = (string) $input->getOption('source-db');
        $targetDb = (string) $input->getOption('target-db');
        $host = (string) $input->getOption('db-host');
        $port = (string) $input->getOption('db-port');
        $user = (string) $input->getOption('db-user');
        $pass = (string) $input->getOption('db-password');

        try {
            $source = $this->connect($host, $port, $user, $pass, $sourceDb);
            $target = $this->connect($host, $port, $user, $pass, $targetDb);
        } catch (\Throwable $e) {
            $io->error('DB connect failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $ulids = new UlidGenerator();
        $idMap = new IdMapping($target, $ulids, $dryRun);
        $hasher = new PasswordHasher();

        if ($input->getOption('truncate-target')) {
            $io->warning('--truncate-target: deleting previously-migrated rows');
            $this->truncateTarget($target, $idMap);
        }

        $stageOpt = (string) $input->getOption('stage');
        $stages = $this->resolveStages($stageOpt);
        if ($stages === []) {
            $io->error('Invalid --stage value: ' . $stageOpt);
            return Command::INVALID;
        }

        $orchestrator = new ImportOrchestrator(
            $source,
            $target,
            $idMap,
            $ulids,
            $hasher,
            $placeholder,
            $dryRun,
        );

        $io->title(sprintf(
            'Legacy → V2 migration  (%s)  [%s → %s]',
            $dryRun ? 'DRY-RUN' : 'APPLY',
            $sourceDb,
            $targetDb,
        ));
        $io->writeln('Stages: ' . implode(', ', $stages));

        try {
            $reports = $orchestrator->run($stages);
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $this->renderReports($io, $reports);
        return Command::SUCCESS;
    }

    private function connect(string $host, string $port, string $user, string $pass, string $db): PDO
    {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec("SET time_zone = '+00:00'");
        return $pdo;
    }

    /**
     * @return list<string>
     */
    private function resolveStages(string $stageOpt): array
    {
        if ($stageOpt === 'all') {
            return ImportOrchestrator::DEFAULT_ORDER;
        }
        if (in_array($stageOpt, ImportOrchestrator::DEFAULT_ORDER, true)) {
            return [$stageOpt];
        }
        return [];
    }

    /**
     * Remove rows previously migrated from the target DB. We use the
     * `legacy_id_mapping` registry to scope deletes so we do not touch
     * rows seeded by migrations or created by the dev user.
     */
    private function truncateTarget(PDO $target, IdMapping $idMap): void
    {
        // Delete in reverse FK order.
        $tables = [
            'account_title_fs_mappings',
            'journal_entry_lines',
            'journal_entries',
            'fixed_assets',
            'sub_account_titles',
            'account_titles',
            'fiscal_terms',
            'entities',
            'users',
        ];
        $target->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $tbl) {
            $sql = sprintf(
                "DELETE FROM %s WHERE id IN (SELECT new_ulid FROM legacy_id_mapping)",
                $tbl,
            );
            $target->exec($sql);
        }
        $target->exec('SET FOREIGN_KEY_CHECKS=1');
        $idMap->truncate();
    }

    /**
     * @param list<ImportReport> $reports
     */
    private function renderReports(SymfonyStyle $io, array $reports): void
    {
        $io->section('Results');
        $rows = [];
        foreach ($reports as $r) {
            $rows[] = [$r->stage, $r->read, $r->inserted, $r->skipped];
        }
        $io->table(['stage', 'read', 'inserted', 'skipped'], $rows);
        foreach ($reports as $r) {
            if ($r->notes !== []) {
                $io->writeln(sprintf('<comment>[%s notes]</>', $r->stage));
                foreach (array_slice($r->notes, 0, 10) as $n) {
                    $io->writeln('  - ' . $n);
                }
                if (count($r->notes) > 10) {
                    $io->writeln(sprintf('  ... (+%d more)', count($r->notes) - 10));
                }
            }
        }
    }
}
