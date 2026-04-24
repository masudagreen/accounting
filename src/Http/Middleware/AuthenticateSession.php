<?php

declare(strict_types=1);

namespace Rucaro\Http\Middleware;

use DateTimeZone;
use Rucaro\Domain\Auth\ApiTokenRepositoryInterface;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Web\SessionStore;

/**
 * Session-backed counterpart to {@see AuthenticateBearer}.
 *
 * The Web UI stores the Bearer plaintext inside `$_SESSION` after login so
 * subsequent requests can be authenticated without re-entering credentials.
 * This middleware validates that stored token against the same
 * {@see ApiTokenRepositoryInterface} the REST API uses — giving the UI and
 * the API a single source of truth for token lifetime and revocation.
 *
 * On any failure (missing token, expired, revoked, repo missing) the caller
 * is expected to redirect the user to the login page. The middleware itself
 * just returns a user id or null.
 */
final class AuthenticateSession
{
    public function __construct(
        private readonly ApiTokenRepositoryInterface $tokens,
        private readonly ClockInterface $clock,
        private readonly SessionStore $session,
    ) {
    }

    /**
     * @return string|null Authenticated user id as ULID, or null when the
     *                     session is anonymous or the stored token has
     *                     become invalid.
     */
    public function authenticate(): ?string
    {
        $userId = $this->session->getUserId();
        $plaintext = $this->session->getTokenPlaintext();
        if ($userId === null || $plaintext === null) {
            return null;
        }
        $hash = BearerTokenGenerator::hash($plaintext);
        $record = $this->tokens->findByHash($hash);
        if ($record === null) {
            $this->session->forgetUser();
            return null;
        }
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        if (!$record->isActive($now)) {
            $this->session->forgetUser();
            return null;
        }
        if (!BearerTokenGenerator::hashEquals($record->tokenHash, $hash)) {
            $this->session->forgetUser();
            return null;
        }
        if ($record->userId !== $userId) {
            // The session claims to be user A but the token actually belongs
            // to user B. Treat the session as tampered and hard-reset.
            $this->session->forgetUser();
            return null;
        }
        $this->tokens->touchLastUsed($record->id, $now);
        return $record->userId;
    }
}
