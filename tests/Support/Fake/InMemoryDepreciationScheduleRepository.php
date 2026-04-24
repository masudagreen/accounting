<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;
use Rucaro\Domain\FixedAsset\DepreciationScheduleRepositoryInterface;

final class InMemoryDepreciationScheduleRepository implements DepreciationScheduleRepositoryInterface
{
    /** @var array<string, DepreciationScheduleEntry> */
    private array $byId = [];

    public function save(DepreciationScheduleEntry $entry): void
    {
        $this->byId[$entry->id] = $entry;
    }

    public function findByAssetAndFiscalTerm(string $fixedAssetId, string $fiscalTermId): ?DepreciationScheduleEntry
    {
        foreach ($this->byId as $e) {
            if ($e->fixedAssetId === $fixedAssetId && $e->fiscalTermId === $fiscalTermId) {
                return $e;
            }
        }
        return null;
    }

    public function findByAsset(string $fixedAssetId): array
    {
        $out = [];
        foreach ($this->byId as $e) {
            if ($e->fixedAssetId === $fixedAssetId) {
                $out[] = $e;
            }
        }
        return array_values($out);
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): array
    {
        // We don't track entity here — tests pass asset ids and filter externally.
        $out = [];
        foreach ($this->byId as $e) {
            if ($e->fiscalTermId === $fiscalTermId) {
                $out[] = $e;
            }
        }
        return array_values($out);
    }
}
