<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal\ValueObject;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\ValueObject\JournalDate;

#[CoversClass(JournalDate::class)]
final class JournalDateTest extends TestCase
{
    public function testFromStringAcceptsIsoDate(): void
    {
        $d = JournalDate::fromString('2026-04-21');
        self::assertSame('2026-04-21', $d->toPrimitive());
    }

    public function testInternalStorageIsUtc(): void
    {
        $d = JournalDate::fromString('2026-04-21');
        self::assertSame('UTC', $d->toDateTime()->getTimezone()->getName());
    }

    public function testConstructorNormalisesTimezoneToUtc(): void
    {
        $jst = new DateTimeImmutable('2026-04-21T23:45:00+09:00');
        $d = new JournalDate($jst);
        // Taking just the date portion should preserve the original local date.
        self::assertSame('2026-04-21', $d->toPrimitive());
    }

    #[DataProvider('invalidInputs')]
    public function testFromStringRejectsInvalidInput(string $bad): void
    {
        $this->expectException(ValidationException::class);
        JournalDate::fromString($bad);
    }

    /**
     * @return list<array{0: string}>
     */
    public static function invalidInputs(): array
    {
        return [
            [''],
            ['2026-4-1'],
            ['2026/04/21'],
            ['26-04-21'],
            ['2026-13-01'], // invalid month
            ['2026-02-30'], // invalid day
            ['not-a-date'],
        ];
    }

    public function testComparisonHelpers(): void
    {
        $a = JournalDate::fromString('2026-01-01');
        $b = JournalDate::fromString('2026-06-30');

        self::assertTrue($a->isBefore($b));
        self::assertFalse($b->isBefore($a));
        self::assertTrue($b->isAfter($a));
        self::assertTrue($a->isOnOrBefore($a));
        self::assertTrue($a->isOnOrAfter($a));
    }

    public function testEqualsIsValueBased(): void
    {
        $a = JournalDate::fromString('2026-04-21');
        $b = JournalDate::fromString('2026-04-21');
        $c = JournalDate::fromString('2026-04-22');

        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
    }

    public function testToStringRendersIsoDate(): void
    {
        $d = JournalDate::fromString('2026-04-21');
        self::assertSame('2026-04-21', (string) $d);
    }

    public function testConstructorIgnoresTimeOfDay(): void
    {
        $utc = new DateTimeImmutable('2026-04-21T13:37:42.123456Z', new DateTimeZone('UTC'));
        $d = new JournalDate($utc);
        self::assertSame('2026-04-21', $d->toPrimitive());
        self::assertSame('00:00:00', $d->toDateTime()->format('H:i:s'));
    }
}
