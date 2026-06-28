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
 *   - `debit`  normal: balance = openingBalance + (debit_total - credit_total)
 *   - `credit` normal: balance = openingBalance + (credit_total - debit_total)
 *
 * `debitTotal`, `creditTotal`, and `balance` are always returned as non-negative
 * on the normal side. A contra balance (e.g. a liability that ends up with a
 * debit balance) surfaces as a negative number, which mirrors the Jpn_TrialBalance
 * legacy convention.
 *
 * `openingBalance` (前期繰越) is the carry-forward at the start of the
 * fiscal-term window the row spans. It is `'0.0000'` for PL accounts and for
 * BS accounts when no `opening_balances` row exists. Phase B-3b folds this
 * into `balance` so multi-period imports do not silently drop carry-forward
 * (see RC-8 in `plan/reality-check-report.md`).
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
        public string $openingBalance = '0.0000',
    ) {
    }

    /**
     * Build a row from raw SUMs, computing `balance` from `normalSide`
     * and folding in any opening (前期繰越) carry-forward.
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
        string $openingBalance = '0.0000',
    ): self {
        $debit = Decimal::normalize($debitTotal);
        $credit = Decimal::normalize($creditTotal);
        $opening = Decimal::normalize($openingBalance);
        $delta = $normalSide === self::NORMAL_CREDIT
            ? self::subtract($credit, $debit)
            : self::subtract($debit, $credit);
        $balance = Decimal::add($opening, $delta);

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
            openingBalance: $opening,
        );
    }

    /**
     * Sum two rows that point at the same account (used to merge a
     * snapshot row with live-journal SUMs for the same account title).
     *
     * The opening (前期繰越) is taken from `$this`; `$other`'s opening is
     * deliberately ignored to avoid double-counting when, e.g., a snapshot
     * row already has the term's opening folded in and a live tail row is
     * appended for unsnapshot dates within the same term.
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
            openingBalance: $this->openingBalance,
        );
    }

    /**
     * Return a copy of this row with the supplied opening folded in.
     *
     * Used by {@see \Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase}
     * to attach 前期繰越 after the per-period SUMs have already been computed
     * (the query layer is opening-blind). Calling this is a no-op when
     * `$opening` is `'0.0000'`.
     */
    public function withOpeningBalance(string $opening): self
    {
        $normalised = Decimal::normalize($opening);
        if ($normalised === $this->openingBalance) {
            return $this;
        }

        return self::compute(
            accountTitleId: $this->accountTitleId,
            accountTitleCode: $this->accountTitleCode,
            accountTitleName: $this->accountTitleName,
            accountCategory: $this->accountCategory,
            normalSide: $this->normalSide,
            debitTotal: $this->debitTotal,
            creditTotal: $this->creditTotal,
            lineCount: $this->lineCount,
            openingBalance: $normalised,
        );
    }

    /**
     * Scale-4 decimal subtraction. Mirrors {@see Decimal::add()} so we don't
     * have to extend the shared helper just for TrialBalance's needs.
     */
    private static function subtract(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            /** @psalm-suppress ArgumentTypeCoercion TrialBalanceRow only sees Decimal-normalised numeric strings */
            return bcsub($a, $b, Decimal::SCALE);
        }
        // Fallback: a - b = a + (-b)
        $negated = str_starts_with($b, '-') ? substr($b, 1) : ('-'.$b);

        return Decimal::add($a, $negated);
    }
}
