<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal\Service;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\Service\JournalReverser;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(JournalReverser::class)]
final class JournalReverserTest extends TestCase
{
    public function testReverseSwapsDebitAndCreditOnEveryLine(): void
    {
        $source = $this->source([
            ['debit', '1100.0000'],
            ['credit', '1100.0000'],
        ]);

        $reverser = new JournalReverser(new UlidGenerator(new FrozenClock()));
        $reversedAt = new DateTimeImmutable('2026-04-22T09:00:00Z');
        $reversal = $reverser->reverse($source, $reversedAt, '01HW7K9B2QV7C8Y4ZUSER000001', 'corrected');

        self::assertCount(2, $reversal->lines);
        self::assertSame('credit', $reversal->lines[0]->side);
        self::assertSame('debit', $reversal->lines[1]->side);
        self::assertSame($source->lines[0]->amount, $reversal->lines[0]->amount);
        self::assertSame($source->lines[1]->amount, $reversal->lines[1]->amount);
    }

    public function testReverseKeepsTotalsAndBalances(): void
    {
        $source = $this->source([
            ['debit', '500.0000'],
            ['debit', '500.0000'],
            ['credit', '1000.0000'],
        ]);

        $reverser = new JournalReverser(new UlidGenerator(new FrozenClock()));
        $reversal = $reverser->reverse(
            $source,
            new DateTimeImmutable('2026-04-22T09:00:00Z'),
            '01HW7K9B2QV7C8Y4ZUSER000001',
            'fix',
        );

        self::assertSame('1000.0000', $reversal->totalAmount);
        // The aggregate constructor already runs the balance check; the
        // instance above exists, so the assertion is that construction
        // succeeded.
        self::assertSame('posted', $reversal->status);
    }

    public function testReverseSummaryIsPrefixed(): void
    {
        $source = $this->source([
            ['debit', '100.0000'],
            ['credit', '100.0000'],
        ]);
        $reverser = new JournalReverser(new UlidGenerator(new FrozenClock()));
        $reversal = $reverser->reverse(
            $source,
            new DateTimeImmutable('2026-04-22T09:00:00Z'),
            '01HW7K9B2QV7C8Y4ZUSER000001',
            'typo',
        );
        self::assertStringStartsWith('[REVERSED:typo]', $reversal->summary);
    }

    /**
     * @param list<array{0: string, 1: string}> $linesSpec
     */
    private function source(array $linesSpec): Journal
    {
        /** @var list<JournalLine> $lines */
        $lines = [];
        $lineNo = 1;
        foreach ($linesSpec as [$side, $amount]) {
            $lines[] = new JournalLine(
                id: sprintf('01HW7K9B2QV7C8Y4ZLINE%05d', $lineNo),
                lineNo: $lineNo,
                side: $side,
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
                subAccountTitleId: null,
                amount: $amount,
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            );
            $lineNo++;
        }

        return new Journal(
            id: '01HW7K9B2QV7C8Y4ZJRNL000001',
            entityId: '01HW7K9B2QV7C8Y4ZENTITY0001',
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21'),
            bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            summary: 'Original',
            totalAmount: Journal::balance($lines),
            currencyCode: 'JPY',
            status: 'posted',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            createdAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            updatedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            deletedAt: null,
            lines: $lines,
        );
    }
}
