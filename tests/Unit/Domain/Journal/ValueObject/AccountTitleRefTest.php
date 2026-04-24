<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\ValueObject\AccountTitleRef;

#[CoversClass(AccountTitleRef::class)]
final class AccountTitleRefTest extends TestCase
{
    public function testHoldsIdAndCode(): void
    {
        $ref = new AccountTitleRef('01HW7K9B2QV7C8Y4ZACCTTL001', '1000');
        self::assertSame('01HW7K9B2QV7C8Y4ZACCTTL001', $ref->id);
        self::assertSame('1000', $ref->code);
        self::assertSame([
            'id'   => '01HW7K9B2QV7C8Y4ZACCTTL001',
            'code' => '1000',
        ], $ref->toPrimitive());
    }

    public function testEmptyIdIsRejected(): void
    {
        $this->expectException(ValidationException::class);
        new AccountTitleRef('', '1000');
    }

    public function testEmptyCodeIsRejected(): void
    {
        $this->expectException(ValidationException::class);
        new AccountTitleRef('01HW7K9B2QV7C8Y4ZACCTTL001', '');
    }

    public function testEqualsByValue(): void
    {
        $a = new AccountTitleRef('01HW7K9B2QV7C8Y4ZACCTTL001', '1000');
        $b = new AccountTitleRef('01HW7K9B2QV7C8Y4ZACCTTL001', '1000');
        $c = new AccountTitleRef('01HW7K9B2QV7C8Y4ZACCTTL001', '2000');

        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
    }
}
