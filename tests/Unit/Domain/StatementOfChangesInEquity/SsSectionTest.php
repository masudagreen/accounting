<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\StatementOfChangesInEquity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\StatementOfChangesInEquity\SsChange;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSection;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;

#[CoversClass(SsSection::class)]
final class SsSectionTest extends TestCase
{
    public function testFromChangesComputesEndingBalance(): void
    {
        $section = SsSection::fromChanges(
            SsSectionCode::RetainedEarnings,
            '100000000.0000',
            [
                SsChange::of(SsChangeType::Dividend, '配当', '-10000000.0000'),
                SsChange::of(SsChangeType::NetIncome, '当期純利益', '25000000.0000', SsChange::SOURCE_JOURNAL_AUTO),
            ],
        );
        self::assertSame('115000000.0000', $section->endingBalance);
        self::assertSame('15000000.0000', $section->totalChange());
    }

    public function testNegativeTotalChangeRendersWithLeadingMinus(): void
    {
        $section = SsSection::fromChanges(
            SsSectionCode::TreasuryStock,
            '0.0000',
            [SsChange::of(SsChangeType::TreasuryPurchase, '自己株式取得', '-5000000.0000')],
        );
        self::assertSame('-5000000.0000', $section->totalChange());
        self::assertSame('-5000000.0000', $section->endingBalance);
    }

    public function testEmptyChangesPreservesOpeningAsEnding(): void
    {
        $section = SsSection::fromChanges(
            SsSectionCode::CapitalStock,
            '50000000.0000',
            [],
        );
        self::assertSame('50000000.0000', $section->endingBalance);
        self::assertSame('0.0000', $section->totalChange());
    }

    public function testLabelFallsBackToEnumDefaultWhenUnspecified(): void
    {
        $section = SsSection::fromChanges(SsSectionCode::CapitalStock, '0.0000', []);
        self::assertSame('資本金', $section->label);
    }
}
