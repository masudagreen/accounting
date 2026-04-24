<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal;

/**
 * Lifecycle states a {@see Journal} can hold.
 *
 * The string values align with the `status` CHECK constraint on
 * `journal_entries` (ADR-002 §journal_entries). New statuses introduced at
 * the domain layer (`reversed`, `voided`) are currently stored as
 * `posted` / `draft` in the DB snapshot respectively, with the domain-side
 * status tracked via reversals / deleted_at. The enum itself encodes the
 * richer domain semantics so UseCase code can reason about transitions
 * without inspecting prose strings.
 */
enum JournalStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Posted = 'posted';
    case Reversed = 'reversed';
    case Voided = 'voided';

    /**
     * Statuses that are accepted by the current DB CHECK constraint.
     *
     * @var list<string>
     */
    public const PERSISTED_STATUSES = ['draft', 'pending_approval', 'approved', 'rejected', 'posted'];

    public static function fromDbString(string $raw): self
    {
        // Domain-only statuses (`reversed`, `voided`) never reach DB today;
        // tolerate legacy / unknown values by falling back to Draft.
        foreach (self::cases() as $case) {
            if ($case->value === $raw) {
                return $case;
            }
        }
        return self::Draft;
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Reversed, self::Voided, self::Rejected => true,
            default => false,
        };
    }

    public function isMutable(): bool
    {
        return $this === self::Draft;
    }
}
