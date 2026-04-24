<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Web;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Web\PeriodQueryHelper;

#[CoversClass(PeriodQueryHelper::class)]
final class PeriodQueryHelperTest extends TestCase
{
    private function pdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE fiscal_terms (id BLOB PRIMARY KEY, entity_id BLOB, start_date TEXT, end_date TEXT)');
        return $pdo;
    }

    public function testParseYearAcceptsValidYear(): void
    {
        self::assertSame(2025, PeriodQueryHelper::parseYear('2025'));
        self::assertSame(1999, PeriodQueryHelper::parseYear('1999'));
    }

    public function testParseYearRejectsGarbageAndOutOfRange(): void
    {
        self::assertNull(PeriodQueryHelper::parseYear(null));
        self::assertNull(PeriodQueryHelper::parseYear(''));
        self::assertNull(PeriodQueryHelper::parseYear('abc'));
        self::assertNull(PeriodQueryHelper::parseYear('1800'));
        self::assertNull(PeriodQueryHelper::parseYear('3000'));
    }

    public function testParseMonthAcceptsOneThroughTwelve(): void
    {
        self::assertSame(1, PeriodQueryHelper::parseMonth('1'));
        self::assertSame(12, PeriodQueryHelper::parseMonth('12'));
    }

    public function testParseMonthRejectsOutOfRangeAndJunk(): void
    {
        self::assertNull(PeriodQueryHelper::parseMonth(null));
        self::assertNull(PeriodQueryHelper::parseMonth(''));
        self::assertNull(PeriodQueryHelper::parseMonth('0'));
        self::assertNull(PeriodQueryHelper::parseMonth('13'));
        self::assertNull(PeriodQueryHelper::parseMonth('abc'));
    }

    public function testResolveReturnsFiscalTermRangeWhenNoYearOrMonth(): void
    {
        $helper = new PeriodQueryHelper($this->pdo());
        [$from, $to, $termStart, $termEnd] = $helper->resolve(null, null, null);
        self::assertNotNull($from);
        self::assertNotNull($to);
        // With no fiscal term id and no year, from falls back to epoch and to to now.
        self::assertSame('1970-01-01', $from->format('Y-m-d'));
        self::assertNull($termStart);
        self::assertNull($termEnd);
    }

    public function testResolveYearOnlyReturnsCalendarYearRange(): void
    {
        $helper = new PeriodQueryHelper($this->pdo());
        [$from, $to] = $helper->resolve(null, 2025, null);
        self::assertSame('2025-01-01', $from->format('Y-m-d'));
        self::assertSame('2025-12-31', $to->format('Y-m-d'));
    }

    public function testResolveYearAndMonthReturnsCalendarMonthRange(): void
    {
        $helper = new PeriodQueryHelper($this->pdo());
        [$from, $to] = $helper->resolve(null, 2025, 2);
        // 2025-02 is not a leap year — last day is 28.
        self::assertSame('2025-02-01', $from->format('Y-m-d'));
        self::assertSame('2025-02-28', $to->format('Y-m-d'));
    }

    public function testResolveYearAndMonthHandlesLeapYear(): void
    {
        $helper = new PeriodQueryHelper($this->pdo());
        [$from, $to] = $helper->resolve(null, 2024, 2);
        self::assertSame('2024-02-01', $from->format('Y-m-d'));
        self::assertSame('2024-02-29', $to->format('Y-m-d'));
    }

    public function testFindLatestFiscalTermIdReturnsNullForUnknownEntity(): void
    {
        $helper = new PeriodQueryHelper($this->pdo());
        // Non-ULID input should return null without touching the DB.
        self::assertNull($helper->findLatestFiscalTermId('not-a-ulid'));
    }
}
