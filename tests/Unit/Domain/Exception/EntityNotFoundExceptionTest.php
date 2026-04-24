<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\DomainException;
use Rucaro\Domain\Exception\EntityNotFoundException;

#[CoversClass(EntityNotFoundException::class)]
final class EntityNotFoundExceptionTest extends TestCase
{
    public function testExtendsDomainException(): void
    {
        $exception = EntityNotFoundException::for('Journal', 'j-1');

        self::assertInstanceOf(DomainException::class, $exception);
    }

    public function testForBuildsHumanReadableMessage(): void
    {
        $exception = EntityNotFoundException::for('Journal', 'j-42');

        self::assertSame("Journal with id 'j-42' was not found.", $exception->getMessage());
    }

    public function testForSetsDomainCode(): void
    {
        $exception = EntityNotFoundException::for('Journal', 'j-42');

        self::assertSame('ENTITY_NOT_FOUND', $exception->domainCode());
    }

    public function testForIncludesEntityMetadataInContext(): void
    {
        $exception = EntityNotFoundException::for('Account', 'a-7');

        self::assertSame(
            [
                'entity' => 'Account',
                'id'     => 'a-7',
            ],
            $exception->context(),
        );
    }
}
