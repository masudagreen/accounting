<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

interface ConsumptionTaxCategoryRepositoryInterface
{
    /** @return list<ConsumptionTaxCategory> */
    public function findAll(): array;

    public function findByCode(ConsumptionTaxCategoryCode $code): ?ConsumptionTaxCategory;
}
