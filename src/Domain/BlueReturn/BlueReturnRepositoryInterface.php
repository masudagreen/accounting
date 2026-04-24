<?php

declare(strict_types=1);

namespace Rucaro\Domain\BlueReturn;

/**
 * Repository port for the {@see BlueReturnForm} aggregate.
 *
 * Implementations MUST persist header + snapshot JSON atomically so
 * readers never observe a partially-written form.
 */
interface BlueReturnRepositoryInterface
{
    public function save(BlueReturnForm $form): void;

    public function findById(string $id): ?BlueReturnForm;

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): ?BlueReturnForm;

    /**
     * @return list<BlueReturnForm>
     */
    public function findByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        bool $includeDeleted = false,
    ): array;

    public function delete(string $id): void;
}
