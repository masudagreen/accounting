<?php

declare(strict_types=1);

namespace Rucaro\Domain\BlueReturn;

/**
 * Lifecycle status of a {@see BlueReturnForm}.
 *
 * State machine (ADR-016):
 *   Draft → Finalized : {@see BlueReturnForm::finalize()}
 *
 * `Draft` forms are freely editable. Once `Finalized` the snapshot becomes
 * immutable (the tax return has been filed) so the compliance trail
 * never diverges from what was submitted to the 税務署.
 */
enum BlueReturnStatus: string
{
    case Draft = 'draft';
    case Finalized = 'finalized';

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isFinalized(): bool
    {
        return $this === self::Finalized;
    }
}
