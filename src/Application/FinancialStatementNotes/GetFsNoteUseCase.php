<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;

final readonly class GetFsNoteUseCase
{
    public function __construct(
        private FsNoteRepositoryInterface $notes,
    ) {
    }

    public function execute(string $id): ?FinancialStatementNote
    {
        return $this->notes->findById($id);
    }
}
