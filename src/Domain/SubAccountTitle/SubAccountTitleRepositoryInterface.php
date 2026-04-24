<?php

declare(strict_types=1);

namespace Rucaro\Domain\SubAccountTitle;

/**
 * Repository port for {@see SubAccountTitle}.
 */
interface SubAccountTitleRepositoryInterface
{
    /**
     * List sub-accounts under the given parent, newest-first suppressed in
     * favour of (sort_order, code) so the UI matches the journal line picker.
     *
     * @return list<SubAccountTitle>
     */
    public function listByAccountTitle(string $accountTitleId): array;

    /**
     * Flatten every sub-account under any of the entity's account titles.
     * Handy for the master list page that groups by parent account.
     *
     * @return list<SubAccountTitle>
     */
    public function listByEntity(string $entityId): array;

    public function findById(string $id): ?SubAccountTitle;

    public function save(SubAccountTitle $sub): void;

    public function softDelete(string $id, \DateTimeImmutable $deletedAt): void;

    public function existsByCode(string $accountTitleId, string $code, ?string $excludeId = null): bool;
}
