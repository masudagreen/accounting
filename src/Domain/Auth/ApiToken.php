<?php

declare(strict_types=1);

namespace Rucaro\Domain\Auth;

use DateTimeImmutable;

/**
 * Persisted opaque Bearer token (see `api_tokens` table, ADR-002 §4).
 *
 * Only `tokenHash` (SHA-256 hex) is stored; the plaintext token exists only
 * at issue time and must never be written to logs. `tokenPrefix` is 8 chars
 * of the plaintext, kept for cross-referencing in ops dashboards.
 */
final readonly class ApiToken
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $tokenHash,
        public string $tokenPrefix,
        public string $scopes,
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public ?DateTimeImmutable $revokedAt,
        public ?DateTimeImmutable $lastUsedAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    public function isRevoked(): bool
    {
        return $this->revokedAt !== null;
    }

    public function isExpired(DateTimeImmutable $now): bool
    {
        return $this->expiresAt <= $now;
    }

    public function isActive(DateTimeImmutable $now): bool
    {
        return !$this->isRevoked() && !$this->isExpired($now);
    }
}
