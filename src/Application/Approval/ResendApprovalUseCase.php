<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use DateTimeZone;
use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Operator-driven resend of a previously-issued approval token.
 *
 * Semantics:
 *   - Token lookup is by prefix — the operator never holds the plaintext.
 *   - Already-responded tokens are rejected (410 at the HTTP layer).
 *   - If the token has not yet expired, the resolver returns the existing
 *     token and re-issues it through a brand-new token via the
 *     {@see IssueApprovalTokenUseCase} so the old capability URL is
 *     invalidated. Plaintext of the old token was never persisted so it
 *     cannot be re-sent directly.
 *
 * Returning the freshly-issued output DTO makes the calling controller
 * symmetrical with {@see IssueApprovalTokenUseCase}.
 */
final readonly class ResendApprovalUseCase
{
    public function __construct(
        private ApprovalTokenRepositoryInterface $tokens,
        private IssueApprovalTokenUseCase $issue,
        private ApprovalTargetResolverInterface $targets,
        private ClockInterface $clock,
    ) {
    }

    public function execute(ResendApprovalUseCaseInput $input): IssueApprovalTokenUseCaseOutput
    {
        $existing = $this->tokens->findByPrefix($input->tokenPrefix);
        if ($existing === null) {
            throw TokenNotFoundException::forPrefix($input->tokenPrefix);
        }
        // Re-validate the target still exists before issuing a fresh token.
        $this->targets->resolve($existing->targetKind, $existing->targetId);

        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $ttlHours = null;
        if ($existing->expiresAt > $now) {
            $remaining = $existing->expiresAt->getTimestamp() - $now->getTimestamp();
            $ttlHours = (int) ceil($remaining / 3600);
            if ($ttlHours < 1) {
                $ttlHours = 1;
            }
        }

        return $this->issue->execute(new IssueApprovalTokenUseCaseInput(
            targetKind: $existing->targetKind,
            targetId: $existing->targetId,
            channel: $existing->channel,
            recipient: $existing->recipient,
            issuedByUserId: $input->issuedByUserId,
            ttlHours: $ttlHours,
        ));
    }
}
