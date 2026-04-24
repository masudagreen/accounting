<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

/**
 * Partial-update DTO: every field except `id` is nullable. A null field means
 * "leave the existing value alone".
 */
final readonly class UpdateFsNoteInput
{
    public function __construct(
        public string $id,
        public ?string $category = null,
        public ?string $label = null,
        public ?string $body = null,
        public ?int $sortOrder = null,
        public ?bool $isActive = null,
    ) {
    }
}
