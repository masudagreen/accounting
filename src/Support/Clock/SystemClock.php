<?php

declare(strict_types=1);

namespace Rucaro\Support\Clock;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Default {@see ClockInterface} implementation backed by the system clock.
 *
 * A timezone can be pinned at construction time; otherwise the application's
 * configured default (Asia/Tokyo per ADR-001) is used.
 */
final readonly class SystemClock implements ClockInterface
{
    public function __construct(
        private DateTimeZone $timezone = new DateTimeZone('Asia/Tokyo'),
    ) {
    }

    public function getCurrentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone);
    }
}
