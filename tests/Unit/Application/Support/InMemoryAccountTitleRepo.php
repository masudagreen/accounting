<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Support;

use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;

final class InMemoryAccountTitleRepo implements AccountTitleRepositoryInterface
{
    /** @var list<AccountTitle> */
    public array $items = [];

    public function add(AccountTitle $a): void
    {
        $this->items[] = $a;
    }

    public function listByEntity(
        string $entityId,
        int $page,
        int $pageSize,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): array {
        $matches = $this->filter($entityId, $category, $isActive, $search);
        return array_slice($matches, ($page - 1) * $pageSize, $pageSize);
    }

    public function countByEntity(
        string $entityId,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): int {
        return count($this->filter($entityId, $category, $isActive, $search));
    }

    public function findById(string $id): ?AccountTitle
    {
        foreach ($this->items as $a) {
            if ($a->id === $id) {
                return $a;
            }
        }
        return null;
    }

    public function findAllByEntity(string $entityId): array
    {
        $out = [];
        foreach ($this->items as $a) {
            if ($a->entityId === $entityId) {
                $out[] = $a;
            }
        }
        return $out;
    }

    public function save(AccountTitle $title): void
    {
        foreach ($this->items as $i => $a) {
            if ($a->id === $title->id) {
                $this->items[$i] = $title;
                return;
            }
        }
        $this->items[] = $title;
    }

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
    {
        unset($deletedAt);
        foreach ($this->items as $i => $a) {
            if ($a->id === $id) {
                unset($this->items[$i]);
                $this->items = array_values($this->items);
                return;
            }
        }
    }

    public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool
    {
        foreach ($this->items as $a) {
            if ($a->entityId !== $entityId) {
                continue;
            }
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

    /**
     * @return list<AccountTitle>
     */
    private function filter(string $entityId, ?string $category, ?bool $isActive, ?string $search): array
    {
        /** @var list<AccountTitle> $out */
        $out = [];
        foreach ($this->items as $a) {
            if ($a->entityId !== $entityId) {
                continue;
            }
            if ($category !== null && $a->category !== $category) {
                continue;
            }
            if ($isActive !== null && $a->isActive !== $isActive) {
                continue;
            }
            if (
                $search !== null
                && stripos($a->name, $search) === false
                && stripos($a->code, $search) === false
            ) {
                continue;
            }
            $out[] = $a;
        }
        return $out;
    }
}
