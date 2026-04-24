<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Auth;

/**
 * Thin wrapper over PHP's password_* API pinned to Argon2id per ADR-003.
 *
 * A dedicated class lets us swap algorithms or tuning params later without
 * touching every use case, and simplifies mocking in unit tests.
 *
 * Parameters are memory/time tuned for server-side login (not for
 * disk-encryption style workloads). Callers must treat `hash()` output as
 * opaque and never truncate it.
 */
final class PasswordHasher
{
    private const ALGO = PASSWORD_ARGON2ID;

    /**
     * @var array<string, int>
     */
    private readonly array $options;

    /**
     * @param array<string, int>|null $options Overrides the defaults. Keys:
     *   - memory_cost (KiB)
     *   - time_cost   (iterations)
     *   - threads     (parallelism)
     */
    public function __construct(?array $options = null)
    {
        $this->options = $options ?? [
            'memory_cost' => 65_536, // 64 MiB
            'time_cost'   => 4,
            'threads'     => 1,
        ];
    }

    public function hash(string $plaintext): string
    {
        /** @var non-empty-string $hash */
        $hash = password_hash($plaintext, self::ALGO, $this->options);
        return $hash;
    }

    public function verify(string $plaintext, string $hash): bool
    {
        return password_verify($plaintext, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, self::ALGO, $this->options);
    }
}
