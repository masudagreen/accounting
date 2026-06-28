<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;

final class InMemoryCashPlanRepository implements CashPlanRepositoryInterface
{
    /** @var array<string, CashPlan> */
    private array $byId = [];

    #[\Override]
    public function save(CashPlan $plan): void
    {
        $this->byId[$plan->id] = $plan;
    }

    #[\Override]
    public function findById(string $id): ?CashPlan
    {
        $plan = $this->byId[$id] ?? null;
        if ($plan !== null && $plan->deletedAt !== null) {
            return null;
        }

        return $plan;
    }

    #[\Override]
    public function findByEntityAndName(string $entityId, string $fiscalTermId, string $name): ?CashPlan
    {
        foreach ($this->byId as $p) {
            if ($p->entityId === $entityId
                && $p->fiscalTermId === $fiscalTermId
                && $p->name === $name
                && $p->deletedAt === null) {
                return $p;
            }
        }

        return null;
    }

    #[\Override]
    public function findByEntity(string $entityId, ?string $fiscalTermId = null, bool $includeDeleted = false): array
    {
        $out = [];
        foreach ($this->byId as $p) {
            if ($p->entityId !== $entityId) {
                continue;
            }
            if ($fiscalTermId !== null && $p->fiscalTermId !== $fiscalTermId) {
                continue;
            }
            if (!$includeDeleted && $p->deletedAt !== null) {
                continue;
            }
            $out[] = $p;
        }

        return $out;
    }

    #[\Override]
    public function delete(string $id): void
    {
        $existing = $this->byId[$id] ?? null;
        if ($existing === null || $existing->deletedAt !== null) {
            return;
        }
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->byId[$id] = new CashPlan(
            id: $existing->id,
            entityId: $existing->entityId,
            fiscalTermId: $existing->fiscalTermId,
            name: $existing->name,
            openingBalance: $existing->openingBalance,
            currencyCode: $existing->currencyCode,
            notes: $existing->notes,
            entries: $existing->entries,
            createdBy: $existing->createdBy,
            createdAt: $existing->createdAt,
            updatedAt: $now,
            deletedAt: $now,
        );
    }
}
