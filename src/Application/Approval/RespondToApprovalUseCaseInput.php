<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use Rucaro\Domain\Approval\ApprovalDecision;

/**
 * Input DTO for {@see RespondToApprovalUseCase}.
 *
 * `actorUserId` is optional: external reviewers may respond via the capability
 * URL without a logged-in session, in which case the UseCase falls back to
 * the token's original issuer so the audit trail still resolves to a user id.
 */
final readonly class RespondToApprovalUseCaseInput
{
    public function __construct(
        public string $tokenPlaintext,
        public ApprovalDecision $decision,
        public string $responseDetail = '',
        public string $actorUserId = '',
    ) {
    }
}
