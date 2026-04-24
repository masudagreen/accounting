<?php

declare(strict_types=1);

namespace Rucaro\Domain\BreakEvenPoint;

/**
 * Repository port for per-entity CVP classifications.
 */
interface AccountTitleCvpClassificationRepositoryInterface
{
    /**
     * @return list<AccountTitleCvpClassification>
     */
    public function findAllByEntity(string $entityId): array;

    public function findByAccountTitle(string $entityId, string $accountTitleId): ?AccountTitleCvpClassification;

    /**
     * Upsert a single classification row.
     */
    public function save(AccountTitleCvpClassification $classification): void;

    /**
     * Bulk upsert — semantically equivalent to one save() per element but
     * implementations MAY batch for efficiency.
     *
     * @param list<AccountTitleCvpClassification> $classifications
     */
    public function saveMany(array $classifications): void;

    public function delete(string $entityId, string $accountTitleId): void;
}
