<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Cash;

use App\Domain\Cash\CashDirection;
use App\Domain\Cash\CashEntry;
use App\Domain\Cash\CashEntryStatus;
use App\Domain\Cash\CashJournalConverter;
use App\Domain\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CashJournalConverter::class)]
final class CashJournalConverterTest extends TestCase
{
    #[Test]
    public function 入金1000円は借方cash貸方salesの仕訳になる(): void
    {
        $entry = CashEntry::of(
            id: 'e-in-001',
            date: new \DateTimeImmutable('2026-04-01'),
            direction: CashDirection::In,
            amount: Money::ofYen(1000),
            counterAccountTitleId: 'sales',
            cashAccountTitleId: 'cash',
            description: '売上入金',
            status: CashEntryStatus::Pending,
        );

        $journal = CashJournalConverter::toJournalEntry($entry);

        // 借方: 現金 1000
        self::assertCount(1, $journal->debits());
        self::assertSame('cash', $journal->debits()[0]->accountTitleId());
        self::assertTrue($journal->debits()[0]->amount()->equals(Money::ofYen(1000)));

        // 貸方: 売上 1000
        self::assertCount(1, $journal->credits());
        self::assertSame('sales', $journal->credits()[0]->accountTitleId());
        self::assertTrue($journal->credits()[0]->amount()->equals(Money::ofYen(1000)));

        // 貸借一致
        self::assertTrue($journal->isBalanced());
    }

    #[Test]
    public function 出金500円は借方rent貸方cashの仕訳になる(): void
    {
        $entry = CashEntry::of(
            id: 'e-out-001',
            date: new \DateTimeImmutable('2026-04-15'),
            direction: CashDirection::Out,
            amount: Money::ofYen(500),
            counterAccountTitleId: 'rent',
            cashAccountTitleId: 'cash',
            description: '家賃支払い',
            status: CashEntryStatus::Pending,
        );

        $journal = CashJournalConverter::toJournalEntry($entry);

        // 借方: 家賃 500
        self::assertCount(1, $journal->debits());
        self::assertSame('rent', $journal->debits()[0]->accountTitleId());
        self::assertTrue($journal->debits()[0]->amount()->equals(Money::ofYen(500)));

        // 貸方: 現金 500
        self::assertCount(1, $journal->credits());
        self::assertSame('cash', $journal->credits()[0]->accountTitleId());
        self::assertTrue($journal->credits()[0]->amount()->equals(Money::ofYen(500)));

        // 貸借一致
        self::assertTrue($journal->isBalanced());
    }

    #[Test]
    public function 入金の総借方合計と総貸方合計が一致する(): void
    {
        $entry = CashEntry::of(
            id: 'e-in-002',
            date: new \DateTimeImmutable('2026-04-30'),
            direction: CashDirection::In,
            amount: Money::ofYen(999_999),
            counterAccountTitleId: 'service-revenue',
            cashAccountTitleId: 'bank-account',
            description: null,
            status: CashEntryStatus::Settled,
        );

        $journal = CashJournalConverter::toJournalEntry($entry);

        self::assertTrue($journal->totalDebits()->equals($journal->totalCredits()));
        self::assertTrue($journal->totalDebits()->equals(Money::ofYen(999_999)));
    }

    #[Test]
    public function 出金の相手勘定と現金科目が正しく割り当てられる(): void
    {
        $entry = CashEntry::of(
            id: 'e-out-002',
            date: new \DateTimeImmutable('2026-05-01'),
            direction: CashDirection::Out,
            amount: Money::ofYen(100_000),
            counterAccountTitleId: 'supplies-expense',
            cashAccountTitleId: 'petty-cash',
            description: '消耗品購入',
            status: CashEntryStatus::Pending,
        );

        $journal = CashJournalConverter::toJournalEntry($entry);

        self::assertSame('supplies-expense', $journal->debits()[0]->accountTitleId());
        self::assertSame('petty-cash', $journal->credits()[0]->accountTitleId());
    }
}
