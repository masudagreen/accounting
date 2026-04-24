<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Common\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Common\ValueObject\EmailAddress;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Validation\AbstractValueObject;

#[CoversClass(EmailAddress::class)]
#[CoversClass(AbstractValueObject::class)]
final class EmailAddressTest extends TestCase
{
    public function testAcceptsValidEmail(): void
    {
        $email = new EmailAddress('user@example.com');

        self::assertSame('user@example.com', $email->value());
    }

    public function testRejectsInvalidEmail(): void
    {
        $this->expectException(ValidationException::class);

        new EmailAddress('not-an-email');
    }

    public function testToPrimitiveReturnsStringValue(): void
    {
        $email = new EmailAddress('user@example.com');

        self::assertSame('user@example.com', $email->toPrimitive());
    }

    public function testStringCastReturnsUnderlyingValue(): void
    {
        $email = new EmailAddress('user@example.com');

        self::assertSame('user@example.com', (string) $email);
    }

    public function testEqualsReturnsTrueForSameValue(): void
    {
        $a = new EmailAddress('user@example.com');
        $b = new EmailAddress('user@example.com');

        self::assertTrue($a->equals($b));
    }

    public function testEqualsReturnsFalseForDifferentValue(): void
    {
        $a = new EmailAddress('a@example.com');
        $b = new EmailAddress('b@example.com');

        self::assertFalse($a->equals($b));
    }
}
