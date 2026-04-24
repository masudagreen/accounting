<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Domain\Exception\InvariantViolationException;

/**
 * Soft-delete a budget.
 *
 * Only Draft budgets may be deleted. Deleting an Approved / Locked
 * budget raises {@see InvariantViolationException} so the history stays
 * auditable. Deleting an already-deleted budget is idempotent (no-op).
 */
final readonly class DeleteBudgetUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
    ) {
    }

    public function execute(string $id): void
    {
        $budget = $this->budgets->findById($id);
        if ($budget === null) {
            // Already gone (or never existed). Accept idempotently so
            // retried requests don't flap between 200 and 404.
            return;
        }
        if ($budget->status !== BudgetStatus::Draft) {
            throw InvariantViolationException::for('budget.delete.not_draft', [
                'budgetId' => $budget->id,
                'status'   => $budget->status->value,
            ]);
        }
        $this->budgets->delete($id);
    }
}
