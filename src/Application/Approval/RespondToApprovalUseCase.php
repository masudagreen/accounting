<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use DateTimeZone;
use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;
use Rucaro\Domain\Approval\Exception\AlreadyRespondedException;
use Rucaro\Domain\Approval\Exception\TokenExpiredException;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Consume a one-time approval token and apply the reviewer's decision to
 * the underlying target aggregate.
 *
 * Validation order is deliberate:
 *   1. Token must exist (opaque 404 — never leak which tokens are valid).
 *   2. Token must not be already responded (410 Gone).
 *   3. Token must not have expired (410 Gone).
 *
 * Target mutations happen through {@see \Rucaro\Domain\Approval\ApprovalTargetInterface},
 * so the approval pipeline stays agnostic of the wrapped aggregate type.
 */
final readonly class RespondToApprovalUseCase
{
    public function __construct(
        private ApprovalTokenRepositoryInterface $tokens,
        private ApprovalTargetResolverInterface $targets,
        private ClockInterface $clock,
    ) {
    }

    public function execute(RespondToApprovalUseCaseInput $input): RespondToApprovalUseCaseOutput
    {
        $hash = BearerTokenGenerator::hash($input->tokenPlaintext);
        $token = $this->tokens->findByTokenHash($hash);
        if ($token === null) {
            throw TokenNotFoundException::forPlaintext();
        }
        if ($token->isResponded()) {
            /** @var \DateTimeImmutable $respondedAt */
            $respondedAt = $token->respondedAt;
            /** @var ApprovalDecision $decision */
            $decision = $token->decision ?? ApprovalDecision::Approved;
            throw AlreadyRespondedException::at($respondedAt, $decision);
        }
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        if ($token->isExpired($now)) {
            throw TokenExpiredException::at($token->expiresAt);
        }

        $target = $this->targets->resolve($token->targetKind, $token->targetId);
        $actor = $input->actorUserId !== '' ? $input->actorUserId : $token->issuedByUserId;

        if ($input->decision === ApprovalDecision::Approved) {
            $target->applyApproval($actor, $now);
        } else {
            $reason = trim($input->responseDetail);
            if ($reason === '') {
                $reason = 'rejected via approval link';
            }
            $target->applyRejection($actor, $now, $reason);
        }

        $updated = $token->respond($input->decision, $input->responseDetail, $now);
        $this->tokens->save($updated);

        return new RespondToApprovalUseCaseOutput(
            token: $updated,
            target: $target,
        );
    }
}
