<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriodRepositoryInterface;

final readonly class ListConsumptionTaxPeriodsUseCase
{
    public function __construct(
        private ConsumptionTaxPeriodRepositoryInterface $periods,
    ) {
    }

    /** @return list<ConsumptionTaxPeriod> */
    public function execute(string $entityId): array
    {
        return $this->periods->findByEntity($entityId);
    }
}
