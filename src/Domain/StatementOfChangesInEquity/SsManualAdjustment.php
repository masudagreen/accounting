<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * Persistent manual adjustment row backing the `ss_manual_adjustments`
 * table (see migration 0017).
 *
 * A {@see SsManualAdjustment} is a single change-row the reviewer
 * entered by hand — typically a dividend, a new-issue event, or a
 * valuation adjustment that the journal layer does not encode in a
 * machine-readable way. The builder ({@see Service\StatementOfChangesInEquityBuilder})
 * folds these rows into their target column alongside any
 * automatically-derived journal changes.
 */
final readonly class SsManualAdjustment
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public SsSectionCode $sectionCode,
        public SsChangeType $changeType,
        public string $amount,
        public string $label,
        public int $sortOrder,
        public ?string $notes,
    ) {
        if ($label === '' || mb_strlen($label) > 128) {
            throw ValidationException::withErrors([
                'label' => ['label must be 1..128 characters.'],
            ]);
        }
        if ($notes !== null && mb_strlen($notes) > 255) {
            throw ValidationException::withErrors([
                'notes' => ['notes must be <= 255 characters when provided.'],
            ]);
        }
        if ($sortOrder < 0) {
            throw ValidationException::withErrors([
                'sortOrder' => ['sortOrder must be >= 0.'],
            ]);
        }
        // Trip fast on inputs bcmath/fixed-point cannot parse.
        Decimal::normalize($amount);
    }

    /**
     * Returns a fresh adjustment with the given mutable fields
     * replaced. Immutable fields (id, entityId, fiscalTermId) stay
     * pinned so callers cannot migrate a row across entities by
     * accident.
     */
    public function with(
        ?SsSectionCode $sectionCode = null,
        ?SsChangeType $changeType = null,
        ?string $amount = null,
        ?string $label = null,
        ?int $sortOrder = null,
        ?string $notes = null,
    ): self {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            sectionCode: $sectionCode ?? $this->sectionCode,
            changeType: $changeType ?? $this->changeType,
            amount: $amount ?? $this->amount,
            label: $label ?? $this->label,
            sortOrder: $sortOrder ?? $this->sortOrder,
            notes: $notes ?? $this->notes,
        );
    }

    public function toSsChange(): SsChange
    {
        return SsChange::of($this->changeType, $this->label, $this->amount, SsChange::SOURCE_MANUAL);
    }
}
