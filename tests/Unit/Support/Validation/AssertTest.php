<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Validation\Assert;

#[CoversClass(Assert::class)]
final class AssertTest extends TestCase
{
    public function testNotEmptyPassesOnNonBlankString(): void
    {
        Assert::notEmpty('hello', 'name');

        $this->expectNotToPerformAssertions();
    }

    public function testNotEmptyThrowsOnEmptyString(): void
    {
        $this->expectException(ValidationException::class);

        Assert::notEmpty('', 'name');
    }

    public function testNotEmptyThrowsOnWhitespaceOnly(): void
    {
        try {
            Assert::notEmpty("   \t\n", 'name');
            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            self::assertArrayHasKey('name', $e->errors());
        }
    }

    public function testMinLengthPassesAtBoundary(): void
    {
        Assert::minLength('abc', 3, 'code');

        $this->expectNotToPerformAssertions();
    }

    public function testMinLengthThrowsWhenTooShort(): void
    {
        try {
            Assert::minLength('ab', 3, 'code');
            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            self::assertArrayHasKey('code', $e->errors());
        }
    }

    public function testMaxLengthPassesAtBoundary(): void
    {
        Assert::maxLength('abc', 3, 'code');

        $this->expectNotToPerformAssertions();
    }

    public function testMaxLengthThrowsWhenTooLong(): void
    {
        try {
            Assert::maxLength('abcd', 3, 'code');
            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            self::assertArrayHasKey('code', $e->errors());
        }
    }

    public function testRegexPassesOnMatch(): void
    {
        Assert::regex('ABC-123', '/^[A-Z]+-\d+$/', 'code');

        $this->expectNotToPerformAssertions();
    }

    public function testRegexThrowsOnNonMatch(): void
    {
        $this->expectException(ValidationException::class);

        Assert::regex('nope', '/^[A-Z]+-\d+$/', 'code');
    }

    public function testEmailPassesOnValidAddress(): void
    {
        Assert::email('user@example.com', 'email');

        $this->expectNotToPerformAssertions();
    }

    public function testEmailThrowsOnInvalidAddress(): void
    {
        $this->expectException(ValidationException::class);

        Assert::email('not-an-email', 'email');
    }

    public function testInRangePassesAtLowerBoundary(): void
    {
        Assert::inRange(0, 0, 10, 'qty');

        $this->expectNotToPerformAssertions();
    }

    public function testInRangePassesAtUpperBoundary(): void
    {
        Assert::inRange(10, 0, 10, 'qty');

        $this->expectNotToPerformAssertions();
    }

    public function testInRangeThrowsWhenBelowMin(): void
    {
        $this->expectException(ValidationException::class);

        Assert::inRange(-1, 0, 10, 'qty');
    }

    public function testInRangeThrowsWhenAboveMax(): void
    {
        $this->expectException(ValidationException::class);

        Assert::inRange(11, 0, 10, 'qty');
    }

    public function testInRangeSupportsFloats(): void
    {
        Assert::inRange(0.5, 0.0, 1.0, 'ratio');

        $this->expectNotToPerformAssertions();
    }

    public function testValidationExceptionCarriesFieldSpecificError(): void
    {
        try {
            Assert::email('bad', 'emailAddress');
            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            self::assertArrayHasKey('emailAddress', $errors);
            self::assertNotEmpty($errors['emailAddress']);
        }
    }
}
