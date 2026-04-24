<?php

declare(strict_types=1);

namespace Rucaro\Domain\Entity;

/**
 * Repository port for {@see Entity}.
 */
interface EntityRepositoryInterface
{
    /**
     * List entities belonging to the given owner with pagination.
     *
     * @param int<1, max> $page     1-based page number
     * @param int<1, max> $pageSize 1..200 per the OpenAPI spec
     * @param string|null $search   Optional fragment matched against name
     * @param bool|null   $isActive Optional filter flag
     * @return list<Entity>
     */
    public function listByOwner(
        string $ownerUserId,
        int $page,
        int $pageSize,
        ?string $search = null,
        ?bool $isActive = null,
    ): array;

    /**
     * Count entities matching the same filters as
     * {@see self::listByOwner()}, used to build pagination meta.
     */
    public function countByOwner(
        string $ownerUserId,
        ?string $search = null,
        ?bool $isActive = null,
    ): int;

    public function findById(string $id): ?Entity;

    /**
     * Persist a new {@see Entity} or update an existing one in full.
     *
     * `deletedAt` is intentionally routed through {@see self::softDelete()} so
     * write paths cannot accidentally resurrect a tombstoned row.
     */
    public function save(Entity $entity): void;

    /**
     * Mark the given entity as logically deleted. Safe to call on an id that
     * does not exist: implementations MUST NOT raise in that case.
     */
    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void;
}
