<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval;

/**
 * Kinds of aggregates an approval token can point at.
 *
 * Phase 5 only implements {@see self::Journal}. Phase 6 adds receipts —
 * {@see self::Receipt} is defined now so resolvers and storage can be kept
 * forward-compatible without a schema change.
 */
enum ApprovalTargetKind: string
{
    case Journal = 'journal';
    case Receipt = 'receipt';
}
