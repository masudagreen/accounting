<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\FinancialStatement;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Port\FsSectionCode;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Infrastructure\FinancialStatement\JsonFinancialStatementSerializer;

#[CoversClass(JsonFinancialStatementSerializer::class)]
final class JsonFinancialStatementSerializerTest extends TestCase
{
    public function testEmptyStatementRendersNullSections(): void
    {
        $fs = new FinancialStatement(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::BalanceSheet,
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2026-04-30', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            bs: [],
            pl: [],
            cs: [],
            totals: [],
            generatedAt: new DateTimeImmutable('2026-04-30T01:02:03.000000Z', new DateTimeZone('UTC')),
        );
        $arr = JsonFinancialStatementSerializer::toArray($fs);
        self::assertNull($arr['bs']);
        self::assertNull($arr['pl']);
        self::assertNull($arr['cs']);
        self::assertSame('BS', $arr['kind']);
        self::assertSame('2026-04-01', $arr['fromDate']);
        self::assertSame('2026-04-30', $arr['asOf']);
    }

    public function testBsAndPlSerializeToOpenApiShape(): void
    {
        $assets = Section::fromLines(Section::CODE_ASSETS, '資産の部', [
            FinancialStatementLine::ofAccount('a1', '101', '現金', '1000'),
        ]);
        $revenue = Section::fromLines(Section::CODE_REVENUE, '収益', [
            FinancialStatementLine::ofAccount('r1', '401', '売上', '2000'),
        ]);
        $fs = new FinancialStatement(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::All,
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2026-04-30', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            bs: [Section::CODE_ASSETS => $assets],
            pl: [Section::CODE_REVENUE => $revenue],
            cs: [],
            totals: ['net_income' => '2000.0000', 'total_assets' => '1000.0000'],
            generatedAt: new DateTimeImmutable('2026-04-30T00:00:00.000000Z', new DateTimeZone('UTC')),
        );
        $arr = JsonFinancialStatementSerializer::toArray($fs);

        self::assertIsArray($arr['bs']);
        self::assertIsArray($arr['pl']);
        self::assertSame('資産の部', $arr['bs']['assets']['title']);
        self::assertSame('1000.0000', $arr['bs']['assets']['subtotal']);
        self::assertSame('2000.0000', $arr['pl']['netIncome']);

        $firstAssetLine = $arr['bs']['assets']['lines'][0];
        self::assertSame('a1', $firstAssetLine['accountTitleId']);
        self::assertSame('101', $firstAssetLine['accountCode']);
        self::assertSame('現金', $firstAssetLine['label']);
        self::assertSame('1000.0000', $firstAssetLine['amount']);
        self::assertFalse($firstAssetLine['isSubtotal']);
    }

    public function testBsSectionsArrayExposesJgaapHierarchyWithBackCompatKeys(): void
    {
        $asset = new Section(
            code: FsSectionCode::BS_ASSET,
            label: '資産の部',
            lines: [],
            subtotal: '2583000.0000',
            parentCode: null,
            sortOrder: 1,
            isSubtotal: false,
            isTotal: false,
        );
        $currentAsset = new Section(
            code: FsSectionCode::BS_CURRENT_ASSET,
            label: '流動資産',
            lines: [
                FinancialStatementLine::ofAccount('c1', '101', '現金', '495000'),
                FinancialStatementLine::ofAccount('c2', '102', '普通預金', '2088000'),
            ],
            subtotal: '2583000.0000',
            parentCode: FsSectionCode::BS_ASSET,
            sortOrder: 10,
            isSubtotal: false,
            isTotal: false,
        );
        $assetTotal = new Section(
            code: FsSectionCode::BS_ASSET_TOTAL,
            label: '資産合計',
            lines: [],
            subtotal: '2583000.0000',
            parentCode: null,
            sortOrder: 99,
            isSubtotal: false,
            isTotal: true,
        );
        $liability = new Section(
            code: FsSectionCode::BS_LIABILITY,
            label: '負債の部',
            lines: [],
            subtotal: '200000.0000',
            parentCode: null,
            sortOrder: 100,
        );
        $liabilityTotal = new Section(
            code: FsSectionCode::BS_LIABILITY_TOTAL,
            label: '負債合計',
            lines: [],
            subtotal: '200000.0000',
            parentCode: null,
            sortOrder: 199,
            isTotal: true,
        );
        $equity = new Section(
            code: FsSectionCode::BS_EQUITY,
            label: '純資産の部',
            lines: [],
            subtotal: '2383000.0000',
            parentCode: null,
            sortOrder: 200,
        );
        $equityTotal = new Section(
            code: FsSectionCode::BS_EQUITY_TOTAL,
            label: '純資産合計',
            lines: [],
            subtotal: '2383000.0000',
            parentCode: null,
            sortOrder: 299,
            isTotal: true,
        );
        $fs = new FinancialStatement(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::BalanceSheet,
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2026-04-30', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            bs: [
                FsSectionCode::BS_ASSET          => $asset,
                FsSectionCode::BS_CURRENT_ASSET  => $currentAsset,
                FsSectionCode::BS_ASSET_TOTAL    => $assetTotal,
                FsSectionCode::BS_LIABILITY      => $liability,
                FsSectionCode::BS_LIABILITY_TOTAL => $liabilityTotal,
                FsSectionCode::BS_EQUITY         => $equity,
                FsSectionCode::BS_EQUITY_TOTAL   => $equityTotal,
            ],
            pl: [],
            cs: [],
            totals: ['total_assets' => '2583000.0000'],
            generatedAt: new DateTimeImmutable('2026-04-30T00:00:00.000000Z', new DateTimeZone('UTC')),
        );
        $arr = JsonFinancialStatementSerializer::toArray($fs);

