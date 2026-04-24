<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalTargetKind;

/**
 * Input DTO for {@see IssueApprovalTokenUseCase}.
 *
 * `ttlHours = null` lets the UseCase fall back to the operator-configured
 * `APPROVAL_TTL_HOURS` default (72 hours out of the box).
 */
final readonly class IssueApprovalTokenUseCaseInput
{
    public function __construct(
        public ApprovalTargetKind $targetKind,
        public string $targetId,
        public ApprovalChannel $channel,
        public string $recipient,
        public string $issuedByUserId,
        public ?int $ttlHours = null,
    ) {
    }
}
