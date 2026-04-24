<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalToken;

/**
 * Result of {@see RespondToApprovalUseCase}. Exposes the fully-consumed
 * token plus the target snapshot for the HTTP layer to serialise.
 */
final readonly class RespondToApprovalUseCaseOutput
{
    public function __construct(
        public ApprovalToken $token,
        public ApprovalTargetInterface $target,
    ) {
    }
}
