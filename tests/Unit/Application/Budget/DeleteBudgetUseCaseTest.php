<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Budget;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Budget\DeleteBudgetUseCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Tests\Support\Fake\InMemoryBudgetRepository;

#[CoversClass(DeleteBudgetUseCase::class)]
final class DeleteBudgetUseCaseTest extends TestCase
{
    public function testDeletesDraftBudget(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seed($repo, BudgetStatus::Draft);
        $uc = new DeleteBudgetUseCase($repo);
        $uc->execute($budget->id);
        self::assertNull($repo->findById($budget->id));
    }

    public function testRejectsDeleteOfApprovedBudget(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seed($repo, BudgetStatus::Approved);
        $uc = new DeleteBudgetUseCase($repo);
        $this->expectException(InvariantViolationException::class);
        $uc->execute($budget->id);
    }

    public function testIdempotentWhenBudgetMissing(): void
    {
        $repo = new InMemoryBudgetRepository();
        $uc = new DeleteBudgetUseCase($repo);
        $uc->execute('01HAAAAAAAAAAAAAAAAAAAAAZZ');
        self::assertNull($repo->findById('01HAAAAAAAAAAAAAAAAAAAAAZZ'));
    }

    private function seed(InMemoryBudgetRepository $repo, BudgetStatus $status): Budget
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $approved = $status !== BudgetStatus::Draft;
        $budget = new Budget(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            name: 'Plan',
            status: $status,
            approvedBy: $approved ? '01HAAAAAAAAAAAAAAAAAAAAAAP' : null,
            approvedAt: $approved ? $now : null,
            notes: null,
            lineItems: [],
            createdBy: '01HAAAAAAAAAAAAAAAAAAAAAA3',
            createdAt: $now,
            updatedAt: $now,
        );
        $repo->save($budget);
        return $budget;
    }
}
