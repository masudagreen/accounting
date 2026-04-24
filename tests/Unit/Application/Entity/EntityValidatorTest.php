<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Entity\EntityValidator;

#[CoversClass(EntityValidator::class)]
final class EntityValidatorTest extends TestCase
{
    public function testValidInputReturnsNoErrors(): void
    {
        $errors = EntityValidator::validate('Acme Inc.', 'JPN', 'JPY', '0401');
        self::assertSame([], $errors);
    }

    public function testEmptyNameReports(): void
    {
        $errors = EntityValidator::validate('', 'JPN', 'JPY', '0101');
        self::assertArrayHasKey('name', $errors);
    }

    public function testInvalidNationCodeReports(): void
    {
        $errors = EntityValidator::validate('Acme', 'JP', 'JPY', '0101');
        self::assertArrayHasKey('nation_code', $errors);
    }

    public function testInvalidCurrencyCodeReports(): void
    {
        $errors = EntityValidator::validate('Acme', 'JPN', 'yen', '0101');
        self::assertArrayHasKey('currency_code', $errors);
    }

    public function testNonFourDigitMmDdReports(): void
    {
        $errors = EntityValidator::validate('Acme', 'JPN', 'JPY', '4/1');
        self::assertArrayHasKey('fiscal_start_mmdd', $errors);
    }

    public function testOutOfRangeMonthReports(): void
    {
        $errors = EntityValidator::validate('Acme', 'JPN', 'JPY', '1301');
        self::assertArrayHasKey('fiscal_start_mmdd', $errors);
    }
}
