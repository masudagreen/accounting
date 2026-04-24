<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FinancialStatement;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Section;

#[CoversClass(Section::class)]
#[CoversClass(FinancialStatementLine::class)]
final class SectionTest extends TestCase
{
    public function testFromLinesComputesSubtotalExcludingSubtotalLines(): void
    {
        $lines = [
            FinancialStatementLine::ofAccount('a1', '101', '現金', '1000'),
            FinancialStatementLine::ofAccount('a2', '102', '売掛金', '2000.5'),
            FinancialStatementLine::subtotal('旧小計', '9999'), // excluded from re-sum
        ];
        $section = Section::fromLines('assets', '資産の部', $lines);

        self::assertSame('assets', $section->code);
        self::assertSame('資産の部', $section->label);
        self::assertCount(3, $section->lines);
        self::assertSame('3000.5000', $section->subtotal);
    }

    public function testWithSubtotalReturnsNewSectionWithOverride(): void
    {
        $section = Section::fromLines('liabilities', '負債の部', [
            FinancialStatementLine::ofAccount('l1', '201', '買掛金', '500'),
        ]);
        self::assertSame('500.0000', $section->subtotal);

        $overridden = $section->withSubtotal('1234.5678');
        self::assertSame('1234.5678', $overridden->subtotal);
        // original is unchanged (immutability)
        self::assertSame('500.0000', $section->subtotal);
        self::assertNotSame($section, $overridden);
    }

    public function testWithAppendedLineRecomputesSubtotal(): void
    {
        $section = Section::fromLines('equity', '純資産の部', [
            FinancialStatementLine::ofAccount('e1', '301', '資本金', '1000'),
        ]);
        $augmented = $section->withAppendedLine(
            FinancialStatementLine::ofAccount('e2', '302', '利益剰余金', '250'),
        );

        self::assertSame('1000.0000', $section->subtotal);
        self::assertSame('1250.0000', $augmented->subtotal);
        self::assertCount(2, $augmented->lines);
    }

    public function testLineFactoriesSetAppropriateFlags(): void
    {
        $account = FinancialStatementLine::ofAccount('acc', '401', '売上', '500');
        self::assertFalse($account->isSubtotal);
        self::assertSame('acc', $account->accountTitleId);
        self::assertSame('401', $account->accountTitleCode);
        self::assertSame('500.0000', $account->amount);

        $sub = FinancialStatementLine::subtotal('売上合計', '500');
        self::assertTrue($sub->isSubtotal);
        self::assertNull($sub->accountTitleId);
        self::assertNull($sub->accountTitleCode);
        self::assertSame('500.0000', $sub->amount);
    }
}
