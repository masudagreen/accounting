<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;

#[CoversClass(BearerTokenGenerator::class)]
final class BearerTokenGeneratorTest extends TestCase
{
    public function testGenerateReturnsHexPlaintextOfExpectedLength(): void
    {
        $gen = new BearerTokenGenerator();

        $out = $gen->generate();

        self::assertSame(BearerTokenGenerator::TOKEN_HEX_LENGTH, strlen($out['plaintext']));
        self::assertMatchesRegularExpression('/^[0-9a-f]+$/', $out['plaintext']);
    }

    public function testGenerateHashMatchesSha256OfPlaintext(): void
    {
        $gen = new BearerTokenGenerator();

        $out = $gen->generate();

        self::assertSame(hash('sha256', $out['plaintext']), $out['hash']);
        self::assertSame(64, strlen($out['hash']));
    }

    public function testGeneratePrefixMatchesFirstEightCharacters(): void
    {
        $gen = new BearerTokenGenerator();

        $out = $gen->generate();

        self::assertSame(BearerTokenGenerator::PREFIX_LENGTH, strlen($out['prefix']));
        self::assertSame(substr($out['plaintext'], 0, 8), $out['prefix']);
    }

    public function testConsecutiveTokensDifferWithHighProbability(): void
    {
        $gen = new BearerTokenGenerator();

        $tokens = [];
        for ($i = 0; $i < 16; $i++) {
            $tokens[] = $gen->generate()['plaintext'];
        }

        self::assertSame(16, count(array_unique($tokens)));
    }

    public function testHashEqualsIsConstantTimeCompare(): void
    {
        $plaintext = 'a-consistent-value';
        $hash = BearerTokenGenerator::hash($plaintext);

        self::assertTrue(BearerTokenGenerator::hashEquals($hash, BearerTokenGenerator::hash($plaintext)));
        self::assertFalse(BearerTokenGenerator::hashEquals($hash, BearerTokenGenerator::hash('other')));
    }
}
