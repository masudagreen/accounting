<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Application\Journal\JournalSearchResult;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalRepositoryInterface;

/**
 * In-memory {@see JournalRepositoryInterface} used by application-layer tests
 * so we don't have to spin up a database just to verify orchestration logic.
 *
 * Kept separately from the shared Phase 4.2 fake
 * ({@see \Rucaro\Tests\Support\Fake\InMemoryJournalRepository}) so the Phase 3
 * tests that predate criteria-based search keep their focused, minimal
 * fixture.
 */
final class InMemoryJournalRepo implements JournalRepositoryInterface
{
    /** @var list<Journal> */
    public array $saved = [];

    public function save(Journal $journal): void
    {
        foreach ($this->saved as $idx => $existing) {
            if ($existing->id === $journal->id) {
                $this->saved[$idx] = $journal;
                return;
            }
        }
        $this->saved[] = $journal;
    }

    public function findById(string $id): ?Journal
    {
        foreach ($this->saved as $j) {
            if ($j->id === $id) {
                return $j;
            }
        }
        return null;
    }

    public function findByCriteria(JournalSearchCriteria $criteria): JournalSearchResult
    {
        $matches = array_values(array_filter(
            $this->saved,
            static fn (Journal $j): bool => $j->entityId === $criteria->entityId
                && ($criteria->includeTrashed || $j->deletedAt === null),
        ));
        $offset = ($criteria->page - 1) * $criteria->pageSize;
        $paged = array_slice($matches, $offset, $criteria->pageSize);
        return new JournalSearchResult(
            items: array_values($paged),
            total: count($matches),
            page: $criteria->page,
            pageSize: $criteria->pageSize,
        );
    }

    public function delete(string $id, DateTimeImmutable $at, string $deletedBy): void
    {
        foreach ($this->saved as $idx => $j) {
            if ($j->id === $id && $j->deletedAt === null) {
                $this->saved[$idx] = $j->softDelete($at);
                unset($deletedBy);
                return;
            }
        }
        throw new EntityNotFoundException(sprintf('Journal %s not found.', $id));
    }

    public function searchByEntity(
        string $entityId,
        int $page,
        int $pageSize,
        ?string $fiscalTermId = null,
        ?string $from = null,
        ?string $to = null,
        ?string $status = null,
        ?string $source = null,
        ?string $search = null,
        bool $includeTrashed = false,
    ): array {
        /** @var list<Journal> $matches */
        $matches = array_values(array_filter(
            $this->saved,
            static fn (Journal $j): bool => $j->entityId === $entityId,
        ));
        return array_slice($matches, ($page - 1) * $pageSize, $pageSize);
    }

    public function countByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        ?string $from = null,
        ?string $to = null,
        ?string $status = null,
        ?string $source = null,
        ?string $search = null,
        bool $includeTrashed = false,
    ): int {
        return count(array_filter(
            $this->saved,
            static fn (Journal $j): bool => $j->entityId === $entityId,
        ));
    }
}
