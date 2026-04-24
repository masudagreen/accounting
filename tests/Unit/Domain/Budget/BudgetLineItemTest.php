<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Budget;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(BudgetLineItem::class)]
final class BudgetLineItemTest extends TestCase
{
    public function testTotalSumsAll12Months(): void
    {
        $li = new BudgetLineItem(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            subAccountTitleId: null,
            sortOrder: 0,
            monthlyAmounts: array_fill(0, 12, '100.0000'),
        );
        self::assertSame('1200.0000', $li->totalAmount());
    }

    public function testCumulativeStopsAtRequestedMonth(): void
    {
        $amounts = ['10.0000', '20.0000', '30.0000', '40.0000', '50.0000', '60.0000',
                    '70.0000', '80.0000', '90.0000', '100.0000', '110.0000', '120.0000'];
        $li = new BudgetLineItem(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            subAccountTitleId: null,
            sortOrder: 0,
            monthlyAmounts: $amounts,
        );
        self::assertSame('10.0000', $li->cumulativeAmount(1));
        self::assertSame('60.0000', $li->cumulativeAmount(3));
        self::assertSame('780.0000', $li->cumulativeAmount(12));
    }

    public function testAmountForMonthReturnsZeroBasedIndex(): void
    {
        $amounts = array_map(static fn (int $i): string => sprintf('%d.0000', $i), range(1, 12));
        $li = new BudgetLineItem(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            subAccountTitleId: null,
            sortOrder: 0,
            monthlyAmounts: $amounts,
        );
        self::assertSame('1.0000', $li->amountForMonth(1));
        self::assertSame('12.0000', $li->amountForMonth(12));
    }

    public function testRejectsWrongMonthCount(): void
    {
        $this->expectException(ValidationException::class);
        new BudgetLineItem(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            subAccountTitleId: null,
            sortOrder: 0,
            monthlyAmounts: array_fill(0, 10, '0.0000'),
        );
    }

    public function testRejectsMonthOutOfRange(): void
    {
        $li = new BudgetLineItem(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            subAccountTitleId: null,
            sortOrder: 0,
            monthlyAmounts: array_fill(0, 12, '0.0000'),
        );
        $this->expectException(ValidationException::class);
        $li->amountForMonth(13);
    }

    public function testRejectsOverlongMemo(): void
    {
        $this->expectException(ValidationException::class);
        new BudgetLineItem(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            subAccountTitleId: null,
            sortOrder: 0,
            monthlyAmounts: array_fill(0, 12, '0.0000'),
            memo: str_repeat('a', 256),
        );
    }
}
