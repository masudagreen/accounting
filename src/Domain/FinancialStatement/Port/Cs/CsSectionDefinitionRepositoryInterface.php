<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Cs;

/**
 * Repository port for {@see CsSectionDefinition} rows.
 *
 * Implementations read from `fs_cs_section_definitions`. Because CS structure
 * is shared across entities (日本基準の標準間接法), there is no entity_id
 * parameter — a future extension may add one when we allow per-entity overrides.
 */
interface CsSectionDefinitionRepositoryInterface
{
    /**
     * Return every CS section definition ordered by `sort_order` ascending.
     * Callers assemble them into a parent/child tree via `parentCode`.
     *
     * @return list<CsSectionDefinition>
     */
    public function findAll(): array;
}
