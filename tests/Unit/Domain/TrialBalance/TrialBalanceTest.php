<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\TrialBalance;

use App\Domain\AccountTitle\AccountClassification;
use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\AccountTree;
use App\Domain\AccountTitle\AccountTreeNode;
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
 * 残高試算表 (Trial Balance).
 *
 * 不変条件:
 *  1. 全科目の (期首借方残高 + 当期借方発生) の合計
 *     == 全科目の (期首貸方残高 + 当期貸方発生) の合計
 *  2. 期首残高 + 当期発生 = 期末残高 (科目別)
 *  3. 借方科目の通常残高は借方側、貸方科目は貸方側
 *
 * 構築要素:
 *  - 勘定科目ツリー (AccountTree)
 *  - 期首残高 (OpeningBalances)
 *  - 期中の元帳 (Ledger)
 */
#[CoversClass(TrialBalance::class)]
#[CoversClass(OpeningBalances::class)]
final class TrialBalanceTest extends TestCase
{
    #[Test]
    public function 試算表は借方合計と貸方合計が一致する(): void
    {
        $tree = self::tinyTree();
        $opening = OpeningBalances::empty();
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::simpleEntry('cash', 'sales', 1000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::simpleEntry('cash', 'sales', 500)],
            ['date' => new \DateTimeImmutable('2026-04-03'), 'entry' => self::simpleEntry('rent', 'cash', 200)],
        ]);

        $tb = TrialBalance::build($tree, $opening, $ledger);

        self::assertTrue(
            $tb->totalDebits()->equals($tb->totalCredits()),
            sprintf(
                'TB unbalanced: debits=%s credits=%s',
                $tb->totalDebits()->toString(),
                $tb->totalCredits()->toString(),
            ),
        );
    }

    #[Test]
    public function 期首残高_当期発生_期末残高(): void
    {
        $tree = self::tinyTree();

        // 期首: 現金1000 / 資本金1000 (= 借方1000 = 貸方1000)
        $opening = OpeningBalances::of([
            'cash' => Money::ofYen(1000),
            'capitalStock' => Money::ofYen(1000),
        ]);

        // 当期: 売上500計上
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::simpleEntry('cash', 'sales', 500)],
        ]);

        $tb = TrialBalance::build($tree, $opening, $ledger);

        // 現金: 期首1000 + 借方発生500 - 貸方発生0 = 1500
        self::assertTrue($tb->closingBalance('cash')->equals(Money::ofYen(1500)));
        // 売上: 期首0 + 貸方発生500 - 借方発生0 = 500 (貸方科目なので balance 関数は通常残高方向)
        self::assertTrue($tb->closingBalance('sales')->equals(Money::ofYen(500)));
    }

    #[Test]
    public function 期首が空の試算表はバランス(): void
    {
        $tree = self::tinyTree();
        $opening = OpeningBalances::empty();
        $ledger = Ledger::fromJournalEntries([]);

        $tb = TrialBalance::build($tree, $opening, $ledger);
        self::assertTrue($tb->totalDebits()->equals(Money::zero()));
        self::assertTrue($tb->totalCredits()->equals(Money::zero()));
    }

    #[Test]
    public function 期首残高は通常残高方向で入力する(): void
    {
        // OpeningBalances::of は科目ID → 金額 のマップ.
        // 金額は常に通常残高方向の絶対値で表現する.
        $opening = OpeningBalances::of([
            'cash' => Money::ofYen(1000),         // 借方科目 → 借方残高1000
            'accountsPayable' => Money::ofYen(500), // 貸方科目 → 貸方残高500
        ]);

        self::assertTrue($opening->amountFor('cash')->equals(Money::ofYen(1000)));
        self::assertTrue($opening->amountFor('accountsPayable')->equals(Money::ofYen(500)));
        self::assertTrue($opening->amountFor('does-not-exist')->isZero());
    }

    private static function tinyTree(): AccountTree
    {
        return AccountTree::of([
            AccountTreeNode::leaf(AccountTitle::of('cash', '現金', AccountClassification::Asset)),
            AccountTreeNode::leaf(AccountTitle::of('rent', '地代家賃', AccountClassification::Expense)),
            AccountTreeNode::leaf(AccountTitle::of('sales', '売上高', AccountClassification::Revenue)),
            AccountTreeNode::leaf(AccountTitle::of('capitalStock', '資本金', AccountClassification::Equity)),
            AccountTreeNode::leaf(AccountTitle::of('accountsPayable', '買掛金', AccountClassification::Liability)),
        ]);
    }

    private static function simpleEntry(string $debitId, string $creditId, int $amount): JournalEntry
    {
        return JournalEntry::of(
            debits: [JournalLine::of($debitId, Money::ofYen($amount))],
            credits: [JournalLine::of($creditId, Money::ofYen($amount))],
        );
    }
}
