<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;

/**
 * In-memory {@see SsManualAdjustmentRepositoryInterface} for unit
 * tests. Keeps rows keyed by id so UseCase tests stay loop-free.
 */
final class InMemorySsManualAdjustmentRepository implements SsManualAdjustmentRepositoryInterface
{
    /** @var array<string, SsManualAdjustment> */
    private array $byId = [];

    public function save(SsManualAdjustment $adjustment): void
    {
        $this->byId[$adjustment->id] = $adjustment;
    }

    public function findById(string $id): ?SsManualAdjustment
    {
        return $this->byId[$id] ?? null;
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): array
    {
        $matches = [];
        foreach ($this->byId as $a) {
            if ($a->entityId === $entityId && $a->fiscalTermId === $fiscalTermId) {
                $matches[] = $a;
            }
        }
        usort($matches, static function (SsManualAdjustment $a, SsManualAdjustment $b): int {
            if ($a->sortOrder !== $b->sortOrder) {
                return $a->sortOrder <=> $b->sortOrder;
            }
            return $a->id <=> $b->id;
        });
        return array_values($matches);
    }

    public function delete(string $id): void
    {
        unset($this->byId[$id]);
    }
}
