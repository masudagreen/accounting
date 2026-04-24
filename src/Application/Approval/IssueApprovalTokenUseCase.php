<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

use DateInterval;
use DateTimeZone;
use Rucaro\Application\Approval\Port\ApprovalNotifierInterface;
use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Issue a fresh approval token and dispatch it via the configured channel.
 *
 * Sequence:
 *   1. Resolve the target aggregate (fails if the draft no longer exists).
 *   2. Generate a 32-byte random plaintext + SHA-256 hash (same primitive
 *      as the Bearer token flow).
 *   3. Persist the hash + metadata.
 *   4. Hand the plaintext + token + target to the notifier for rendering
 *      and dispatch.
 *
 * The plaintext only lives inside the UseCase's call frame and the returned
 * DTO; it never enters the repository.
 */
final readonly class IssueApprovalTokenUseCase
{
    public const DEFAULT_TTL_HOURS = 72;
    public const PREFIX_LENGTH = 16;

    public function __construct(
        private ApprovalTokenRepositoryInterface $tokens,
        private ApprovalTargetResolverInterface $targets,
        private ApprovalNotifierInterface $notifier,
        private BearerTokenGenerator $tokenGenerator,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
        private int $defaultTtlHours = self::DEFAULT_TTL_HOURS,
    ) {
    }

    public function execute(IssueApprovalTokenUseCaseInput $input): IssueApprovalTokenUseCaseOutput
    {
        $target = $this->targets->resolve($input->targetKind, $input->targetId);

        $generated = $this->tokenGenerator->generate();
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $ttlHours = $input->ttlHours ?? $this->defaultTtlHours;
        if ($ttlHours < 1) {
            $ttlHours = $this->defaultTtlHours;
        }
        $expiresAt = $now->add(new DateInterval(sprintf('PT%dH', $ttlHours)));

        $token = new ApprovalToken(
            id: $this->ulids->generate(),
            targetKind: $input->targetKind,
            targetId: $input->targetId,
            tokenHash: $generated['hash'],
            tokenPrefix: substr($generated['plaintext'], 0, self::PREFIX_LENGTH),
            channel: $input->channel,
            recipient: $input->recipient,
            issuedAt: $now,
            expiresAt: $expiresAt,
            respondedAt: null,
            decision: null,
            responseDetail: '',
            issuedByUserId: $input->issuedByUserId,
            createdAt: $now,
            updatedAt: $now,
        );
        $this->tokens->save($token);

        $this->notifier->notifyIssued($token, $generated['plaintext'], $target);

        return new IssueApprovalTokenUseCaseOutput(
            tokenPlaintext: $generated['plaintext'],
            tokenPrefix: $token->tokenPrefix,
            channel: $token->channel,
            recipient: $token->recipient,
            expiresAt: $expiresAt,
        );
    }
}
