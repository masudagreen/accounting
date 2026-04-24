<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\FinancialStatement;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Section;

#[CoversClass(FinancialStatement::class)]
#[CoversClass(FinancialStatementKind::class)]
final class FinancialStatementTest extends TestCase
{
    public function testExposesFieldsAndKindHelpers(): void
    {
        $section = Section::fromLines('assets', '資産の部', [
            FinancialStatementLine::ofAccount('acc1', '101', '現金', '1000'),
        ]);
        $fs = new FinancialStatement(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::BalanceSheet,
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2026-04-30', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            bs: ['assets' => $section],
            pl: [],
            cs: [],
            totals: ['total_assets' => '1000.0000'],
            generatedAt: new DateTimeImmutable('2026-04-30T00:00:00Z', new DateTimeZone('UTC')),
        );

        self::assertTrue($fs->hasBalanceSheet());
        self::assertFalse($fs->hasProfitAndLoss());
        self::assertFalse($fs->hasCashFlow());
        self::assertSame('1000.0000', $fs->totals['total_assets']);
        self::assertSame(FinancialStatementKind::BalanceSheet, $fs->kind);
    }

    public function testKindFromQueryStringDefaultsToAll(): void
    {
        self::assertSame(FinancialStatementKind::All, FinancialStatementKind::fromQueryString(null));
        self::assertSame(FinancialStatementKind::All, FinancialStatementKind::fromQueryString(''));
        self::assertSame(FinancialStatementKind::BalanceSheet, FinancialStatementKind::fromQueryString('bs'));
        self::assertSame(FinancialStatementKind::BalanceSheet, FinancialStatementKind::fromQueryString('BS'));
        self::assertSame(FinancialStatementKind::ProfitAndLoss, FinancialStatementKind::fromQueryString('pl'));
        self::assertSame(FinancialStatementKind::CashFlow, FinancialStatementKind::fromQueryString('cs'));
        self::assertSame(FinancialStatementKind::All, FinancialStatementKind::fromQueryString('ALL'));
        self::assertSame(FinancialStatementKind::All, FinancialStatementKind::fromQueryString('unknown'));
    }

    public function testKindInclusionFlags(): void
    {
        self::assertTrue(FinancialStatementKind::All->includesBalanceSheet());
        self::assertTrue(FinancialStatementKind::All->includesProfitAndLoss());
        self::assertTrue(FinancialStatementKind::All->includesCashFlow());

        self::assertTrue(FinancialStatementKind::BalanceSheet->includesBalanceSheet());
        self::assertFalse(FinancialStatementKind::BalanceSheet->includesProfitAndLoss());
        self::assertFalse(FinancialStatementKind::BalanceSheet->includesCashFlow());

        self::assertFalse(FinancialStatementKind::ProfitAndLoss->includesBalanceSheet());
        self::assertTrue(FinancialStatementKind::ProfitAndLoss->includesProfitAndLoss());
        self::assertFalse(FinancialStatementKind::ProfitAndLoss->includesCashFlow());

        self::assertFalse(FinancialStatementKind::CashFlow->includesBalanceSheet());
        self::assertFalse(FinancialStatementKind::CashFlow->includesProfitAndLoss());
        self::assertTrue(FinancialStatementKind::CashFlow->includesCashFlow());
    }
}
