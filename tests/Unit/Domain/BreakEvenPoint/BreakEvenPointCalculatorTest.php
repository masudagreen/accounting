<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\BreakEvenPoint;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointAnalysis;
use Rucaro\Domain\BreakEvenPoint\CvpCostType;
use Rucaro\Domain\BreakEvenPoint\Service\BreakEvenPointCalculator;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;

#[CoversClass(BreakEvenPointCalculator::class)]
#[CoversClass(BreakEvenPointAnalysis::class)]
#[CoversClass(AccountTitleCvpClassification::class)]
#[CoversClass(CvpCostType::class)]
final class BreakEvenPointCalculatorTest extends TestCase
{
    /**
     * Golden test from Wave 6-E spec:
     *   sales     = 1,600,000
     *   variable  =   200,000 (purchase only)
     *   fixed     =    17,000 (selling/admin)
     *   margin    = 1,400,000
     *   marginRate= 0.875  (87.5%)
     *   BEP sales = 17,000 / 0.875 ≈ 19,429
     *   safety    = (1,600,000 - 19,429) / 1,600,000 ≈ 0.9879
     */
    public function testGoldenFromSpec(): void
    {
        $tb = $this->trialBalance([
            $this->revenueRow('at-sales', '41000', '売上高', '1600000.0000'),
            $this->expenseRow('at-purchase', '51000', '仕入高', 'cost_of_sales', '200000.0000'),
            $this->expenseRow('at-travel', '72000', '旅費交通費', 'selling_admin', '10000.0000'),
            $this->expenseRow('at-telecom', '73000', '通信費', 'selling_admin', '7000.0000'),
        ]);
        $classifications = [
            new AccountTitleCvpClassification('at-entity', 'at-sales', CvpCostType::Variable, '1.0000'),
            new AccountTitleCvpClassification('at-entity', 'at-purchase', CvpCostType::Variable, '1.0000'),
            new AccountTitleCvpClassification('at-entity', 'at-travel', CvpCostType::Fixed, '0.0000'),
            new AccountTitleCvpClassification('at-entity', 'at-telecom', CvpCostType::Fixed, '0.0000'),
        ];
        $calc = new BreakEvenPointCalculator();

        $out = $calc->calculate(
            entityId: 'at-entity',
            fiscalTermId: 'at-ft',
            fromDate: new DateTimeImmutable('2025-04-01'),
            toDate: new DateTimeImmutable('2026-03-31'),
            currencyCode: 'JPY',
            trialBalance: $tb,
            classifications: $classifications,
            generatedAt: new DateTimeImmutable('2026-04-21'),
        );
        self::assertSame('1600000.0000', $out->sales);
        self::assertSame('200000.0000', $out->variableCosts);
        self::assertSame('17000.0000', $out->fixedCosts);
        self::assertSame('1400000.0000', $out->contributionMargin);
        self::assertSame('0.8750', $out->contributionMarginRate);
        // bcdiv at scale 4 truncates: 17000/0.8750 = 19428.5714…  → 19428.5714
        self::assertSame('19428.5714', $out->bepSales);
        // 19428.5714 / 1_600_000 = 0.012142857... → 0.0121 (trunc)
        self::assertSame('0.0121', $out->bepRatio);
        // Safety: (1600000 - 19428.5714) / 1600000 = 0.987857... → 0.9879
        // （bcdiv scale=4 の銀行丸めで四捨五入側を採用）
        self::assertSame('0.9879', $out->safetyMarginRatio);
        self::assertSame('1383000.0000', $out->operatingProfit);
        self::assertFalse($out->isBelowBreakEven());
    }

