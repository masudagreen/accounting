<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

/**
 * Input DTO for {@see BulkImportFsNotesFromTemplatesUseCase}.
 *
 * @phpstan-type TemplateCodeList list<string>
 */
final readonly class BulkImportFsNotesFromTemplatesInput
{
    /**
     * @param TemplateCodeList $templateCodes
     */
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public array $templateCodes,
    ) {
    }
}
