<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;

final readonly class ListCashPlansUseCase
{
    public function __construct(
        private CashPlanRepositoryInterface $plans,
    ) {
    }

    /**
     * @return list<CashPlan>
     */
    public function execute(string $entityId, ?string $fiscalTermId = null): array
    {
        return $this->plans->findByEntity($entityId, $fiscalTermId, false);
    }
}
