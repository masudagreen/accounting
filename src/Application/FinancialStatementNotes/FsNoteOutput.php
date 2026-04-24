<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;

/**
 * Standard output envelope for write UseCases (create / update / bulk import).
 */
final readonly class FsNoteOutput
{
    public function __construct(public FinancialStatementNote $note)
    {
    }
}
