<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxRate;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxRateRepositoryInterface;

final readonly class ListConsumptionTaxRatesUseCase
{
    public function __construct(
        private ConsumptionTaxRateRepositoryInterface $rates,
    ) {
    }

    /** @return list<ConsumptionTaxRate> */
    public function execute(): array
    {
        return $this->rates->findAll();
    }
}
