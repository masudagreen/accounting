<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Budget\BudgetStatus;

final readonly class ListBudgetsUseCase
{
    public function __construct(
        private BudgetRepositoryInterface $budgets,
    ) {
    }

    /**
     * @return list<Budget>
     */
    public function execute(
        string $entityId,
        ?string $fiscalTermId = null,
        ?BudgetStatus $status = null,
    ): array {
        return $this->budgets->findByEntity($entityId, $fiscalTermId, $status, false);
    }
}
