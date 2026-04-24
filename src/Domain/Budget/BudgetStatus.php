<?php

declare(strict_types=1);

namespace Rucaro\Domain\Budget;

/**
 * Lifecycle status of a {@see Budget}.
 *
 * State machine (ADR-015):
 *   Draft    → Approved : {@see Budget::approve()}
 *   Approved → Locked   : {@see Budget::lock()}
 *
 * `Draft` budgets are freely editable / deletable. Once `Approved`, the
 * header and line items become immutable; the only operation left is
 * `lock()` which freezes the plan after the fiscal term closes. `Locked`
 * budgets are strictly read-only — variance analysis stays available so
 * reviewers can still see how the year played out.
 */
enum BudgetStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Locked = 'locked';

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isApproved(): bool
    {
        return $this === self::Approved || $this === self::Locked;
    }

    public function isLocked(): bool
    {
        return $this === self::Locked;
    }
}