        self::assertIsArray($arr['bs']);
        self::assertArrayHasKey('sections', $arr['bs']);
        self::assertArrayHasKey('assets', $arr['bs']);
        self::assertArrayHasKey('liabilities', $arr['bs']);
        self::assertArrayHasKey('equity', $arr['bs']);
        self::assertArrayHasKey('totals', $arr['bs']);

        $sections = $arr['bs']['sections'];
        self::assertCount(7, $sections);
        // Order by sortOrder ascending.
        self::assertSame('asset', $sections[0]['code']);
        self::assertSame('current_asset', $sections[1]['code']);
        self::assertSame('asset', $sections[1]['parentCode']);
        self::assertSame(10, $sections[1]['sortOrder']);
        self::assertFalse($sections[1]['isSubtotal']);
        self::assertFalse($sections[1]['isTotal']);
        self::assertSame('2583000.0000', $sections[1]['subtotal']);
        self::assertCount(2, $sections[1]['lines']);
        self::assertSame('現金', $sections[1]['lines'][0]['label']);

        self::assertSame('asset_total', $sections[2]['code']);
        self::assertTrue($sections[2]['isTotal']);
        self::assertNull($sections[2]['parentCode']);

        // Top-level totals shortcut.
        self::assertSame('2583000.0000', $arr['bs']['totals']['assets']);
        self::assertSame('200000.0000',  $arr['bs']['totals']['liabilities']);
        self::assertSame('2383000.0000', $arr['bs']['totals']['equity']);

        // Back-compat flat keys still present and populated from the J-GAAP root.
        self::assertSame('資産の部', $arr['bs']['assets']['title']);
        self::assertSame('2583000.0000', $arr['bs']['assets']['subtotal']);
    }

    public function testPlSerializerEmitsSectionsArrayWithStagedSubtotals(): void
    {
        $revenue = new Section(
            code: 'operating_revenue',
            label: '売上高',
            lines: [FinancialStatementLine::ofAccount('s1', '401', '売上', '100000')],
            subtotal: '100000.0000',
            parentCode: null,
            sortOrder: 10,
        );
        $netIncome = new Section(
            code: 'net_income',
            label: '当期純利益',
            lines: [],
            subtotal: '10000.0000',
            parentCode: null,
            sortOrder: 130,
            isSubtotal: true,
            isTotal: true,
        );
        $fs = new FinancialStatement(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::ProfitAndLoss,
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2026-04-30', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            bs: [],
            pl: [
                'net_income'         => $netIncome,
                'operating_revenue'  => $revenue,
            ],
            cs: [],
            totals: ['net_income' => '10000.0000'],
            generatedAt: new DateTimeImmutable('2026-04-30T00:00:00.000000Z', new DateTimeZone('UTC')),
        );
        $arr = JsonFinancialStatementSerializer::toArray($fs);

        self::assertIsArray($arr['pl']);
        self::assertArrayHasKey('sections', $arr['pl']);
        // Ordered by sortOrder ascending, even though we inserted net_income first.
        self::assertSame('operating_revenue', $arr['pl']['sections'][0]['code']);
        self::assertSame('net_income', $arr['pl']['sections'][1]['code']);
        self::assertTrue($arr['pl']['sections'][1]['isTotal']);
        self::assertSame('10000.0000', $arr['pl']['netIncome']);
    }
}
