<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatementNotes;

/**
 * Renders the 注記表 (array of {@see FinancialStatementNote}) to a PDF byte string.
 *
 * The adapter handles grouping-by-category, ordering, and Japanese font
 * registration. Domain stays storage / layout agnostic.
 */
interface FsNotesPdfGeneratorInterface
{
    /**
     * @param list<FinancialStatementNote> $notes
     */
    public function render(
        array $notes,
        string $entityId,
        string $fiscalTermId,
    ): string;
}
