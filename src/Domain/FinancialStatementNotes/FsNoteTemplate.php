<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatementNotes;

use Rucaro\Domain\Exception\ValidationException;

/**
 * Ship-time template row seeded by `0018_fs_notes_seed.sql`.
 *
 * Templates give users a one-click starting point for a new 注記表; they are
 * NOT per-entity and MUST NOT be mutated at runtime. Edits to the cloned
 * {@see FinancialStatementNote} never propagate back.
 */
final readonly class FsNoteTemplate
{
    public function __construct(
        public string $id,
        public string $code,
        public FsNoteCategory $category,
        public string $label,
        public string $defaultBody,
        public int $sortOrder,
    ) {
        if ($code === '' || strlen($code) > 32) {
            throw ValidationException::withErrors([
                'code' => ['code must be 1..32 characters.'],
            ]);
        }
        if ($label === '' || mb_strlen($label) > 128) {
            throw ValidationException::withErrors([
                'label' => ['label must be 1..128 characters.'],
            ]);
        }
        if ($defaultBody === '') {
            throw ValidationException::withErrors([
                'defaultBody' => ['defaultBody must not be empty.'],
            ]);
        }
        if ($sortOrder < 0) {
            throw ValidationException::withErrors([
                'sortOrder' => ['sortOrder must be non-negative.'],
            ]);
        }
    }
}
