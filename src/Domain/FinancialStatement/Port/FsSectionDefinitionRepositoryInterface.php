<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port;

/**
 * Repository port for {@see FsSectionDefinition} rows.
 *
 * Implementations read from `fs_section_definitions` (shared master) and
 * optionally an entity-specific override table when that feature lands.
 */
interface FsSectionDefinitionRepositoryInterface
{
    /**
     * Return every section definition for the given kind, ordered by
     * `sort_order` ascending. Callers turn this into a parent/child tree.
     *
     * @return list<FsSectionDefinition>
     */
    public function findAllByKind(FsKind $kind): array;
}
