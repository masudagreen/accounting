<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax\Service;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;

/**
 * Port for consumption-tax settlement calculators.
 *
 * Implementations must be pure: given the same input period and set of
 * transactions, they always return the same settlement aggregate.
 */
interface ConsumptionTaxCalculatorInterface
{
    /**
     * @param list<TaxableTransaction> $transactions
     */
    public function calculate(ConsumptionTaxPeriod $period, array $transactions): ConsumptionTaxSettlement;
}
