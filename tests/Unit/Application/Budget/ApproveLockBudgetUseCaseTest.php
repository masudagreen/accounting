<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Budget;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Budget\ApproveBudgetUseCase;
use Rucaro\Application\Budget\LockBudgetUseCase;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBudgetRepository;

#[CoversClass(ApproveBudgetUseCase::class)]
#[CoversClass(LockBudgetUseCase::class)]
final class ApproveLockBudgetUseCaseTest extends TestCase
{
    public function testApproveThenLockRoundTrips(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seedDraft($repo);
        $clock = new FrozenClock('2026-05-01T00:00:00.000Z');
        $approver = '01HAAAAAAAAAAAAAAAAAAAAAAP';

        $approveUc = new ApproveBudgetUseCase($repo, $clock);
        $approved = $approveUc->execute($budget->id, $approver);
        self::assertSame(BudgetStatus::Approved, $approved->budget->status);
        self::assertSame($approver, $approved->budget->approvedBy);

        $lockUc = new LockBudgetUseCase($repo, $clock);
        $locked = $lockUc->execute($budget->id);
        self::assertSame(BudgetStatus::Locked, $locked->budget->status);
    }

    public function testLockBeforeApproveFails(): void
    {
        $repo = new InMemoryBudgetRepository();
        $budget = $this->seedDraft($repo);
        $lockUc = new LockBudgetUseCase($repo, new FrozenClock());
        $this->expectException(InvariantViolationException::class);
        $lockUc->execute($budget->id);
    }

    public function testApproveUnknownBudgetRaisesValidationException(): void
    {
        $repo = new InMemoryBudgetRepository();
        $uc = new ApproveBudgetUseCase($repo, new FrozenClock());
        $this->expectException(ValidationException::class);
        $uc->execute('01HAAAAAAAAAAAAAAAAAAAAAZZ', '01HAAAAAAAAAAAAAAAAAAAAAAP');
    }

    private function seedDraft(InMemoryBudgetRepository $repo): Budget
    {
        $now = new DateTimeImmutable('2026-04-01T00:00:00Z');
        $budget = new Budget(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            name: 'Plan',
            status: BudgetStatus::Draft,
            approvedBy: null,
            approvedAt: null,
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
