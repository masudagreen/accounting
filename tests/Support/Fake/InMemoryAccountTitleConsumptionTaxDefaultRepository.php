<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefaultRepositoryInterface;

final class InMemoryAccountTitleConsumptionTaxDefaultRepository implements AccountTitleConsumptionTaxDefaultRepositoryInterface
{
    /** @var array<string, AccountTitleConsumptionTaxDefault> */
    private array $byKey = [];

    public function findByEntity(string $entityId): array
    {
        /** @var list<AccountTitleConsumptionTaxDefault> $out */
        $out = [];
        foreach ($this->byKey as $row) {
            if ($row->entityId === $entityId) {
                $out[] = $row;
            }
        }
        return $out;
    }

    public function findByAccountTitle(string $entityId, string $accountTitleId): ?AccountTitleConsumptionTaxDefault
    {
        return $this->byKey[$this->key($entityId, $accountTitleId)] ?? null;
    }

    public function save(AccountTitleConsumptionTaxDefault $row): void
    {
        $this->byKey[$this->key($row->entityId, $row->accountTitleId)] = $row;
    }

    public function saveAll(array $rows): void
    {
        foreach ($rows as $row) {
            $this->save($row);
        }
    }

    public function delete(string $entityId, string $accountTitleId): void
    {
        unset($this->byKey[$this->key($entityId, $accountTitleId)]);
    }

    private function key(string $entityId, string $accountTitleId): string
    {
        return $entityId . '#' . $accountTitleId;
    }
}
