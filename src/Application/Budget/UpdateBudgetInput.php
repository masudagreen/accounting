<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

/**
 * Partial-update payload for an existing budget.
 *
 *  - null `name` / `notes` means "leave unchanged".
 *  - null `lineItems` means "keep existing rows".
 *  - an empty `lineItems` array means "delete every row".
 *
 * The budget must be in {@see \Rucaro\Domain\Budget\BudgetStatus::Draft}
 * for the update to succeed; otherwise the domain raises an
 * {@see \Rucaro\Domain\Exception\InvariantViolationException}.
 */
final readonly class UpdateBudgetInput
{
    /**
     * @param list<BudgetLineItemInput>|null $lineItems
     */
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?string $notes = null,
        public ?array $lineItems = null,
    ) {
    }
}
