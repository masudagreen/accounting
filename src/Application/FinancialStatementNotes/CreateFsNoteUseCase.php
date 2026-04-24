<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use InvalidArgumentException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Create a new 注記表 row for (entity, fiscal term).
 *
 * Validation of label / body lengths happens inside the domain aggregate
 * so the HTTP layer always sees a {@see ValidationException} on bad input.
 */
final readonly class CreateFsNoteUseCase
{
    public function __construct(
        private FsNoteRepositoryInterface $notes,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(CreateFsNoteInput $input): FsNoteOutput
    {
        if (!UlidGenerator::isValid($input->entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->fiscalTermId)) {
            throw new InvalidArgumentException('fiscalTermId must be a ULID.');
        }
        $category = FsNoteCategory::tryFrom($input->category);
        if ($category === null) {
            throw ValidationException::withErrors([
                'category' => [sprintf('category "%s" is not a valid FsNoteCategory.', $input->category)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $note = new FinancialStatementNote(
            id: $this->ulids->generate(),
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            templateCode: $input->templateCode,
            category: $category,
            label: $input->label,
            body: $input->body,
            sortOrder: $input->sortOrder,
            isActive: $input->isActive,
            createdAt: $now,
            updatedAt: $now,
        );
        $this->notes->save($note);
        return new FsNoteOutput($note);
    }
}
