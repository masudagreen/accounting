<?php

declare(strict_types=1);

namespace Rucaro\Http\Middleware;

use DateTimeZone;
use Rucaro\Domain\Auth\ApiTokenRepositoryInterface;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Resolves a Bearer token from the `Authorization` header into an authenticated
 * user id, or returns null when authentication fails.
 *
 * The controllers call {@see self::authenticate()} with the incoming header
 * value; on success they receive the user id, on failure they render 401 via
 * {@see \Rucaro\Http\Response\ErrorResponse::unauthorized()}.
 *
 * We deliberately do not throw for auth failures — keeping it a return value
 * avoids tangling exception flow with normal unauthenticated responses.
 */
final class AuthenticateBearer
{
    public function __construct(
        private readonly ApiTokenRepositoryInterface $tokens,
        private readonly ClockInterface $clock,
    ) {
    }

    /**
     * @return string|null Authenticated user id as ULID, or null on failure.
     */
    public function authenticate(?string $authorizationHeader): ?string
    {
        $plaintext = self::extractBearer($authorizationHeader);
        if ($plaintext === null) {
            return null;
        }
        $hash = BearerTokenGenerator::hash($plaintext);
        $record = $this->tokens->findByHash($hash);
        if ($record === null) {
            return null;
        }
        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        if (!$record->isActive($now)) {
            return null;
        }
        // Constant-time re-check defeats identical-hash collision edge cases.
        if (!BearerTokenGenerator::hashEquals($record->tokenHash, $hash)) {
            return null;
        }
        $this->tokens->touchLastUsed($record->id, $now);
        return $record->userId;
    }

    public static function extractBearer(?string $header): ?string
    {
        if ($header === null || $header === '') {
            return null;
        }
        $header = trim($header);
        if (stripos($header, 'Bearer ') !== 0) {
            return null;
        }
        $token = substr($header, 7);
        $token = trim($token);
        return $token === '' ? null : $token;
    }
}
