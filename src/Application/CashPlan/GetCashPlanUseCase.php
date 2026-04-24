<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;

final readonly class GetCashPlanUseCase
{
    public function __construct(
        private CashPlanRepositoryInterface $plans,
    ) {
    }

    public function execute(string $id): ?CashPlan
    {
        return $this->plans->findById($id);
    }
}
