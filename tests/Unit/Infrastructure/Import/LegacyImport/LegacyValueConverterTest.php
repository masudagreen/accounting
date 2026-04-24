<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Import\LegacyImport;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Import\LegacyImport\LegacyValueConverter;

/**
 * Pure conversion helpers under
 * {@see \Rucaro\Infrastructure\Import\LegacyImport\LegacyValueConverter}.
 *
 * No DB, no side effects — just input/output.
 */
final class LegacyValueConverterTest extends TestCase
{
    public function testStampToTimestampProducesUtcMicrosecondString(): void
    {
        $stamp = 1_569_596_400; // 2019-09-27 15:00:00 UTC
        $result = LegacyValueConverter::stampToTimestamp($stamp);
        self::assertSame('2019-09-27 15:00:00.000000', $result);
    }

    public function testStampToTimestampHandlesEpochZero(): void
    {
        self::assertSame('1970-01-01 00:00:00.000000', LegacyValueConverter::stampToTimestamp(0));
    }

    public function testStampToDateUsesAsiaTokyoByDefault(): void
    {
        // 2019-09-27 15:00:00 UTC → 2019-09-28 in Asia/Tokyo.
        self::assertSame('2019-09-28', LegacyValueConverter::stampToDate(1_569_596_400));
    }

    public function testStampToDateAcceptsExplicitTimezone(): void
    {
        self::assertSame('2019-09-27', LegacyValueConverter::stampToDate(1_569_596_400, 'UTC'));
    }

    public function testSplitCommaArrayHandlesEmptyInput(): void
    {
        self::assertSame([], LegacyValueConverter::splitCommaArray(null));
        self::assertSame([], LegacyValueConverter::splitCommaArray(''));
        self::assertSame([], LegacyValueConverter::splitCommaArray(',,,'));
    }

    public function testSplitCommaArrayStripsSurroundingCommas(): void
    {
        self::assertSame(['cash'], LegacyValueConverter::splitCommaArray(',cash,'));
        self::assertSame(
            ['cash', 'salaries'],
            LegacyValueConverter::splitCommaArray(',cash,salaries,')
        );
    }

    public function testSplitCommaArrayTrimsWhitespaceAndDropsBlanks(): void
    {
        self::assertSame(
            ['foo', 'bar'],
            LegacyValueConverter::splitCommaArray(' , foo ,  , bar ,')
        );
    }

    public function testFiscalTermDatesComputesStandard12MonthTerm(): void
    {
        $dates = LegacyValueConverter::fiscalTermDates(2019, 7, 12);
        self::assertSame('2019-07-01', $dates['start']);
        self::assertSame('2020-06-30', $dates['end']);
    }

    public function testFiscalTermDatesHandlesShortTerm(): void
    {
        $dates = LegacyValueConverter::fiscalTermDates(2020, 4, 6);
        self::assertSame('2020-04-01', $dates['start']);
        self::assertSame('2020-09-30', $dates['end']);
    }

    public function testFiscalTermDatesRejectsInvalidMonth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LegacyValueConverter::fiscalTermDates(2020, 13);
    }

    public function testFiscalTermDatesRejectsInvalidTermMonths(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LegacyValueConverter::fiscalTermDates(2020, 4, 0);
    }

    public function testSyntheticAccountTitleCodeFormats(): void
    {
        self::assertSame('L0001', LegacyValueConverter::syntheticAccountTitleCode(1));
        self::assertSame('L0042', LegacyValueConverter::syntheticAccountTitleCode(42));
        self::assertSame('L9999', LegacyValueConverter::syntheticAccountTitleCode(9999));
    }

    public function testSyntheticAccountTitleCodeRejectsOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LegacyValueConverter::syntheticAccountTitleCode(10_000);
    }
}
