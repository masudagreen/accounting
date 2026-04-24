<?php

declare(strict_types=1);

namespace Rucaro\Application\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\TrialBalance\TrialBalanceQueryInterface;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshot;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshotRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * Recomputes one month of trial-balance snapshot rows and persists them.
 *
 * Idempotent — the existing month is deleted first, then the freshly SUMed
 * rows are written. Only posted (approved) journal entries contribute, which
 * is enforced by the query service's SQL (`status IN ('posted','approved')`).
 *
 * Typical caller: an end-of-month cron, or the closer-of-books action when
 * the user marks a fiscal term's month as closed.
 */
final readonly class RefreshTrialBalanceSnapshotUseCase
{
    public function __construct(
        private TrialBalanceQueryInterface $query,
        private TrialBalanceSnapshotRepositoryInterface $snapshots,
        private UlidGenerator $ulids,
        private ClockInterface $clock = new SystemClock(),
    ) {
    }

    public function execute(RefreshTrialBalanceSnapshotUseCaseInput $input): int
    {
        $generatedAt = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));

        $live = $this->query->queryByPeriod(
            $input->entityId,
            $input->fiscalTermId,
            $input->monthStartDate,
            $input->monthEndDate,
        );

        $this->snapshots->deleteByMonth(
            $input->entityId,
            $input->fiscalTermId,
            $input->monthEndDate,
        );

        $records = [];
        foreach ($live->rows as $row) {
            $records[] = new TrialBalanceSnapshot(
                id: $this->ulids->generate(),
                entityId: $input->entityId,
                fiscalTermId: $input->fiscalTermId,
                snapshotDate: $this->atUtcMidnight($input->monthEndDate),
                accountTitleId: $row->accountTitleId,
                debitTotal: $row->debitTotal,
                creditTotal: $row->creditTotal,
                balance: $row->balance,
                lineCount: $row->lineCount,
                generatedAt: $generatedAt,
            );
        }
        $this->snapshots->saveAll($records);

        return count($records);
    }

    private function atUtcMidnight(DateTimeImmutable $d): DateTimeImmutable
    {
        // Preserve the calendar date regardless of the caller's timezone —
        // we only care about "the month end" at day granularity, and the DB
        // column is DATE anyway.
        return new DateTimeImmutable(
            $d->format('Y-m-d') . 'T00:00:00',
            new DateTimeZone('UTC'),
        );
    }
}
