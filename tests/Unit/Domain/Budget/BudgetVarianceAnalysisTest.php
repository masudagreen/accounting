<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Budget;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Budget\BudgetVarianceAnalysis;
use Rucaro\Domain\Budget\BudgetVarianceRow;

#[CoversClass(BudgetVarianceAnalysis::class)]
final class BudgetVarianceAnalysisTest extends TestCase
{
    public function testTotalsAggregateEveryRow(): void
    {
        $analysis = $this->analysis([
            BudgetVarianceRow::compute('01HAAAAAAAAAAAAAAAAAAAAAC0', '4000', '売上',       '1000000.0000', '800000.0000'),
            BudgetVarianceRow::compute('01HAAAAAAAAAAAAAAAAAAAAAC1', '5000', '仕入',       '300000.0000',  '350000.0000'),
            BudgetVarianceRow::compute('01HAAAAAAAAAAAAAAAAAAAAAC2', '5500', '販管費',     '50000.0000',   '60000.0000'),
        ]);
        self::assertSame('1350000.0000', $analysis->totalBudget());
        self::assertSame('1210000.0000', $analysis->totalActual());
        self::assertSame('-140000.0000', $analysis->totalVariance());
    }

    public function testPartitionsOverAndUnderBudget(): void
    {
        $analysis = $this->analysis([
            BudgetVarianceRow::compute('01HAAAAAAAAAAAAAAAAAAAAAC0', '4000', '売上',   '1000000.0000', '800000.0000'),
            BudgetVarianceRow::compute('01HAAAAAAAAAAAAAAAAAAAAAC1', '5000', '仕入',   '300000.0000',  '350000.0000'),
            BudgetVarianceRow::compute('01HAAAAAAAAAAAAAAAAAAAAAC2', '5500', '販管費', '50000.0000',   '50000.0000'),
        ]);
        self::assertCount(1, $analysis->underBudgetRows());
        self::assertCount(1, $analysis->overBudgetRows());
    }

    /**
     * @param list<BudgetVarianceRow> $rows
     */
    private function analysis(array $rows): BudgetVarianceAnalysis
    {
        $now = new DateTimeImmutable('2026-05-01T00:00:00Z');
        return new BudgetVarianceAnalysis(
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            budgetName: 'Plan 2026',
            status: BudgetStatus::Approved,
            periodFrom: new DateTimeImmutable('2026-04-01'),
            periodTo: $now,
            currencyCode: 'JPY',
            rows: $rows,
            generatedAt: $now,
        );
    }
}
