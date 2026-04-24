<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

/**
 * DTO for one cash-plan entry line on create / update.
 *
 * `monthlyAmounts` must contain exactly 12 scale-4 decimal strings,
 * indexed 0..11 for fiscal-term months 1..12. Validation is deferred to
 * {@see \Rucaro\Domain\CashPlan\CashPlanEntry}.
 */
final readonly class CashPlanEntryInput
{
    /**
     * @param list<string> $monthlyAmounts
     */
    public function __construct(
        public string $category,
        public string $label,
        public int $sortOrder,
        public array $monthlyAmounts,
        public ?string $memo = null,
        public ?string $id = null,
    ) {
    }
}
