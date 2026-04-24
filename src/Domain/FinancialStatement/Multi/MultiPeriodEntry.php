<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Multi;

use DateTimeImmutable;
use Rucaro\Domain\FinancialStatement\FinancialStatement;

/**
 * One column in a {@see MultiPeriodFinancialStatement} — the single-period
 * {@see FinancialStatement} for a specific `fiscal_term` plus the label and
 * date range callers need to render a meaningful column header ("第 X 期",
 * "2026-04-01 〜 2027-03-31").
 *
 * Immutable value object; the wrapped {@see FinancialStatement} is already
 * readonly so this type adds no mutation surface.
 */
final readonly class MultiPeriodEntry
{
    public function __construct(
        public string $fiscalTermId,
        public string $fiscalTermLabel,
        public DateTimeImmutable $fromDate,
        public DateTimeImmutable $toDate,
        public FinancialStatement $statement,
    ) {
    }
}
