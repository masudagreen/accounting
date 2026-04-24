<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriodRepositoryInterface;

final class InMemoryConsumptionTaxPeriodRepository implements ConsumptionTaxPeriodRepositoryInterface
{
    /** @var array<string, ConsumptionTaxPeriod> */
    private array $rows = [];

    public function save(ConsumptionTaxPeriod $period): void
    {
        $this->rows[$period->id] = $period;
    }

    public function findById(string $id): ?ConsumptionTaxPeriod
    {
        return $this->rows[$id] ?? null;
    }

    public function findByEntity(string $entityId): array
    {
        /** @var list<ConsumptionTaxPeriod> $out */
        $out = [];
        foreach ($this->rows as $p) {
            if ($p->entityId === $entityId) {
                $out[] = $p;
            }
        }
        usort($out, static fn (ConsumptionTaxPeriod $a, ConsumptionTaxPeriod $b): int
            => $a->periodFrom <=> $b->periodFrom);
        return $out;
    }

    public function delete(string $id): void
    {
        unset($this->rows[$id]);
    }
}
