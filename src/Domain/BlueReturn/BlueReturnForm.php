<?php

declare(strict_types=1);

namespace Rucaro\Domain\BlueReturn;

use DateTimeImmutable;
use Rucaro\Domain\Exception\InvariantViolationException;

/**
 * Aggregate root for a 青色申告決算書 submission.
 *
 * Holds the header (entity, fiscal term, form type, status) plus a
 * {@see BlueReturnSnapshot} value object carrying the full 4-page
 * payload.
 *
 * State machine (ADR-016):
 *   Draft → Finalized : {@see self::finalize()}
 *
 * Invariants:
 *   - `Draft` forms are editable; snapshot may be replaced via
 *     {@see self::withSnapshot()};
 *   - `Finalized` forms are read-only; both {@see self::withSnapshot()}
 *     and {@see self::finalize()} throw {@see InvariantViolationException}.
 */
final readonly class BlueReturnForm
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public BlueReturnFormType $formType,
        public BlueReturnStatus $status,
        public BlueReturnSnapshot $snapshot,
        public ?DateTimeImmutable $finalizedAt,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt = null,
    ) {
    }

    public function finalize(DateTimeImmutable $now): self
    {
        if ($this->status !== BlueReturnStatus::Draft) {
            throw InvariantViolationException::for('blue_return.finalize.wrong_status', [
                'formId' => $this->id,
                'status' => $this->status->value,
            ]);
        }
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            formType: $this->formType,
            status: BlueReturnStatus::Finalized,
            snapshot: $this->snapshot,
            finalizedAt: $now,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }

    public function withSnapshot(BlueReturnSnapshot $snapshot, DateTimeImmutable $now): self
    {
        if (!$this->status->isEditable()) {
            throw InvariantViolationException::for('blue_return.not_editable', [
                'formId' => $this->id,
                'status' => $this->status->value,
            ]);
        }
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            formType: $this->formType,
            status: $this->status,
            snapshot: $snapshot,
            finalizedAt: $this->finalizedAt,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }

    public function withFormType(BlueReturnFormType $formType, DateTimeImmutable $now): self
    {
        if (!$this->status->isEditable()) {
            throw InvariantViolationException::for('blue_return.not_editable', [
                'formId' => $this->id,
                'status' => $this->status->value,
            ]);
        }
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            formType: $formType,
            status: $this->status,
            snapshot: $this->snapshot,
            finalizedAt: $this->finalizedAt,
            createdBy: $this->createdBy,
            createdAt: $this->createdAt,
            updatedAt: $now,
            deletedAt: $this->deletedAt,
        );
    }
}
