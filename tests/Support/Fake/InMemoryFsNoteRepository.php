<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;

/**
 * In-memory {@see FsNoteRepositoryInterface} for fast UseCase tests.
 */
final class InMemoryFsNoteRepository implements FsNoteRepositoryInterface
{
    /** @var array<string, FinancialStatementNote> */
    private array $byId = [];

    public function save(FinancialStatementNote $note): void
    {
        $this->byId[$note->id] = $note;
    }

    public function findById(string $id): ?FinancialStatementNote
    {
        return $this->byId[$id] ?? null;
    }

    public function findByEntityAndTerm(
        string $entityId,
        string $fiscalTermId,
        bool $onlyActive = false,
    ): array {
        $out = [];
        foreach ($this->byId as $n) {
            if ($n->entityId !== $entityId || $n->fiscalTermId !== $fiscalTermId) {
                continue;
            }
            if ($onlyActive && !$n->isActive) {
                continue;
            }
            $out[] = $n;
        }
        usort(
            $out,
            static fn (FinancialStatementNote $a, FinancialStatementNote $b): int =>
                $a->sortOrder <=> $b->sortOrder ?: strcmp($a->id, $b->id),
        );
        return array_values($out);
    }

    public function countByTemplateCode(
        string $entityId,
        string $fiscalTermId,
        string $templateCode,
    ): int {
        $n = 0;
        foreach ($this->byId as $note) {
            if ($note->entityId === $entityId
                && $note->fiscalTermId === $fiscalTermId
                && $note->templateCode === $templateCode) {
                $n++;
            }
        }
        return $n;
    }

    public function delete(string $id): void
    {
        unset($this->byId[$id]);
    }
}
