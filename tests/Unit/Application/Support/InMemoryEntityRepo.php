<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Support;

use Rucaro\Domain\Entity\Entity;
use Rucaro\Domain\Entity\EntityRepositoryInterface;

final class InMemoryEntityRepo implements EntityRepositoryInterface
{
    /** @var list<Entity> */
    public array $items = [];

    public function add(Entity $e): void
    {
        $this->items[] = $e;
    }

    public function listByOwner(
        string $ownerUserId,
        int $page,
        int $pageSize,
        ?string $search = null,
        ?bool $isActive = null,
    ): array {
        $filtered = $this->filter($ownerUserId, $search, $isActive);
        return array_slice($filtered, ($page - 1) * $pageSize, $pageSize);
    }

    public function countByOwner(
        string $ownerUserId,
        ?string $search = null,
        ?bool $isActive = null,
    ): int {
        return count($this->filter($ownerUserId, $search, $isActive));
    }

    public function findById(string $id): ?Entity
    {
        foreach ($this->items as $e) {
            if ($e->id === $id) {
                return $e;
            }
        }
        return null;
    }

    public function save(Entity $entity): void
    {
        foreach ($this->items as $i => $e) {
            if ($e->id === $entity->id) {
                $this->items[$i] = $entity;
                return;
            }
        }
        $this->items[] = $entity;
    }

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
    {
        unset($deletedAt);
        foreach ($this->items as $i => $e) {
            if ($e->id === $id) {
                unset($this->items[$i]);
                $this->items = array_values($this->items);
                return;
            }
        }
    }

    /**
     * @return list<Entity>
     */
    private function filter(string $owner, ?string $search, ?bool $isActive): array
    {
        /** @var list<Entity> $out */
        $out = [];
        foreach ($this->items as $e) {
            if ($e->ownerUserId !== $owner) {
                continue;
            }
            if ($search !== null && stripos($e->name, $search) === false) {
                continue;
            }
            if ($isActive !== null && $e->isActive !== $isActive) {
                continue;
            }
            $out[] = $e;
        }
        return $out;
    }
}
