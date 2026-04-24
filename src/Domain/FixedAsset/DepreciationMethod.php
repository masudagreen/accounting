<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

/**
 * Enumerates the depreciation methods supported by the fixed-asset port
 * (ADR-012). Values match the DB CHECK constraint in
 * {@see scripts/migrate/0011_fixed_assets.sql}.
 *
 * The three `declining_balance_*` variants reflect the successive Japanese
 * tax reforms that reset the declining-balance rate tables:
 *   - 2007-04-01 250% (old "平成19年新定率") table
 *   - 2012-04-01 200% (平成24年改正) table
 *   - 2016-04-01 200% applied to machinery/equipment/etc.
 *
 * `old_*` variants are used for assets acquired before 2007-04-01 where the
 * "旧定額法 / 旧定率法" rules apply (residual 10%, up to 95% depreciable,
 * then 5-year equal writedown to memo 1 yen).
 */
enum DepreciationMethod: string
{
    case StraightLine = 'straight_line';
    case DecliningBalance = 'declining_balance';
    case DecliningBalance2007 = 'declining_balance_2007';
    case DecliningBalance2012 = 'declining_balance_2012';
    case DecliningBalance2016 = 'declining_balance_2016';
    case OldStraightLine = 'old_straight_line';
    case OldDecliningBalance = 'old_declining_balance';
    case OneShot = 'one_shot';
    case ThreeYearEqual = 'three_year_equal';
    case None = 'none';

    public static function fromDbString(string $raw): self
    {
        return self::from($raw);
    }

    public function isDepreciable(): bool
    {
        return $this !== self::None;
    }

    public function isDeclining(): bool
    {
        return match ($this) {
            self::DecliningBalance,
            self::DecliningBalance2007,
            self::DecliningBalance2012,
            self::DecliningBalance2016,
            self::OldDecliningBalance => true,
            default => false,
        };
    }
}
