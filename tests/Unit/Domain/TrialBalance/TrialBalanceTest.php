<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;

#[CoversClass(TrialBalance::class)]
#[CoversClass(TrialBalanceRow::class)]
final class TrialBalanceTest extends TestCase
{
    public function testAggregateIsReadOnly(): void
    {
        $tb = $this->fixture();

        $ref = new \ReflectionClass($tb);
        self::assertTrue($ref->isReadOnly(), 'TrialBalance must be a readonly class.');
    }

    public function testDebitAndCreditTotalsSumAcrossRows(): void
    {
        $tb = $this->fixture();

        self::assertSame('15000.0000', $tb->debitTotal());
        self::assertSame('15000.0000', $tb->creditTotal());
        self::assertTrue($tb->isBalanced());
    }

    public function testImbalanceIsDetected(): void
    {
        $tb = new TrialBalance(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
            currencyCode: 'JPY',
            rows: [
                TrialBalanceRow::compute('A', '101', 'a', 'asset',   'debit',  '100', '0', 1),
                TrialBalanceRow::compute('B', '401', 'b', 'revenue', 'credit', '0', '50',  1),
            ],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );

        self::assertFalse($tb->isBalanced());
    }

    public function testEmptyTrialBalanceIsTriviallyBalanced(): void
    {
        $tb = new TrialBalance(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
            currencyCode: 'JPY',
            rows: [],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );

        self::assertSame('0.0000', $tb->debitTotal());
        self::assertSame('0.0000', $tb->creditTotal());
        self::assertTrue($tb->isBalanced());
    }

    private function fixture(): TrialBalance
    {
        return new TrialBalance(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            fromDate: new DateTimeImmutable('2026-04-01'),
            toDate: new DateTimeImmutable('2026-04-30'),
            currencyCode: 'JPY',
            rows: [
                TrialBalanceRow::compute('A', '101', '現金', 'asset',   'debit',  '12000', '3000',  4),
                TrialBalanceRow::compute('B', '401', '売上', 'revenue', 'credit', '0',     '12000', 1),
                TrialBalanceRow::compute('C', '501', '仕入', 'expense', 'debit',  '3000',  '0',     1),
            ],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
    }
}
