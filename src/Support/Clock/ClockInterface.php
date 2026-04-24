<?php

declare(strict_types=1);

namespace Rucaro\Support\Clock;

use DateTimeImmutable;

/**
 * Abstraction over "the current time".
 *
 * Exists so that domain and application code can stay testable:
 * production wires {@see SystemClock}, tests can supply a frozen /
 * deterministic implementation without touching global state.
 */
interface ClockInterface
{
    /**
     * Returns the current wall-clock time as an immutable value.
     *
     * Implementations MUST return a {@see DateTimeImmutable} so callers can
     * rely on it not mutating between reads.
     */
    public function getCurrentTime(): DateTimeImmutable;
}
