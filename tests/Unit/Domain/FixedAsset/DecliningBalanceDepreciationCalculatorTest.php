<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FixedAsset;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FixedAsset\Service\DecliningBalanceDepreciationCalculator;
use Rucaro\Domain\FixedAsset\Service\DepreciationCalculationRequest;

#[CoversClass(DecliningBalanceDepreciationCalculator::class)]
final class DecliningBalanceDepreciationCalculatorTest extends TestCase
{
    public function testFirstYearFiveYearLife200Percent(): void
    {
        $calc = new DecliningBalanceDepreciationCalculator('declining_balance_2012');
        $req = self::req(
            cost: '1000000.0000',
            usefulLife: 5,
            openingBook: '1000000.0000',
            accumulated: '0.0000',
        );
        $result = $calc->calculate($req);
        // rate = 0.400 → 400,000
        self::assertSame('400000.0000', $result->depreciationAmount);
        self::assertSame('600000.0000', $result->closingBookValue);
    }

    public function testSecondYearAppliesRateToBookValue(): void
    {
        $calc = new DecliningBalanceDepreciationCalculator('declining_balance_2012');
        $req = self::req(
            cost: '1000000.0000',
            usefulLife: 5,
            openingBook: '600000.0000',
            accumulated: '400000.0000',
        );
        $result = $calc->calculate($req);
        // 600,000 * 0.400 = 240,000
        self::assertSame('240000.0000', $result->depreciationAmount);
        self::assertSame('360000.0000', $result->closingBookValue);
        self::assertSame('640000.0000', $result->accumulatedDepreciation);
    }

    public function testSwitchesToUpdateRateOnceComputedFallsBelowAssured(): void
    {
        $calc = new DecliningBalanceDepreciationCalculator('declining_balance_2012');
        // 5-year life: assured_rate = 0.10800 → assured = 108,000.
        // If book-value × 0.400 < 108,000 (i.e. book < 270,000), switch.
        $req = self::req(
            cost: '1000000.0000',
            usefulLife: 5,
            openingBook: '200000.0000',
            accumulated: '800000.0000',
        );
        $result = $calc->calculate($req);
        // After switch, updateRate = 0.500 applied straight-line to opening:
        // 200,000 × 0.500 = 100,000
        self::assertSame('100000.0000', $result->depreciationAmount);
        self::assertSame('100000.0000', $result->closingBookValue);
    }

    public function testUnknownUsefulLifeFallsBackToStraightLine(): void
    {
        $calc = new DecliningBalanceDepreciationCalculator('declining_balance_2012');
        $req = self::req(
            cost: '1000000.0000',
            usefulLife: 99, // not in table
            openingBook: '1000000.0000',
            accumulated: '0.0000',
        );
        $result = $calc->calculate($req);
        // Straight line fallback: 1,000,000 / 99 ≈ 10,101.0101 → floor → 10,101.0101
        // bcdiv floor → 10101.0101 (but scale-4 floor on result)
        self::assertNotSame('0.0000', $result->depreciationAmount);
    }

    private static function req(
        string $cost,
        int $usefulLife,
        string $openingBook,
        string $accumulated,
    ): DepreciationCalculationRequest {
        return new DepreciationCalculationRequest(
            acquisitionCost: $cost,
            residualValue: '0.0000',
            usefulLifeYears: $usefulLife,
            serviceStartDate: new DateTimeImmutable('2020-04-01'),
            periodStartDate: new DateTimeImmutable('2020-04-01'),
            periodEndDate: new DateTimeImmutable('2021-03-31'),
            periodNumber: 1,
            monthsInService: 12,
            fiscalTermMonths: 12,
            openingBookValue: $openingBook,
            openingAccumulatedDepreciation: $accumulated,
        );
    }
}
