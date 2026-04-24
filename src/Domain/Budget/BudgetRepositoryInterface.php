<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

/**
 * Repository port for the {@see Budget} aggregate.
 *
 * Implementations MUST persist header + line items atomically (single
 * transaction) so readers never observe a budget with a stale line-item
 * set. See {@see \Rucaro\Infrastructure\Budget\PdoBudgetRepository} for the
 * reference adapter.
 */
interface BudgetRepositoryInterface
{
    public function save(Budget $budget): void;

    public function findById(string $id): ?Budget;

    public function findByEntityAndName(string $entityId, string $fiscalTermId, string $name): ?Budget;

    /**
     * List budgets filtered by (entity, optional fiscal term, optional status).
     * Excludes soft-deleted rows unless explicitly asked to include them.
     *
     * @return list<Budget>
     */
    public function findByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        ?BudgetStatus $status = null,
        bool $includeDeleted = false,
    ): array;

    public function delete(string $id): void;
}
