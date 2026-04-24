<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * One row of a {@see Budget} — a single (account title, optional sub
 * account title) tuple × 12 monthly amounts.
 *
 * Invariants:
 *   - `monthlyAmounts` has exactly 12 scale-4 decimal strings indexed 0..11;
 *   - amounts may be negative (e.g. revenue credit vs expense debit is
 *     decided by the account's normal side at report time, not here);
 *   - `memo` is null or <= 255 chars.
 */
final readonly class BudgetLineItem
{
    public const MONTHS = 12;

    /**
     * @param list<string> $monthlyAmounts Exactly 12 scale-4 decimal strings.
     */
    public function __construct(
        public string $id,
        public string $budgetId,
        public string $accountTitleId,
        public ?string $subAccountTitleId,
        public int $sortOrder,
        public array $monthlyAmounts,
        public ?string $memo = null,
    ) {
        if (count($monthlyAmounts) !== self::MONTHS) {
            throw ValidationException::withErrors([
                'monthlyAmounts' => [sprintf(
                    'monthlyAmounts must contain exactly %d entries, got %d.',
                    self::MONTHS,
                    count($monthlyAmounts),
                )],
            ]);
        }
        foreach ($monthlyAmounts as $amount) {
            // normalize throws InvalidArgumentException on syntactic garbage,
            // giving us a scale-4 Decimal check for free.
            Decimal::normalize($amount);
        }
        if ($memo !== null && mb_strlen($memo) > 255) {
            throw ValidationException::withErrors([
                'memo' => ['memo must be <= 255 characters.'],
            ]);
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
     * Sum of the first `$month` months (inclusive). `cumulativeAmount(12)`
     * is the annual total.
     */
    public function cumulativeAmount(int $month): string
    {
        if ($month < 1 || $month > self::MONTHS) {
            throw ValidationException::withErrors([
                'month' => [sprintf('month must be in 1..%d.', self::MONTHS)],
            ]);
        }
        $sum = '0.0000';
        for ($m = 1; $m <= $month; $m++) {
            $sum = Decimal::add($sum, $this->monthlyAmounts[$m - 1]);
        }
        return Decimal::normalize($sum);
    }

    /**
     * Annual total across all 12 months.
     */
    public function totalAmount(): string
    {
        return $this->cumulativeAmount(self::MONTHS);
    }
}
