<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Dto;

use App\Application\Dto\ProfitAndLossDto;
use PHPUnit\Framework\TestCase;

/**
 * ProfitAndLossDto のユニットテスト.
 */
final class ProfitAndLossDtoTest extends TestCase
{
    public function testToArrayContainsAllProfitAndLossFields(): void
    {
        // Arrange
        $dto = new ProfitAndLossDto(
            sales: 1000000,
            costOfSales: 600000,
            grossProfit: 400000,
            sellingAndAdmin: 200000,
            operatingIncome: 200000,
            nonOperatingIncome: 5000,
            nonOperatingExpenses: 3000,
            ordinaryIncome: 202000,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            incomeBeforeTax: 202000,
            tax: 60000,
            netIncome: 142000,
        );

        // Act
        $arr = $dto->toArray();

        // Assert
        $this->assertSame(1000000, $arr['sales']);
        $this->assertSame(600000, $arr['costOfSales']);
        $this->assertSame(400000, $arr['grossProfit']);
        $this->assertSame(200000, $arr['sellingAndAdmin']);
        $this->assertSame(200000, $arr['operatingIncome']);
        $this->assertSame(5000, $arr['nonOperatingIncome']);
        $this->assertSame(3000, $arr['nonOperatingExpenses']);
        $this->assertSame(202000, $arr['ordinaryIncome']);
        $this->assertSame(0, $arr['extraordinaryIncome']);
        $this->assertSame(0, $arr['extraordinaryLosses']);
        $this->assertSame(202000, $arr['incomeBeforeTax']);
        $this->assertSame(60000, $arr['tax']);
        $this->assertSame(142000, $arr['netIncome']);
    }

    public function testAllZeroValuesAreValid(): void
    {
        $dto = new ProfitAndLossDto(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $arr = $dto->toArray();
        $this->assertSame(0, $arr['netIncome']);
    }
}
