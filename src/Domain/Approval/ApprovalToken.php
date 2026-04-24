<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval;

use DateTimeImmutable;

/**
 * Persisted approval token aggregate (see `approval_tokens` table, ADR-002).
 *
 * Capabilities: the token's plaintext form is a capability URL parameter —
 * anyone who holds the URL can respond exactly once. To avoid leaking live
 * tokens through DB backups, only the SHA-256 hex digest (`tokenHash`) is
 * stored. The first 16 chars of the plaintext are retained in `tokenPrefix`
 * so operators can correlate ops dashboards without revealing the live token.
 */
final readonly class ApprovalToken
{
    public function __construct(
        public string $id,
        public ApprovalTargetKind $targetKind,
        public string $targetId,
        public string $tokenHash,
        public string $tokenPrefix,
        public ApprovalChannel $channel,
        public string $recipient,
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public ?DateTimeImmutable $respondedAt,
        public ?ApprovalDecision $decision,
        public string $responseDetail,
        public string $issuedByUserId,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    public function isExpired(DateTimeImmutable $now): bool
    {
        return $this->expiresAt <= $now;
    }

    public function isResponded(): bool
    {
        return $this->respondedAt !== null;
    }

    public function isActive(DateTimeImmutable $now): bool
    {
        return !$this->isResponded() && !$this->isExpired($now);
    }

    /**
     * Returns a new aggregate with the response fields populated. Pure:
     * the existing instance is not mutated.
     */
    public function respond(ApprovalDecision $decision, string $detail, DateTimeImmutable $at): self
    {
        return new self(
            id: $this->id,
            targetKind: $this->targetKind,
            targetId: $this->targetId,
            tokenHash: $this->tokenHash,
            tokenPrefix: $this->tokenPrefix,
            channel: $this->channel,
            recipient: $this->recipient,
            issuedAt: $this->issuedAt,
            expiresAt: $this->expiresAt,
            respondedAt: $at,
            decision: $decision,
            responseDetail: $detail,
            issuedByUserId: $this->issuedByUserId,
            createdAt: $this->createdAt,
            updatedAt: $at,
        );
    }
}
