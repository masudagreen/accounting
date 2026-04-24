<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

use Rucaro\Domain\FixedAsset\DepreciationMethod;

/**
 * Dispatches a {@see DepreciationMethod} enum to the concrete calculator.
 */
final class DepreciationCalculatorFactory
{
    public function resolve(DepreciationMethod $method): DepreciationCalculatorInterface
    {
        return match ($method) {
            DepreciationMethod::StraightLine,
            DepreciationMethod::OldStraightLine => new StraightLineDepreciationCalculator(),
            DepreciationMethod::DecliningBalance => new DecliningBalanceDepreciationCalculator('declining_balance_2012'),
            DepreciationMethod::DecliningBalance2007 => new DecliningBalanceDepreciationCalculator('declining_balance_2007'),
            DepreciationMethod::DecliningBalance2012 => new DecliningBalanceDepreciationCalculator('declining_balance_2012'),
            DepreciationMethod::DecliningBalance2016 => new DecliningBalanceDepreciationCalculator('declining_balance_2016'),
            DepreciationMethod::OldDecliningBalance => new OldStraightLineDepreciationCalculator(),
            DepreciationMethod::OneShot => new OneShotDepreciationCalculator(),
            DepreciationMethod::ThreeYearEqual => new ThreeYearEqualDepreciationCalculator(),
            DepreciationMethod::None => new NoDepreciationCalculator(),
        };
    }
}
