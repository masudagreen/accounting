<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal\ValueObject;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;
use Rucaro\Support\Validation\AbstractValueObject;

/**
 * Consumption-tax rate applied to a journal line.
 *
 * Modelled as a DECIMAL(5,2) percentage to match
 * `journal_entry_lines.tax_rate_percent`. The `$isReduced` flag distinguishes
 * the Japanese reduced 8% rate from the historical (pre-2019) 8% rate; both
 * use the same numeric value but map to different tax-report buckets.
 */
final readonly class TaxRate extends AbstractValueObject
{
    public const STANDARD_10 = '10.00';
    public const REDUCED_8   = '8.00';
    public const EXEMPT_0    = '0.00';

    public string $percent;

    public function __construct(string $percent, public bool $isReduced = false)
    {
        if (preg_match('/^\d{1,3}(\.\d{1,2})?$/', $percent) !== 1) {
            throw ValidationException::withErrors([
                'taxRatePercent' => ['taxRatePercent must match DECIMAL(5,2) format.'],
            ]);
        }
        // Normalize to 2-scale canonical form (e.g. "10" -> "10.00").
        $this->percent = self::normalize2($percent);
        if (Decimal::compare($this->percent, '0.00') < 0 || Decimal::compare($this->percent, '100.00') > 0) {
            throw ValidationException::withErrors([
                'taxRatePercent' => ['taxRatePercent must be between 0 and 100.'],
            ]);
        }
    }

    public static function standard10(): self
    {
        return new self(self::STANDARD_10, false);
    }

    public static function reduced8(): self
    {
        return new self(self::REDUCED_8, true);
    }

    public static function exempt(): self
    {
        return new self(self::EXEMPT_0, false);
    }

    public function toDecimal(): string
    {
        return $this->percent;
    }

    public function toPrimitive(): string
    {
        return ($this->isReduced ? 'R' : 'S') . ':' . $this->percent;
    }

    private static function normalize2(string $v): string
    {
        // Decimal::normalize works in scale 4; rewrite to scale 2 for tax rates.
        $dot = strpos($v, '.');
        if ($dot === false) {
            return $v . '.00';
        }
        $int = substr($v, 0, $dot);
        $frac = substr($v, $dot + 1);
        $frac = substr($frac . '00', 0, 2);
        return $int . '.' . $frac;
    }
}
