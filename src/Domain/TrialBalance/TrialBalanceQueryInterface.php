<?php

declare(strict_types=1);

namespace Rucaro\Domain\TrialBalance;

use DateTimeImmutable;

/**
 * Port for querying a TrialBalance read model directly from the Journal
 * tables.
 *
 * Implementations live in the Infrastructure layer — typically a PDO-backed
 * query service that issues a GROUP BY SUM over `journal_entry_lines`. The
 * application layer depends on this interface only, so the use case remains
 * persistence-agnostic (ADR-006).
 */
interface TrialBalanceQueryInterface
{
    /**
     * SUM posted journal lines for the given entity + fiscal term over the
     * closed interval [$from, $to] and return one row per account title.
     */
    public function queryByPeriod(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): TrialBalance;

    /**
     * Most recent snapshot_date on record for the given (entity, fiscal term),
     * or null if no snapshot has been generated yet.
     */
    public function latestSnapshotDate(
        string $entityId,
        string $fiscalTermId,
    ): ?DateTimeImmutable;
}
