<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\BlueReturn;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\Service\BlueReturnBuilder;

#[CoversClass(BlueReturnBuilder::class)]
final class BlueReturnBuilderTest extends TestCase
{
    public function testBuildComputesNetIncomeAndTotals(): void
    {
        $builder = new BlueReturnBuilder();

        $snap = $builder->build(
            formType: BlueReturnFormType::General,
            revenueByAccount: ['売上' => '10000000.0000', '雑収入' => '500000.0000'],
            costOfSalesByAccount: ['仕入' => '3000000.0000'],
            expensesByAccount: ['給料賃金' => '2000000.0000', '地代家賃' => '1200000.0000'],
            monthlyRows: [
                ['month' => 1, 'sales' => '800000.0000', 'purchase' => '250000.0000', 'salary' => '166000.0000'],
                ['month' => 2, 'sales' => '900000.0000', 'purchase' => '260000.0000', 'salary' => '166000.0000'],
            ],
            breakdown: [
                'depreciation' => [['name' => '車両', 'periodDepreciation' => '120000']],
                'rent'         => [['label' => '店舗', 'amount' => '1200000']],
            ],
            assetsByAccount: ['現金' => '400000.0000', '売掛金' => '800000.0000'],
            liabilitiesByAccount: ['買掛金' => '300000.0000'],
            equityByAccount: ['元入金' => '900000.0000'],
        );

        self::assertSame('10500000.0000', $snap->page1Pl['revenueTotal']);
        self::assertSame('3000000.0000', $snap->page1Pl['costOfSalesTotal']);
        self::assertSame('3200000.0000', $snap->page1Pl['expensesTotal']);
        // 10_500_000 - 3_000_000 - 3_200_000 = 4_300_000
        self::assertSame('4300000.0000', $snap->page1Pl['netIncome']);
        self::assertSame('1700000.0000', $snap->page2Monthly['totals']['sales']);
        self::assertSame('510000.0000', $snap->page2Monthly['totals']['purchase']);
        self::assertCount(1, $snap->page3Breakdown['depreciation']);
        self::assertSame('1200000.0000', $snap->page4Bs['assetsTotal']);
        self::assertSame('300000.0000', $snap->page4Bs['liabilitiesTotal']);
    }

    public function testBuildHandlesNegativeNetIncome(): void
    {
        $builder = new BlueReturnBuilder();
        $snap = $builder->build(
            formType: BlueReturnFormType::General,
            revenueByAccount: ['売上' => '1000000.0000'],
            costOfSalesByAccount: ['仕入' => '800000.0000'],
            expensesByAccount: ['給料賃金' => '500000.0000'],
            monthlyRows: [],
            breakdown: [],
            assetsByAccount: [],
            liabilitiesByAccount: [],
            equityByAccount: [],
        );
        // 1_000_000 - 800_000 - 500_000 = -300_000
        self::assertSame('-300000.0000', $snap->page1Pl['netIncome']);
    }

    public function testBuildProducesEmptyTotalsForEmptyInputs(): void
    {
        $builder = new BlueReturnBuilder();
        $snap = $builder->build(
            formType: BlueReturnFormType::Agricultural,
            revenueByAccount: [],
            costOfSalesByAccount: [],
            expensesByAccount: [],
            monthlyRows: [],
            breakdown: [],
            assetsByAccount: [],
            liabilitiesByAccount: [],
            equityByAccount: [],
        );
        self::assertSame('0.0000', $snap->page1Pl['netIncome']);
        self::assertSame('0.0000', $snap->page4Bs['assetsTotal']);
        self::assertSame('agricultural', $snap->page1Pl['formType']);
    }
}
