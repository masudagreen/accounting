<?php

declare(strict_types=1);

namespace Rucaro\Domain\FiscalTerm;

/**
 * Repository port for {@see FiscalTerm}.
 *
 * `fiscal_terms` does not use soft deletes in the schema — Phase 7-4-A treats
 * deletion as destructive but keeps it behind an explicit confirmation screen.
 */
interface FiscalTermRepositoryInterface
{
    /**
     * @return list<FiscalTerm>
     */
    public function listByEntity(string $entityId): array;

    public function findById(string $id): ?FiscalTerm;

    public function save(FiscalTerm $term): void;

    /**
     * Remove the given fiscal term. Implementations MUST leave referential
     * integrity to the database (journals FK to fiscal_terms.id ON RESTRICT).
     */
    public function delete(string $id): void;

    /**
     * True when another fiscal term in the same entity already uses the given
     * period number. `excludeId` lets the update path ignore the row being
     * edited.
     */
    public function existsByPeriod(string $entityId, int $fiscalPeriod, ?string $excludeId = null): bool;
}
