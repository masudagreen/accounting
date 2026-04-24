<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use InvalidArgumentException;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Promote an Approved budget to Locked. Typically invoked once the
 * fiscal term has closed and the variance review has been signed off.
 */
final readonly class LockBudgetUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $budgetId): BudgetOutput
    {
        if (!UlidGenerator::isValid($budgetId)) {
            throw new InvalidArgumentException('budgetId must be a ULID.');
        }

        $budget = $this->budgets->findById($budgetId);
        if ($budget === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('budget %s was not found.', $budgetId)],
            ]);
        }

        $locked = $budget->lock($this->clock->getCurrentTime());
        $this->budgets->save($locked);
        return new BudgetOutput($locked);
    }
}
