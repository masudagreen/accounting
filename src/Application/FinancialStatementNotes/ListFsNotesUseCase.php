<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;

final readonly class ListFsNotesUseCase
{
    public function __construct(
        private FsNoteRepositoryInterface $notes,
    ) {
    }

    /**
     * @return list<FinancialStatementNote>
     */
    public function execute(
        string $entityId,
        string $fiscalTermId,
        bool $onlyActive = false,
    ): array {
        return $this->notes->findByEntityAndTerm($entityId, $fiscalTermId, $onlyActive);
    }
}
