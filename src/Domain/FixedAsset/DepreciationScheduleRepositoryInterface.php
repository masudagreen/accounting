<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset;

/**
 * Repository port for depreciation schedule entries.
 *
 * Schedules are not an aggregate in the DDD sense — they are derived values
 * recomputed on demand — but we persist them so:
 *   1. Reading the full schedule is cheap.
 *   2. Posted flags survive across regenerations (the use case merges).
 */
interface DepreciationScheduleRepositoryInterface
{
    public function save(DepreciationScheduleEntry $entry): void;

    public function findByAssetAndFiscalTerm(string $fixedAssetId, string $fiscalTermId): ?DepreciationScheduleEntry;

    /**
     * @return list<DepreciationScheduleEntry>
     */
    public function findByAsset(string $fixedAssetId): array;

    /**
     * @return list<DepreciationScheduleEntry>
     */
    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): array;
}
