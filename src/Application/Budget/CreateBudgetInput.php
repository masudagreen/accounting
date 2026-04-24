<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

final readonly class CreateBudgetInput
{
    /**
     * @param list<BudgetLineItemInput> $lineItems
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public string $name,
        public ?string $notes,
        public array $lineItems,
        public string $createdBy,
    ) {
    }
}
