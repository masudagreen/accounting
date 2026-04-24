<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Container\Container;
use Rucaro\Support\Container\Exception\ContainerException;
use Rucaro\Support\Container\Exception\NotFoundException;

#[CoversClass(Container::class)]
final class ContainerTest extends TestCase
{
    public function testHasReturnsFalseForUnknownId(): void
    {
        $c = new Container();

        self::assertFalse($c->has('Unknown'));
    }

    public function testSetAndGetYieldsCachedInstance(): void
    {
        $c = new Container();
        $c->set('service', static fn (): object => new \stdClass());

        $first = $c->get('service');
        $second = $c->get('service');

        self::assertIsObject($first);
        self::assertSame($first, $second);
    }

    public function testGetThrowsNotFoundOnMissingId(): void
    {
        $c = new Container();

        $this->expectException(NotFoundException::class);

        $c->get('Nope');
    }

    public function testGetWrapsFactoryFailuresInContainerException(): void
    {
        $c = new Container();
        $c->set('bad', static function (): void {
            throw new \RuntimeException('broken');
        });

        $this->expectException(ContainerException::class);

        $c->get('bad');
    }

    public function testSetInstanceBypassesFactory(): void
    {
        $c = new Container();
        $obj = new \stdClass();
        $c->setInstance('foo', $obj);

        self::assertSame($obj, $c->get('foo'));
    }
}
