<?php

declare(strict_types=1);

namespace Rucaro\Domain\CashPlan;

/**
 * Repository port for the {@see CashPlan} aggregate.
 *
 * Implementations MUST persist header + entries atomically (single
 * transaction) so callers never observe a plan with a stale entry set.
 */
interface CashPlanRepositoryInterface
{
    public function save(CashPlan $plan): void;

    public function findById(string $id): ?CashPlan;

    public function findByEntityAndName(string $entityId, string $fiscalTermId, string $name): ?CashPlan;

    /**
     * @return list<CashPlan>
     */
    public function findByEntity(string $entityId, ?string $fiscalTermId = null, bool $includeDeleted = false): array;

    public function delete(string $id): void;
}
