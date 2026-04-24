<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Auth\PasswordHasher;

#[CoversClass(PasswordHasher::class)]
final class PasswordHasherTest extends TestCase
{
    public function testHashAndVerifyRoundTripsTheOriginalPassword(): void
    {
        $hasher = new PasswordHasher(['memory_cost' => 1024, 'time_cost' => 1, 'threads' => 1]);

        $hash = $hasher->hash('correct-horse-battery-staple');

        self::assertNotSame('correct-horse-battery-staple', $hash);
        self::assertTrue($hasher->verify('correct-horse-battery-staple', $hash));
    }

    public function testVerifyRejectsWrongPassword(): void
    {
        $hasher = new PasswordHasher(['memory_cost' => 1024, 'time_cost' => 1, 'threads' => 1]);

        $hash = $hasher->hash('s3cret');

        self::assertFalse($hasher->verify('wrong', $hash));
    }

    public function testHashProducesArgon2idOutput(): void
    {
        $hasher = new PasswordHasher(['memory_cost' => 1024, 'time_cost' => 1, 'threads' => 1]);

        $hash = $hasher->hash('hello-world');

        self::assertStringStartsWith('$argon2id$', $hash);
    }

    public function testTwoHashesOfTheSamePasswordDiffer(): void
    {
        $hasher = new PasswordHasher(['memory_cost' => 1024, 'time_cost' => 1, 'threads' => 1]);

        $a = $hasher->hash('same');
        $b = $hasher->hash('same');

        self::assertNotSame($a, $b);
        self::assertTrue($hasher->verify('same', $a));
        self::assertTrue($hasher->verify('same', $b));
    }

    public function testNeedsRehashReturnsTrueWhenAlgoSettingsDrifted(): void
    {
        $strong = new PasswordHasher(['memory_cost' => 4096, 'time_cost' => 2, 'threads' => 1]);
        $cheap = new PasswordHasher(['memory_cost' => 1024, 'time_cost' => 1, 'threads' => 1]);

        $cheapHash = $cheap->hash('payload');

        self::assertTrue($strong->needsRehash($cheapHash));
        self::assertFalse($cheap->needsRehash($cheapHash));
    }
}
