<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax\Service;

use DateTimeImmutable;
use Rucaro\Support\Decimal\Decimal;

/**
 * インボイス制度の経過措置（非登録事業者からの仕入れ）計算。
 *
 *   2023-10-01 〜 2026-09-30 : 80% 控除
 *   2026-10-01 〜 2029-09-30 : 50% 控除
 *   2029-10-01 〜           : 0%  控除
 *
 * Given a tax amount paid to a non-registered counter-party and the
 * transaction date, returns the portion that is still deductible under
 * the transitional measure.
 */
final class InvoiceDeductionCalculator
{
    private const FULL_DEDUCTION_START = '2023-10-01';
    private const HALF_DEDUCTION_START = '2026-10-01';
    private const NO_DEDUCTION_START   = '2029-10-01';

    /** Returns the deductible ratio as a scale-2 percentage (e.g. "80.00"). */
    public function deductibleRatio(DateTimeImmutable $bookedOn): string
    {
        $full = new DateTimeImmutable(self::FULL_DEDUCTION_START);
        $half = new DateTimeImmutable(self::HALF_DEDUCTION_START);
        $none = new DateTimeImmutable(self::NO_DEDUCTION_START);

        if ($bookedOn < $full) {
            // Before invoice regime: still fully deductible.
            return '100.00';
        }
        if ($bookedOn < $half) {
            return '80.00';
        }
        if ($bookedOn < $none) {
            return '50.00';
        }
        return '0.00';
    }

    /**
     * Calculates the deductible portion of a tax amount charged by a
     * non-registered counter-party.
     */
    public function deductibleAmount(DateTimeImmutable $bookedOn, string $taxAmount): string
    {
        $ratio = $this->deductibleRatio($bookedOn);
        if (function_exists('bcmul')) {
            return bcdiv(bcmul($taxAmount, $ratio, 8), '100', 4);
        }
        $v = ((float) $taxAmount) * ((float) $ratio) / 100.0;
        return number_format($v, 4, '.', '');
    }

    /**
     * Disallowed portion = taxAmount − deductibleAmount.
     */
    public function disallowedAmount(DateTimeImmutable $bookedOn, string $taxAmount): string
    {
        $ded = $this->deductibleAmount($bookedOn, $taxAmount);
        return Decimal::add($taxAmount, '-' . ltrim(Decimal::normalize($ded), '-'));
    }
}
