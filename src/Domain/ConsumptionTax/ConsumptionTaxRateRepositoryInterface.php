<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use DateTimeImmutable;

/**
 * Read-only port for the `consumption_tax_rates` master table.
 */
interface ConsumptionTaxRateRepositoryInterface
{
    /** @return list<ConsumptionTaxRate> */
    public function findAll(): array;

    public function findByCode(string $code): ?ConsumptionTaxRate;

    /**
     * @return list<ConsumptionTaxRate>
     */
    public function findEffectiveOn(DateTimeImmutable $at): array;
}
