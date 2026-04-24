<?php

declare(strict_types=1);

namespace Rucaro\Support\Result;

use LogicException;
use Throwable;

/**
 * Minimal Result monad-style value.
 *
 * Phase 1 scope: just enough to start modelling fallible operations without
 * exceptions. The full fluent API (map / flatMap / fold / etc.) will be
 * introduced in Phase 2 alongside the domain layer.
 *
 * @template TValue
 * @template TError of Throwable|string
 */
final readonly class Result
{
    /**
     * @param TValue|null        $value
     * @param TError|null        $error
     */
    private function __construct(
        public bool $isOk,
        private mixed $value = null,
        private mixed $error = null,
    ) {
    }

    /**
     * @template T
     * @param  T $value
     * @return self<T, never>
     */
    public static function ok(mixed $value): self
    {
        return new self(true, $value, null);
    }

    /**
     * @template E of Throwable|string
     * @param  E $error
     * @return self<never, E>
     */
    public static function err(mixed $error): self
    {
        return new self(false, null, $error);
    }

    public function isErr(): bool
    {
        return !$this->isOk;
    }

    /**
     * Unwraps the success value.
     *
     * @return TValue
     *
     * @throws LogicException when called on an error result.
     */
    public function unwrap(): mixed
    {
        if (!$this->isOk) {
            throw new LogicException('Cannot unwrap() an Err result.');
        }

        return $this->value;
    }

    /**
     * @return TError
     *
     * @throws LogicException when called on an ok result.
     */
    public function unwrapErr(): mixed
    {
        if ($this->isOk) {
            throw new LogicException('Cannot unwrapErr() an Ok result.');
        }

        return $this->error;
    }
}
