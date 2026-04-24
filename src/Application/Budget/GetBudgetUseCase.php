<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;

final readonly class GetBudgetUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
    ) {
    }

    public function execute(string $id): ?Budget
    {
        return $this->budgets->findById($id);
    }
}
