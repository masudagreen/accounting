<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FinancialStatement\Multi;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodEntry;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;

#[CoversClass(MultiPeriodFinancialStatement::class)]
#[CoversClass(MultiPeriodEntry::class)]
final class MultiPeriodFinancialStatementTest extends TestCase
{
    public function testConstructRequiresAtLeastOnePeriod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MultiPeriodFinancialStatement(
            entityId: 'ENT',
            kind: FinancialStatementKind::All,
            periods: [],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
    }

    public function testConstructRejectsDescendingPeriods(): void
    {
        $later = $this->makeEntry('T2', '2026-04-01', '2027-03-31');
        $earlier = $this->makeEntry('T1', '2025-04-01', '2026-03-31');

        $this->expectException(InvalidArgumentException::class);
        new MultiPeriodFinancialStatement(
            entityId: 'ENT',
            kind: FinancialStatementKind::All,
            periods: [$later, $earlier],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
    }

    public function testLatestAndPreviousPeriodReturnExpectedEntries(): void
    {
        $a = $this->makeEntry('T1', '2025-04-01', '2026-03-31');
        $b = $this->makeEntry('T2', '2026-04-01', '2027-03-31');
        $c = $this->makeEntry('T3', '2027-04-01', '2028-03-31');

        $multi = new MultiPeriodFinancialStatement(
            entityId: 'ENT',
            kind: FinancialStatementKind::All,
            periods: [$a, $b, $c],
            generatedAt: new DateTimeImmutable('2028-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );

        self::assertSame(3, $multi->periodCount());
        self::assertSame('T3', $multi->latestPeriod()->fiscalTermId);
        $prev = $multi->previousPeriod();
        self::assertNotNull($prev);
        self::assertSame('T2', $prev->fiscalTermId);
    }

    public function testPreviousPeriodIsNullWhenOnlyOnePeriod(): void
    {
        $a = $this->makeEntry('T1', '2025-04-01', '2026-03-31');
        $multi = new MultiPeriodFinancialStatement(
            entityId: 'ENT',
            kind: FinancialStatementKind::All,
            periods: [$a],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
        self::assertSame(1, $multi->periodCount());
        self::assertNull($multi->previousPeriod());
        self::assertSame('T1', $multi->latestPeriod()->fiscalTermId);
    }

    private function makeEntry(string $termId, string $from, string $to): MultiPeriodEntry
    {
        $tz = new DateTimeZone('UTC');
        $fromDt = new DateTimeImmutable($from, $tz);
        $toDt = new DateTimeImmutable($to, $tz);
        $fs = new FinancialStatement(
            entityId: 'ENT',
            fiscalTermId: $termId,
            kind: FinancialStatementKind::All,
            fromDate: $fromDt,
            toDate: $toDt,
            currencyCode: 'JPY',
            bs: [],
            pl: [],
            cs: [],
            totals: [],
            generatedAt: $toDt,
        );
        return new MultiPeriodEntry(
            fiscalTermId: $termId,
            fiscalTermLabel: '第 X 期',
            fromDate: $fromDt,
            toDate: $toDt,
            statement: $fs,
        );
    }
}
