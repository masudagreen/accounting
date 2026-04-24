<?php

declare(strict_types=1);

namespace Rucaro\Domain\CashPlan;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * One labelled row (ex. "売上入金", "給与支給") × 12 monthly amounts in a
 * {@see CashPlan}. Amounts are stored as positive scale-4 decimals; the
 * category carries the sign when we fold into a running balance.
 *
 * Invariants:
 *   - each of `month_1` .. `month_12` is a non-negative decimal string;
 *   - `label` is non-empty and <= 128 chars.
 */
final readonly class CashPlanEntry
{
    public const MONTHS = 12;

    /**
     * @param list<string> $monthlyAmounts Exactly 12 scale-4 decimal strings,
     *     indexed 0..11 for fiscal-term months 1..12.
     */
    public function __construct(
        public string $id,
        public string $cashPlanId,
        public CashPlanCategory $category,
        public string $label,
        public int $sortOrder,
        public array $monthlyAmounts,
        public ?string $memo = null,
    ) {
        if ($label === '') {
            throw ValidationException::withErrors([
                'label' => ['label must not be empty.'],
            ]);
        }
        if (mb_strlen($label) > 128) {
            throw ValidationException::withErrors([
                'label' => ['label must be <= 128 characters.'],
            ]);
        }
        if (count($monthlyAmounts) !== self::MONTHS) {
            throw ValidationException::withErrors([
                'monthlyAmounts' => [sprintf(
                    'monthlyAmounts must contain exactly %d entries, got %d.',
                    self::MONTHS,
                    count($monthlyAmounts),
                )],
            ]);
        }
        foreach ($monthlyAmounts as $i => $amount) {
            if (Decimal::compare($amount, '0.0000') < 0) {
                throw ValidationException::withErrors([
                    'monthlyAmounts' => [sprintf('month_%d must be >= 0.', $i + 1)],
                ]);
            }
        }
    }

    /**
     * Monthly amount for the fiscal month number `1..12`.
     */
    public function amountForMonth(int $month): string
    {
        if ($month < 1 || $month > self::MONTHS) {
            throw ValidationException::withErrors([
                'month' => [sprintf('month must be in 1..%d.', self::MONTHS)],
            ]);
        }
        return $this->monthlyAmounts[$month - 1];
    }

    /**
     * Sum across all 12 months, normalised scale-4.
     */
    public function total(): string
    {
        $sum = '0.0000';
        foreach ($this->monthlyAmounts as $a) {
            $sum = Decimal::add($sum, $a);
        }
        return Decimal::normalize($sum);
    }
}
