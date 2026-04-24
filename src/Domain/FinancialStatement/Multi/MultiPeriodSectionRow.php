<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Multi;

/**
 * One row in a multi-period comparison table — the horizontal slice across
 * every period for a single section or line (e.g. "売上高" across 2024, 2025,
 * 2026).
 *
 * `amounts` is keyed by `fiscal_term_id` so the renderer can reuse the same
 * order as {@see MultiPeriodFinancialStatement::$periods}; missing keys mean
 * "that period didn't report this row" (treated as 0 by the variance helper).
 *
 * `variance` is `latest - previous` computed at scale-4; `variancePercent` is
 * `(latest - previous) / previous` expressed as a scale-4 percentage
 * ("12.5000" → +12.5%). Both are `null` when only one period is compared, or
 * when `variancePercent`'s denominator (previous) is exactly zero so division
 * would be undefined.
 */
final readonly class MultiPeriodSectionRow
{
    /**
     * @param array<string, string> $amounts fiscalTermId → scale-4 decimal.
     */
    public function __construct(
        public string $sectionCode,
        public string $lineCode,
        public string $label,
        public array $amounts,
        public ?string $variance = null,
        public ?string $variancePercent = null,
        public int $depth = 0,
        public bool $isSubtotal = false,
        public bool $isTotal = false,
    ) {
    }
}
