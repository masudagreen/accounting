<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use Rucaro\Support\Decimal\Decimal;

/**
 * Internal decimal arithmetic helpers used by the depreciation calculators.
 *
 * {@see \Rucaro\Support\Decimal\Decimal} intentionally only ships with
 * add/compare/normalize so the accounting use cases never multiply or
 * divide user-supplied decimals. For depreciation we do need a narrow
 * multiplication (rate * book value) and subtraction; these helpers wrap
 * `bcmath` when available and fall back to a float-based implementation
 * rounded to scale 4 — safe for the value ranges we exercise
 * (DECIMAL(18,4), i.e. |amount| <= 10^14).
 */
final class DecimalMath
{
    public static function sub(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            /** @var string */
            return bcsub($a, $b, Decimal::SCALE);
        }
        $negated = str_starts_with($b, '-') ? substr($b, 1) : ('-' . $b);
        return Decimal::add($a, $negated);
    }

    /**
     * Multiply a DECIMAL(18,4) by a non-negative real number with rounding
     * mode "truncate toward zero" (切り捨て / floor for positives).
     * This matches the legacy default `flagFractionDep = 'floor'` behaviour.
     */
    public static function mulFloor(string $a, float $factor): string
    {
        if (function_exists('bcmul')) {
            /** @var string $fs */
            $fs = rtrim(rtrim(sprintf('%.12F', $factor), '0'), '.');
            if ($fs === '' || $fs === '-') {
                $fs = '0';
            }
            /** @var string $product */
            $product = bcmul($a, $fs, Decimal::SCALE + 4);
            return self::floorToScale($product);
        }
        $value = (float) $a * $factor;
        return self::floatToScale4Floor($value);
    }

    /**
     * Divide by an integer with floor rounding.
     */
    public static function divFloor(string $a, int $divisor): string
    {
        if ($divisor === 0) {
            throw new \InvalidArgumentException('Division by zero.');
        }
        if (function_exists('bcdiv')) {
            /** @var string $quotient */
            $quotient = bcdiv($a, (string) $divisor, Decimal::SCALE + 4);
            return self::floorToScale($quotient);
        }
        $value = (float) $a / $divisor;
        return self::floatToScale4Floor($value);
    }

    /**
     * Returns `min(a, b)` at scale 4.
     */
    public static function min(string $a, string $b): string
    {
        return Decimal::compare($a, $b) <= 0 ? Decimal::normalize($a) : Decimal::normalize($b);
    }

    /**
     * Returns `max(a, b)` at scale 4.
     */
    public static function max(string $a, string $b): string
    {
        return Decimal::compare($a, $b) >= 0 ? Decimal::normalize($a) : Decimal::normalize($b);
    }

    /**
     * Truncate a scale-8 intermediate to scale 4 with floor rounding.
     */
    private static function floorToScale(string $v): string
    {
        $negative = str_starts_with($v, '-');
        $abs = ltrim($v, '-');
        $dot = strpos($abs, '.');
        if ($dot === false) {
            return ($negative ? '-' : '') . $abs . '.0000';
        }
        $intPart = substr($abs, 0, $dot);
        $fracPart = substr($abs, $dot + 1);
        $fracPart = substr(str_pad($fracPart, 4, '0'), 0, 4);
        $result = $intPart . '.' . $fracPart;
        return $negative ? '-' . $result : $result;
    }

    private static function floatToScale4Floor(float $value): string
    {
        $negative = $value < 0;
        $abs = abs($value);
        // Scale by 10000 and floor.
        $scaled = (int) floor($abs * 10000);
        $intPart = (string) intdiv($scaled, 10000);
        $frac = str_pad((string) ($scaled % 10000), 4, '0', STR_PAD_LEFT);
        $formatted = $intPart . '.' . $frac;
        return $negative ? '-' . $formatted : $formatted;
    }
}
