<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Budget;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Budget\BudgetLineItemInput;
use Rucaro\Application\Budget\UpdateBudgetInput;
use Rucaro\Application\Budget\UpdateBudgetUseCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBudgetRepository;

#[CoversClass(UpdateBudgetUseCase::class)]
final class UpdateBudgetUseCaseTest extends TestCase
{
    public function testUpdateReplacesLineItems(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seedDraft($repo);

        $uc = new UpdateBudgetUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());
        $out = $uc->execute(new UpdateBudgetInput(
            id: $budget->id,
            name: 'Revised Plan',
            notes: 'updated',
            lineItems: [
                new BudgetLineItemInput(
                    accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAD0',
                    subAccountTitleId: null,
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '200000.0000'),
                ),
            ],
        ));
        self::assertSame('Revised Plan', $out->budget->name);
        self::assertSame('updated', $out->budget->notes);
        self::assertCount(1, $out->budget->lineItems);
        self::assertSame('01HAAAAAAAAAAAAAAAAAAAAAD0', $out->budget->lineItems[0]->accountTitleId);
    }

    public function testUpdateOnApprovedBudgetIsBlocked(): void
    {
        $repo = new InMemoryBudgetRepository();
        $draft = $this->seedDraft($repo);
        $approver = '01HAAAAAAAAAAAAAAAAAAAAAAP';
        $approved = $draft->approve($approver, new DateTimeImmutable('2026-05-01T00:00:00Z'));
        $repo->save($approved);

        $uc = new UpdateBudgetUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());
        $this->expectException(InvariantViolationException::class);
        $uc->execute(new UpdateBudgetInput(
            id: $approved->id,
            name: 'should fail',
        ));
    }

    public function testUnknownIdRaisesValidationException(): void
    {
        $repo = new InMemoryBudgetRepository();
        $uc = new UpdateBudgetUseCase($repo, new UlidGenerator(new FrozenClock()), new FrozenClock());
        $this->expectException(ValidationException::class);
        $uc->execute(new UpdateBudgetInput(id: '01HAAAAAAAAAAAAAAAAAAAAAZZ'));
    }

    private function seedDraft(InMemoryBudgetRepository $repo): Budget
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $budget = new Budget(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            name: 'Initial',
            status: BudgetStatus::Draft,
            approvedBy: null,
            approvedAt: null,
            notes: null,
            lineItems: [
                new BudgetLineItem(
                    id: '01HAAAAAAAAAAAAAAAAAAAAAB1',
                    budgetId: '01HAAAAAAAAAAAAAAAAAAAAAB0',
                    accountTitleId: '01HAAAAAAAAAAAAAAAAAAAAAC0',
                    subAccountTitleId: null,
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '100000.0000'),
                ),
            ],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAA3',
            createdAt: $now,
            updatedAt: $now,
        );
        $repo->save($budget);
        return $budget;
    }
}
