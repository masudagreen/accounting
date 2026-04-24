<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatementNotes;

/**
 * Repository port for {@see FinancialStatementNote}.
 *
 * Implementations MUST persist a note atomically and MUST NOT silently
 * insert on save() when the row is already gone (that would resurrect
 * deleted notes). See {@see \Rucaro\Infrastructure\FinancialStatementNotes\PdoFsNoteRepository}
 * for the reference adapter.
 */
interface FsNoteRepositoryInterface
{
    public function save(FinancialStatementNote $note): void;

    public function findById(string $id): ?FinancialStatementNote;

    /**
     * List notes for one (entity, fiscalTerm).
     *
     * @return list<FinancialStatementNote>
     */
    public function findByEntityAndTerm(
        string $entityId,
        string $fiscalTermId,
        bool $onlyActive = false,
    ): array;

    /**
     * Count notes already anchored to a template code inside a fiscal term.
     * Used by bulk-import to stay idempotent (don't re-insert the same
     * template twice). Returns zero when no match.
     */
    public function countByTemplateCode(
        string $entityId,
        string $fiscalTermId,
        string $templateCode,
    ): int;

    public function delete(string $id): void;
}
