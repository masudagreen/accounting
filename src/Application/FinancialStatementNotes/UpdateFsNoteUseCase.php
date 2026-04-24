<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Partial update for a single note. Fields left at null keep their prior
 * value, so the HTTP layer can trust PATCH semantics.
 */
final readonly class UpdateFsNoteUseCase
{
    public function __construct(
        private FsNoteRepositoryInterface $notes,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateFsNoteInput $input): FsNoteOutput
    {
        $existing = $this->notes->findById($input->id);
        if ($existing === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('note %s was not found.', $input->id)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $updated = $existing;

        if ($input->category !== null || $input->label !== null || $input->body !== null) {
            $category = $input->category !== null
                ? FsNoteCategory::tryFrom($input->category)
                : $existing->category;
            if ($category === null) {
                throw ValidationException::withErrors([
                    'category' => [sprintf('category "%s" is not a valid FsNoteCategory.', (string) $input->category)],
                ]);
            }
            $updated = $updated->withContent(
                category: $category,
                label: $input->label ?? $existing->label,
                body: $input->body ?? $existing->body,
                now: $now,
            );
        }
        if ($input->sortOrder !== null) {
            $updated = $updated->withSortOrder($input->sortOrder, $now);
        }
        if ($input->isActive !== null) {
            $updated = $updated->withActive($input->isActive, $now);
        }

        $this->notes->save($updated);
        return new FsNoteOutput($updated);
    }
}
