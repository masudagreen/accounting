<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FinancialStatement;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
use App\Domain\AccountTitle\PlSection;
use App\Domain\FinancialStatement\ProfitAndLossBuilder;
use App\Domain\FinancialStatement\ProfitAndLossStatement;
use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Ledger\Ledger;
use App\Domain\Money\Money;
use App\Domain\TrialBalance\OpeningBalances;
use App\Domain\TrialBalance\TrialBalance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 損益計算書 (Profit and Loss).
 *
 * 計算式:
 *   売上総利益    = 売上 - 売上原価
 *   営業利益      = 売上総利益 - 販売費及び一般管理費
 *   経常利益      = 営業利益 + 営業外収益 - 営業外費用
 *   税引前当期純利益 = 経常利益 + 特別利益 - 特別損失
 *   当期純利益    = 税引前当期純利益 - 法人税等
 */
#[CoversClass(ProfitAndLossStatement::class)]
#[CoversClass(ProfitAndLossBuilder::class)]
final class ProfitAndLossStatementTest extends TestCase
{
    #[Test]
    public function 空の試算表ではすべて0(): void
    {
        $tree = self::tree();
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), Ledger::fromJournalEntries([]));
        $pl = ProfitAndLossBuilder::build($tree, $tb);

        self::assertTrue($pl->sales()->isZero());
        self::assertTrue($pl->grossProfit()->isZero());
        self::assertTrue($pl->operatingIncome()->isZero());
        self::assertTrue($pl->ordinaryIncome()->isZero());
        self::assertTrue($pl->incomeBeforeTax()->isZero());
        self::assertTrue($pl->netIncome()->isZero());
    }

    #[Test]
    public function 売上のみ_当期純利益が売上に等しい(): void
    {
        $tree = self::tree();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('cash', 'sales', 1000)],
        ]);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);

        self::assertTrue($pl->sales()->equals(Money::ofYen(1000)));
        self::assertTrue($pl->netIncome()->equals(Money::ofYen(1000)));
    }

    #[Test]
    public function 売上総利益_売上から売上原価を控除(): void
    {
        $tree = self::tree();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('cash', 'sales', 1000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::entry('purchases', 'cash', 600)],
        ]);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);

        self::assertTrue($pl->sales()->equals(Money::ofYen(1000)));
        self::assertTrue($pl->costOfSales()->equals(Money::ofYen(600)));
        self::assertTrue($pl->grossProfit()->equals(Money::ofYen(400)));
    }

    #[Test]
    public function 営業利益_売上総利益から販管費を控除(): void
    {
        $tree = self::tree();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('cash', 'sales', 1000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::entry('purchases', 'cash', 600)],
            ['date' => new \DateTimeImmutable('2026-04-03'), 'entry' => self::entry('salaries', 'cash', 100)],
        ]);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);

        self::assertTrue($pl->grossProfit()->equals(Money::ofYen(400)));
        self::assertTrue($pl->sellingAndAdmin()->equals(Money::ofYen(100)));
        self::assertTrue($pl->operatingIncome()->equals(Money::ofYen(300)));
    }

    #[Test]
    public function 経常利益_営業利益に営業外損益を加減(): void
    {
        $tree = self::tree();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('cash', 'sales', 1000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::entry('purchases', 'cash', 600)],
            ['date' => new \DateTimeImmutable('2026-04-03'), 'entry' => self::entry('salaries', 'cash', 100)],
            // 営業外収益: 受取利息 50
            ['date' => new \DateTimeImmutable('2026-04-04'), 'entry' => self::entry('cash', 'interestIncome', 50)],
            // 営業外費用: 支払利息 30
            ['date' => new \DateTimeImmutable('2026-04-05'), 'entry' => self::entry('interestExpense', 'cash', 30)],
        ]);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);

        // 300 + 50 - 30 = 320
        self::assertTrue($pl->ordinaryIncome()->equals(Money::ofYen(320)));
    }

    #[Test]
    public function 当期純利益_経常利益から特別損益と税金を加減(): void
    {
        $tree = self::tree();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('cash', 'sales', 1000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::entry('purchases', 'cash', 600)],
            // 特別利益: 固定資産売却益 200
            ['date' => new \DateTimeImmutable('2027-02-01'), 'entry' => self::entry('cash', 'gainOnSaleOfFixedAssets', 200)],
            // 特別損失: 固定資産除却損 50
            ['date' => new \DateTimeImmutable('2027-02-02'), 'entry' => self::entry('lossOnDisposalOfFixedAssets', 'cash', 50)],
            // 法人税等 100
            ['date' => new \DateTimeImmutable('2027-03-31'), 'entry' => self::entry('corporateTax', 'cash', 100)],
        ]);
        $tb = TrialBalance::build($tree, OpeningBalances::empty(), $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);

        // 売上総利益 = 1000 - 600 = 400
        // 営業利益 = 400 (販管費なし)
        // 経常利益 = 400 (営業外なし)
        // 税引前 = 400 + 200 - 50 = 550
        // 当期純利益 = 550 - 100 = 450
        self::assertTrue($pl->incomeBeforeTax()->equals(Money::ofYen(550)));
        self::assertTrue($pl->netIncome()->equals(Money::ofYen(450)));
    }

    /**
     * 試験用の小さな科目ツリー (PL のみ).
     */
    private static function tree(): AccountTree
    {
        $cash = AccountTitle::of('cash', '現金', AccountClassification::Asset);

        return AccountTree::of([
            AccountTreeNode::leaf($cash),
            AccountTreeNode::leaf(AccountTitle::of(
                'sales', '売上高', AccountClassification::Revenue,
                plSection: PlSection::Sales,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'purchases', '仕入高', AccountClassification::Expense,
                plSection: PlSection::CostOfSales,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'salaries', '給料手当', AccountClassification::Expense,
                plSection: PlSection::SellingAndAdmin,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'interestIncome', '受取利息', AccountClassification::Revenue,
                plSection: PlSection::NonOperatingIncome,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'interestExpense', '支払利息', AccountClassification::Expense,
                plSection: PlSection::NonOperatingExpenses,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'gainOnSaleOfFixedAssets', '固定資産売却益', AccountClassification::Revenue,
                plSection: PlSection::ExtraordinaryIncome,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'lossOnDisposalOfFixedAssets', '固定資産除却損', AccountClassification::Expense,
                plSection: PlSection::ExtraordinaryLosses,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'corporateTax', '法人税等', AccountClassification::Expense,
                plSection: PlSection::Tax,
            )),
        ]);
    }

    private static function entry(string $debitId, string $creditId, int $amount): JournalEntry
    {
        return JournalEntry::of(
            debits: [JournalLine::of($debitId, Money::ofYen($amount))],
            credits: [JournalLine::of($creditId, Money::ofYen($amount))],
        );
    }
}
