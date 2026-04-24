<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Ledger;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;

#[CoversClass(LedgerBook::class)]
#[CoversClass(LedgerEntry::class)]
final class LedgerBookTest extends TestCase
{
    public function testBookIsReadOnly(): void
    {
        $book = LedgerBook::compute(
            accountTitleId: 'A',
            accountTitleCode: '101',
            accountTitleName: '現金',
            normalSide: LedgerBook::NORMAL_DEBIT,
            openingBalance: '0',
            rawEntries: [],
        );

        $ref = new \ReflectionClass($book);
        self::assertTrue($ref->isReadOnly(), 'LedgerBook must be a readonly class.');
    }

    public function testRunningBalanceMovesUpOnDebitForDebitNormalAccounts(): void
    {
        $book = LedgerBook::compute(
            accountTitleId: 'A',
            accountTitleCode: '101',
            accountTitleName: '現金',
            normalSide: LedgerBook::NORMAL_DEBIT,
            openingBalance: '1000',
            rawEntries: [
                $this->raw('2026-04-10', 'debit', '5000'),
                $this->raw('2026-04-12', 'credit', '3000'),
                $this->raw('2026-04-15', 'debit', '2000'),
            ],
        );

        self::assertSame('1000.0000', $book->openingBalance);
        self::assertSame('7000.0000', $book->debitTotal);
        self::assertSame('3000.0000', $book->creditTotal);
        // 1000 + 5000 - 3000 + 2000 = 5000
        self::assertSame('5000.0000', $book->closingBalance);

        self::assertSame('6000.0000', $book->entries[0]->runningBalance);
        self::assertSame('3000.0000', $book->entries[1]->runningBalance);
        self::assertSame('5000.0000', $book->entries[2]->runningBalance);
    }

    public function testRunningBalanceMovesUpOnCreditForCreditNormalAccounts(): void
    {
        $book = LedgerBook::compute(
            accountTitleId: 'B',
            accountTitleCode: '401',
            accountTitleName: '売上',
            normalSide: LedgerBook::NORMAL_CREDIT,
            openingBalance: '0',
            rawEntries: [
                $this->raw('2026-04-10', 'credit', '5000'),
                $this->raw('2026-04-12', 'debit', '1000'),
                $this->raw('2026-04-15', 'credit', '3000'),
            ],
        );

        self::assertSame('8000.0000', $book->creditTotal);
        self::assertSame('1000.0000', $book->debitTotal);
        // credit-normal: 0 + 5000 - 1000 + 3000 = 7000
        self::assertSame('7000.0000', $book->closingBalance);

        self::assertSame('5000.0000', $book->entries[0]->runningBalance);
        self::assertSame('4000.0000', $book->entries[1]->runningBalance);
        self::assertSame('7000.0000', $book->entries[2]->runningBalance);
    }

    public function testEmptyBookReturnsOpeningAsClosing(): void
    {
        $book = LedgerBook::compute(
            accountTitleId: 'A',
            accountTitleCode: '101',
            accountTitleName: '現金',
            normalSide: LedgerBook::NORMAL_DEBIT,
            openingBalance: '1234.5600',
            rawEntries: [],
        );

        self::assertSame([], $book->entries);
        self::assertSame('0.0000', $book->debitTotal);
        self::assertSame('0.0000', $book->creditTotal);
        self::assertSame('1234.5600', $book->closingBalance);
    }

    public function testBookCanGoNegativeWhenContraEntriesExceedNormal(): void
    {
        $book = LedgerBook::compute(
            accountTitleId: 'A',
            accountTitleCode: '101',
            accountTitleName: '現金',
            normalSide: LedgerBook::NORMAL_DEBIT,
            openingBalance: '0',
            rawEntries: [
                $this->raw('2026-04-10', 'credit', '1000'),
            ],
        );

        self::assertSame('-1000.0000', $book->closingBalance);
        self::assertSame('-1000.0000', $book->entries[0]->runningBalance);
    }

    /**
     * @return array{
     *     journalEntryId: string,
     *     journalEntryLineId: string,
     *     entryDate: DateTimeImmutable,
     *     summary: string,
     *     memo: string,
     *     counterAccountCode: string,
     *     counterAccountName: string,
     *     debitAmount: string,
     *     creditAmount: string,
     * }
     */
    private function raw(string $date, string $side, string $amount): array
    {
        return [
            'journalEntryId'     => 'E-' . $date,
            'journalEntryLineId' => 'L-' . $date,
            'entryDate'          => new DateTimeImmutable($date),
            'summary'            => 'test',
            'memo'               => '',
            'counterAccountCode' => '999',
            'counterAccountName' => 'counter',
            'debitAmount'        => $side === 'debit' ? $amount : '0',
            'creditAmount'       => $side === 'credit' ? $amount : '0',
        ];
    }
}
