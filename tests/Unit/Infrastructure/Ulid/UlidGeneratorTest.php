<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Ulid;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

#[CoversClass(UlidGenerator::class)]
final class UlidGeneratorTest extends TestCase
{
    public function testGenerateProducesTwentySixCrockfordChars(): void
    {
        $gen = new UlidGenerator();

        $ulid = $gen->generate();

        self::assertSame(26, strlen($ulid));
        self::assertMatchesRegularExpression('/^[0-9A-HJKMNP-TV-Z]{26}$/', $ulid);
    }

    public function testBinaryIsSixteenBytes(): void
    {
        $gen = new UlidGenerator();

        self::assertSame(16, strlen($gen->binary()));
    }

    public function testEncodeDecodeIsLossless(): void
    {
        // Deterministic input: timestamp = 0, randomness = 10 bytes of 0xFF.
        $epoch = new DateTimeImmutable('@0');
        $bin = UlidGenerator::buildBinary($epoch, str_repeat("\xFF", 10));

        $encoded = UlidGenerator::encode($bin);

        self::assertSame($bin, UlidGenerator::decode($encoded));
    }

    public function testEncodeRejectsWrongByteLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        UlidGenerator::encode(str_repeat("\x00", 15));
    }

    public function testDecodeRejectsWrongStringLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        UlidGenerator::decode('short');
    }

    public function testIsValidAcceptsWellFormedUlid(): void
    {
        $gen = new UlidGenerator();

        self::assertTrue(UlidGenerator::isValid($gen->generate()));
    }

    public function testIsValidRejectsLowercaseOrAmbiguous(): void
    {
        self::assertFalse(UlidGenerator::isValid('LOWERCASEINVALIDSTRING0000'));
        self::assertFalse(UlidGenerator::isValid('01HW7K9B2QV7C8Y4ZEXAMPLE0I')); // contains I
    }

    public function testTimestampBitsAppearInFirstTenChars(): void
    {
        $time = new DateTimeImmutable('2026-04-21T12:00:00.000Z', new DateTimeZone('UTC'));

        $clock = new class ($time) implements ClockInterface {
            public function __construct(private DateTimeImmutable $now)
            {
            }
            public function getCurrentTime(): DateTimeImmutable
            {
                return $this->now;
            }
        };

        $a = new UlidGenerator($clock);
        $u1 = $a->generate();
        $u2 = $a->generate();

        // Same clock => same 10-char timestamp prefix, differing random suffix.
        self::assertSame(substr($u1, 0, 10), substr($u2, 0, 10));
        self::assertNotSame($u1, $u2);
    }
}
