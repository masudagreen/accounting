<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Deterministic {@see ClockInterface} that returns a fixed instant.
 *
 * Prefer this over constructing anonymous classes in every test file — it
 * documents the intent ("time is frozen") and keeps the instantiation terse.
 */
final class FrozenClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(string $iso = '2026-04-21T12:00:00.000Z')
    {
        $this->now = new DateTimeImmutable($iso, new DateTimeZone('UTC'));
    }

    public function getCurrentTime(): DateTimeImmutable
    {
        return $this->now;
    }

    public function advance(string $modifier): void
    {
        $this->now = $this->now->modify($modifier) ?: $this->now;
    }

    public function set(DateTimeImmutable $at): void
    {
        $this->now = $at;
    }
}
