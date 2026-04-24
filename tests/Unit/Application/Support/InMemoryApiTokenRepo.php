<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Support;

use DateTimeImmutable;
use Rucaro\Domain\Auth\ApiToken;
use Rucaro\Domain\Auth\ApiTokenRepositoryInterface;

final class InMemoryApiTokenRepo implements ApiTokenRepositoryInterface
{
    /** @var list<ApiToken> */
    public array $tokens = [];

    public function save(ApiToken $token): void
    {
        $this->tokens[] = $token;
    }

    public function findByHash(string $tokenHash): ?ApiToken
    {
        foreach ($this->tokens as $i => $t) {
            if ($t->tokenHash === $tokenHash) {
                return $t;
            }
        }
        return null;
    }

    public function touchLastUsed(string $id, DateTimeImmutable $at): void
    {
        foreach ($this->tokens as $i => $t) {
            if ($t->id === $id) {
                $this->tokens[$i] = new ApiToken(
                    id: $t->id,
                    userId: $t->userId,
                    tokenHash: $t->tokenHash,
                    tokenPrefix: $t->tokenPrefix,
                    scopes: $t->scopes,
                    issuedAt: $t->issuedAt,
                    expiresAt: $t->expiresAt,
                    revokedAt: $t->revokedAt,
                    lastUsedAt: $at,
                    createdAt: $t->createdAt,
                    updatedAt: $at,
                );
                return;
            }
        }
    }

    public function revoke(string $id, DateTimeImmutable $at): void
    {
        foreach ($this->tokens as $i => $t) {
            if ($t->id === $id) {
                $this->tokens[$i] = new ApiToken(
                    id: $t->id,
                    userId: $t->userId,
                    tokenHash: $t->tokenHash,
                    tokenPrefix: $t->tokenPrefix,
                    scopes: $t->scopes,
                    issuedAt: $t->issuedAt,
                    expiresAt: $t->expiresAt,
                    revokedAt: $at,
                    lastUsedAt: $t->lastUsedAt,
                    createdAt: $t->createdAt,
                    updatedAt: $at,
                );
                return;
            }
        }
    }
}
