<?php

declare(strict_types=1);

namespace Rucaro\Domain\TrialBalance;

use DateTimeImmutable;

/**
 * One persisted snapshot row — the month-end cache of a single account's
 * debit/credit totals.
 *
 * Pure DTO; the aggregation logic that produces these values lives in
 * {@see \Rucaro\Application\TrialBalance\RefreshTrialBalanceSnapshotUseCase}.
 */
final readonly class TrialBalanceSnapshot
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $snapshotDate,
        public string $accountTitleId,
        public string $debitTotal,
        public string $creditTotal,
        public string $balance,
        public int $lineCount,
        public DateTimeImmutable $generatedAt,
    ) {
    }
}
