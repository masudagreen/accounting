<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Journal;

use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Journal\UnbalancedJournalException;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 複式簿記の仕訳。
 *
 * 不変条件:
 *  1. 借方明細1件以上、貸方明細1件以上
 *  2. 借方合計 == 貸方合計
 *  3. 各明細に勘定科目ID必須 (空文字/null禁止)
 *  4. 各明細の金額は0以上 (マイナス仕訳は別エントリで反対仕訳を切る運用)
 */
#[CoversClass(JournalEntry::class)]
#[CoversClass(JournalLine::class)]
final class JournalEntryTest extends TestCase
{
    #[Test]
    public function 単純仕訳_借方_貸方_各1件で成立(): void
    {
        $entry = JournalEntry::of(
            debits: [JournalLine::of('cash', Money::ofYen(1000))],
            credits: [JournalLine::of('sales', Money::ofYen(1000))],
        );

        self::assertTrue($entry->totalDebits()->equals(Money::ofYen(1000)));
        self::assertTrue($entry->totalCredits()->equals(Money::ofYen(1000)));
        self::assertTrue($entry->isBalanced());
    }

    #[Test]
    public function 複合仕訳_複数借方_複数貸方でも成立(): void
    {
        $entry = JournalEntry::of(
            debits: [
                JournalLine::of('cash', Money::ofYen(700)),
                JournalLine::of('accountsReceivable', Money::ofYen(300)),
            ],
            credits: [
                JournalLine::of('sales', Money::ofYen(900)),
                JournalLine::of('consumptionTaxReceived', Money::ofYen(100)),
            ],
        );

        self::assertTrue($entry->totalDebits()->equals(Money::ofYen(1000)));
        self::assertTrue($entry->totalCredits()->equals(Money::ofYen(1000)));
        self::assertTrue($entry->isBalanced());
    }

    #[Test]
    public function アンバランスな仕訳は例外(): void
    {
        $this->expectException(UnbalancedJournalException::class);
        JournalEntry::of(
            debits: [JournalLine::of('cash', Money::ofYen(1000))],
            credits: [JournalLine::of('sales', Money::ofYen(999))],
        );
    }

    #[Test]
    public function 借方が0行ならエラー(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        JournalEntry::of(
            debits: [],
            credits: [JournalLine::of('sales', Money::ofYen(1000))],
        );
    }

    #[Test]
    public function 貸方が0行ならエラー(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        JournalEntry::of(
            debits: [JournalLine::of('cash', Money::ofYen(1000))],
            credits: [],
        );
    }

    #[Test]
    public function 勘定科目IDが空文字ならエラー(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        JournalLine::of('', Money::ofYen(1000));
    }

    #[Test]
    public function 明細金額が負ならエラー(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        JournalLine::of('cash', Money::ofYen(-1));
    }

    #[Test]
    public function ゼロ円明細は許容する_減価償却の0円ケース等(): void
    {
        // 償却済みでもログとして残るため0円明細は禁止しない
        $entry = JournalEntry::of(
            debits: [JournalLine::of('depreciation', Money::ofYen(0))],
            credits: [JournalLine::of('accumulatedDepreciation', Money::ofYen(0))],
        );
        self::assertTrue($entry->isBalanced());
    }

    #[Test]
    public function 部門と補助科目を伴う明細(): void
    {
        $line = JournalLine::of(
            accountTitleId: 'sales',
            amount: Money::ofYen(1000),
            departmentId: 'sales-dept',
            subAccountTitleId: 'product-A',
        );
        self::assertSame('sales', $line->accountTitleId());
        self::assertSame('sales-dept', $line->departmentId());
        self::assertSame('product-A', $line->subAccountTitleId());
    }

    #[Test]
    public function 端数を含む金額でもバランスする(): void
    {
        // 消費税端数で 333.33 + 333.33 + 333.34 = 1000 のようなケース
        $entry = JournalEntry::of(
            debits: [JournalLine::of('cash', Money::ofYen('1000'))],
            credits: [
                JournalLine::of('sales', Money::ofYen('333.33')),
                JournalLine::of('sales', Money::ofYen('333.33')),
                JournalLine::of('sales', Money::ofYen('333.34')),
            ],
        );
        self::assertTrue($entry->isBalanced());
    }
}
