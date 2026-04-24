<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Support;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Support\Clock\ClockInterface;

final class FixedClock implements ClockInterface
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

    public function advance(int $seconds): void
    {
        $this->now = $this->now->modify('+' . $seconds . ' seconds');
    }
}
