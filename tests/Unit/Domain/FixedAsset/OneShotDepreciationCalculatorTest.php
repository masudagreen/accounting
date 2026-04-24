<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FixedAsset;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FixedAsset\Service\DepreciationCalculationRequest;
use Rucaro\Domain\FixedAsset\Service\OneShotDepreciationCalculator;
use Rucaro\Domain\FixedAsset\Service\ThreeYearEqualDepreciationCalculator;
use Rucaro\Domain\FixedAsset\Service\NoDepreciationCalculator;

#[CoversClass(OneShotDepreciationCalculator::class)]
#[CoversClass(ThreeYearEqualDepreciationCalculator::class)]
#[CoversClass(NoDepreciationCalculator::class)]
final class OneShotDepreciationCalculatorTest extends TestCase
{
    public function testOneShotDepreciatesEntireBookMinusMemoInFirstPeriod(): void
    {
        $calc = new OneShotDepreciationCalculator();
        $req = self::req(cost: '290000.0000', openingBook: '290000.0000', periodNumber: 1);
        $result = $calc->calculate($req);
        // Full book minus 1 yen memo
        self::assertSame('289999.0000', $result->depreciationAmount);
        self::assertSame('1.0000', $result->closingBookValue);
    }

    public function testOneShotReturnsZeroAfterFullyDepreciated(): void
    {
        $calc = new OneShotDepreciationCalculator();
        $req = self::req(cost: '290000.0000', openingBook: '1.0000', periodNumber: 2);
        $result = $calc->calculate($req);
        self::assertSame('0.0000', $result->depreciationAmount);
    }

    public function testThreeYearEqualDepreciatesCostOverExactlyThreePeriods(): void
    {
        $calc = new ThreeYearEqualDepreciationCalculator();
        $r1 = $calc->calculate(self::req(cost: '300000.0000', openingBook: '300000.0000', periodNumber: 1));
        self::assertSame('100000.0000', $r1->depreciationAmount);
        $r2 = $calc->calculate(self::req(cost: '300000.0000', openingBook: '200000.0000', periodNumber: 2));
        self::assertSame('100000.0000', $r2->depreciationAmount);
        $r3 = $calc->calculate(self::req(cost: '300000.0000', openingBook: '100000.0000', periodNumber: 3));
        self::assertSame('100000.0000', $r3->depreciationAmount);
        self::assertSame('0.0000', $r3->closingBookValue);
    }

    public function testNoDepreciationCalculatorAlwaysReturnsZero(): void
    {
        $calc = new NoDepreciationCalculator();
        $req = self::req(cost: '5000000.0000', openingBook: '5000000.0000', periodNumber: 5);
        $result = $calc->calculate($req);
        self::assertSame('0.0000', $result->depreciationAmount);
        self::assertSame('5000000.0000', $result->closingBookValue);
    }

    private static function req(
        string $cost,
        string $openingBook,
        int $periodNumber,
    ): DepreciationCalculationRequest {
        return new DepreciationCalculationRequest(
            acquisitionCost: $cost,
            residualValue: '0.0000',
            usefulLifeYears: 1,
            serviceStartDate: new DateTimeImmutable('2020-04-01'),
            periodStartDate: new DateTimeImmutable('2020-04-01'),
            periodEndDate: new DateTimeImmutable('2021-03-31'),
            periodNumber: $periodNumber,
            monthsInService: 12,
            fiscalTermMonths: 12,
            openingBookValue: $openingBook,
            openingAccumulatedDepreciation: '0.0000',
        );
    }
}
