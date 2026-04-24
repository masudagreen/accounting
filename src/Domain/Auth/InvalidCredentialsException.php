<?php

declare(strict_types=1);

namespace Rucaro\Domain\Auth;

use Rucaro\Domain\Exception\DomainException;

/**
 * Thrown when email/password combination is not valid, or when the user
 * record is inactive / soft-deleted.
 *
 * The message stays deliberately generic so we don't leak whether the email
 * exists vs the password was wrong.
 */
final class InvalidCredentialsException extends DomainException
{
    private const DOMAIN_CODE = 'INVALID_CREDENTIALS';

    public static function create(): self
    {
        return new self(
            message: 'Invalid email or password.',
            domainCode: self::DOMAIN_CODE,
        );
    }
}
