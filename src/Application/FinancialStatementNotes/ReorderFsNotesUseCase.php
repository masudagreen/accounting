<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatementNotes;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Apply an explicit (id → sort_order) map to a set of notes belonging to
 * the same (entity, fiscal term). Notes that do not match the scoping pair
 * are skipped defensively so a crafted request cannot reorder someone
 * else's data.
 */
final readonly class ReorderFsNotesUseCase
{
    public function __construct(
        private FsNoteRepositoryInterface $notes,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @param list<string> $orderedIds Ids in the new presentation order.
     * @return int Number of notes whose sort order was updated.
     */
    public function execute(
        string $entityId,
        string $fiscalTermId,
        array $orderedIds,
    ): int {
        if ($orderedIds === []) {
            return 0;
        }
        $seen = [];
        foreach ($orderedIds as $id) {
            if (!is_string($id) || $id === '' || isset($seen[$id])) {
                throw ValidationException::withErrors([
                    'orderedIds' => ['orderedIds must contain unique, non-empty strings.'],
                ]);
            }
            $seen[$id] = true;
        }

        $now = $this->clock->getCurrentTime();
        $updated = 0;
        foreach ($orderedIds as $idx => $id) {
            $note = $this->notes->findById($id);
            if ($note === null) {
                continue;
            }
            if ($note->entityId !== $entityId || $note->fiscalTermId !== $fiscalTermId) {
                continue;
            }
            if ($note->sortOrder === $idx) {
                continue;
            }
            $this->notes->save($note->withSortOrder($idx, $now));
            $updated++;
        }
        return $updated;
    }
}
