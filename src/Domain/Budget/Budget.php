<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

use DateTimeImmutable;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * Aggregate root for 予算 (the annual budget).
 *
 * Carries the header (entity, fiscal term, name, status) plus the full
 * list of {@see BudgetLineItem} rows. Monthly / annual totals are always
 * derived, never stored, so the view layer never has to trust hand-edited
 * sums.
 *
 * Invariants:
 *   - `name` is non-empty and <= 128 chars;
 *   - status transitions are Draft → Approved → Locked only;
 *   - while status is Approved or Locked the header and line items are
 *     immutable. Mutators ({@see withHeader()}, {@see withLineItems()})
 *     therefore throw {@see InvariantViolationException}.
 */
final readonly class Budget
{
    /**
     * @param list<BudgetLineItem> $lineItems
     */
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public string $name,
        public BudgetStatus $status,
        public ?string $approvedBy,
        public ?DateTimeImmutable $approvedAt,
        public ?string $notes,
        public array $lineItems,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt = null,
    ) {
        if ($name === '' || mb_strlen($name) > 128) {
            throw ValidationException::withErrors([
                'name' => ['name must be 1..128 characters.'],
            ]);
        }
        // approvedBy / approvedAt must be set together and only when status
        // is past Draft.
        if ($status === BudgetStatus::Draft) {
            if ($approvedBy !== null || $approvedAt !== null) {
                throw ValidationException::withErrors([
                    'status' => ['approvedBy / approvedAt must be null while draft.'],
                ]);
            }
        } else {
            if ($approvedBy === null || $approvedAt === null) {
                throw ValidationException::withErrors([
                    'status' => ['approvedBy / approvedAt are required once approved.'],
                ]);
            }
        }
    }

    /**
     * Monthly total across every line item for the given fiscal month.
     */
    public function monthlyTotal(int $month): string
    {
        $sum = '0.0000';
        foreach ($this->lineItems as $li) {
            $sum = Decimal::add($sum, $li->amountForMonth($month));
        }
        return Decimal::normalize($sum);
    }

    /**
     * Annual total across every line item (12-month sum).
     */
    public function annualTotal(): string
    {
        $sum = '0.0000';
        foreach ($this->lineItems as $li) {
            $sum = Decimal::add($sum, $li->totalAmount());
        }
        return Decimal::normalize($sum);
    }

    /**
     * Promote Draft → Approved. Rejects any other source state so the
     * state machine stays one-way.
     */
    public function approve(string $approverId, DateTimeImmutable $now): self
    {
        if ($this->status !== BudgetStatus::Draft) {
            throw InvariantViolationException::for('budget.approve.wrong_status', [
                'budgetId' => $this->id,
                'status'   => $this->status->value,
            ]);
        }
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: $this->name,
            status: BudgetStatus::Approved,
            approvedBy: $approverId,
            approvedAt: $now,
            notes: $this->notes,
            lineItems: $this->lineItems,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }

    /**
     * Promote Approved → Locked. Intended for use once the fiscal term
     * closes and the variance report has been signed off.
     */
    public function lock(DateTimeImmutable $now): self
    {
        if ($this->status !== BudgetStatus::Approved) {
            throw InvariantViolationException::for('budget.lock.wrong_status', [
                'budgetId' => $this->id,
                'status'   => $this->status->value,
            ]);
        }
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: $this->name,
            status: BudgetStatus::Locked,
            approvedBy: $this->approvedBy,
            approvedAt: $this->approvedAt,
            notes: $this->notes,
            lineItems: $this->lineItems,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }

    public function withHeader(
        string $name,
        ?string $notes,
        DateTimeImmutable $now,
    ): self {
        $this->assertEditable('header');
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: $name,
            status: $this->status,
            approvedBy: $this->approvedBy,
            approvedAt: $this->approvedAt,
            notes: $notes,
            lineItems: $this->lineItems,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }

    /**
     * Replace the entire line-item list.
     *
     * @param list<BudgetLineItem> $lineItems
     */
    public function withLineItems(array $lineItems, DateTimeImmutable $now): self
    {
        $this->assertEditable('lineItems');
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            name: $this->name,
            status: $this->status,
            approvedBy: $this->approvedBy,
            approvedAt: $this->approvedAt,
            notes: $this->notes,
            lineItems: $lineItems,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }

    private function assertEditable(string $field): void
    {
        if (!$this->status->isEditable()) {
            throw InvariantViolationException::for('budget.not_editable', [
                'budgetId' => $this->id,
                'field'    => $field,
                'status'   => $this->status->value,
            ]);
        }
    }
}
