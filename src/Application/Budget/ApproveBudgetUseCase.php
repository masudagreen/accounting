<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use InvalidArgumentException;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Promote a Draft budget to Approved. Rejects the transition from any
 * other source state via the aggregate's own invariant guard.
 */
final readonly class ApproveBudgetUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $budgetId, string $approverId): BudgetOutput
    {
        if (!UlidGenerator::isValid($budgetId)) {
            throw new InvalidArgumentException('budgetId must be a ULID.');
        }
        if (!UlidGenerator::isValid($approverId)) {
            throw new InvalidArgumentException('approverId must be a ULID.');
        }

        $budget = $this->budgets->findById($budgetId);
        if ($budget === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('budget %s was not found.', $budgetId)],
            ]);
        }

        $approved = $budget->approve($approverId, $this->clock->getCurrentTime());
        $this->budgets->save($approved);
        return new BudgetOutput($approved);
    }
}
