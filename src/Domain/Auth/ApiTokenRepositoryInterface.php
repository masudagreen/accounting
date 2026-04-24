<?php

declare(strict_types=1);

namespace Rucaro\Domain\Auth;

/**
 * Repository port for {@see ApiToken}.
 */
interface ApiTokenRepositoryInterface
{
    public function save(ApiToken $token): void;

    /**
     * Look up a token record by its SHA-256 hex hash. The caller is expected
     * to compute the hash from the Bearer plaintext using a constant-time
     * hashing function.
     */
    public function findByHash(string $tokenHash): ?ApiToken;

    public function touchLastUsed(string $id, \DateTimeImmutable $at): void;

    public function revoke(string $id, \DateTimeImmutable $at): void;
}
