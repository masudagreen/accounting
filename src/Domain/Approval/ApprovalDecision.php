<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval;

/**
 * Terminal decision captured on an approval token response.
 *
 * String values align with the `approval_tokens.response` CHECK constraint
 * (ADR-002 §approval_tokens).
 */
enum ApprovalDecision: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function isApproved(): bool
    {
        return $this === self::Approved;
    }

    public function isRejected(): bool
    {
        return $this === self::Rejected;
    }
}
