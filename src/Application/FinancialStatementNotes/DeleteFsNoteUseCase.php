<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;

/**
 * Hard-delete a note. 注記表 entries have no workflow / audit trail, so a
 * physical delete matches the legacy behaviour. Idempotent: deleting a
 * missing note is a no-op (no 404 thrown from the UseCase layer).
 */
final readonly class DeleteFsNoteUseCase
{
    public function __construct(
        private FsNoteRepositoryInterface $notes,
    ) {
    }

    public function execute(string $id): void
    {
        if ($this->notes->findById($id) === null) {
            return;
        }
        $this->notes->delete($id);
    }
}
