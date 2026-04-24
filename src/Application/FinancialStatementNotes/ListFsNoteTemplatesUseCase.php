<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplate;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplateRepositoryInterface;

final readonly class ListFsNoteTemplatesUseCase
{
    public function __construct(
        private FsNoteTemplateRepositoryInterface $templates,
    ) {
    }

    /**
     * @return list<FsNoteTemplate>
     */
    public function execute(): array
    {
        return $this->templates->findAll();
    }
}
