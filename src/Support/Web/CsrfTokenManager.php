<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

use Rucaro\Support\Clock\ClockInterface;

/**
 * Per-form CSRF token manager backed by `$_SESSION`.
 *
 * Design:
 *   - `generateToken($formId)` mints a fresh random token and stores its
 *     issuance timestamp so `validateToken` can reject stale ones.
 *   - Tokens are scoped by `formId` so replays across forms fail even if a
 *     single token leaks.
 *   - Tokens auto-expire after 1 hour (enough for realistic operator flows,
 *     short enough to limit replay windows).
 *   - Validation uses `hash_equals` to defeat timing side channels.
 *
 * Not a cryptographic primitive — PHP session storage is already trusted;
 * the token just proves the POST originated from a page we served.
 */
final class CsrfTokenManager
{
    /** Token lifetime in seconds (1 hour). */
    public const TTL_SECONDS = 3600;

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function generateToken(string $formId): string
    {
        $bag = $this->bag();
        $token = bin2hex(random_bytes(32));
        $bag[$formId] = [
            'token'     => $token,
            'issued_at' => $this->clock->getCurrentTime()->getTimestamp(),
        ];
        $_SESSION[SessionStore::KEY_CSRF_TOKENS] = $bag;
        return $token;
    }

    public function validateToken(string $formId, string $submitted): bool
    {
        if ($submitted === '') {
            return false;
        }
        $bag = $this->bag();
        $entry = $bag[$formId] ?? null;
        if (!is_array($entry)) {
            return false;
        }
        $stored = $entry['token'] ?? null;
        $issuedAt = $entry['issued_at'] ?? null;
        if (!is_string($stored) || !is_int($issuedAt)) {
            return false;
        }
        $now = $this->clock->getCurrentTime()->getTimestamp();
        if ($now - $issuedAt > self::TTL_SECONDS) {
            unset($bag[$formId]);
            $_SESSION[SessionStore::KEY_CSRF_TOKENS] = $bag;
            return false;
        }
        if (!hash_equals($stored, $submitted)) {
            return false;
        }
        // One-shot semantics for state-changing POSTs.
        unset($bag[$formId]);
        $_SESSION[SessionStore::KEY_CSRF_TOKENS] = $bag;
        return true;
    }

    /**
     * @return array<string, array{token: string, issued_at: int}>
     */
    private function bag(): array
    {
        $raw = $_SESSION[SessionStore::KEY_CSRF_TOKENS] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        /** @var array<string, array{token: string, issued_at: int}> $typed */
        $typed = [];
        foreach ($raw as $k => $v) {
            if (!is_string($k) || !is_array($v)) {
                continue;
            }
            $t = $v['token'] ?? null;
            $i = $v['issued_at'] ?? null;
            if (is_string($t) && is_int($i)) {
                $typed[$k] = ['token' => $t, 'issued_at' => $i];
            }
        }
        return $typed;
    }
}
