<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatementNotes;

/**
 * Read-only repository port for {@see FsNoteTemplate}.
 *
 * Templates are seeded by migration and never mutated at runtime, so only
 * read-side methods are exposed.
 */
interface FsNoteTemplateRepositoryInterface
{
    /**
     * @return list<FsNoteTemplate>
     */
    public function findAll(): array;

    public function findByCode(string $code): ?FsNoteTemplate;

    /**
     * @param list<string> $codes
     * @return list<FsNoteTemplate>
     */
    public function findByCodes(array $codes): array;
}
