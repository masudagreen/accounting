<?php

declare(strict_types=1);

namespace Rucaro\Application\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceQueryInterface;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshotRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * Returns a TrialBalance over the requested period.
 *
 * Strategy:
 *   1. If no monthly snapshots exist yet → run one live SUM for the whole
 *      period via the {@see TrialBalanceQueryInterface}.
 *   2. Otherwise use every snapshot up to the most recent month end that is
 *      <= `asOf`, and add a live SUM only for the unsnapshot tail
 *      (latestSnapshotDate + 1 day .. asOf).
 *
 * Rows from both sources are merged by accountTitleId so the caller always
 * receives one row per account, regardless of how it was produced.
 */
final readonly class QueryTrialBalanceUseCase
{
    public function __construct(
        private TrialBalanceQueryInterface $query,
        private TrialBalanceSnapshotRepositoryInterface $snapshots,
        private ClockInterface $clock = new SystemClock(),
    ) {
    }

    public function execute(QueryTrialBalanceUseCaseInput $input): TrialBalance
    {
        $generatedAt = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $latestSnapshot = $this->query->latestSnapshotDate($input->entityId, $input->fiscalTermId);

        if ($latestSnapshot === null || $latestSnapshot > $input->asOf) {
            // No cached month qualifies — just aggregate live.
            $live = $this->query->queryByPeriod(
                $input->entityId,
                $input->fiscalTermId,
                $input->fiscalTermStartDate,
                $input->asOf,
            );
            return $this->rebuildWith($input, $live->rows, $generatedAt);
        }

        $snapshotRows = $this->collectSnapshotRows($input, $latestSnapshot);

        $tailFrom = $this->addOneDay($latestSnapshot);
        if ($tailFrom > $input->asOf) {
            return $this->rebuildWith($input, $snapshotRows, $generatedAt);
        }

        $tail = $this->query->queryByPeriod(
            $input->entityId,
            $input->fiscalTermId,
            $tailFrom,
            $input->asOf,
        );
        $merged = $this->mergeRows($snapshotRows, $tail->rows);
        return $this->rebuildWith($input, $merged, $generatedAt);
    }

    /**
     * @return list<TrialBalanceRow>
     */
    private function collectSnapshotRows(
        QueryTrialBalanceUseCaseInput $input,
        DateTimeImmutable $monthEnd,
    ): array {
        $snapshots = $this->snapshots->findByMonth(
            $input->entityId,
            $input->fiscalTermId,
            $monthEnd,
        );
        // Snapshots carry totals but no chart-of-accounts metadata; fill
        // name/code/category from the query layer's last live pull of the
        // same period so the response stays consistent.
        $enrichedBy = [];
        if ($snapshots !== []) {
            $live = $this->query->queryByPeriod(
                $input->entityId,
                $input->fiscalTermId,
                $input->fiscalTermStartDate,
                $monthEnd,
            );
            foreach ($live->rows as $r) {
                $enrichedBy[$r->accountTitleId] = $r;
            }
        }
        $rows = [];
        foreach ($snapshots as $s) {
            $meta = $enrichedBy[$s->accountTitleId] ?? null;
            $rows[] = TrialBalanceRow::compute(
                accountTitleId: $s->accountTitleId,
                accountTitleCode: $meta?->accountTitleCode ?? '',
                accountTitleName: $meta?->accountTitleName ?? '',
                accountCategory: $meta?->accountCategory ?? '',
                normalSide: $meta?->normalSide ?? TrialBalanceRow::NORMAL_DEBIT,
                debitTotal: $s->debitTotal,
                creditTotal: $s->creditTotal,
                lineCount: $s->lineCount,
            );
        }
        return $rows;
    }

    /**
     * @param list<TrialBalanceRow> $a
     * @param list<TrialBalanceRow> $b
     * @return list<TrialBalanceRow>
     */
    private function mergeRows(array $a, array $b): array
    {
        /** @var array<string, TrialBalanceRow> $byId */
        $byId = [];
        foreach ($a as $row) {
            $byId[$row->accountTitleId] = $row;
        }
        foreach ($b as $row) {
            if (isset($byId[$row->accountTitleId])) {
                $byId[$row->accountTitleId] = $byId[$row->accountTitleId]->add($row);
            } else {
                $byId[$row->accountTitleId] = $row;
            }
        }
        $result = array_values($byId);
        usort(
            $result,
            static fn (TrialBalanceRow $x, TrialBalanceRow $y): int => strcmp($x->accountTitleCode, $y->accountTitleCode),
        );
        return $result;
    }

    /**
     * @param list<TrialBalanceRow> $rows
     */
    private function rebuildWith(
        QueryTrialBalanceUseCaseInput $input,
        array $rows,
        DateTimeImmutable $generatedAt,
    ): TrialBalance {
        return new TrialBalance(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            fromDate: $input->fiscalTermStartDate,
            toDate: $input->asOf,
            currencyCode: $input->currencyCode,
            rows: $rows,
            generatedAt: $generatedAt,
        );
    }

    private function addOneDay(DateTimeImmutable $d): DateTimeImmutable
    {
        return $d->modify('+1 day');
    }
}
