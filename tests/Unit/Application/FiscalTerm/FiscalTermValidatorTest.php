<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FiscalTerm;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FiscalTerm\FiscalTermValidator;

#[CoversClass(FiscalTermValidator::class)]
final class FiscalTermValidatorTest extends TestCase
{
    public function testValidInputReturnsNoErrors(): void
    {
        $r = FiscalTermValidator::validate(1, '2026-04-01', '2027-03-31');
        self::assertSame([], $r['errors']);
        self::assertNotNull($r['startDate']);
        self::assertNotNull($r['endDate']);
    }

    public function testZeroPeriodReports(): void
    {
        $r = FiscalTermValidator::validate(0, '2026-04-01', '2027-03-31');
        self::assertArrayHasKey('fiscal_period', $r['errors']);
    }

    public function testInvalidStartDateFormatReports(): void
    {
        $r = FiscalTermValidator::validate(1, '2026/04/01', '2027-03-31');
        self::assertArrayHasKey('start_date', $r['errors']);
        self::assertNull($r['startDate']);
    }

    public function testEndBeforeStartReports(): void
    {
        $r = FiscalTermValidator::validate(1, '2026-04-01', '2026-03-31');
        self::assertArrayHasKey('end_date', $r['errors']);
    }
}
