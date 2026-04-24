<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\CashPlan;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\CashPlan\CashPlanCategory;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(CashPlanEntry::class)]
final class CashPlanEntryTest extends TestCase
{
    public function testAcceptsExactly12MonthlyAmounts(): void
    {
        $entry = $this->make(array_fill(0, 12, '1000.0000'));
        self::assertSame('1000.0000', $entry->amountForMonth(1));
        self::assertSame('12000.0000', $entry->total());
    }

    public function testRejectsWrongMonthCount(): void
    {
        $this->expectException(ValidationException::class);
        $this->make(['1', '2', '3']);
    }

    public function testRejectsNegativeAmount(): void
    {
        $this->expectException(ValidationException::class);
        $this->make([
            '-1.0000', '0', '0', '0', '0', '0',
            '0', '0', '0', '0', '0', '0',
        ]);
    }

    public function testRejectsEmptyLabel(): void
    {
        $this->expectException(ValidationException::class);
        new CashPlanEntry(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            cashPlanId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            category: CashPlanCategory::OperatingIn,
            label: '',
            sortOrder: 0,
            monthlyAmounts: array_fill(0, 12, '0.0000'),
        );
    }

    public function testAmountForMonthRejectsOutOfRange(): void
    {
        $entry = $this->make(array_fill(0, 12, '0.0000'));
        $this->expectException(ValidationException::class);
        $entry->amountForMonth(13);
    }

    /**
     * @param list<string> $amounts
     */
    private function make(array $amounts): CashPlanEntry
    {
        return new CashPlanEntry(
            id: '01HAAAAAAAAAAAAAAAAAAAAAAA',
            cashPlanId: '01HAAAAAAAAAAAAAAAAAAAAAAB',
            category: CashPlanCategory::OperatingIn,
            label: 'test',
            sortOrder: 0,
            monthlyAmounts: $amounts,
        );
    }
}
