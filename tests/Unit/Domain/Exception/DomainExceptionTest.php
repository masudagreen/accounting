<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\DomainException;
use RuntimeException;

#[CoversClass(DomainException::class)]
final class DomainExceptionTest extends TestCase
{
    public function testExtendsRuntimeException(): void
    {
        $exception = new ConcreteDomainException('something went wrong');

        self::assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testStoresDomainCodeAsString(): void
    {
        $exception = new ConcreteDomainException(
            message: 'not balanced',
            domainCode: 'JOURNAL_NOT_BALANCED',
        );

        self::assertSame('JOURNAL_NOT_BALANCED', $exception->domainCode());
    }

    public function testDomainCodeIsNullableByDefault(): void
    {
        $exception = new ConcreteDomainException('no code provided');

        self::assertNull($exception->domainCode());
    }

    public function testContextDefaultsToEmptyArray(): void
    {
        $exception = new ConcreteDomainException('plain');

        self::assertSame([], $exception->context());
    }

    public function testContextIsPreservedFromConstructor(): void
    {
        $context = ['journalId' => 'j-123', 'delta' => 50];

        $exception = new ConcreteDomainException(
            message: 'bad',
            context: $context,
        );

        self::assertSame($context, $exception->context());
    }

    public function testWithContextReturnsNewInstance(): void
    {
        $original = new ConcreteDomainException(
            message: 'bad',
            domainCode: 'ERR',
            context: ['a' => 1],
        );

        $derived = $original->withContext(['a' => 1, 'b' => 2]);

        self::assertNotSame($original, $derived);
        self::assertSame(['a' => 1], $original->context());
        self::assertSame(['a' => 1, 'b' => 2], $derived->context());
    }

    public function testWithContextPreservesMessageAndCode(): void
    {
        $original = new ConcreteDomainException(
            message: 'bad',
            domainCode: 'ERR',
            context: [],
        );

        $derived = $original->withContext(['x' => 'y']);

        self::assertSame('bad', $derived->getMessage());
        self::assertSame('ERR', $derived->domainCode());
    }
}
