<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval;

use DateTimeImmutable;

/**
 * Hexagonal port: the subject of an approval workflow.
 *
 * Implementations wrap an underlying aggregate (Journal draft today, Receipt
 * draft in Phase 6) and translate the approval decision into domain-specific
 * state transitions. Keeping the approval pipeline target-agnostic means the
 * notifier, UseCase, and HTTP layers never depend on a concrete aggregate.
 */
interface ApprovalTargetInterface
{
    public function kind(): ApprovalTargetKind;

    /**
     * Stable string identifier of the wrapped aggregate. For Journal this is
     * the ULID; for Receipt it will be the same.
     */
    public function id(): string;

    /**
     * Human-readable one-liner displayed in the approval email / message.
     */
    public function summary(): string;

    /**
     * Structured detail map for template rendering. Keys are locale-agnostic
     * snake_case strings (e.g. `total_amount`, `journal_date`). Values are
     * simple scalars or lists safe to serialise into a template context.
     *
     * @return array<string, mixed>
     */
    public function details(): array;

    /**
     * Apply the approval decision to the underlying aggregate.
     *
     * Implementations are responsible for persisting the new aggregate state
     * through their own repository — the UseCase will only invoke this once
     * the token bookkeeping has been verified.
     */
    public function applyApproval(string $actorUserId, DateTimeImmutable $at): void;

    /**
     * Apply a rejection decision, recording the operator-supplied reason.
     */
    public function applyRejection(string $actorUserId, DateTimeImmutable $at, string $reason): void;
}
