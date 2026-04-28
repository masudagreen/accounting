<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\FinancialStatement;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
use App\Domain\AccountTitle\PlSection;
use App\Domain\FinancialStatement\BalanceSheet;
use App\Domain\FinancialStatement\BalanceSheetBuilder;
use App\Domain\FinancialStatement\ProfitAndLossBuilder;
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
 * 貸借対照表 (Balance Sheet).
 *
 * 不変条件:
 *   1. 資産合計 == 負債合計 + 純資産合計 + 当期純利益
 *      (期末締切前の試算表に対して. 当期純利益は別行で純資産扱い)
 */
#[CoversClass(BalanceSheet::class)]
#[CoversClass(BalanceSheetBuilder::class)]
final class BalanceSheetTest extends TestCase
{
    #[Test]
    public function 期首_資本金_資産負債なし_BSはバランス(): void
    {
        $tree = self::tree();
        $opening = OpeningBalances::of([
            'cash' => Money::ofYen(1_000_000),
            'capitalStock' => Money::ofYen(1_000_000),
        ]);

        $tb = TrialBalance::build($tree, $opening, Ledger::fromJournalEntries([]));
        $pl = ProfitAndLossBuilder::build($tree, $tb);
        $bs = BalanceSheetBuilder::build($tree, $tb, $pl);

        self::assertTrue($bs->totalAssets()->equals(Money::ofYen(1_000_000)));
        self::assertTrue($bs->totalEquity()->equals(Money::ofYen(1_000_000)));
        self::assertTrue($bs->totalLiabilities()->isZero());
        self::assertBalanceSheetIsBalanced($bs, $pl->netIncome());
    }

    #[Test]
    public function 売上計上後_当期純利益が右側で増える(): void
    {
        $tree = self::tree();
        $opening = OpeningBalances::of([
            'cash' => Money::ofYen(1_000_000),
            'capitalStock' => Money::ofYen(1_000_000),
        ]);
        $ledger = Ledger::fromJournalEntries([
            // 売上 500 → 現金 +500
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('cash', 'sales', 500)],
        ]);

        $tb = TrialBalance::build($tree, $opening, $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);
        $bs = BalanceSheetBuilder::build($tree, $tb, $pl);

        // 資産: 1,000,000 + 500 = 1,000,500
        self::assertTrue($bs->totalAssets()->equals(Money::ofYen(1_000_500)));
        // 純資産: 1,000,000 (期首)
        self::assertTrue($bs->totalEquity()->equals(Money::ofYen(1_000_000)));
        // 当期純利益: 500
        self::assertTrue($pl->netIncome()->equals(Money::ofYen(500)));
        // 不変条件: 1,000,500 = 0 + 1,000,000 + 500
        self::assertBalanceSheetIsBalanced($bs, $pl->netIncome());
    }

    #[Test]
    public function 借入_負債が増える(): void
    {
        $tree = self::tree();
        $opening = OpeningBalances::empty();
        $ledger = Ledger::fromJournalEntries([
            // 借入 1,000,000
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('cash', 'shortTermLoan', 1_000_000)],
        ]);

        $tb = TrialBalance::build($tree, $opening, $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);
        $bs = BalanceSheetBuilder::build($tree, $tb, $pl);

        self::assertTrue($bs->totalAssets()->equals(Money::ofYen(1_000_000)));
        self::assertTrue($bs->totalLiabilities()->equals(Money::ofYen(1_000_000)));
        self::assertTrue($bs->totalEquity()->isZero());
        self::assertBalanceSheetIsBalanced($bs, $pl->netIncome());
    }

    #[Test]
    public function 損失でも不変条件は成立(): void
    {
        $tree = self::tree();
        $opening = OpeningBalances::of([
            'cash' => Money::ofYen(1_000_000),
            'capitalStock' => Money::ofYen(1_000_000),
        ]);
        $ledger = Ledger::fromJournalEntries([
            // 給料 800 → 現金 -800, 費用 +800
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::entry('salaries', 'cash', 800)],
        ]);
        $tb = TrialBalance::build($tree, $opening, $ledger);
        $pl = ProfitAndLossBuilder::build($tree, $tb);
        $bs = BalanceSheetBuilder::build($tree, $tb, $pl);

        // 資産: 1,000,000 - 800 = 999,200
        self::assertTrue($bs->totalAssets()->equals(Money::ofYen(999_200)));
        // 純資産: 1,000,000 (期首)
        self::assertTrue($bs->totalEquity()->equals(Money::ofYen(1_000_000)));
        // 当期純損失: -800 (netIncome は負数)
        self::assertTrue($pl->netIncome()->equals(Money::ofYen(-800)));
        self::assertBalanceSheetIsBalanced($bs, $pl->netIncome());
    }

    private static function assertBalanceSheetIsBalanced(BalanceSheet $bs, Money $netIncome): void
    {
        $rhs = $bs->totalLiabilities()->plus($bs->totalEquity())->plus($netIncome);
        self::assertTrue(
            $bs->totalAssets()->equals($rhs),
            sprintf(
                'BS unbalanced: assets=%s, liab+equity+netIncome=%s',
                $bs->totalAssets()->toString(),
                $rhs->toString(),
            ),
        );
    }

    private static function tree(): AccountTree
    {
        return AccountTree::of([
            AccountTreeNode::leaf(AccountTitle::of('cash', '現金', AccountClassification::Asset)),
            AccountTreeNode::leaf(AccountTitle::of('shortTermLoan', '短期借入金', AccountClassification::Liability)),
            AccountTreeNode::leaf(AccountTitle::of('capitalStock', '資本金', AccountClassification::Equity)),
            AccountTreeNode::leaf(AccountTitle::of(
                'sales', '売上高', AccountClassification::Revenue,
                plSection: PlSection::Sales,
            )),
            AccountTreeNode::leaf(AccountTitle::of(
                'salaries', '給料手当', AccountClassification::Expense,
                plSection: PlSection::SellingAndAdmin,
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
