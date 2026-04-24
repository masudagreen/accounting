<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use InvalidArgumentException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplateRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Seed a fresh 注記表 by cloning one or more {@see \Rucaro\Domain\FinancialStatementNotes\FsNoteTemplate}
 * rows into (entity, fiscalTerm).
 *
 * Idempotency rule (ADR-018 §3): a template whose `code` already has at
 * least one note in the target fiscal term is skipped rather than
 * duplicated. That lets UI re-submit "import these 6 templates" after a
 * partial failure without producing 12 rows on the second call.
 */
final readonly class BulkImportFsNotesFromTemplatesUseCase
{
    public function __construct(
        private FsNoteRepositoryInterface $notes,
        private FsNoteTemplateRepositoryInterface $templates,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @return list<FinancialStatementNote> The notes that were actually inserted (skipped ones are not returned).
     */
    public function execute(BulkImportFsNotesFromTemplatesInput $input): array
    {
        if (!UlidGenerator::isValid($input->entityId)) {
            throw new InvalidArgumentException('entityId must be a ULID.');
        }
        if (!UlidGenerator::isValid($input->fiscalTermId)) {
            throw new InvalidArgumentException('fiscalTermId must be a ULID.');
        }
        if ($input->templateCodes === []) {
            throw ValidationException::withErrors([
                'templateCodes' => ['templateCodes must not be empty.'],
            ]);
        }

        $templates = $this->templates->findByCodes($input->templateCodes);
        if ($templates === []) {
            throw ValidationException::withErrors([
                'templateCodes' => ['no known templates matched the requested codes.'],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        $inserted = [];
        foreach ($templates as $tpl) {
            $existingCount = $this->notes->countByTemplateCode(
                $input->entityId,
                $input->fiscalTermId,
                $tpl->code,
            );
            if ($existingCount > 0) {
                continue;
            }
            $note = new FinancialStatementNote(
                id: $this->ulids->generate(),
                entityId: $input->entityId,
                fiscalTermId: $input->fiscalTermId,
                templateCode: $tpl->code,
                category: $tpl->category,
                label: $tpl->label,
                body: $tpl->defaultBody,
                sortOrder: $tpl->sortOrder,
                isActive: true,
                createdAt: $now,
                updatedAt: $now,
            );
            $this->notes->save($note);
            $inserted[] = $note;
        }
        return $inserted;
    }
}
