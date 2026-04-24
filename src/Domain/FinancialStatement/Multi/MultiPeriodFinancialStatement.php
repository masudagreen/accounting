<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Multi;

use DateTimeImmutable;
use InvalidArgumentException;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;

/**
 * Aggregate that holds one {@see MultiPeriodEntry} per fiscal term the caller
 * asked to compare — the N-period view model underneath Phase 6 Wave 6-I's
 * 複数期比較決算書 (multi-period financial statements).
 *
 * Legacy `FinancialStatementMulti*.php` used to carry 3 periods + diff columns
 * hard-coded into a 2D CSV matrix. This readonly aggregate is the shape-less
 * equivalent: ordering is enforced at construction (`periods` must be in
 * ascending `fromDate` order so the rightmost column is the most recent
 * period), but there is no hard cap — higher layers (the HTTP controller)
 * enforce the domain rule that at most five periods are usable for a single
 * A4-landscape PDF.
 */
final readonly class MultiPeriodFinancialStatement
{
    /**
     * @param list<MultiPeriodEntry> $periods Ordered ascending by `fromDate`.
     */
    public function __construct(
        public string $entityId,
        public FinancialStatementKind $kind,
        public array $periods,
        public DateTimeImmutable $generatedAt,
    ) {
        if ($periods === []) {
            throw new InvalidArgumentException('MultiPeriodFinancialStatement requires at least one period.');
        }
        $previous = null;
        foreach ($periods as $entry) {
            if ($previous !== null && $entry->fromDate < $previous->fromDate) {
                throw new InvalidArgumentException(
                    'MultiPeriodFinancialStatement periods must be ordered ascending by fromDate.',
                );
            }
            $previous = $entry;
        }
    }

    public function periodCount(): int
    {
        return count($this->periods);
    }

    /**
     * Most-recent period — rightmost column in the rendered statement.
     */
    public function latestPeriod(): MultiPeriodEntry
    {
        /** @var MultiPeriodEntry $last */
        $last = $this->periods[count($this->periods) - 1];
        return $last;
    }

    /**
     * Period immediately before {@see latestPeriod()}, or `null` when the
     * caller only asked for one period (no variance can be computed).
     */
    public function previousPeriod(): ?MultiPeriodEntry
    {
        $n = count($this->periods);
        if ($n < 2) {
            return null;
        }
        /** @var MultiPeriodEntry $prev */
        $prev = $this->periods[$n - 2];
        return $prev;
    }
}
