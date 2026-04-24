<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval\Exception;

use DateTimeImmutable;

/**
 * Raised when a lookup succeeds but the token has already passed
 * `expires_at`. Mapped to HTTP 410 Gone by the controllers.
 */
final class TokenExpiredException extends ApprovalException
{
    private const DOMAIN_CODE = 'APPROVAL_TOKEN_EXPIRED';

    public static function at(DateTimeImmutable $expiredAt): self
    {
        return new self(
            message: 'Approval token has expired.',
            domainCode: self::DOMAIN_CODE,
            context: ['expired_at' => $expiredAt->format(DATE_ATOM)],
        );
    }
}
