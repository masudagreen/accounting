<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Ledger;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Ledger\LedgerEntry;

#[CoversClass(LedgerEntry::class)]
final class LedgerEntryTest extends TestCase
{
    public function testEntryIsReadOnly(): void
    {
        $entry = $this->fixture();

        $ref = new \ReflectionClass($entry);
        self::assertTrue($ref->isReadOnly(), 'LedgerEntry must be a readonly class.');
    }

    public function testAllFieldsAreExposedAsPublicPromotedProperties(): void
    {
        $entry = $this->fixture();

        self::assertSame('E-01', $entry->journalEntryId);
        self::assertSame('L-01', $entry->journalEntryLineId);
        self::assertSame('2026-04-10', $entry->entryDate->format('Y-m-d'));
        self::assertSame('現金売上', $entry->summary);
        self::assertSame('レジNo.1', $entry->memo);
        self::assertSame('401', $entry->counterAccountCode);
        self::assertSame('売上', $entry->counterAccountName);
        self::assertSame('5000.0000', $entry->debitAmount);
        self::assertSame('0.0000', $entry->creditAmount);
        self::assertSame('5000.0000', $entry->runningBalance);
    }

    public function testCounterSundriesConstantMatchesLegacyLabel(): void
    {
        // The legacy UI rendered 'else' → "諸口" via $vars['varsItem']['strSundries'].
        self::assertSame('諸口', LedgerEntry::COUNTER_SUNDRIES);
    }

    private function fixture(): LedgerEntry
    {
        return new LedgerEntry(
            journalEntryId: 'E-01',
            journalEntryLineId: 'L-01',
            entryDate: new DateTimeImmutable('2026-04-10'),
            summary: '現金売上',
            memo: 'レジNo.1',
            counterAccountCode: '401',
            counterAccountName: '売上',
            debitAmount: '5000.0000',
            creditAmount: '0.0000',
            runningBalance: '5000.0000',
        );
    }
}
