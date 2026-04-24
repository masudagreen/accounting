<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Budget;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Budget\BudgetVarianceRow;

#[CoversClass(BudgetVarianceRow::class)]
final class BudgetVarianceRowTest extends TestCase
{
    public function testOverBudgetRowComputesPositiveVariance(): void
    {
        $row = BudgetVarianceRow::compute(
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC0',
            accountTitleCode: '5000',
            accountTitleName: '販管費',
            budgetAmount: '100000.0000',
            actualAmount: '120000.0000',
        );
        self::assertSame('20000.0000', $row->varianceAmount);
        self::assertSame('120.00', $row->usageRatePercent);
        self::assertTrue($row->isOverBudget());
        self::assertFalse($row->isUnderBudget());
    }

    public function testUnderBudgetRowComputesNegativeVariance(): void
    {
        $row = BudgetVarianceRow::compute(
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC0',
            accountTitleCode: '4000',
            accountTitleName: '売上',
            budgetAmount: '500000.0000',
            actualAmount: '400000.0000',
        );
        self::assertSame('-100000.0000', $row->varianceAmount);
        self::assertSame('80.00', $row->usageRatePercent);
        self::assertTrue($row->isUnderBudget());
        self::assertFalse($row->isOverBudget());
    }

    public function testZeroBudgetYieldsNullUsage(): void
    {
        $row = BudgetVarianceRow::compute(
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC0',
            accountTitleCode: '9999',
            accountTitleName: '未分類',
            budgetAmount: '0.0000',
            actualAmount: '5000.0000',
        );
        self::assertNull($row->usageRatePercent);
        self::assertSame('5000.0000', $row->varianceAmount);
        self::assertTrue($row->isOverBudget());
    }

    public function testEqualAmountsAreNeutral(): void
    {
        $row = BudgetVarianceRow::compute(
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC0',
            accountTitleCode: '5001',
            accountTitleName: '水道光熱費',
            budgetAmount: '10000.0000',
            actualAmount: '10000.0000',
        );
        self::assertSame('0.0000', $row->varianceAmount);
        self::assertSame('100.00', $row->usageRatePercent);
        self::assertFalse($row->isOverBudget());
        self::assertFalse($row->isUnderBudget());
    }
}
