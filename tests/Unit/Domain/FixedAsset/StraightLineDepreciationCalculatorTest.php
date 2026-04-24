<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FixedAsset;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FixedAsset\Service\DepreciationCalculationRequest;
use Rucaro\Domain\FixedAsset\Service\StraightLineDepreciationCalculator;

#[CoversClass(StraightLineDepreciationCalculator::class)]
final class StraightLineDepreciationCalculatorTest extends TestCase
{
    public function testTenYearStraightLineProducesTenPercentAnnually(): void
    {
        $calc = new StraightLineDepreciationCalculator();
        $req = self::req(
            cost: '1000000.0000',
            residual: '0.0000',
            usefulLife: 10,
            months: 12,
            openingBook: '1000000.0000',
            accumulated: '0.0000',
        );
        $result = $calc->calculate($req);
        self::assertSame('100000.0000', $result->depreciationAmount);
        self::assertSame('100000.0000', $result->accumulatedDepreciation);
        self::assertSame('900000.0000', $result->closingBookValue);
    }

    public function testHalfYearInServiceProratesByMonths(): void
    {
        $calc = new StraightLineDepreciationCalculator();
        $req = self::req(
            cost: '1200000.0000',
            residual: '0.0000',
            usefulLife: 10,
            months: 6,
            openingBook: '1200000.0000',
            accumulated: '0.0000',
        );
        $result = $calc->calculate($req);
        // 1,200,000 / 10 = 120,000 → half = 60,000
        self::assertSame('60000.0000', $result->depreciationAmount);
        self::assertSame('1140000.0000', $result->closingBookValue);
    }

    public function testBookValueStopsAboveOneYenMemo(): void
    {
        $calc = new StraightLineDepreciationCalculator();
        // Final period where opening book is almost fully depreciated.
        $req = self::req(
            cost: '1000000.0000',
            residual: '0.0000',
            usefulLife: 10,
            months: 12,
            openingBook: '50.0000',
            accumulated: '999950.0000',
        );
        $result = $calc->calculate($req);
        // Depreciation capped at opening - 1 yen memo = 49
        self::assertSame('49.0000', $result->depreciationAmount);
        self::assertSame('1.0000', $result->closingBookValue);
    }

    public function testZeroUsefulLifeTreatsAsOneShot(): void
    {
        $calc = new StraightLineDepreciationCalculator();
        $req = self::req(
            cost: '250000.0000',
            residual: '0.0000',
            usefulLife: 0,
            months: 12,
            openingBook: '250000.0000',
            accumulated: '0.0000',
        );
        $result = $calc->calculate($req);
        self::assertSame('250000.0000', $result->depreciationAmount);
        self::assertSame('0.0000', $result->closingBookValue);
    }

    public function testNonZeroResidualValueIsRespected(): void
    {
        $calc = new StraightLineDepreciationCalculator();
        $req = self::req(
            cost: '100000.0000',
            residual: '10000.0000',
            usefulLife: 5,
            months: 12,
            openingBook: '100000.0000',
            accumulated: '0.0000',
        );
        $result = $calc->calculate($req);
        // base = 90,000, yearly = 18,000
        self::assertSame('18000.0000', $result->depreciationAmount);
        self::assertSame('82000.0000', $result->closingBookValue);
    }

    private static function req(
        string $cost,
        string $residual,
        int $usefulLife,
        int $months,
        string $openingBook,
        string $accumulated,
    ): DepreciationCalculationRequest {
        return new DepreciationCalculationRequest(
            acquisitionCost: $cost,
            residualValue: $residual,
            usefulLifeYears: $usefulLife,
            serviceStartDate: new DateTimeImmutable('2020-04-01'),
            periodStartDate: new DateTimeImmutable('2020-04-01'),
            periodEndDate: new DateTimeImmutable('2021-03-31'),
            periodNumber: 1,
            monthsInService: $months,
            fiscalTermMonths: 12,
            openingBookValue: $openingBook,
            openingAccumulatedDepreciation: $accumulated,
        );
    }
}
