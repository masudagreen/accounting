<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

final readonly class CreateFsNoteInput
{
    public function __construct(
        public string $entityId,
        public string $fiscalTermId,
        public string $category,
        public string $label,
        public string $body,
        public ?string $templateCode = null,
        public int $sortOrder = 0,
        public bool $isActive = true,
    ) {
    }
}
