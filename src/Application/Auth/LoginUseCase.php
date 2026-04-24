<?php

declare(strict_types=1);

namespace Rucaro\Application\Auth;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\Auth\ApiToken;
use Rucaro\Domain\Auth\ApiTokenRepositoryInterface;
use Rucaro\Domain\Auth\InvalidCredentialsException;
use Rucaro\Domain\User\UserRepositoryInterface;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Infrastructure\Auth\PasswordHasher;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Exchange email + password for a new opaque Bearer token.
 *
 * The use case runs in four steps:
 *   1. Look up the user by email and reject deleted / inactive accounts
 *   2. Constant-time verify password against the stored Argon2id hash
 *   3. Mint a fresh 32-byte hex token and persist its SHA-256 digest
 *   4. Return the plaintext ONLY ONCE in {@see LoginUseCaseOutput}
 *
 * On any failure in steps 1 or 2, {@see InvalidCredentialsException} is thrown
 * with a generic message so callers do not leak whether the email exists.
 */
final readonly class LoginUseCase
{
    /** Default token lifetime: 24h — short enough to limit exposure, long
     *  enough for day-long operator workflows without re-login. */
    private const DEFAULT_LIFETIME_SECONDS = 86400;

    public function __construct(
        private UserRepositoryInterface $users,
        private ApiTokenRepositoryInterface $tokens,
        private PasswordHasher $passwords,
        private BearerTokenGenerator $tokenGenerator,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
        private int $lifetimeSeconds = self::DEFAULT_LIFETIME_SECONDS,
    ) {
    }

    public function execute(LoginUseCaseInput $input): LoginUseCaseOutput
    {
        $user = $this->users->findByEmail($input->email);
        if ($user === null || !$user->isActive || $user->isDeleted()) {
            throw InvalidCredentialsException::create();
        }
        if (!$this->passwords->verify($input->password, $user->passwordHash)) {
            throw InvalidCredentialsException::create();
        }

        $now = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $expires = $now->add(new DateInterval('PT' . $this->lifetimeSeconds . 'S'));

        $material = $this->tokenGenerator->generate();

        $apiToken = new ApiToken(
            id: $this->ulids->generate(),
            userId: $user->id,
            tokenHash: $material['hash'],
            tokenPrefix: $material['prefix'],
            scopes: '',
            issuedAt: $now,
            expiresAt: $expires,
            revokedAt: null,
            lastUsedAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
        $this->tokens->save($apiToken);
        $this->users->touchLastLogin($user->id, $now);

        return new LoginUseCaseOutput(
            token: $material['plaintext'],
            tokenPrefix: $material['prefix'],
            issuedAt: $now,
            expiresAt: $expires,
            userId: $user->id,
            loginId: $user->loginId,
            displayName: $user->displayName,
            email: $user->email,
        );
    }
}
