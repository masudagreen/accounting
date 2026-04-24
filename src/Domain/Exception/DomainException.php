<?php

declare(strict_types=1);

namespace Rucaro\Domain\Exception;

use RuntimeException;
use Throwable;

/**
 * Base class for all domain-level exceptions.
 *
 * Concrete subclasses MUST remain simple wrappers so their identity stays
 * meaningful (e.g. `catch (EntityNotFoundException $e)` vs a generic error).
 *
 * Carries two extras on top of RuntimeException:
 *
 * - A stable string-like `domainCode` (e.g. 'JOURNAL_NOT_BALANCED') that is
 *   safe to expose to API responses and log aggregators.
 * - A structured `context` map for observability — never put secrets in it.
 *
 * Designed to be semi-immutable: {@see self::withContext()} returns a new
 * instance rather than mutating the current one, matching the project's
 * immutability-first coding style.
 *
 * @phpstan-consistent-constructor
 */
abstract class DomainException extends RuntimeException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        string $message,
        protected readonly ?string $domainCode = null,
        protected readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function domainCode(): ?string
    {
        return $this->domainCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    /**
     * Returns a new instance with the supplied context map replacing the
     * current one. Preserves message, code, and previous exception.
     *
     * @param array<string, mixed> $context
     */
    final public function withContext(array $context): static
    {
        /**
         * Child classes are expected to keep the same 4-argument constructor
         * shape defined on this base. This cast is safe because `static`
         * resolves to the concrete class at runtime.
         *
         * @var static $clone
         */
        $clone = new static(
            $this->getMessage(),
            $this->domainCode,
            $context,
            $this->getPrevious(),
        );

        return $clone;
    }
}
