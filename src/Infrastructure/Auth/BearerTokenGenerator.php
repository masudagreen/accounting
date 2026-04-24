<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Auth;

/**
 * Generates opaque Bearer tokens for the `/api/v1/auth/login` flow.
 *
 * Characteristics:
 *   - 32 random bytes from the OS CSPRNG, hex-encoded to 64 chars.
 *   - Accompanying SHA-256 digest used as the DB lookup key
 *     (`api_tokens.token_hash`) so a DB leak never exposes live tokens.
 *   - 8-char plaintext prefix retained for operator correlation.
 *
 * Not a JWT: the server validates by recomputing SHA-256 and selecting the
 * row, then constant-time comparing hashes to defeat timing side-channels.
 */
final class BearerTokenGenerator
{
    public const TOKEN_BYTE_LENGTH = 32;
    public const TOKEN_HEX_LENGTH = 64;
    public const PREFIX_LENGTH = 8;

    /**
     * @return array{plaintext: string, hash: string, prefix: string}
     */
    public function generate(): array
    {
        $bytes = random_bytes(self::TOKEN_BYTE_LENGTH);
        $plaintext = bin2hex($bytes);
        return [
            'plaintext' => $plaintext,
            'hash'      => self::hash($plaintext),
            'prefix'    => substr($plaintext, 0, self::PREFIX_LENGTH),
        ];
    }

    /**
     * Compute the storage hash for a Bearer plaintext. Always SHA-256 hex so
     * lookups stay index-friendly (`CHAR(64)`).
     */
    public static function hash(string $plaintext): string
    {
        return hash('sha256', $plaintext);
    }

    /**
     * Constant-time compare between two hex hashes. Avoid this on untrusted
     * byte strings of different lengths — pre-validate lengths where possible.
     */
    public static function hashEquals(string $expected, string $actual): bool
    {
        return hash_equals($expected, $actual);
    }
}
