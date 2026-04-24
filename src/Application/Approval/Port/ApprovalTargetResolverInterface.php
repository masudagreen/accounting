<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval\Port;

use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalTargetKind;

/**
 * Looks up the concrete {@see ApprovalTargetInterface} for a given
 * kind/id pair.
 *
 * The resolver is the single switch-point between the approval pipeline and
 * the target aggregates. Phase 5 only supports
 * {@see ApprovalTargetKind::Journal}; Phase 6 adds the Receipt branch by
 * composing in `ReceiptApprovalTargetResolver` without touching the UseCase
 * layer.
 */
interface ApprovalTargetResolverInterface
{
    /**
     * Resolves the target or throws {@see \Rucaro\Domain\Exception\EntityNotFoundException}
     * when the id does not map to a live aggregate. Implementations SHOULD
     * throw {@see \InvalidArgumentException} when they do not support the
     * supplied kind.
     */
    public function resolve(ApprovalTargetKind $kind, string $id): ApprovalTargetInterface;
}
