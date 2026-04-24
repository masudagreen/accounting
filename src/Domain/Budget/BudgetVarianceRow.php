<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

use Rucaro\Support\Decimal\Decimal;

/**
 * One row of a Budget-vs-Actual variance report.
 *
 * Readonly value object. All decimal fields are scale-4 strings so they
 * round-trip cleanly through DECIMAL(18,4). `usageRatePercent` is stored
 * as a string percentage (e.g. `"112.50"` for 112.5%) or null when the
 * budget is zero — callers then render "N/A" rather than dividing by
 * zero.
 */
final readonly class BudgetVarianceRow
{
    public function __construct(
        public string $accountTitleId,
        public string $accountTitleCode,
        public string $accountTitleName,
        public string $budgetAmount,
        public string $actualAmount,
        public string $varianceAmount,
        public ?string $usageRatePercent,
    ) {
    }

    /**
     * Build a row from raw budget and actual amounts.
     *
     *  - `varianceAmount = actual - budget` (over budget → positive)
     *  - `usageRatePercent = actual / budget * 100` rounded to 2 decimals;
     *    null when `budget == 0` regardless of actual.
     */
    public static function compute(
        string $accountTitleId,
        string $accountTitleCode,
        string $accountTitleName,
        string $budgetAmount,
        string $actualAmount,
    ): self {
        $budget = Decimal::normalize($budgetAmount);
        $actual = Decimal::normalize($actualAmount);
        $variance = self::subtract($actual, $budget);
        $usage = self::safeUsage($budget, $actual);
        return new self(
            accountTitleId: $accountTitleId,
            accountTitleCode: $accountTitleCode,
            accountTitleName: $accountTitleName,
            budgetAmount: $budget,
            actualAmount: $actual,
            varianceAmount: $variance,
            usageRatePercent: $usage,
        );
    }

    /**
     * True when actual exceeds budget by > 0.0001.
     */
    public function isOverBudget(): bool
    {
        return Decimal::compare($this->actualAmount, $this->budgetAmount) > 0;
    }

    /**
     * True when actual is below budget by > 0.0001.
     */
    public function isUnderBudget(): bool
    {
        return Decimal::compare($this->actualAmount, $this->budgetAmount) < 0;
    }

    private static function subtract(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            /** @var string */
            return bcsub($a, $b, Decimal::SCALE);
        }
        $negated = str_starts_with($b, '-') ? substr($b, 1) : ('-' . $b);
        return Decimal::add($a, $negated);
    }

    private static function safeUsage(string $budget, string $actual): ?string
    {
        if (Decimal::compare($budget, '0.0000') === 0) {
            return null;
        }
        // Percent = actual / budget * 100. Use bcmath when available for
        // deterministic string output; otherwise fall back to float math
        // (acceptable because the output is a presentation string, not an
        // accounting amount).
        if (function_exists('bcdiv') && function_exists('bcmul')) {
            /** @var string $div */
            $div = bcdiv($actual, $budget, 6);
            /** @var string $pct */
            $pct = bcmul($div, '100', 2);
            return $pct;
        }
        $b = (float) $budget;
        $a = (float) $actual;
        return number_format(($a / $b) * 100.0, 2, '.', '');
    }
}
