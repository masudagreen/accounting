<?php

declare(strict_types=1);

namespace Rucaro\Domain\Approval\Exception;

use DateTimeImmutable;
use Rucaro\Domain\Approval\ApprovalDecision;

/**
 * Raised when a token is used a second time. Mapped to HTTP 410 Gone by
 * the controllers; the existing decision is surfaced in the context so the
 * client can explain what already happened.
 */
final class AlreadyRespondedException extends ApprovalException
{
    private const DOMAIN_CODE = 'APPROVAL_TOKEN_ALREADY_RESPONDED';

    public static function at(DateTimeImmutable $respondedAt, ApprovalDecision $decision): self
    {
        return new self(
            message: 'Approval token has already been responded to.',
            domainCode: self::DOMAIN_CODE,
            context: [
                'responded_at' => $respondedAt->format(DATE_ATOM),
                'decision'     => $decision->value,
            ],
        );
    }
}
