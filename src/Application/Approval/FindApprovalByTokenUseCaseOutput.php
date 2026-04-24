<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalToken;

/**
 * Result of {@see FindApprovalByTokenUseCase}.
 *
 * `status` is one of `active`, `expired`, or `responded` — the HTTP layer
 * uses it to pick between 200 / 410 / 200 with a message.
 */
final readonly class FindApprovalByTokenUseCaseOutput
{
    public function __construct(
        public ApprovalToken $token,
        public ApprovalTargetInterface $target,
        public string $status,
    ) {
    }
}
