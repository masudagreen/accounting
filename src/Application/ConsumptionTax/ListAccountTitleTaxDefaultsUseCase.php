<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefaultRepositoryInterface;

final readonly class ListAccountTitleTaxDefaultsUseCase
{
    public function __construct(
        private AccountTitleConsumptionTaxDefaultRepositoryInterface $defaults,
    ) {
    }

    /** @return list<AccountTitleConsumptionTaxDefault> */
    public function execute(string $entityId): array
    {
        return $this->defaults->findByEntity($entityId);
    }
}
