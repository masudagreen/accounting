<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal;

use DateTimeImmutable;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\ValueObject\FiscalPeriod;
use Rucaro\Domain\Journal\ValueObject\JournalDate;
use Rucaro\Support\Decimal\Decimal;

/**
 * Journal entry aggregate root.
 *
 * Invariants enforced here (ADR-002 §4 / OpenAPI `/journals` POST):
 *   - At least one debit line and one credit line
 *   - Sum(debit.amount) == Sum(credit.amount)
 *   - totalAmount == Sum(debit.amount)
 *
 * Arithmetic uses BCMath with scale 4 (DECIMAL(18, 4)).
 *
 * Phase 4.2 adds lifecycle transitions ({@see approve}, {@see post},
 * {@see reverse}, {@see void}) plus a structural {@see withLines} helper.
 * The original Phase 3 constructor shape is preserved so the existing
 * use cases and test fixtures keep working without modification.
 */
final readonly class Journal
{
    public const STATUSES = [
        'draft',
        'pending_approval',
        'approved',
        'rejected',
        'posted',
        'reversed',
        'voided',
    ];
    public const SOURCES = ['manual', 'ai_receipt', 'bank_import', 'mail_import'];

    /**
     * @param list<JournalLine> $lines
     */
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public DateTimeImmutable $journalDate,
        public DateTimeImmutable $bookedAt,
        public string $summary,
        public string $totalAmount,
        public string $currencyCode,
        public string $status,
        public string $source,
        public ?string $sourceReceiptId,
        public string $createdBy,
        public ?string $approvedBy,
        public ?DateTimeImmutable $approvedAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt,
        public array $lines,
    ) {
        if (!in_array($status, self::STATUSES, true)) {
            throw ValidationException::withErrors([
                'status' => [sprintf("status must be one of: %s", implode(', ', self::STATUSES))],
            ]);
        }
        if (!in_array($source, self::SOURCES, true)) {
            throw ValidationException::withErrors([
                'source' => [sprintf("source must be one of: %s", implode(', ', self::SOURCES))],
            ]);
        }
        self::ensureBalanced($lines, $totalAmount);
    }

    /**
     * Factory helper that computes `totalAmount` from the lines and verifies
     * the balance invariant. Intended for use by the `CreateJournalUseCase`.
     *
     * @param list<JournalLine> $lines
     */
    public static function balance(array $lines): string
    {
        if (count($lines) < 2) {
            throw InvariantViolationException::for('journal.min_lines', [
                'expected' => 2,
                'actual'   => count($lines),
            ]);
        }

        $debit = '0.0000';
        $credit = '0.0000';
        foreach ($lines as $line) {
            if ($line->isDebit()) {
                $debit = Decimal::add($debit, $line->amount);
            } else {
                $credit = Decimal::add($credit, $line->amount);
            }
        }

        if (Decimal::compare($debit, '0.0000') === 0) {
            throw InvariantViolationException::for('journal.must_have_debit', [
                'debit_total'  => $debit,
                'credit_total' => $credit,
            ]);
        }
        if (Decimal::compare($credit, '0.0000') === 0) {
            throw InvariantViolationException::for('journal.must_have_credit', [
                'debit_total'  => $debit,
                'credit_total' => $credit,
            ]);
        }
        if (Decimal::compare($debit, $credit) !== 0) {
            throw InvariantViolationException::for('journal.must_balance', [
                'debit_total'  => $debit,
                'credit_total' => $credit,
            ]);
        }
        return Decimal::normalize($debit);
    }

    /**
     * @param list<JournalLine> $lines
     */
    private static function ensureBalanced(array $lines, string $expectedTotal): void
    {
        $computed = self::balance($lines);
        if (Decimal::compare($computed, $expectedTotal) !== 0) {
            throw InvariantViolationException::for('journal.total_matches_debits', [
                'expected' => $expectedTotal,
                'actual'   => $computed,
            ]);
        }
    }

    /**
     * Returns the lifecycle state as a strongly-typed enum.
     */
    public function statusEnum(): JournalStatus
    {
        return JournalStatus::fromDbString($this->status);
    }

    /**
     * Returns a {@see JournalDate} view of `journalDate` pinned to UTC.
     */
    public function entryDate(): JournalDate
    {
        return new JournalDate($this->journalDate);
    }

    /**
     * Guard that the entry date sits inside `$period`.
     */
    public function assertWithinFiscalPeriod(FiscalPeriod $period): void
    {
        if (!$period->contains($this->entryDate())) {
            throw InvariantViolationException::for('journal.entry_date_out_of_fiscal_period', [
                'journalDate' => $this->entryDate()->toPrimitive(),
                'period'      => $period->toPrimitive(),
            ]);
        }
        if ($this->fiscalTermId !== $period->fiscalTermId) {
            throw InvariantViolationException::for('journal.fiscal_term_mismatch', [
                'expected' => $period->fiscalTermId,
                'actual'   => $this->fiscalTermId,
            ]);
        }
    }

    /**
     * Returns a new aggregate with the given `$lines`; recomputes the total
     * and re-validates every invariant. Only usable while the aggregate is
     * in `draft` state — mutating a posted entry would break the audit
     * trail, so attempts raise {@see InvariantViolationException}.
     *
     * @param list<JournalLine> $lines
     */
    public function withLines(array $lines): self
    {
        if (!$this->statusEnum()->isMutable()) {
            throw InvariantViolationException::for('journal.immutable_after_draft', [
                'status' => $this->status,
            ]);
        }
        $total = self::balance($lines);
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            journalDate: $this->journalDate,
            bookedAt: $this->bookedAt,
            summary: $this->summary,
            totalAmount: $total,
            currencyCode: $this->currencyCode,
            status: $this->status,
            source: $this->source,
            sourceReceiptId: $this->sourceReceiptId,
            createdBy: $this->createdBy,
            approvedBy: $this->approvedBy,
            approvedAt: $this->approvedAt,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
            deletedAt: $this->deletedAt,
            lines: $lines,
        );
    }

    /**
     * Draft -> Approved transition. Idempotent on approved state? No:
     * re-approving signals a reviewer error, so we surface it.
     */
    public function approve(DateTimeImmutable $at, string $approvedBy): self
    {
        if ($this->statusEnum() !== JournalStatus::Draft && $this->statusEnum() !== JournalStatus::PendingApproval) {
            throw InvariantViolationException::for('journal.cannot_approve_from_status', [
                'status' => $this->status,
            ]);
        }
        return $this->copyWith(
            status: JournalStatus::Approved->value,
            approvedBy: $approvedBy,
            approvedAt: $at,
            updatedAt: $at,
        );
    }

    /**
     * Draft | PendingApproval -> Rejected. Mirrors {@see approve} for the
     * negative path driven by the email/message approval pipeline
     * (ADR-007, Phase 5). Rejection is terminal — the aggregate must be
     * cloned / edited into a fresh draft if the operator wants to retry.
     */
    public function reject(DateTimeImmutable $at, string $rejectedBy, string $reason): self
    {
        if ($this->statusEnum() !== JournalStatus::Draft && $this->statusEnum() !== JournalStatus::PendingApproval) {
            throw InvariantViolationException::for('journal.cannot_reject_from_status', [
                'status' => $this->status,
            ]);
        }
        if (trim($reason) === '') {
            throw ValidationException::withErrors([
                'reason' => ['reason must be a non-empty string.'],
            ]);
        }
        return $this->copyWith(
            status: JournalStatus::Rejected->value,
            approvedBy: $rejectedBy,
            approvedAt: $at,
            updatedAt: $at,
            summary: trim(sprintf('%s [REJECTED:%s]', $this->summary, $reason)),
        );
    }

    /**
     * Approved -> Posted. Post requires a fiscal term + at least one line.
     */
    public function post(DateTimeImmutable $at, string $postedBy): self
    {
        if ($this->statusEnum() !== JournalStatus::Approved) {
            throw InvariantViolationException::for('journal.cannot_post_from_status', [
                'status' => $this->status,
            ]);
        }
        if ($this->fiscalTermId === '') {
            throw InvariantViolationException::for('journal.post_requires_fiscal_term', []);
        }
        return $this->copyWith(
            status: JournalStatus::Posted->value,
            approvedBy: $postedBy,
            approvedAt: $at,
            updatedAt: $at,
        );
    }

    /**
     * Posted -> Reversed. Reversal leaves the aggregate as an audit record
     * and triggers a new reversing journal via {@see \Rucaro\Domain\Journal\Service\JournalReverser}
     * — this method handles the *source* side only.
     */
    public function reverse(DateTimeImmutable $at, string $reversedBy, string $reason): self
    {
        if ($this->statusEnum() !== JournalStatus::Posted) {
            throw InvariantViolationException::for('journal.cannot_reverse_from_status', [
                'status' => $this->status,
            ]);
        }
        if (trim($reason) === '') {
            throw ValidationException::withErrors([
                'reason' => ['reason must be a non-empty string.'],
            ]);
        }
        return $this->copyWith(
            status: JournalStatus::Reversed->value,
            approvedBy: $reversedBy,
            approvedAt: $at,
            updatedAt: $at,
            summary: trim(sprintf('%s [REVERSED:%s]', $this->summary, $reason)),
        );
    }

    /**
     * Draft -> Voided (soft void). Post-approval entries cannot be voided;
     * use {@see reverse} instead so the ledger remains auditable.
     */
    public function void(DateTimeImmutable $at, string $voidedBy, string $reason): self
    {
        if ($this->statusEnum() !== JournalStatus::Draft) {
            throw InvariantViolationException::for('journal.cannot_void_from_status', [
                'status' => $this->status,
            ]);
        }
        if (trim($reason) === '') {
            throw ValidationException::withErrors([
                'reason' => ['reason must be a non-empty string.'],
            ]);
        }
        return $this->copyWith(
            status: JournalStatus::Voided->value,
            deletedAt: $at,
            approvedBy: $voidedBy,
            updatedAt: $at,
            summary: trim(sprintf('%s [VOIDED:%s]', $this->summary, $reason)),
        );
    }

    /**
     * Soft-delete flag toggle, used by `DeleteJournalUseCase` when a draft
     * is discarded.
     */
    public function softDelete(DateTimeImmutable $at): self
    {
        if (!$this->statusEnum()->isMutable()) {
            throw InvariantViolationException::for('journal.immutable_after_draft', [
                'status' => $this->status,
            ]);
        }
        return $this->copyWith(deletedAt: $at, updatedAt: $at);
    }

    /**
     * Internal helper that builds a new instance with selected fields
     * overridden. All unspecified fields fall through from `$this`.
     */
    private function copyWith(
        ?string $status = null,
        ?string $approvedBy = null,
        ?DateTimeImmutable $approvedAt = null,
        ?DateTimeImmutable $updatedAt = null,
        ?DateTimeImmutable $deletedAt = null,
        ?string $summary = null,
    ): self {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            journalDate: $this->journalDate,
            bookedAt: $this->bookedAt,
            summary: $summary ?? $this->summary,
            totalAmount: $this->totalAmount,
            currencyCode: $this->currencyCode,
            status: $status ?? $this->status,
            source: $this->source,
            sourceReceiptId: $this->sourceReceiptId,
            createdBy: $this->createdBy,
            approvedBy: $approvedBy ?? $this->approvedBy,
            approvedAt: $approvedAt ?? $this->approvedAt,
            createdAt: $this->createdAt,
            updatedAt: $updatedAt ?? $this->updatedAt,
            deletedAt: $deletedAt ?? $this->deletedAt,
            lines: $this->lines,
        );
    }
}
