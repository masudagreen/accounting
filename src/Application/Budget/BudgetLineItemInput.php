<?php

declare(strict_types=1);

namespace Rucaro\Application\Budget;

/**
 * DTO for one budget line item on create / update.
 *
 * `monthlyAmounts` must contain exactly 12 scale-4 decimal strings indexed
 * 0..11 for fiscal-term months 1..12. Validation happens inside the
 * {@see \Rucaro\Domain\Budget\BudgetLineItem} constructor so invalid
 * payloads bubble up as {@see \Rucaro\Domain\Exception\ValidationException}.
 */
final readonly class BudgetLineItemInput
{
    /**
     * @param list<string> $monthlyAmounts
     */
    public function __construct(
        public string $accountTitleId,
        public ?string $subAccountTitleId,
        public int $sortOrder,
        public array $monthlyAmounts,
        public ?string $memo = null,
        public ?string $id = null,
    ) {
    }
}
