<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;

/**
 * Minimal in-memory {@see AccountTitleRepositoryInterface} for application-layer
 * tests. Only implements the methods the FS use case actually touches.
 */
final class InMemoryAccountTitleRepository implements AccountTitleRepositoryInterface
{
    /** @var array<string, list<AccountTitle>> */
    private array $byEntity = [];

    public function seed(
        string $entityId,
        string $id,
        string $code,
        string $name,
        string $category,
        string $normalSide,
    ): void {
        $account = new AccountTitle(
            id: $id,
            entityId: $entityId,
            code: $code,
            name: $name,
            category: $category,
            normalSide: $normalSide,
            parentId: null,
            sortOrder: 0,
            isActive: true,
            createdAt: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            updatedAt: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
        );
        $this->byEntity[$entityId] ??= [];
        $this->byEntity[$entityId][] = $account;
    }

    public function listByEntity(
        string $entityId,
        int $page,
        int $pageSize,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): array {
        return $this->byEntity[$entityId] ?? [];
    }

    public function countByEntity(
        string $entityId,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): int {
        return count($this->byEntity[$entityId] ?? []);
    }

    public function findById(string $id): ?AccountTitle
    {
        foreach ($this->byEntity as $accounts) {
            foreach ($accounts as $a) {
                if ($a->id === $id) {
                    return $a;
                }
            }
        }
        return null;
    }

    public function findAllByEntity(string $entityId): array
    {
        return $this->byEntity[$entityId] ?? [];
    }

    public function save(AccountTitle $title): void
    {
        $this->byEntity[$title->entityId] ??= [];
        foreach ($this->byEntity[$title->entityId] as $i => $a) {
            if ($a->id === $title->id) {
                $this->byEntity[$title->entityId][$i] = $title;
                return;
            }
        }
        $this->byEntity[$title->entityId][] = $title;
    }

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
    {
        unset($deletedAt);
        foreach ($this->byEntity as $entityId => $accounts) {
            foreach ($accounts as $i => $a) {
                if ($a->id === $id) {
                    unset($this->byEntity[$entityId][$i]);
                    $this->byEntity[$entityId] = array_values($this->byEntity[$entityId]);
                    return;
                }
            }
        }
    }

    public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool
    {
        foreach ($this->byEntity[$entityId] ?? [] as $a) {
            if ($a->code !== $code) {
                continue;
            }
            if ($excludeId !== null && $a->id === $excludeId) {
                continue;
            }
            return true;
        }
        return false;
    }
}
