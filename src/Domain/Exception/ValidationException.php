<?php

declare(strict_types=1);

namespace Rucaro\Domain\Exception;

/**
 * Thrown when input data fails validation at the domain boundary.
 *
 * Carries a field-indexed error map so HTTP adapters can render 422
 * responses with per-field messages without re-deriving them.
 *
 * The error map is stored inside `context['errors']` so that the inherited
 * `withContext()` immutability contract round-trips cleanly without needing
 * a parallel mutable field.
 *
 * @phpstan-type ErrorMap array<string, list<string>>
 */
final class ValidationException extends DomainException
{
    private const DOMAIN_CODE = 'VALIDATION_FAILED';

    /**
     * @param ErrorMap $errors
     */
    public static function withErrors(array $errors): self
    {
        return new self(
            message: 'Validation failed for one or more fields.',
            domainCode: self::DOMAIN_CODE,
            context: ['errors' => $errors],
        );
    }

    /**
     * @return ErrorMap
     */
    public function errors(): array
    {
        $context = $this->context();
        $errors = $context['errors'] ?? [];

        if (!is_array($errors)) {
            return [];
        }

        /** @var ErrorMap $errors */
        return $errors;
    }
}
