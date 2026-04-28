<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Ledger;

use App\Domain\AccountTitle\NormalBalance;
use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Ledger\Ledger;
use App\Domain\Ledger\LedgerEntry;
use App\Domain\Ledger\LedgerFilter;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 元帳 (Ledger).
 *
 * 仕訳の集まりを科目別/補助科目別/部門別に集計し、各科目の借方合計・貸方合計・残高を計算する.
 *
 * 元実装の `accountingLogCalcJpn` が果たしていた役割:
 *   仕訳1件 → 借方/貸方 サイドごとに展開された LedgerEntry を集計用に持つ.
 */
#[CoversClass(Ledger::class)]
#[CoversClass(LedgerEntry::class)]
#[CoversClass(LedgerFilter::class)]
final class LedgerTest extends TestCase
{
    #[Test]
    public function 仕訳から元帳エントリへ展開_借方と貸方が両方記録される(): void
    {
        $entry = JournalEntry::of(
            debits: [JournalLine::of('cash', Money::ofYen(1000))],
            credits: [JournalLine::of('sales', Money::ofYen(1000))],
        );

        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => $entry],
        ]);

        $cashEntries = $ledger->entriesFor('cash');
        self::assertCount(1, $cashEntries);
        self::assertSame(NormalBalance::Debit, $cashEntries[0]->side());
        self::assertTrue($cashEntries[0]->amount()->equals(Money::ofYen(1000)));

        $salesEntries = $ledger->entriesFor('sales');
        self::assertCount(1, $salesEntries);
        self::assertSame(NormalBalance::Credit, $salesEntries[0]->side());
    }

    #[Test]
    public function 科目別の借方合計と貸方合計(): void
    {
        $ledger = Ledger::fromJournalEntries(self::sampleEntries());

        // cash には 1000 (Debit) + 500 (Debit) = 1500 借方
        self::assertTrue($ledger->totalDebits('cash')->equals(Money::ofYen(1500)));
        self::assertTrue($ledger->totalCredits('cash')->equals(Money::zero()));

        // sales には 1500 貸方
        self::assertTrue($ledger->totalDebits('sales')->equals(Money::zero()));
        self::assertTrue($ledger->totalCredits('sales')->equals(Money::ofYen(1500)));
    }

    #[Test]
    public function 期間絞込(): void
    {
        $entries = [
            ['date' => new \DateTimeImmutable('2026-04-15'), 'entry' => self::simpleEntry('cash', 'sales', 1000)],
            ['date' => new \DateTimeImmutable('2026-05-15'), 'entry' => self::simpleEntry('cash', 'sales', 500)],
            ['date' => new \DateTimeImmutable('2026-06-15'), 'entry' => self::simpleEntry('cash', 'sales', 300)],
        ];
        $ledger = Ledger::fromJournalEntries($entries);

        $filter = LedgerFilter::byDateRange(
            from: new \DateTimeImmutable('2026-05-01'),
            to: new \DateTimeImmutable('2026-05-31'),
        );

        $entriesFor = $ledger->entriesFor('cash', $filter);
        self::assertCount(1, $entriesFor);
        self::assertTrue($entriesFor[0]->amount()->equals(Money::ofYen(500)));
    }

    #[Test]
    public function 部門別絞込(): void
    {
        $entry1 = JournalEntry::of(
            debits: [JournalLine::of('cash', Money::ofYen(1000))],
            credits: [JournalLine::of('sales', Money::ofYen(1000), departmentId: 'shop-A')],
        );
        $entry2 = JournalEntry::of(
            debits: [JournalLine::of('cash', Money::ofYen(500))],
            credits: [JournalLine::of('sales', Money::ofYen(500), departmentId: 'shop-B')],
        );
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => $entry1],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => $entry2],
        ]);

        $shopA = $ledger->entriesFor('sales', LedgerFilter::byDepartment('shop-A'));
        self::assertCount(1, $shopA);
        self::assertTrue($shopA[0]->amount()->equals(Money::ofYen(1000)));

        $shopB = $ledger->entriesFor('sales', LedgerFilter::byDepartment('shop-B'));
        self::assertCount(1, $shopB);
        self::assertTrue($shopB[0]->amount()->equals(Money::ofYen(500)));
    }

    #[Test]
    public function 補助科目別絞込(): void
    {
        $entry = JournalEntry::of(
            debits: [
                JournalLine::of('cash', Money::ofYen(700)),
                JournalLine::of('cash', Money::ofYen(300), subAccountTitleId: 'pettyA'),
            ],
            credits: [JournalLine::of('sales', Money::ofYen(1000))],
        );
        $ledger = Ledger::fromJournalEntries([
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => $entry],
        ]);

        $petty = $ledger->entriesFor('cash', LedgerFilter::bySubAccount('pettyA'));
        self::assertCount(1, $petty);
        self::assertTrue($petty[0]->amount()->equals(Money::ofYen(300)));
    }

    /** 借方科目 (cash, expenseなど) の残高 = 借方合計 - 貸方合計 */
    #[Test]
    public function 借方科目の残高は借方累計マイナス貸方累計(): void
    {
        // 現金 1500 入金, 200 出金 → 残高 1300 借方
        $entries = [
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::simpleEntry('cash', 'sales', 1500)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::simpleEntry('rent', 'cash', 200)],
        ];
        $ledger = Ledger::fromJournalEntries($entries);
        self::assertTrue(
            $ledger->balance('cash', NormalBalance::Debit)->equals(Money::ofYen(1300)),
        );
    }

    /** 貸方科目 (sales, accountsPayable) の残高 = 貸方合計 - 借方合計 */
    #[Test]
    public function 貸方科目の残高は貸方累計マイナス借方累計(): void
    {
        // 売上 1500 計上 (返品なし) → 残高 1500 貸方
        $entries = [
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::simpleEntry('cash', 'sales', 1500)],
        ];
        $ledger = Ledger::fromJournalEntries($entries);
        self::assertTrue(
            $ledger->balance('sales', NormalBalance::Credit)->equals(Money::ofYen(1500)),
        );
    }

    /**
     * @return list<array{date: \DateTimeImmutable, entry: JournalEntry}>
     */
    private static function sampleEntries(): array
    {
        return [
            ['date' => new \DateTimeImmutable('2026-04-01'), 'entry' => self::simpleEntry('cash', 'sales', 1000)],
            ['date' => new \DateTimeImmutable('2026-04-02'), 'entry' => self::simpleEntry('cash', 'sales', 500)],
        ];
    }

    private static function simpleEntry(string $debitId, string $creditId, int $amount): JournalEntry
    {
        return JournalEntry::of(
            debits: [JournalLine::of($debitId, Money::ofYen($amount))],
            credits: [JournalLine::of($creditId, Money::ofYen($amount))],
        );
    }
}
