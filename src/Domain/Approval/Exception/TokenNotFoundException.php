<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval\Exception;

/**
 * Raised when the supplied capability token does not match any issued
 * approval record. Mapped to HTTP 404 by the controllers.
 */
final class TokenNotFoundException extends ApprovalException
{
    private const DOMAIN_CODE = 'APPROVAL_TOKEN_NOT_FOUND';

    public static function forPrefix(string $prefix): self
    {
        return new self(
            message: 'Approval token was not found.',
            domainCode: self::DOMAIN_CODE,
            context: ['token_prefix' => $prefix],
        );
    }

    public static function forPlaintext(): self
    {
        return new self(
            message: 'Approval token was not found.',
            domainCode: self::DOMAIN_CODE,
            context: [],
        );
    }
}
