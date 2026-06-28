<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;

/**
 * In-memory fake for {@see ApprovalTokenRepositoryInterface}.
 *
 * Keeps tests free of a live DB while preserving the semantics the UseCase
 * layer depends on: idempotent save by hash, prefix lookup, and a best-effort
 * `expirePastDue` that simply returns the number of currently-expired tokens.
 */
final class InMemoryApprovalTokenRepository implements ApprovalTokenRepositoryInterface
{
    /** @var array<string, ApprovalToken> */
    public array $byHash = [];

    #[\Override]
    public function save(ApprovalToken $token): void
    {
        $this->byHash[$token->tokenHash] = $token;
    }

    #[\Override]
    public function findByTokenHash(string $tokenHash): ?ApprovalToken
    {
        return $this->byHash[$tokenHash] ?? null;
    }

    #[\Override]
    public function findByPrefix(string $tokenPrefix): ?ApprovalToken
    {
        $match = null;
        foreach ($this->byHash as $token) {
            if ($token->tokenPrefix !== $tokenPrefix) {
                continue;
            }
            if ($match === null || $token->issuedAt > $match->issuedAt) {
                $match = $token;
            }
        }

        return $match;
    }

    #[\Override]
    public function expirePastDue(\DateTimeImmutable $now): int
    {
        $count = 0;
        foreach ($this->byHash as $token) {
            if (!$token->isResponded() && $token->expiresAt <= $now) {
                ++$count;
            }
        }

        return $count;
    }
}
