<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\AccountTitle;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\AccountTitleValidator;

#[CoversClass(AccountTitleValidator::class)]
final class AccountTitleValidatorTest extends TestCase
{
    public function testValidInputReturnsNoErrors(): void
    {
        $errors = AccountTitleValidator::validate('A101', '現金', 'asset', 'debit');
        self::assertSame([], $errors);
    }

    public function testEmptyCodeReports(): void
    {
        $errors = AccountTitleValidator::validate('', '現金', 'asset', 'debit');
        self::assertArrayHasKey('code', $errors);
    }

    public function testTooLongCodeReports(): void
    {
        $code = str_repeat('A', AccountTitleValidator::CODE_MAX_LENGTH + 1);
        $errors = AccountTitleValidator::validate($code, '名称', 'asset', 'debit');
        self::assertArrayHasKey('code', $errors);
    }

    public function testCodeWithSpaceIsRejected(): void
    {
        $errors = AccountTitleValidator::validate('A 101', '名称', 'asset', 'debit');
        self::assertArrayHasKey('code', $errors);
    }

    public function testEmptyNameReports(): void
    {
        $errors = AccountTitleValidator::validate('A101', '', 'asset', 'debit');
        self::assertArrayHasKey('name', $errors);
    }

    public function testInvalidCategoryReports(): void
    {
        $errors = AccountTitleValidator::validate('A101', '現金', 'not_a_category', 'debit');
        self::assertArrayHasKey('category', $errors);
    }

    public function testInvalidNormalSideReports(): void
    {
        $errors = AccountTitleValidator::validate('A101', '現金', 'asset', 'both');
        self::assertArrayHasKey('normal_side', $errors);
    }
}
