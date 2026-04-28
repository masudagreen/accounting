<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FinancialStatement;

use App\Domain\FinancialStatement\EquityChange;
use App\Domain\FinancialStatement\EquityChangeType;
use App\Domain\FinancialStatement\EquitySection;
use App\Domain\FinancialStatement\StatementOfEquity;
use App\Domain\FinancialStatement\StatementOfEquityBuilder;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 株主資本等変動計算書 (Statement of Shareholders' Equity).
 *
 * 不変条件:
 *   期末残高 (各科目) = 期首残高 + 当期変動の合計
 *   純資産合計期末     = 純資産合計期首 + 全変動の合計
 */
#[CoversClass(StatementOfEquity::class)]
#[CoversClass(StatementOfEquityBuilder::class)]
#[CoversClass(EquityChange::class)]
#[CoversClass(EquityChangeType::class)]
#[CoversClass(EquitySection::class)]
final class StatementOfEquityTest extends TestCase
{
    // ---------------------------------------------------------------
    // テスト 1: 期首のみ_変動なし → 期末 = 期首
    // ---------------------------------------------------------------
    #[Test]
    public function 期首のみ_変動なし_期末が期首と等しい(): void
    {
        $opening = [
            EquitySection::CapitalStock->value    => Money::ofYen(1_000_000),
            EquitySection::RetainedEarnings->value => Money::ofYen(200_000),
        ];

        $ss = StatementOfEquityBuilder::build(
            openingBalances: $opening,
            changes: [],
        );

        self::assertTrue(
            $ss->closingBalance(EquitySection::CapitalStock)->equals(Money::ofYen(1_000_000)),
            '変動なしの場合、資本金期末は期首に等しい',
        );
        self::assertTrue(
            $ss->closingBalance(EquitySection::RetainedEarnings)->equals(Money::ofYen(200_000)),
            '変動なしの場合、利益剰余金期末は期首に等しい',
        );
        self::assertTrue(
            $ss->totalEquityClosing()->equals(Money::ofYen(1_200_000)),
            '純資産合計期末 = 1,000,000 + 200,000',
        );
    }

    // ---------------------------------------------------------------
    // テスト 2: 当期純利益 500,000 → 利益剰余金 +500,000
    // ---------------------------------------------------------------
    #[Test]
    public function 当期純利益を加算すると利益剰余金が増加する(): void
    {
        $opening = [
            EquitySection::CapitalStock->value     => Money::ofYen(1_000_000),
            EquitySection::RetainedEarnings->value => Money::ofYen(0),
        ];

        $changes = [
            EquityChange::of(
                type: EquityChangeType::NetIncome,
                section: EquitySection::RetainedEarnings,
                amount: Money::ofYen(500_000),
            ),
        ];

        $ss = StatementOfEquityBuilder::build(
            openingBalances: $opening,
            changes: $changes,
        );

        self::assertTrue(
            $ss->closingBalance(EquitySection::RetainedEarnings)->equals(Money::ofYen(500_000)),
            '利益剰余金期末 = 0 + 500,000',
        );
        self::assertTrue(
            $ss->totalEquityClosing()->equals(Money::ofYen(1_500_000)),
            '純資産合計期末 = 1,000,000 + 500,000',
        );
    }

    // ---------------------------------------------------------------
    // テスト 3: 配当 100,000 → 利益剰余金 -100,000
    // ---------------------------------------------------------------
    #[Test]
    public function 配当により利益剰余金が減少する(): void
    {
        $opening = [
            EquitySection::RetainedEarnings->value => Money::ofYen(500_000),
        ];

        $changes = [
            EquityChange::of(
                type: EquityChangeType::DividendsDeclared,
                section: EquitySection::RetainedEarnings,
                amount: Money::ofYen(-100_000),  // 配当は利益剰余金の減少 = 負
            ),
        ];

        $ss = StatementOfEquityBuilder::build(
            openingBalances: $opening,
            changes: $changes,
        );

        self::assertTrue(
            $ss->closingBalance(EquitySection::RetainedEarnings)->equals(Money::ofYen(400_000)),
            '利益剰余金期末 = 500,000 - 100,000 = 400,000',
        );
    }

    // ---------------------------------------------------------------
    // テスト 4: 新株発行 1,000,000 → 資本金 +1,000,000
    // ---------------------------------------------------------------
    #[Test]
    public function 新株発行により資本金が増加する(): void
    {
        $opening = [
            EquitySection::CapitalStock->value => Money::ofYen(500_000),
        ];

        $changes = [
            EquityChange::of(
                type: EquityChangeType::NewSharesIssued,
                section: EquitySection::CapitalStock,
                amount: Money::ofYen(1_000_000),
            ),
        ];

        $ss = StatementOfEquityBuilder::build(
            openingBalances: $opening,
            changes: $changes,
        );

        self::assertTrue(
            $ss->closingBalance(EquitySection::CapitalStock)->equals(Money::ofYen(1_500_000)),
            '資本金期末 = 500,000 + 1,000,000 = 1,500,000',
        );
    }

    // ---------------------------------------------------------------
    // テスト 5: 純資産合計の期首 + 変動 = 期末 の不変条件
    // ---------------------------------------------------------------
    #[Test]
    public function 純資産合計の期首プラス変動が期末に等しい_不変条件(): void
    {
        $opening = [
            EquitySection::CapitalStock->value      => Money::ofYen(1_000_000),
            EquitySection::CapitalSurplus->value    => Money::ofYen(200_000),
            EquitySection::RetainedEarnings->value  => Money::ofYen(300_000),
            EquitySection::TreasuryStock->value     => Money::ofYen(-50_000),
        ];

        $changes = [
            EquityChange::of(
                type: EquityChangeType::NewSharesIssued,
                section: EquitySection::CapitalStock,
                amount: Money::ofYen(500_000),
            ),
            EquityChange::of(
                type: EquityChangeType::NewSharesIssued,
                section: EquitySection::CapitalSurplus,
                amount: Money::ofYen(500_000),
            ),
            EquityChange::of(
                type: EquityChangeType::NetIncome,
                section: EquitySection::RetainedEarnings,
                amount: Money::ofYen(400_000),
            ),
            EquityChange::of(
                type: EquityChangeType::DividendsDeclared,
                section: EquitySection::RetainedEarnings,
                amount: Money::ofYen(-100_000),
            ),
            EquityChange::of(
                type: EquityChangeType::TreasuryStockAcquisition,
                section: EquitySection::TreasuryStock,
                amount: Money::ofYen(-200_000),
            ),
        ];

        $ss = StatementOfEquityBuilder::build(
            openingBalances: $opening,
            changes: $changes,
        );

        // 期首合計 = 1,000,000 + 200,000 + 300,000 + (-50,000) = 1,450,000
        $openingTotal = Money::ofYen(1_450_000);
        // 変動合計 = 500,000 + 500,000 + 400,000 - 100,000 - 200,000 = 1,100,000
        $totalChange = Money::ofYen(1_100_000);
        // 期末合計 = 2,550,000

        self::assertTrue(
            $ss->totalEquityOpening()->equals($openingTotal),
            '純資産合計期首: ' . $ss->totalEquityOpening()->toString(),
        );
        self::assertTrue(
            $ss->totalChange()->equals($totalChange),
            '純資産変動合計: ' . $ss->totalChange()->toString(),
        );
        self::assertTrue(
            $ss->totalEquityClosing()->equals($openingTotal->plus($totalChange)),
            '不変条件: 期末 = 期首 + 変動',
        );
    }

    // ---------------------------------------------------------------
    // テスト 6: openingBalance で指定していない section は 0 として扱う
    // ---------------------------------------------------------------
    #[Test]
    public function 期首未指定のセクションはゼロとして扱われる(): void
    {
        $ss = StatementOfEquityBuilder::build(
            openingBalances: [],
            changes: [
                EquityChange::of(
                    type: EquityChangeType::Other,
                    section: EquitySection::Other,
                    amount: Money::ofYen(100_000),
                    description: '評価差額金',
                ),
            ],
        );

        self::assertTrue(
            $ss->openingBalance(EquitySection::Other)->isZero(),
        );
        self::assertTrue(
            $ss->closingBalance(EquitySection::Other)->equals(Money::ofYen(100_000)),
        );
    }

    // ---------------------------------------------------------------
    // テスト 7: 自己株式取得と処分の複合
    // ---------------------------------------------------------------
    #[Test]
    public function 自己株式取得と処分の複合変動(): void
    {
        $opening = [
            EquitySection::TreasuryStock->value => Money::ofYen(-100_000),
        ];

        $changes = [
            EquityChange::of(
                type: EquityChangeType::TreasuryStockAcquisition,
                section: EquitySection::TreasuryStock,
                amount: Money::ofYen(-50_000),  // 追加取得 (純資産減少)
            ),
            EquityChange::of(
                type: EquityChangeType::TreasuryStockDisposal,
                section: EquitySection::TreasuryStock,
                amount: Money::ofYen(30_000),   // 処分 (純資産増加)
            ),
        ];

        $ss = StatementOfEquityBuilder::build(
            openingBalances: $opening,
            changes: $changes,
        );

        // 期末 = -100,000 - 50,000 + 30,000 = -120,000
        self::assertTrue(
            $ss->closingBalance(EquitySection::TreasuryStock)->equals(Money::ofYen(-120_000)),
        );
    }

    // ---------------------------------------------------------------
    // テスト 8: changesForSection でセクション別変動一覧が取得できる
    // ---------------------------------------------------------------
    #[Test]
    public function セクション別変動一覧を取得できる(): void
    {
        $changes = [
            EquityChange::of(
                type: EquityChangeType::NetIncome,
                section: EquitySection::RetainedEarnings,
                amount: Money::ofYen(500_000),
            ),
            EquityChange::of(
                type: EquityChangeType::DividendsDeclared,
                section: EquitySection::RetainedEarnings,
                amount: Money::ofYen(-100_000),
            ),
            EquityChange::of(
                type: EquityChangeType::NewSharesIssued,
                section: EquitySection::CapitalStock,
                amount: Money::ofYen(1_000_000),
            ),
        ];

        $ss = StatementOfEquityBuilder::build(
            openingBalances: [],
            changes: $changes,
        );

        $retainedChanges = $ss->changesForSection(EquitySection::RetainedEarnings);
        self::assertCount(2, $retainedChanges);

        $capitalChanges = $ss->changesForSection(EquitySection::CapitalStock);
        self::assertCount(1, $capitalChanges);
    }
}
