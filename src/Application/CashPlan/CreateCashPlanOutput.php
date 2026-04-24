<?php

declare(strict_types=1);

namespace Rucaro\Application\CashPlan;

use Rucaro\Domain\CashPlan\CashPlan;

final readonly class CreateCashPlanOutput
{
    public function __construct(public CashPlan $plan)
    {
    }
}
