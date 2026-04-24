<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

interface ConsumptionTaxPeriodRepositoryInterface
{
    public function save(ConsumptionTaxPeriod $period): void;

    public function findById(string $id): ?ConsumptionTaxPeriod;

    /** @return list<ConsumptionTaxPeriod> */
    public function findByEntity(string $entityId): array;

    public function delete(string $id): void;
}
