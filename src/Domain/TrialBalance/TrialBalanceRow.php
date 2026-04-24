<?php

declare(strict_types=1);

namespace Rucaro\Domain\TrialBalance;

use Rucaro\Support\Decimal\Decimal;

/**
 * One row of a trial balance — a per-account SUM of posted journal lines.
 *
 * Read model. Immutable by design: all fields are set at construction time
 * and {@see Decimal}-normalised so the rendered JSON / CSV never drifts from
 * the underlying DECIMAL(18,4) precision.
 *
 * `balance` follows the account's `normalSide`:
 *   - `debit`  normal: balance = debit_total - credit_total
 *   - `credit` normal: balance = credit_total - debit_total
 *
 * `debitTotal`, `creditTotal`, and `balance` are always returned as non-negative
 * on the normal side. A contra balance (e.g. a liability that ends up with a
 * debit balance) surfaces as a negative number, which mirrors the Jpn_TrialBalance
 * legacy convention.
 */
final readonly class TrialBalanceRow
{
    public const NORMAL_DEBIT = 'debit';
    public const NORMAL_CREDIT = 'credit';

    public function __construct(
        public string $accountTitleId,
        public string $accountTitleCode,
        public string $accountTitleName,
        public string $accountCategory,
        public string $normalSide,
        public string $debitTotal,
        public string $creditTotal,
        public string $balance,
        public int $lineCount,
    ) {
    }

    /**
     * Build a row from raw SUMs, computing `balance` from `normalSide`.
     */
    public static function compute(
        string $accountTitleId,
        string $accountTitleCode,
        string $accountTitleName,
        string $accountCategory,
        string $normalSide,
        string $debitTotal,
        string $creditTotal,
        int $lineCount,
    ): self {
        $debit  = Decimal::normalize($debitTotal);
        $credit = Decimal::normalize($creditTotal);
        $balance = $normalSide === self::NORMAL_CREDIT
            ? self::subtract($credit, $debit)
            : self::subtract($debit, $credit);
        return new self(
            accountTitleId: $accountTitleId,
            accountTitleCode: $accountTitleCode,
            accountTitleName: $accountTitleName,
            accountCategory: $accountCategory,
            normalSide: $normalSide,
            debitTotal: $debit,
            creditTotal: $credit,
            balance: $balance,
            lineCount: $lineCount,
        );
    }

    /**
     * Sum two rows that point at the same account (used to merge a
     * snapshot row with live-journal SUMs for the same account title).
     */
    public function add(self $other): self
    {
        if ($this->accountTitleId !== $other->accountTitleId) {
            throw new \InvalidArgumentException('Cannot add TrialBalanceRow of different accounts.');
        }
        return self::compute(
            accountTitleId: $this->accountTitleId,
            accountTitleCode: $this->accountTitleCode,
            accountTitleName: $this->accountTitleName,
            accountCategory: $this->accountCategory,
            normalSide: $this->normalSide,
            debitTotal: Decimal::add($this->debitTotal, $other->debitTotal),
            creditTotal: Decimal::add($this->creditTotal, $other->creditTotal),
            lineCount: $this->lineCount + $other->lineCount,
        );
    }

    /**
     * Scale-4 decimal subtraction. Mirrors {@see Decimal::add()} so we don't
     * have to extend the shared helper just for TrialBalance's needs.
     */
    private static function subtract(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            /** @var string */
            return bcsub($a, $b, Decimal::SCALE);
        }
        // Fallback: a - b = a + (-b)
        $negated = str_starts_with($b, '-') ? substr($b, 1) : ('-' . $b);
        return Decimal::add($a, $negated);
    }
}
