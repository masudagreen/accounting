<?php

declare(strict_types=1);

namespace Rucaro\Support\Validation;

use Rucaro\Domain\Exception\ValidationException;

/**
 * Lightweight assertion helpers for value-object constructors and other
 * domain-boundary validation.
 *
 * Every failure surfaces as a {@see ValidationException} whose error map is
 * keyed by the `$field` argument. Callers that need to batch multiple
 * checks should accumulate errors themselves and throw
 * `ValidationException::withErrors()` directly.
 *
 * This class is intentionally a static-only utility: it holds no state and
 * exists only to keep validation call sites terse and consistent.
 */
final class Assert
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
        // not instantiable
    }

    public static function notEmpty(string $value, string $field): void
    {
        if (trim($value) === '') {
            throw ValidationException::withErrors([
                $field => [sprintf("'%s' must not be empty.", $field)],
            ]);
        }
    }

    public static function minLength(string $value, int $min, string $field): void
    {
        if (mb_strlen($value) < $min) {
            throw ValidationException::withErrors([
                $field => [sprintf(
                    "'%s' must be at least %d characters long.",
                    $field,
                    $min,
                )],
            ]);
        }
    }

    public static function maxLength(string $value, int $max, string $field): void
    {
        if (mb_strlen($value) > $max) {
            throw ValidationException::withErrors([
                $field => [sprintf(
                    "'%s' must be at most %d characters long.",
                    $field,
                    $max,
                )],
            ]);
        }
    }

    public static function regex(string $value, string $pattern, string $field): void
    {
        if (preg_match($pattern, $value) !== 1) {
            throw ValidationException::withErrors([
                $field => [sprintf(
                    "'%s' does not match the required pattern.",
                    $field,
                )],
            ]);
        }
    }

    public static function email(string $value, string $field): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw ValidationException::withErrors([
                $field => [sprintf(
                    "'%s' is not a valid email address.",
                    $field,
                )],
            ]);
        }
    }

    public static function inRange(
        int|float $value,
        int|float $min,
        int|float $max,
        string $field,
    ): void {
        if ($value < $min || $value > $max) {
            throw ValidationException::withErrors([
                $field => [sprintf(
                    "'%s' must be between %s and %s.",
                    $field,
                    (string) $min,
                    (string) $max,
                )],
            ]);
        }
    }
}
