<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FinancialStatement;

use App\Domain\FinancialStatement\CashFlowAdjustment;
use App\Domain\FinancialStatement\CashFlowSection;
use App\Domain\FinancialStatement\CashFlowStatement;
use App\Domain\FinancialStatement\CashFlowStatementBuilder;
use App\Domain\FinancialStatement\ProfitAndLossStatement;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * キャッシュフロー計算書 (Cash Flow Statement, 間接法).
 *
 * 不変条件:
 *   期末現金 - 期首現金 = 営業CF + 投資CF + 財務CF
 *   期末現金 = 期首現金 + 全CF合計
 */
#[CoversClass(CashFlowStatement::class)]
#[CoversClass(CashFlowStatementBuilder::class)]
#[CoversClass(CashFlowAdjustment::class)]
#[CoversClass(CashFlowSection::class)]
final class CashFlowStatementTest extends TestCase
{
    // ---------------------------------------------------------------
    // テスト 1: 現金売上のみ → 営業CF = 当期純利益
    // ---------------------------------------------------------------
    #[Test]
    public function 現金売上のみ_営業CFが純利益に等しい(): void
    {
        // 売上 1,000 (全額現金)、費用なし、税なし
        $pl = $this->makePl(
            sales: 1_000,
            costOfSales: 0,
            sellingAndAdmin: 0,
            nonOperatingIncome: 0,
            nonOperatingExpenses: 0,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            tax: 0,
        );

        // 現金: 期首 0 → 期末 1,000、売上債権 / 棚卸資産 / 仕入債務は 0
        $openingCash = Money::ofYen(0);
        $bsOpening = ['cash' => Money::ofYen(0)];
        $bsClosing = [
            'cash'               => Money::ofYen(1_000),
            'accountsReceivable' => Money::ofYen(0),
            'inventory'          => Money::ofYen(0),
            'accountsPayable'    => Money::ofYen(0),
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(0),
            adjustments: [],
        );

        self::assertTrue(
            $cs->operatingCashFlow()->equals(Money::ofYen(1_000)),
            '営業CF = 純利益 1,000 (運転資本変動なし、減価償却なし)',
        );
        self::assertTrue(
            $cs->investingCashFlow()->isZero(),
        );
        self::assertTrue(
            $cs->financingCashFlow()->isZero(),
        );
        // 不変条件
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // テスト 2: 減価償却費が調整される
    // ---------------------------------------------------------------
    #[Test]
    public function 減価償却費が営業CFに加算される(): void
    {
        // シナリオ:
        //   売上 1,000 (全額現金), 減価償却費 200 (費用計上・現金支払なし)
        //   PL: 純利益 (税引前) = 1,000 - 200 = 800
        //   現金: 期首 0 → 現金売上 +1,000 → 期末 1,000
        //         (減価償却費は現金の動きなし)
        //
        //   営業CF = 純利益 800 + 減価償却 200 = 1,000
        //   期末現金 - 期首現金 = 1,000 - 0 = 1,000 = 営業CF ✓
        $pl = $this->makePl(
            sales: 1_000,
            costOfSales: 0,
            sellingAndAdmin: 200,   // 減価償却費 200 を販管費として積んだ想定
            nonOperatingIncome: 0,
            nonOperatingExpenses: 0,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            tax: 0,
        );
        // incomeBeforeTax = netIncome = 1,000 - 200 = 800

        $bsOpening = ['cash' => Money::ofYen(0)];
        $bsClosing = [
            'cash'               => Money::ofYen(1_000), // 現金売上 1,000 入金のみ
            'accountsReceivable' => Money::ofYen(0),
            'inventory'          => Money::ofYen(0),
            'accountsPayable'    => Money::ofYen(0),
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(200),   // 非現金費用として別入力
            adjustments: [],
        );

        // 営業CF = 純利益 (税引前) 800 + 減価償却 200 = 1,000
        self::assertTrue(
            $cs->operatingCashFlow()->equals(Money::ofYen(1_000)),
            '営業CF = 純利益 + 減価償却費: ' . $cs->operatingCashFlow()->toString(),
        );
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // テスト 3: 売掛金の増加で営業CFが減る
    // ---------------------------------------------------------------
    #[Test]
    public function 売掛金の増加により営業CFが減少する(): void
    {
        // PL: 売上 1,000 (全額掛売上)
        $pl = $this->makePl(
            sales: 1_000,
            costOfSales: 0,
            sellingAndAdmin: 0,
            nonOperatingIncome: 0,
            nonOperatingExpenses: 0,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            tax: 0,
        );

        $bsOpening = [
            'cash'               => Money::ofYen(0),
            'accountsReceivable' => Money::ofYen(0),
        ];
        $bsClosing = [
            'cash'               => Money::ofYen(0),     // 現金は増えない (掛売)
            'accountsReceivable' => Money::ofYen(1_000), // 売掛金増加
            'inventory'          => Money::ofYen(0),
            'accountsPayable'    => Money::ofYen(0),
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(0),
            adjustments: [],
        );

        // 営業CF = 純利益 1,000 - 売掛金増加 1,000 = 0
        self::assertTrue(
            $cs->operatingCashFlow()->isZero(),
            '売掛金全額増加で営業CF = 0',
        );
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // テスト 4: 借入で財務CF がプラス
    // ---------------------------------------------------------------
    #[Test]
    public function 借入により財務CFがプラスになる(): void
    {
        $pl = $this->makePl(0, 0, 0, 0, 0, 0, 0, 0);

        $bsOpening = ['cash' => Money::ofYen(0)];
        $bsClosing = [
            'cash'               => Money::ofYen(1_000_000),
            'accountsReceivable' => Money::ofYen(0),
            'inventory'          => Money::ofYen(0),
            'accountsPayable'    => Money::ofYen(0),
        ];

        $adjustments = [
            CashFlowAdjustment::of(
                section: CashFlowSection::Financing,
                amount: Money::ofYen(1_000_000),
                description: '短期借入金の増加',
            ),
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(0),
            adjustments: $adjustments,
        );

        self::assertTrue(
            $cs->financingCashFlow()->equals(Money::ofYen(1_000_000)),
            '財務CF = 借入 1,000,000',
        );
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // テスト 5: 固定資産取得で投資CF がマイナス
    // ---------------------------------------------------------------
    #[Test]
    public function 固定資産取得により投資CFがマイナスになる(): void
    {
        $pl = $this->makePl(0, 0, 0, 0, 0, 0, 0, 0);

        $bsOpening = ['cash' => Money::ofYen(500_000)];
        $bsClosing = [
            'cash'               => Money::ofYen(200_000), // 現金減少 300,000
            'accountsReceivable' => Money::ofYen(0),
            'inventory'          => Money::ofYen(0),
            'accountsPayable'    => Money::ofYen(0),
        ];

        $adjustments = [
            CashFlowAdjustment::of(
                section: CashFlowSection::Investing,
                amount: Money::ofYen(-300_000),
                description: '有形固定資産の取得',
            ),
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(0),
            adjustments: $adjustments,
        );

        self::assertTrue(
            $cs->investingCashFlow()->equals(Money::ofYen(-300_000)),
            '投資CF = -300,000',
        );
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // テスト 6: 期末現金 - 期首現金 = 全CF合計 の不変条件 (複合シナリオ)
    // ---------------------------------------------------------------
    #[Test]
    public function 期末現金マイナス期首現金が全CF合計に等しい_不変条件(): void
    {
        // シナリオ:
        //   売上 2,000 (掛売 500, 現金 1,500)
        //   販管費 300 (全額現金)
        //   減価償却費 200 (費用計上済み = 販管費に含む想定 → 別途加算)
        //   借入 500,000
        //   固定資産取得 -800,000

        // PL: 純利益 = 2,000 - 300 = 1,700 (減価償却200含む)
        $pl = $this->makePl(
            sales: 2_000,
            costOfSales: 0,
            sellingAndAdmin: 300,
            nonOperatingIncome: 0,
            nonOperatingExpenses: 0,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            tax: 0,
        );

        $bsOpening = [
            'cash'               => Money::ofYen(1_000_000),
            'accountsReceivable' => Money::ofYen(0),
        ];
        $bsClosing = [
            'cash'               => Money::ofYen(1_201_500), // 後で計算
            'accountsReceivable' => Money::ofYen(500),
            'inventory'          => Money::ofYen(0),
            'accountsPayable'    => Money::ofYen(0),
        ];

        // 営業CF = 純利益 1,700 + 減価償却 200 - 売掛金増加 500 = 1,400
        // 投資CF = -800,000
        // 財務CF = +500,000
        // 全CF = 1,400 - 800,000 + 500,000 = -298,600
        // 期末現金 = 1,000,000 + (-298,600) = 701,400 (← 整合させる)
        $bsClosing['cash'] = Money::ofYen(701_400);

        $adjustments = [
            CashFlowAdjustment::of(
                section: CashFlowSection::Financing,
                amount: Money::ofYen(500_000),
                description: '借入金の増加',
            ),
            CashFlowAdjustment::of(
                section: CashFlowSection::Investing,
                amount: Money::ofYen(-800_000),
                description: '固定資産の取得',
            ),
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(200),
            adjustments: $adjustments,
        );

        // 営業CF = 1,700 + 200 - 500 = 1,400
        self::assertTrue(
            $cs->operatingCashFlow()->equals(Money::ofYen(1_400)),
            '営業CF: ' . $cs->operatingCashFlow()->toString(),
        );
        // 投資CF
        self::assertTrue(
            $cs->investingCashFlow()->equals(Money::ofYen(-800_000)),
        );
        // 財務CF
        self::assertTrue(
            $cs->financingCashFlow()->equals(Money::ofYen(500_000)),
        );

        // 不変条件: 期末現金 - 期首現金 = 全CF合計
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // テスト 7: 棚卸資産の増加で営業CFが減る
    // ---------------------------------------------------------------
    #[Test]
    public function 棚卸資産の増加により営業CFが減少する(): void
    {
        $pl = $this->makePl(
            sales: 0,
            costOfSales: 0,
            sellingAndAdmin: 0,
            nonOperatingIncome: 0,
            nonOperatingExpenses: 0,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            tax: 0,
        );

        $bsOpening = [
            'cash'      => Money::ofYen(500_000),
            'inventory' => Money::ofYen(0),
        ];
        $bsClosing = [
            'cash'               => Money::ofYen(300_000), // 現金 200,000 減少
            'accountsReceivable' => Money::ofYen(0),
            'inventory'          => Money::ofYen(200_000), // 棚卸資産 増加
            'accountsPayable'    => Money::ofYen(0),
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(0),
            adjustments: [],
        );

        // 純利益 0 - 棚卸資産増加 200,000 = -200,000
        self::assertTrue(
            $cs->operatingCashFlow()->equals(Money::ofYen(-200_000)),
            '棚卸資産増加で営業CF = -200,000',
        );
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // テスト 8: 仕入債務の増加で営業CFがプラス
    // ---------------------------------------------------------------
    #[Test]
    public function 仕入債務の増加により営業CFがプラスになる(): void
    {
        $pl = $this->makePl(
            sales: 0,
            costOfSales: 500,
            sellingAndAdmin: 0,
            nonOperatingIncome: 0,
            nonOperatingExpenses: 0,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            tax: 0,
        );
        // netIncome = -500 (仕入費用)

        $bsOpening = [
            'cash'            => Money::ofYen(1_000),
            'accountsPayable' => Money::ofYen(0),
        ];
        $bsClosing = [
            'cash'               => Money::ofYen(1_000), // 現金は変わらない (掛仕入)
            'accountsReceivable' => Money::ofYen(0),
            'inventory'          => Money::ofYen(0),
            'accountsPayable'    => Money::ofYen(500),   // 仕入債務増加
        ];

        $cs = CashFlowStatementBuilder::build(
            profitAndLoss: $pl,
            openingBsBalances: $bsOpening,
            closingBsBalances: $bsClosing,
            depreciation: Money::ofYen(0),
            adjustments: [],
        );

        // 営業CF = 純利益 (-500) + 仕入債務増加 500 = 0
        self::assertTrue(
            $cs->operatingCashFlow()->isZero(),
            '仕入全額掛で営業CF = 0: ' . $cs->operatingCashFlow()->toString(),
        );
        self::assertCashFlowInvariant($cs, $bsOpening, $bsClosing);
    }

    // ---------------------------------------------------------------
    // ヘルパー
    // ---------------------------------------------------------------

    /**
     * 不変条件: 期末現金 - 期首現金 = 全CF合計.
     *
     * @param array<string, Money> $bsOpening
     * @param array<string, Money> $bsClosing
     */
    private static function assertCashFlowInvariant(
        CashFlowStatement $cs,
        array $bsOpening,
        array $bsClosing,
    ): void {
        $openingCash = $bsOpening['cash'] ?? Money::ofYen(0);
        $closingCash = $bsClosing['cash'] ?? Money::ofYen(0);
        $expectedChange = $closingCash->minus($openingCash);
        $actualChange = $cs->netCashChange();

        self::assertTrue(
            $actualChange->equals($expectedChange),
            sprintf(
                '不変条件違反: 期末現金(%s) - 期首現金(%s) = %s, 全CF合計 = %s',
                $closingCash->toString(),
                $openingCash->toString(),
                $expectedChange->toString(),
                $actualChange->toString(),
            ),
        );
    }

    private function makePl(
        int $sales,
        int $costOfSales,
        int $sellingAndAdmin,
        int $nonOperatingIncome,
        int $nonOperatingExpenses,
        int $extraordinaryIncome,
        int $extraordinaryLosses,
        int $tax,
    ): ProfitAndLossStatement {
        return new ProfitAndLossStatement(
            sales: Money::ofYen($sales),
            costOfSales: Money::ofYen($costOfSales),
            sellingAndAdmin: Money::ofYen($sellingAndAdmin),
            nonOperatingIncome: Money::ofYen($nonOperatingIncome),
            nonOperatingExpenses: Money::ofYen($nonOperatingExpenses),
            extraordinaryIncome: Money::ofYen($extraordinaryIncome),
            extraordinaryLosses: Money::ofYen($extraordinaryLosses),
            tax: Money::ofYen($tax),
        );
    }
}
