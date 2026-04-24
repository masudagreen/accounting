<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Planning;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Controller\Ui\Planning\PlanningFormSupport;

#[CoversClass(PlanningFormSupport::class)]
final class PlanningFormSupportTest extends TestCase
{
    public function testNormalizeAmountPadsTrailingZeros(): void
    {
        self::assertSame('1000.0000', PlanningFormSupport::normalizeAmount('1,000'));
        self::assertSame('1234.5000', PlanningFormSupport::normalizeAmount('1234.5'));
        self::assertSame('0.0000', PlanningFormSupport::normalizeAmount(''));
    }

    public function testNormalizeAmountKeepsNegativeSign(): void
    {
        self::assertSame('-1200.0000', PlanningFormSupport::normalizeAmount('-1,200'));
    }

    public function testNormalizeAmountTruncatesBeyondScale4(): void
    {
        self::assertSame('1.2345', PlanningFormSupport::normalizeAmount('1.234567'));
    }

    public function testBoolAcceptsCommonTruthyValues(): void
    {
        self::assertTrue(PlanningFormSupport::bool('1'));
        self::assertTrue(PlanningFormSupport::bool('true'));
        self::assertTrue(PlanningFormSupport::bool('on'));
        self::assertTrue(PlanningFormSupport::bool(true));
        self::assertFalse(PlanningFormSupport::bool(null));
        self::assertFalse(PlanningFormSupport::bool('false'));
    }

    public function testStrReturnsDefaultForMissingOrNonString(): void
    {
        self::assertSame('hello', PlanningFormSupport::str(['name' => 'hello'], 'name'));
        self::assertSame('', PlanningFormSupport::str(['name' => null], 'name'));
        self::assertSame('default', PlanningFormSupport::str([], 'missing', 'default'));
    }

    public function testExtractMonthlyRowsDropsEmptyRows(): void
    {
        $bag = [
            'entries' => [
                0 => ['label' => 'Row1', 'category' => 'operating_in', 'monthly' => ['100', '', '', '', '', '', '', '', '', '', '', ''], 'memo' => ''],
                1 => ['label' => '', 'category' => '', 'monthly' => ['', '', '', '', '', '', '', '', '', '', '', ''], 'memo' => ''],
            ],
        ];
        $rows = PlanningFormSupport::extractMonthlyRows($bag, 'entries');
        self::assertCount(1, $rows);
        self::assertSame('Row1', $rows[0]['label']);
        self::assertSame('100.0000', $rows[0]['monthly'][0]);
        self::assertSame('0.0000', $rows[0]['monthly'][1]);
    }
}
