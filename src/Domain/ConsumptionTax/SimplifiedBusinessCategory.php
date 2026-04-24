<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use Rucaro\Domain\Exception\ValidationException;

/**
 * Simplified-tax 事業区分 / みなし仕入率 table.
 *
 *   1種: 卸売業         - 90%
 *   2種: 小売業         - 80%
 *   3種: 製造業等       - 70%
 *   4種: その他         - 60%
 *   5種: サービス業等   - 50%
 *   6種: 不動産業       - 40%
 *
 * Values match the National Tax Agency guidance (2015-04-01 以降の
 * 事業区分). Used by {@see Service\SimplifiedConsumptionTaxCalculator}.
 */
enum SimplifiedBusinessCategory: int
{
    case Wholesale     = 1;
    case Retail        = 2;
    case Manufacturing = 3;
    case Other         = 4;
    case Service       = 5;
    case RealEstate    = 6;

    public function deemedPurchaseRatio(): string
    {
        return match ($this) {
            self::Wholesale     => '90',
            self::Retail        => '80',
            self::Manufacturing => '70',
            self::Other         => '60',
            self::Service       => '50',
            self::RealEstate    => '40',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Wholesale     => '第1種：卸売業',
            self::Retail        => '第2種：小売業',
            self::Manufacturing => '第3種：製造業等',
            self::Other         => '第4種：その他',
            self::Service       => '第5種：サービス業',
            self::RealEstate    => '第6種：不動産業',
        };
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $c = self::tryFrom($value);
        if ($c === null) {
            throw ValidationException::withErrors([
                'simplifiedBusinessCategory' => ['simplifiedBusinessCategory must be in 1..6.'],
            ]);
        }
        return $c;
    }
}
