<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Budget\BudgetStatus;

/**
 * In-memory {@see BudgetRepositoryInterface} for unit tests.
 *
 * Stores budgets keyed by ID and never queries a database, so tests can
 * exercise UseCases in under a millisecond.
 */
final class InMemoryBudgetRepository implements BudgetRepositoryInterface
{
    /** @var array<string, Budget> */
    private array $byId = [];

    public function save(Budget $budget): void
    {
        $this->byId[$budget->id] = $budget;
    }

    public function findById(string $id): ?Budget
    {
        $budget = $this->byId[$id] ?? null;
        if ($budget === null || $budget->deletedAt !== null) {
            return null;
        }
        return $budget;
    }

    public function findByEntityAndName(string $entityId, string $fiscalTermId, string $name): ?Budget
    {
        foreach ($this->byId as $b) {
            if ($b->entityId === $entityId
                && $b->fiscalTermId === $fiscalTermId
                && $b->name === $name
                && $b->deletedAt === null) {
                return $b;
            }
        }
        return null;
    }

    public function findByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        ?BudgetStatus $status = null,
        bool $includeDeleted = false,
    ): array {
        $out = [];
        foreach ($this->byId as $b) {
            if ($b->entityId !== $entityId) {
                continue;
            }
            if ($fiscalTermId !== null && $b->fiscalTermId !== $fiscalTermId) {
                continue;
            }
            if ($status !== null && $b->status !== $status) {
                continue;
            }
            if (!$includeDeleted && $b->deletedAt !== null) {
                continue;
            }
            $out[] = $b;
        }
        return array_values($out);
    }

    public function delete(string $id): void
    {
        $existing = $this->byId[$id] ?? null;
        if ($existing === null || $existing->deletedAt !== null) {
            return;
        }
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->byId[$id] = new Budget(
            id: $existing->id,
            entityId: $existing->entityId,
            fiscalTermId: $existing->fiscalTermId,
            name: $existing->name,
            status: $existing->status,
            approvedBy: $existing->approvedBy,
            approvedAt: $existing->approvedAt,
            notes: $existing->notes,
            lineItems: $existing->lineItems,
            createdBy: $existing->createdBy,
            createdAt: $existing->createdAt,
            updatedAt: $now,
            deletedAt: $now,
        );
    }
}
