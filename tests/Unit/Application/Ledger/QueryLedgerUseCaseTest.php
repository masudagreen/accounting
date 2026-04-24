<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Ledger;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Ledger\QueryLedgerUseCase;
use Rucaro\Application\Ledger\QueryLedgerUseCaseInput;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(QueryLedgerUseCase::class)]
final class QueryLedgerUseCaseTest extends TestCase
{
    private const ENT = 'ENT';
    private const TERM = 'TRM';

    public function testSingleAccountBookReturnsChronologicalEntries(): void
    {
        $query = $this->seedCashSalesTwoEntries();
        $out = $this->makeUseCase($query, [])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: 'CASH',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertCount(1, $out->ledger->books);
        $book = $out->ledger->books[0];
        self::assertSame('101', $book->accountTitleCode);
        self::assertSame(LedgerBook::NORMAL_DEBIT, $book->normalSide);
        self::assertCount(2, $book->entries);
        self::assertSame('2026-04-10', $book->entries[0]->entryDate->format('Y-m-d'));
        self::assertSame('2026-04-20', $book->entries[1]->entryDate->format('Y-m-d'));
    }

    public function testTwoLineEntryComputesCounterAccountByName(): void
    {
        $query = $this->seedCashSalesTwoEntries();
        $out = $this->makeUseCase($query, [])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: 'CASH',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
        ));

        $first = $out->ledger->books[0]->entries[0];
        self::assertSame('401', $first->counterAccountCode);
        self::assertSame('売上', $first->counterAccountName);
    }

    public function testMultiLineEntryCollapsesCounterIntoSundries(): void
    {
        $query = new InMemoryLedgerQuery();
        $query->registerAccount('CASH', '101', '現金', LedgerBook::NORMAL_DEBIT);
        $query->registerAccount('BANK', '102', '当座預金', LedgerBook::NORMAL_DEBIT);
        $query->registerAccount('SALES', '401', '売上', LedgerBook::NORMAL_CREDIT);

        // One entry with three lines: CASH 3000 / BANK 7000 / SALES 10000
        $query->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-15'), 'E1', 'L1', 1, 'debit', '3000',  'CASH',  'mixed sales');
        $query->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-15'), 'E1', 'L2', 2, 'debit', '7000',  'BANK',  'mixed sales');
        $query->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-15'), 'E1', 'L3', 3, 'credit','10000', 'SALES', 'mixed sales');

        $out = $this->makeUseCase($query, [])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: 'SALES',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertCount(1, $out->ledger->books[0]->entries);
        $entry = $out->ledger->books[0]->entries[0];
        self::assertSame('', $entry->counterAccountCode);
        self::assertSame(LedgerEntry::COUNTER_SUNDRIES, $entry->counterAccountName);
    }

    public function testDebitNormalRunningBalanceUsesDebitMinusCredit(): void
    {
        $query = $this->seedCashSalesTwoEntries();
        $out = $this->makeUseCase($query, [])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: 'CASH',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
        ));

        $entries = $out->ledger->books[0]->entries;
        // Opening 0 + 5000 debit = 5000
        self::assertSame('5000.0000', $entries[0]->runningBalance);
        // 5000 + 3000 debit = 8000
        self::assertSame('8000.0000', $entries[1]->runningBalance);
        self::assertSame('8000.0000', $out->ledger->books[0]->closingBalance);
    }

    public function testCreditNormalRunningBalanceUsesCreditMinusDebit(): void
    {
        $query = $this->seedCashSalesTwoEntries();
        $out = $this->makeUseCase($query, [])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: 'SALES',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
        ));

        $entries = $out->ledger->books[0]->entries;
        // credit-normal: 0 + 5000 credit = 5000
        self::assertSame('5000.0000', $entries[0]->runningBalance);
        self::assertSame('8000.0000', $entries[1]->runningBalance);
        self::assertSame('8000.0000', $out->ledger->books[0]->closingBalance);
    }

    public function testOpeningBalanceAdvancesRunningBalance(): void
    {
        $query = $this->seedCashSalesTwoEntries();
        $out = $this->makeUseCase($query, ['CASH' => '1000'])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: 'CASH',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
        ));

        $book = $out->ledger->books[0];
        self::assertSame('1000.0000', $book->openingBalance);
        self::assertSame('6000.0000', $book->entries[0]->runningBalance);
        self::assertSame('9000.0000', $book->entries[1]->runningBalance);
        self::assertSame('9000.0000', $book->closingBalance);
    }

    public function testAllAccountsReturnsSortedBooksWhenAccountIdIsNull(): void
    {
        $query = $this->seedCashSalesTwoEntries();
        $out = $this->makeUseCase($query, [])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: null,
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertCount(2, $out->ledger->books);
        self::assertSame('101', $out->ledger->books[0]->accountTitleCode);
        self::assertSame('401', $out->ledger->books[1]->accountTitleCode);
    }

    public function testMissingDateBoundsRaises(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeUseCase(new InMemoryLedgerQuery(), [])->execute(new QueryLedgerUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            accountTitleId: null,
            fromDate: null,
            toDate: null,
        ));
    }

    /**
     * @param array<string, string> $openingBalances
     */
    private function makeUseCase(InMemoryLedgerQuery $query, array $openingBalances): QueryLedgerUseCase
    {
        $repo = new InMemoryOpeningBalanceRepository();
        foreach ($openingBalances as $id => $amount) {
            $repo->set($id, $amount);
        }
        return new QueryLedgerUseCase($query, $repo, new FrozenClock());
    }

    private function seedCashSalesTwoEntries(): InMemoryLedgerQuery
    {
        $q = new InMemoryLedgerQuery();
        $q->registerAccount('CASH', '101', '現金', LedgerBook::NORMAL_DEBIT);
        $q->registerAccount('SALES', '401', '売上', LedgerBook::NORMAL_CREDIT);

        // Entry 1 (Apr 10): cash 5000 / sales 5000
        $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'E1', 'L1', 1, 'debit',  '5000', 'CASH',  '4/10 sales');
        $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'E1', 'L2', 2, 'credit', '5000', 'SALES', '4/10 sales');

        // Entry 2 (Apr 20): cash 3000 / sales 3000
        $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-20'), 'E2', 'L3', 1, 'debit',  '3000', 'CASH',  '4/20 sales');
        $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-20'), 'E2', 'L4', 2, 'credit', '3000', 'SALES', '4/20 sales');

        return $q;
    }
}
