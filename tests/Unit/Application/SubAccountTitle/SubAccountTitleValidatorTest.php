<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\SubAccountTitle;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\SubAccountTitle\SubAccountTitleValidator;

#[CoversClass(SubAccountTitleValidator::class)]
final class SubAccountTitleValidatorTest extends TestCase
{
    public function testValidInputReturnsNoErrors(): void
    {
        $errors = SubAccountTitleValidator::validate('01HX7MXPARENTULID00000000', 'S01', '東京支店');
        self::assertSame([], $errors);
    }

    public function testMissingParentReports(): void
    {
        $errors = SubAccountTitleValidator::validate('', 'S01', '東京支店');
        self::assertArrayHasKey('account_title_id', $errors);
    }

    public function testEmptyCodeReports(): void
    {
        $errors = SubAccountTitleValidator::validate('01HXPARENT', '', '東京支店');
        self::assertArrayHasKey('code', $errors);
    }

    public function testEmptyNameReports(): void
    {
        $errors = SubAccountTitleValidator::validate('01HXPARENT', 'S01', '');
        self::assertArrayHasKey('name', $errors);
    }

    public function testInvalidCodeCharsReport(): void
    {
        $errors = SubAccountTitleValidator::validate('01HXPARENT', 'S 01', '東京支店');
        self::assertArrayHasKey('code', $errors);
    }
}
