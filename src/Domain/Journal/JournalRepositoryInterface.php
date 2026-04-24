<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal;

use DateTimeImmutable;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Application\Journal\JournalSearchResult;

/**
 * Repository port for {@see Journal}. Implementations persist the aggregate
 * and its lines together as a single transactional unit.
 */
interface JournalRepositoryInterface
{
    public function save(Journal $journal): void;

    public function findById(string $id): ?Journal;

    /**
     * Criteria-driven search returning items + pagination metadata in a
     * single round-trip. Preferred entry point for new code.
     */
    public function findByCriteria(JournalSearchCriteria $criteria): JournalSearchResult;

    /**
     * Soft-deletes the target row by setting `deleted_at = $at`. Throws
     * {@see \Rucaro\Domain\Exception\EntityNotFoundException} when the id
     * does not resolve to a live journal.
     */
    public function delete(string $id, DateTimeImmutable $at, string $deletedBy): void;

    /**
     * Legacy search entry point kept for the Phase 3 list controller.
     *
     * @param int<1, max> $page
     * @param int<1, max> $pageSize
     * @return list<Journal>
     */
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
    ): array;

    public function countByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        ?string $from = null,
        ?string $to = null,
        ?string $status = null,
        ?string $source = null,
        ?string $search = null,
        bool $includeTrashed = false,
    ): int;
}
