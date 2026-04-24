<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use DateTimeImmutable;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Application\Journal\JournalSearchResult;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalRepositoryInterface;

/**
 * In-memory fake used by UseCase-level tests so we don't need to spin up
 * a MariaDB instance to verify orchestration logic. Implements every method
 * on the full port so the same fake works across Phase 3 (legacy search)
 * and Phase 4.2 (criteria-based search) code paths.
 */
final class InMemoryJournalRepository implements JournalRepositoryInterface
{
    /** @var array<string, Journal> */
    public array $byId = [];

    public function save(Journal $journal): void
    {
        $this->byId[$journal->id] = $journal;
    }

    public function findById(string $id): ?Journal
    {
        return $this->byId[$id] ?? null;
    }

    public function findByCriteria(JournalSearchCriteria $criteria): JournalSearchResult
    {
        $matches = $this->filter($criteria);
        $total = count($matches);
        $offset = ($criteria->page - 1) * $criteria->pageSize;
        $paged = array_slice($matches, $offset, $criteria->pageSize);
        return new JournalSearchResult(
            items: array_values($paged),
            total: $total,
            page: $criteria->page,
            pageSize: $criteria->pageSize,
        );
    }

    public function delete(string $id, DateTimeImmutable $at, string $deletedBy): void
    {
        $existing = $this->byId[$id] ?? null;
        if ($existing === null || $existing->deletedAt !== null) {
            throw new EntityNotFoundException(sprintf('Journal %s not found.', $id));
        }
        $this->byId[$id] = $existing->softDelete($at);
        unset($deletedBy);
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
        $all = array_values(array_filter(
            $this->byId,
            static function (Journal $j) use (
                $entityId,
                $fiscalTermId,
                $from,
                $to,
                $status,
                $source,
                $search,
                $includeTrashed,
            ): bool {
                if ($j->entityId !== $entityId) {
                    return false;
                }
                if (!$includeTrashed && $j->deletedAt !== null) {
                    return false;
                }
                if ($fiscalTermId !== null && $j->fiscalTermId !== $fiscalTermId) {
                    return false;
                }
                if ($from !== null && $j->journalDate->format('Y-m-d') < $from) {
                    return false;
                }
                if ($to !== null && $j->journalDate->format('Y-m-d') > $to) {
                    return false;
                }
                if ($status !== null && $j->status !== $status) {
                    return false;
                }
                if ($source !== null && $j->source !== $source) {
                    return false;
                }
                if ($search !== null && $search !== '' && !str_contains($j->summary, $search)) {
                    return false;
                }
                return true;
            },
        ));
        $offset = ($page - 1) * $pageSize;
        return array_values(array_slice($all, $offset, $pageSize));
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
        return count($this->searchByEntity(
            $entityId,
            1,
            PHP_INT_MAX,
            $fiscalTermId,
            $from,
            $to,
            $status,
            $source,
            $search,
            $includeTrashed,
        ));
    }

    /**
     * @return list<Journal>
     */
    private function filter(JournalSearchCriteria $criteria): array
    {
        /** @var list<Journal> $ordered */
        $ordered = array_values($this->byId);
        usort(
            $ordered,
            static fn (Journal $a, Journal $b): int => $b->bookedAt <=> $a->bookedAt ?: strcmp($b->id, $a->id),
        );

        return array_values(array_filter(
            $ordered,
            static function (Journal $j) use ($criteria): bool {
                if ($j->entityId !== $criteria->entityId) {
                    return false;
                }
                if (!$criteria->includeTrashed && $j->deletedAt !== null) {
                    return false;
                }
                if ($criteria->fiscalTermId !== null && $j->fiscalTermId !== $criteria->fiscalTermId) {
                    return false;
                }
                if ($criteria->from !== null
                    && $j->journalDate->format('Y-m-d') < $criteria->from->toPrimitive()
                ) {
                    return false;
                }
                if ($criteria->to !== null
                    && $j->journalDate->format('Y-m-d') > $criteria->to->toPrimitive()
                ) {
                    return false;
                }
                if ($criteria->status !== null && $j->status !== $criteria->status->value) {
                    return false;
                }
                if ($criteria->source !== null && $criteria->source !== '' && $j->source !== $criteria->source) {
                    return false;
                }
                if ($criteria->textQuery !== null
                    && $criteria->textQuery !== ''
                    && !str_contains($j->summary, $criteria->textQuery)
                ) {
                    return false;
                }
                if ($criteria->accountTitleId !== null && $criteria->accountTitleId !== '') {
                    $hit = false;
                    foreach ($j->lines as $line) {
                        if ($line->accountTitleId === $criteria->accountTitleId) {
                            $hit = true;
                            break;
                        }
                    }
                    if (!$hit) {
                        return false;
                    }
                }
                return true;
            },
        ));
    }
}
