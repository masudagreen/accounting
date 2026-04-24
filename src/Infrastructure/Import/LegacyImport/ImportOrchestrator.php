<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

use PDO;
use Rucaro\Infrastructure\Auth\PasswordHasher;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use RuntimeException;

/**
 * Top-level driver that composes the individual importers in the right
 * order and surfaces aggregate statistics.
 *
 * Each stage runs inside its own transaction so a mid-run failure only
 * aborts the current stage, not the entire migration. This is important
 * because the migration is long-running and a single bad row shouldn't
 * invalidate the whole pipeline.
 */
final class ImportOrchestrator
{
    public const STAGE_USERS = 'users';
    public const STAGE_ENTITIES = 'entities';
    public const STAGE_FISCAL_TERMS = 'terms';
    public const STAGE_ACCOUNT_TITLES = 'account-titles';
    public const STAGE_SUB_ACCOUNTS = 'sub-accounts';
    public const STAGE_JOURNALS = 'journals';
    public const STAGE_FIXED_ASSETS = 'fixed-assets';
    public const STAGE_FS_MAPPINGS = 'fs-mappings';

    public const DEFAULT_ORDER = [
        self::STAGE_USERS,
        self::STAGE_ENTITIES,
        self::STAGE_FISCAL_TERMS,
        self::STAGE_ACCOUNT_TITLES,
        self::STAGE_SUB_ACCOUNTS,
        self::STAGE_JOURNALS,
        self::STAGE_FIXED_ASSETS,
        self::STAGE_FS_MAPPINGS,
    ];

    public function __construct(
        private readonly PDO $source,
        private readonly PDO $target,
        private readonly IdMapping $idMap,
        private readonly UlidGenerator $ulids,
        private readonly PasswordHasher $hasher,
        private readonly string $placeholderPassword,
        private readonly bool $dryRun,
    ) {
    }

    /**
     * @param list<string> $stages
     * @return list<ImportReport>
     */
    public function run(array $stages): array
    {
        /** @var list<ImportReport> $reports */
        $reports = [];
        foreach ($stages as $stage) {
            $reports[] = $this->runStage($stage);
        }
        return $reports;
    }

    private function runStage(string $stage): ImportReport
    {
        $importer = $this->buildImporter($stage);

        if ($this->dryRun) {
            return $importer->run();
        }

        // Create the bookkeeping table before opening a transaction.
        // MariaDB implicitly commits on DDL so a mid-transaction
        // `CREATE TABLE` would otherwise break rollback semantics.
        $this->idMap->bootstrapSchema();

        $this->target->beginTransaction();
        try {
            $report = $importer->run();
            if ($this->target->inTransaction()) {
                $this->target->commit();
            }
            return $report;
        } catch (\Throwable $e) {
            if ($this->target->inTransaction()) {
                $this->target->rollBack();
            }
            throw new RuntimeException(
                sprintf('stage "%s" failed: %s', $stage, $e->getMessage()),
                0,
                $e,
            );
        }
    }

    /**
     * @return LegacyUserImporter|LegacyEntityImporter|LegacyFiscalTermImporter|LegacyAccountTitleImporter|LegacySubAccountTitleImporter|LegacyJournalImporter|LegacyFixedAssetImporter|LegacyFsMappingImporter
     */
    private function buildImporter(string $stage): object
    {
        return match ($stage) {
            self::STAGE_USERS => new LegacyUserImporter(
                $this->source,
                $this->target,
                $this->idMap,
                $this->hasher,
                $this->placeholderPassword,
                $this->dryRun,
            ),
            self::STAGE_ENTITIES => new LegacyEntityImporter(
                $this->source,
                $this->target,
                $this->idMap,
                $this->dryRun,
            ),
            self::STAGE_FISCAL_TERMS => new LegacyFiscalTermImporter(
                $this->source,
                $this->target,
                $this->idMap,
                $this->dryRun,
            ),
            self::STAGE_ACCOUNT_TITLES => new LegacyAccountTitleImporter(
                $this->source,
                $this->target,
                $this->idMap,
                $this->dryRun,
            ),
            self::STAGE_SUB_ACCOUNTS => new LegacySubAccountTitleImporter(
                $this->source,
                $this->target,
                $this->idMap,
                $this->dryRun,
            ),
            self::STAGE_JOURNALS => new LegacyJournalImporter(
                $this->source,
                $this->target,
                $this->idMap,
                $this->ulids,
                $this->dryRun,
            ),
            self::STAGE_FIXED_ASSETS => new LegacyFixedAssetImporter(
                $this->source,
                $this->target,
                $this->idMap,
                $this->dryRun,
            ),
            self::STAGE_FS_MAPPINGS => new LegacyFsMappingImporter(
                $this->target,
                $this->idMap,
                $this->ulids,
                $this->dryRun,
            ),
            default => throw new RuntimeException(sprintf('Unknown stage: %s', $stage)),
        };
    }
}
