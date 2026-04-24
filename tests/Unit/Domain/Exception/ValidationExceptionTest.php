<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\DomainException;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(ValidationException::class)]
final class ValidationExceptionTest extends TestCase
{
    public function testExtendsDomainException(): void
    {
        $exception = ValidationException::withErrors(['email' => ['invalid']]);

        self::assertInstanceOf(DomainException::class, $exception);
    }

    public function testWithErrorsReturnsErrorMap(): void
    {
        $errors = [
            'email' => ['Email is not a valid address'],
            'age'   => ['Age must be at least 0', 'Age must be at most 150'],
        ];

        $exception = ValidationException::withErrors($errors);

        self::assertSame($errors, $exception->errors());
    }

    public function testWithErrorsSetsDomainCode(): void
    {
        $exception = ValidationException::withErrors(['email' => ['bad']]);

        self::assertSame('VALIDATION_FAILED', $exception->domainCode());
    }

    public function testWithErrorsIncludesErrorsInContext(): void
    {
        $errors = ['name' => ['is required']];

        $exception = ValidationException::withErrors($errors);

        self::assertSame(['errors' => $errors], $exception->context());
    }

    public function testMessageMentionsValidation(): void
    {
        $exception = ValidationException::withErrors(['email' => ['bad']]);

        self::assertStringContainsStringIgnoringCase('validation', $exception->getMessage());
    }
}
