<?php

declare(strict_types=1);

namespace Rucaro\Domain\Exception;

/**
 * Thrown when an aggregate's invariant is violated — e.g. a journal whose
 * debit and credit totals do not balance.
 *
 * `invariant` should be a stable dotted identifier (e.g.
 * `journal.must_balance`) so callers can route on it without parsing
 * prose messages.
 */
final class InvariantViolationException extends DomainException
{
    private const DOMAIN_CODE = 'INVARIANT_VIOLATION';

    /**
     * @param array<string, mixed> $context
     */
    public static function for(string $invariant, array $context = []): self
    {
        return new self(
            message: sprintf("Domain invariant violated: '%s'.", $invariant),
            domainCode: self::DOMAIN_CODE,
            context: array_merge($context, ['invariant' => $invariant]),
        );
    }
}
