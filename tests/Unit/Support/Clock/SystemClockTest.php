<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Clock;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

#[CoversClass(SystemClock::class)]
final class SystemClockTest extends TestCase
{
    public function testImplementsClockInterface(): void
    {
        self::assertInstanceOf(ClockInterface::class, new SystemClock());
    }

    public function testDefaultTimezoneIsAsiaTokyo(): void
    {
        $clock = new SystemClock();
        $now = $clock->getCurrentTime();

        self::assertInstanceOf(DateTimeImmutable::class, $now);
        self::assertSame('Asia/Tokyo', $now->getTimezone()->getName());
    }

    public function testCustomTimezoneIsHonoured(): void
    {
        $clock = new SystemClock(new DateTimeZone('UTC'));
        $now = $clock->getCurrentTime();

        self::assertSame('UTC', $now->getTimezone()->getName());
    }

    public function testSuccessiveCallsAreMonotonicallyNonDecreasing(): void
    {
        $clock = new SystemClock();

        $first = $clock->getCurrentTime();
        $second = $clock->getCurrentTime();

        self::assertGreaterThanOrEqual(
            $first->getTimestamp(),
            $second->getTimestamp(),
        );
    }
}
