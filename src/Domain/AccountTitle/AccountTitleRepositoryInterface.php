<?php

declare(strict_types=1);

namespace Rucaro\Domain\AccountTitle;

/**
 * Repository port for {@see AccountTitle}.
 */
interface AccountTitleRepositoryInterface
{
    /**
     * @param int<1, max> $page
     * @param int<1, max> $pageSize
     * @return list<AccountTitle>
     */
    public function listByEntity(
        string $entityId,
        int $page,
        int $pageSize,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): array;

    public function countByEntity(
        string $entityId,
        ?string $category = null,
        ?bool $isActive = null,
        ?string $search = null,
    ): int;

    public function findById(string $id): ?AccountTitle;

    /**
     * Fetch every active account title for the given entity, sorted by
     * (sort_order, code). Intended for read-only aggregations such as
     * {@see \Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase}.
     *
     * @return list<AccountTitle>
     */
    public function findAllByEntity(string $entityId): array;

    /**
     * Persist a new {@see AccountTitle} or replace an existing one in full.
     *
     * Implementations MUST respect the unique `(entity_id, code)` constraint
     * and should treat `deleted_at` as immutable through this write path.
     */
    public function save(AccountTitle $title): void;

    /**
     * Mark the given account title as logically deleted. Safe to call on an
     * id that does not exist: implementations MUST NOT raise in that case.
     */
    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void;

    /**
     * Return true when another account title in the same entity already uses
     * the given code. `excludeId` lets the update path ignore the row being
     * edited.
     */
    public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool;
}
