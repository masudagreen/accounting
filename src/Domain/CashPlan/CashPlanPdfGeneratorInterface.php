<?php

declare(strict_types=1);

namespace Rucaro\Domain\CashPlan;

/**
 * Port for CashPlan PDF rendering.
 */
interface CashPlanPdfGeneratorInterface
{
    public function render(CashPlan $plan): string;
}