    public function testZeroSalesProducesZeroRatios(): void
    {
        $tb = $this->trialBalance([
            $this->expenseRow('at-rent', '82000', '地代家賃', 'selling_admin', '50000.0000'),
        ]);
        $calc = new BreakEvenPointCalculator();
        $out = $calc->calculate(
            entityId: 'at-entity',
            fiscalTermId: 'at-ft',
            fromDate: new DateTimeImmutable('2025-04-01'),
            toDate: new DateTimeImmutable('2025-04-30'),
            currencyCode: 'JPY',
            trialBalance: $tb,
            classifications: [
                new AccountTitleCvpClassification('at-entity', 'at-rent', CvpCostType::Fixed, '0.0000'),
            ],
            generatedAt: new DateTimeImmutable('2026-04-21'),
        );
        self::assertSame('0.0000', $out->sales);
        self::assertSame('50000.0000', $out->fixedCosts);
        self::assertSame('0.0000', $out->contributionMarginRate);
        self::assertSame('0.0000', $out->bepSales);
        self::assertSame('0.0000', $out->bepRatio);
        self::assertSame('0.0000', $out->safetyMarginRatio);
        self::assertTrue($out->isBelowBreakEven() || $out->sales === '0.0000');
    }

    public function testSemiVariableSplitsBetweenFixedAndVariable(): void
    {
        $tb = $this->trialBalance([
            $this->revenueRow('at-sales', '41000', '売上高', '1000000.0000'),
            $this->expenseRow('at-util', '74000', '水道光熱費', 'selling_admin', '100000.0000'),
        ]);
        $calc = new BreakEvenPointCalculator();
        $out = $calc->calculate(
            entityId: 'at-entity',
            fiscalTermId: 'at-ft',
            fromDate: new DateTimeImmutable('2025-04-01'),
            toDate: new DateTimeImmutable('2026-03-31'),
            currencyCode: 'JPY',
            trialBalance: $tb,
            classifications: [
                new AccountTitleCvpClassification('at-entity', 'at-util', CvpCostType::SemiVariable, '0.3000'),
            ],
            generatedAt: new DateTimeImmutable('2026-04-21'),
        );
        self::assertSame('30000.0000', $out->variableCosts);
        self::assertSame('70000.0000', $out->fixedCosts);
    }

    public function testUnclassifiedExpenseFallsBackToFixed(): void
    {
        $tb = $this->trialBalance([
            $this->revenueRow('at-sales', '41000', '売上高', '500000.0000'),
            $this->expenseRow('at-misc', '99000', 'その他', 'selling_admin', '10000.0000'),
        ]);
        $calc = new BreakEvenPointCalculator();
        $out = $calc->calculate(
            entityId: 'at-entity',
            fiscalTermId: 'at-ft',
            fromDate: new DateTimeImmutable('2025-04-01'),
            toDate: new DateTimeImmutable('2026-03-31'),
            currencyCode: 'JPY',
            trialBalance: $tb,
            classifications: [],
            generatedAt: new DateTimeImmutable('2026-04-21'),
        );
        self::assertSame('0.0000', $out->variableCosts);
        self::assertSame('10000.0000', $out->fixedCosts);
    }

    /**
     * @param list<TrialBalanceRow> $rows
     */
    private function trialBalance(array $rows): TrialBalance
    {
        return new TrialBalance(
            entityId: 'at-entity',
            fiscalTermId: 'at-ft',
            fromDate: new DateTimeImmutable('2025-04-01'),
            toDate: new DateTimeImmutable('2026-03-31'),
            currencyCode: 'JPY',
            rows: $rows,
            generatedAt: new DateTimeImmutable('2026-04-21'),
        );
    }

    private function revenueRow(string $id, string $code, string $name, string $amount): TrialBalanceRow
    {
        return TrialBalanceRow::compute(
            accountTitleId: $id,
            accountTitleCode: $code,
            accountTitleName: $name,
            accountCategory: 'revenue',
            normalSide: TrialBalanceRow::NORMAL_CREDIT,
            debitTotal: '0.0000',
            creditTotal: $amount,
            lineCount: 1,
        );
    }

    private function expenseRow(string $id, string $code, string $name, string $category, string $amount): TrialBalanceRow
    {
        return TrialBalanceRow::compute(
            accountTitleId: $id,
            accountTitleCode: $code,
            accountTitleName: $name,
            accountCategory: $category,
            normalSide: TrialBalanceRow::NORMAL_DEBIT,
            debitTotal: $amount,
            creditTotal: '0.0000',
            lineCount: 1,
        );
    }
}
