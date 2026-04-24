<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

/**
 * Replace the header + entries of an existing cash plan.
 *
 * Optional fields (null) mean "leave unchanged"; `entries` null means
 * "keep the current entry list".
 *
 * @phpstan-type OptEntries list<CashPlanEntryInput>|null
 */
final readonly class UpdateCashPlanInput
{
    /**
     * @param list<CashPlanEntryInput>|null $entries
     */
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?string $openingBalance = null,
        public ?string $currencyCode = null,
        public ?string $notes = null,
        public ?array $entries = null,
    ) {
    }
}
