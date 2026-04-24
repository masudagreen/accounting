<?php

declare(strict_types=1);

namespace Rucaro\Domain\TrialBalance;

use DateTimeImmutable;

/**
 * Repository port for the `trial_balance_snapshots` table.
 *
 * `saveAll` writes one INSERT per row inside a single transaction (or at least
 * an atomic batch) so partial failures never leave a split-brain month.
 *
 * `deleteByMonth` matches the refresh-use-case's idempotency requirement: the
 * caller wipes the month's rows first, then writes the freshly computed set.
 */
interface TrialBalanceSnapshotRepositoryInterface
{
    /**
     * @param list<TrialBalanceSnapshot> $snapshots
     */
    public function saveAll(array $snapshots): void;

    public function deleteByMonth(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $monthEnd,
    ): void;

    /**
     * Fetch all snapshot rows for a single month end.
     *
     * @return list<TrialBalanceSnapshot>
     */
    public function findByMonth(
        string $entityId,
        string $fiscalTermId,
        DateTimeImmutable $monthEnd,
    ): array;
}
