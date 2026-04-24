<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use DateTimeImmutable;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalTargetKind;

/**
 * Lightweight {@see ApprovalTargetInterface} used to verify that the
 * approval pipeline invokes the correct transition method on the underlying
 * target, without dragging Journal wiring into every test.
 */
final class FakeApprovalTarget implements ApprovalTargetInterface
{
    public ?string $approvedBy = null;
    public ?DateTimeImmutable $approvedAt = null;
    public ?string $rejectedBy = null;
    public ?DateTimeImmutable $rejectedAt = null;
    public ?string $rejectReason = null;

    public function __construct(
        private readonly ApprovalTargetKind $kind,
        private readonly string $id,
        private readonly string $summary = 'fake target',
    ) {
    }

    public function kind(): ApprovalTargetKind
    {
        return $this->kind;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function summary(): string
    {
        return $this->summary;
    }

    public function details(): array
    {
        return ['id' => $this->id, 'summary' => $this->summary];
    }

    public function applyApproval(string $actorUserId, DateTimeImmutable $at): void
    {
        $this->approvedBy = $actorUserId;
        $this->approvedAt = $at;
    }

    public function applyRejection(string $actorUserId, DateTimeImmutable $at, string $reason): void
    {
        $this->rejectedBy = $actorUserId;
        $this->rejectedAt = $at;
        $this->rejectReason = $reason;
    }
}
