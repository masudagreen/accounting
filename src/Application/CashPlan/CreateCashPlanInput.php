<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

final readonly class CreateCashPlanInput
{
    /**
     * @param list<CashPlanEntryInput> $entries
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public string $name,
        public string $openingBalance,
        public string $currencyCode,
        public ?string $notes,
        public array $entries,
        public string $createdBy,
    ) {
    }
}
