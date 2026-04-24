<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

interface AccountTitleConsumptionTaxDefaultRepositoryInterface
{
    /** @return list<AccountTitleConsumptionTaxDefault> */
    public function findByEntity(string $entityId): array;

    public function findByAccountTitle(string $entityId, string $accountTitleId): ?AccountTitleConsumptionTaxDefault;

    public function save(AccountTitleConsumptionTaxDefault $row): void;

    /**
     * @param list<AccountTitleConsumptionTaxDefault> $rows
     */
    public function saveAll(array $rows): void;

    public function delete(string $entityId, string $accountTitleId): void;
}
