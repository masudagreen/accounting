<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\ValueObject\FiscalPeriod;
use Rucaro\Domain\Journal\ValueObject\JournalDate;

#[CoversClass(FiscalPeriod::class)]
final class FiscalPeriodTest extends TestCase
{
    public function testContainsIsInclusive(): void
    {
        $p = new FiscalPeriod(
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            startDate: JournalDate::fromString('2026-04-01'),
            endDate: JournalDate::fromString('2027-03-31'),
        );

        self::assertTrue($p->contains(JournalDate::fromString('2026-04-01')));
        self::assertTrue($p->contains(JournalDate::fromString('2027-03-31')));
        self::assertTrue($p->contains(JournalDate::fromString('2026-10-01')));
    }

    public function testContainsRejectsOutOfRangeDates(): void
    {
        $p = new FiscalPeriod(
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            startDate: JournalDate::fromString('2026-04-01'),
            endDate: JournalDate::fromString('2027-03-31'),
        );

        self::assertFalse($p->contains(JournalDate::fromString('2026-03-31')));
        self::assertFalse($p->contains(JournalDate::fromString('2027-04-01')));
    }

    public function testEmptyFiscalTermIdIsRejected(): void
    {
        $this->expectException(ValidationException::class);
        new FiscalPeriod(
            fiscalTermId: '',
            startDate: JournalDate::fromString('2026-04-01'),
            endDate: JournalDate::fromString('2027-03-31'),
        );
    }

    public function testReversedRangeIsRejected(): void
    {
        $this->expectException(InvariantViolationException::class);
        new FiscalPeriod(
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            startDate: JournalDate::fromString('2027-03-31'),
            endDate: JournalDate::fromString('2026-04-01'),
        );
    }

    public function testToPrimitiveReturnsStructuredArray(): void
    {
        $p = new FiscalPeriod(
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            startDate: JournalDate::fromString('2026-04-01'),
            endDate: JournalDate::fromString('2027-03-31'),
        );

        self::assertSame([
            'fiscalTermId' => '01HW7K9B2QV7C8Y4ZFTTERM0001',
            'startDate'    => '2026-04-01',
            'endDate'      => '2027-03-31',
        ], $p->toPrimitive());
    }

    public function testEqualsByValue(): void
    {
        $a = new FiscalPeriod(
            '01HW7K9B2QV7C8Y4ZFTTERM0001',
            JournalDate::fromString('2026-04-01'),
            JournalDate::fromString('2027-03-31'),
        );
        $b = new FiscalPeriod(
            '01HW7K9B2QV7C8Y4ZFTTERM0001',
            JournalDate::fromString('2026-04-01'),
            JournalDate::fromString('2027-03-31'),
        );
        self::assertTrue($a->equals($b));
    }
}
