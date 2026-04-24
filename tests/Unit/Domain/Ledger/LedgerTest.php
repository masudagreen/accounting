<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Ledger;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Ledger\Ledger;
use Rucaro\Domain\Ledger\LedgerBook;

#[CoversClass(Ledger::class)]
final class LedgerTest extends TestCase
{
    public function testAggregateIsReadOnly(): void
    {
        $ledger = $this->fixture();
        $ref = new \ReflectionClass($ledger);
        self::assertTrue($ref->isReadOnly(), 'Ledger must be a readonly class.');
    }

    public function testLedgerExposesAllConstructionFields(): void
    {
        $ledger = $this->fixture();

        self::assertSame('ENT', $ledger->entityId);
        self::assertSame('TRM', $ledger->fiscalTermId);
        self::assertSame('2026-04-01', $ledger->fromDate->format('Y-m-d'));
        self::assertSame('2026-04-30', $ledger->toDate->format('Y-m-d'));
        self::assertSame('JPY', $ledger->currencyCode);
        self::assertCount(1, $ledger->books);
        self::assertSame('101', $ledger->books[0]->accountTitleCode);
    }

    public function testLedgerSupportsEmptyBookList(): void
    {
        $ledger = new Ledger(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
            currencyCode: 'JPY',
            books: [],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );

        self::assertSame([], $ledger->books);
    }

    private function fixture(): Ledger
    {
        $book = LedgerBook::compute(
            accountTitleId: 'A',
            accountTitleCode: '101',
            accountTitleName: '現金',
            normalSide: LedgerBook::NORMAL_DEBIT,
            openingBalance: '0',
            rawEntries: [],
        );
        return new Ledger(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
            currencyCode: 'JPY',
            books: [$book],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
    }
}
