<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategory;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryRepositoryInterface;

final readonly class ListConsumptionTaxCategoriesUseCase
{
    public function __construct(
        private ConsumptionTaxCategoryRepositoryInterface $categories,
    ) {
    }

    /** @return list<ConsumptionTaxCategory> */
    public function execute(): array
    {
        return $this->categories->findAll();
    }
}
