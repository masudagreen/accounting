<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;

/**
 * Value object for a single consumption-tax rate row.
 *
 * Ports the legacy `accountingConsumptionTaxJpn` "rate" records surfaced
 * by {@see \Code_Else_Plugin_Accounting_Jpn_ConsumptionTaxList} and the
 * 2019-10-01 軽減税率対応パッチ (Batch14800). Instead of embedding the
 * rate inside a giant Smarty vars tree, we treat it as a first-class
 * master record.
 *
 * Invariants:
 *   - code: non-empty, <= 32 chars;
 *   - ratePercent: scale-2 decimal string in [0.00, 99.99];
 *   - effectiveFrom <= effectiveUntil when both set.
 */
final readonly class ConsumptionTaxRate
{
    public function __construct(
        public string $id,
        public string $code,
        public string $label,
        public string $ratePercent,
        public DateTimeImmutable $effectiveFrom,
        public ?DateTimeImmutable $effectiveUntil,
        public bool $isTaxable,
        public bool $isReduced,
        public int $sortOrder = 0,
    ) {
        if ($code === '' || strlen($code) > 32) {
            throw ValidationException::withErrors([
                'code' => ['code must be 1..32 chars.'],
            ]);
        }
        if (!preg_match('/^\d{1,2}(\.\d{1,2})?$/', $ratePercent)) {
            throw ValidationException::withErrors([
                'ratePercent' => ['ratePercent must be a positive decimal in [0.00, 99.99].'],
            ]);
        }
        if ($effectiveUntil !== null && $effectiveUntil < $effectiveFrom) {
            throw ValidationException::withErrors([
                'effectiveUntil' => ['effectiveUntil must be on or after effectiveFrom.'],
            ]);
        }
    }

    public function isEffectiveOn(DateTimeImmutable $at): bool
    {
        if ($at < $this->effectiveFrom) {
            return false;
        }
        if ($this->effectiveUntil !== null && $at > $this->effectiveUntil) {
            return false;
        }
        return true;
    }

    /**
     * Compute the tax amount for a tax-exclusive base. Returns scale-4.
     * Uses bcmath when available, otherwise a pure-PHP fallback.
     */
    public function taxFromBase(string $base): string
    {
        return self::multiplyPercent($base, $this->ratePercent);
    }

    /**
     * Compute the tax portion of a tax-inclusive gross amount.
     * base = gross / (1 + rate/100); tax = gross - base.
     */
    public function taxFromGross(string $gross): string
    {
        if (function_exists('bcadd')) {
            $factor = bcadd('1', bcdiv($this->ratePercent, '100', 8), 8);
            $base = bcdiv($gross, $factor, 8);
            return bcsub($gross, $base, 4);
        }
        $grossF = (float) $gross;
        $rateF = (float) $this->ratePercent / 100.0;
        $base = $grossF / (1.0 + $rateF);
        $tax = $grossF - $base;
        return number_format($tax, 4, '.', '');
    }

    private static function multiplyPercent(string $amount, string $percent): string
    {
        if (function_exists('bcmul')) {
            return bcdiv(bcmul($amount, $percent, 8), '100', 4);
        }
        $v = ((float) $amount) * ((float) $percent) / 100.0;
        return number_format($v, 4, '.', '');
    }
}
