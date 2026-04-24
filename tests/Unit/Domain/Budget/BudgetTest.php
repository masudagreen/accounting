<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Budget;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(Budget::class)]
#[CoversClass(BudgetStatus::class)]
final class BudgetTest extends TestCase
{
    public function testMonthlyTotalSumsAcrossLineItems(): void
    {
        $budget = $this->draftBudget([
            $this->lineItem(array_fill(0, 12, '1000000.0000')),
            $this->lineItem(array_fill(0, 12, '300000.0000')),
        ]);
        self::assertSame('1300000.0000', $budget->monthlyTotal(1));
        self::assertSame('15600000.0000', $budget->annualTotal());
    }

    public function testApproveMovesDraftToApproved(): void
    {
        $budget = $this->draftBudget([]);
        $now = new DateTimeImmutable('2026-05-01T00:00:00Z');
        $approver = '01HAAAAAAAAAAAAAAAAAAAAAAP';

        $approved = $budget->approve($approver, $now);

        self::assertSame(BudgetStatus::Approved, $approved->status);
        self::assertSame($approver, $approved->approvedBy);
        self::assertEquals($now, $approved->approvedAt);
    }

    public function testApproveFailsFromAnyOtherState(): void
    {
        $budget = $this->draftBudget([])->approve(
            '01HAAAAAAAAAAAAAAAAAAAAAAP',
            new DateTimeImmutable('2026-05-01T00:00:00Z'),
        );
        $this->expectException(InvariantViolationException::class);
        $budget->approve('01HAAAAAAAAAAAAAAAAAAAAAAQ', new DateTimeImmutable('2026-05-02T00:00:00Z'));
    }

    public function testLockRequiresApprovedState(): void
    {
        $draft = $this->draftBudget([]);
        $this->expectException(InvariantViolationException::class);
        $draft->lock(new DateTimeImmutable('2026-05-01T00:00:00Z'));
    }

    public function testLockPromotesApprovedToLocked(): void
    {
        $now = new DateTimeImmutable('2026-05-01T00:00:00Z');
        $budget = $this->draftBudget([])->approve('01HAAAAAAAAAAAAAAAAAAAAAAP', $now);
        $locked = $budget->lock($now->modify('+1 day'));
        self::assertSame(BudgetStatus::Locked, $locked->status);
    }

    public function testWithHeaderRejectedOnceApproved(): void
    {
        $budget = $this->draftBudget([])->approve(
            '01HAAAAAAAAAAAAAAAAAAAAAAP',
            new DateTimeImmutable('2026-05-01T00:00:00Z'),
        );
        $this->expectException(InvariantViolationException::class);
        $budget->withHeader('renamed', null, new DateTimeImmutable('2026-05-02T00:00:00Z'));
    }

    public function testWithLineItemsRejectedOnceLocked(): void
    {
        $now = new DateTimeImmutable('2026-05-01T00:00:00Z');
        $budget = $this->draftBudget([])
            ->approve('01HAAAAAAAAAAAAAAAAAAAAAAP', $now)
            ->lock($now->modify('+1 day'));
        $this->expectException(InvariantViolationException::class);
        $budget->withLineItems([], $now->modify('+2 days'));
    }

    public function testRejectsEmptyName(): void
    {
        $this->expectException(ValidationException::class);
        $this->draftBudget([], '');
    }

    public function testDraftRejectsApprovedMetadata(): void
    {
        $now = new DateTimeImmutable('2026-05-01T00:00:00Z');
        $this->expectException(ValidationException::class);
        new Budget(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            name: 'has approver while draft',
            status: BudgetStatus::Draft,
            approvedBy: '01HAAAAAAAAAAAAAAAAAAAAAAP',
            approvedAt: $now,
            notes: null,
            lineItems: [],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAA3',
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * @param list<BudgetLineItem> $items
     */
    private function draftBudget(array $items, string $name = 'Plan 2026'): Budget
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        return new Budget(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            name: $name,
            status: BudgetStatus::Draft,
            approvedBy: null,
            approvedAt: null,
            notes: null,
            lineItems: $items,
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAA3',
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * @param list<string> $amounts
     */
    private function lineItem(array $amounts): BudgetLineItem
    {
        return new BudgetLineItem(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            budgetId: '01HAAAAAAAAAAAAAAAAAAAAAA0',
            accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC0',
            subAccountTitleId: null,
            sortOrder: 0,
            monthlyAmounts: $amounts,
        );
    }
}
