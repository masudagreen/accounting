<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;

#[CoversClass(Journal::class)]
#[CoversClass(JournalLine::class)]
final class JournalTest extends TestCase
{
    public function testBalancePassesWhenDebitEqualsCredit(): void
    {
        $lines = [
            $this->line(1, 'debit', '1100.0000'),
            $this->line(2, 'credit', '1100.0000'),
        ];

        $total = Journal::balance($lines);

        self::assertSame('1100.0000', $total);
    }

    public function testBalanceFailsWhenDebitAndCreditMismatch(): void
    {
        $lines = [
            $this->line(1, 'debit', '1000.0000'),
            $this->line(2, 'credit', '999.0000'),
        ];

        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/must_balance/');

        Journal::balance($lines);
    }

    public function testBalanceFailsWithFewerThanTwoLines(): void
    {
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/min_lines/');

        Journal::balance([$this->line(1, 'debit', '100.0000')]);
    }

    public function testBalanceFailsWithoutAnyDebit(): void
    {
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/must_have_debit/');

        Journal::balance([
            $this->line(1, 'credit', '100.0000'),
            $this->line(2, 'credit', '100.0000'),
        ]);
    }

    public function testBalanceFailsWithoutAnyCredit(): void
    {
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/must_have_credit/');

        Journal::balance([
            $this->line(1, 'debit', '100.0000'),
            $this->line(2, 'debit', '100.0000'),
        ]);
    }

    public function testBalanceSumsMultipleDebitAndCreditLines(): void
    {
        $lines = [
            $this->line(1, 'debit', '600.0000'),
            $this->line(2, 'debit', '400.0000'),
            $this->line(3, 'credit', '1000.0000'),
        ];

        self::assertSame('1000.0000', Journal::balance($lines));
    }

    public function testConstructorRejectsTotalThatDoesNotMatchDebits(): void
    {
        $lines = [
            $this->line(1, 'debit', '500.0000'),
            $this->line(2, 'credit', '500.0000'),
        ];

        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/total_matches_debits/');

        $this->makeJournal($lines, total: '999.0000');
    }

    public function testConstructorSucceedsWhenTotalMatches(): void
    {
        $lines = [
            $this->line(1, 'debit', '500.0000'),
            $this->line(2, 'credit', '500.0000'),
        ];

        $j = $this->makeJournal($lines, total: '500.0000');

        self::assertSame('500.0000', $j->totalAmount);
        self::assertSame(2, count($j->lines));
    }

    private function line(int $no, string $side, string $amount): JournalLine
    {
        return new JournalLine(
            id: sprintf('01HW7K9B2QV7C8Y4ZEXAMPLE%02d', $no),
            lineNo: $no,
            side: $side,
            accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL00A',
            subAccountTitleId: null,
            amount: $amount,
            taxRatePercent: '0.00',
            taxAmount: '0.0000',
            isTaxReduced: false,
            memo: '',
            bookedAt: new DateTimeImmutable('2026-04-21T12:00:00.000Z'),
        );
    }

    /**
     * @param list<JournalLine> $lines
     */
    private function makeJournal(array $lines, string $total): Journal
    {
        return new Journal(
            id: '01HW7K9B2QV7C8Y4ZJRNLMAIN00',
            entityId: '01HW7K9B2QV7C8Y4ZENTITY0001',
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21'),
            bookedAt: new DateTimeImmutable('2026-04-21T12:00:00.000Z'),
            summary: 'Test journal',
            totalAmount: $total,
            currencyCode: 'JPY',
            status: 'draft',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedBy: null,
            approvedAt: null,
            createdAt: new DateTimeImmutable('2026-04-21T12:00:00.000Z'),
            updatedAt: new DateTimeImmutable('2026-04-21T12:00:00.000Z'),
            deletedAt: null,
            lines: $lines,
        );
    }
}
