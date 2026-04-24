<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use DateTimeImmutable;
use Rucaro\Domain\Approval\ApprovalChannel;

/**
 * Result of {@see IssueApprovalTokenUseCase}.
 *
 * `tokenPlaintext` exists only in the response so the caller can render the
 * approval URL into their confirmation screen; it is NEVER persisted or
 * logged on the server side.
 */
final readonly class IssueApprovalTokenUseCaseOutput
{
    public function __construct(
        public string $tokenPlaintext,
        public string $tokenPrefix,
        public ApprovalChannel $channel,
        public string $recipient,
        public DateTimeImmutable $expiresAt,
    ) {
    }
}
