<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\StatementOfChangesInEquity;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\StatementOfChangesInEquity\Service\StatementOfChangesInEquityBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\SsChange;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;

#[CoversClass(StatementOfChangesInEquityBuilder::class)]
final class StatementOfChangesInEquityBuilderTest extends TestCase
{
    public function testEndingEqualsOpeningPlusChangesPerSection(): void
    {
        $builder = new StatementOfChangesInEquityBuilder();
        $adjustments = [
            $this->makeAdj(SsSectionCode::CapitalStock, SsChangeType::NewIssue, '5000000.0000', 0, 'New Issue'),
            $this->makeAdj(SsSectionCode::RetainedEarnings, SsChangeType::Dividend, '-12000000.0000', 1, 'Dividend'),
            $this->makeAdj(SsSectionCode::TreasuryStock, SsChangeType::TreasuryPurchase, '-3000000.0000', 2, 'Treasury buyback'),
        ];

        $ss = $builder->build(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            openingBalances: [
                SsSectionCode::CapitalStock->value        => '50000000.0000',
                SsSectionCode::RetainedEarnings->value    => '180000000.0000',
                SsSectionCode::TreasuryStock->value       => '-2000000.0000',
            ],
            adjustments: $adjustments,
            netIncome: '45000000.0000',
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z'),
        );

        $capitalStock = $ss->sectionByCode(SsSectionCode::CapitalStock);
        self::assertNotNull($capitalStock);
        self::assertSame('55000000.0000', $capitalStock->endingBalance);

        $retained = $ss->sectionByCode(SsSectionCode::RetainedEarnings);
        self::assertNotNull($retained);
        // 180,000,000 - 12,000,000 + 45,000,000 = 213,000,000
        self::assertSame('213000000.0000', $retained->endingBalance);
        self::assertCount(2, $retained->changes);

        $treasury = $ss->sectionByCode(SsSectionCode::TreasuryStock);
        self::assertNotNull($treasury);
        self::assertSame('-5000000.0000', $treasury->endingBalance);
    }

    public function testNetIncomeFoldsIntoRetainedEarningsAsJournalAuto(): void
    {
        $builder = new StatementOfChangesInEquityBuilder();
        $ss = $builder->build(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            openingBalances: [SsSectionCode::RetainedEarnings->value => '0.0000'],
            adjustments: [],
            netIncome: '10000000.0000',
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z'),
        );

        $retained = $ss->sectionByCode(SsSectionCode::RetainedEarnings);
        self::assertNotNull($retained);
        self::assertCount(1, $retained->changes);
        self::assertSame(SsChangeType::NetIncome, $retained->changes[0]->changeType);
        self::assertSame(SsChange::SOURCE_JOURNAL_AUTO, $retained->changes[0]->source);
        self::assertSame('10000000.0000', $retained->endingBalance);
    }

    public function testNullNetIncomeSkipsAutoRow(): void
    {
        $builder = new StatementOfChangesInEquityBuilder();
        $ss = $builder->build(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            openingBalances: [SsSectionCode::RetainedEarnings->value => '100000000.0000'],
            adjustments: [],
            netIncome: null,
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z'),
        );

        $retained = $ss->sectionByCode(SsSectionCode::RetainedEarnings);
        self::assertNotNull($retained);
        self::assertSame([], $retained->changes);
        self::assertSame('100000000.0000', $retained->endingBalance);
    }

    public function testAdjustmentsAreSortedByOrderThenId(): void
    {
        $builder = new StatementOfChangesInEquityBuilder();
        $adjustments = [
            $this->makeAdj(SsSectionCode::CapitalStock, SsChangeType::NewIssue, '3000000.0000', 10, 'Later'),
            $this->makeAdj(SsSectionCode::CapitalStock, SsChangeType::NewIssue, '2000000.0000', 1, 'Earlier'),
        ];
        $ss = $builder->build(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            openingBalances: [],
            adjustments: $adjustments,
            netIncome: null,
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z'),
        );
        $capitalStock = $ss->sectionByCode(SsSectionCode::CapitalStock);
        self::assertNotNull($capitalStock);
        self::assertCount(2, $capitalStock->changes);
        self::assertSame('Earlier', $capitalStock->changes[0]->label);
        self::assertSame('Later', $capitalStock->changes[1]->label);
    }

    public function testTotalsAggregateAcrossAllColumns(): void
    {
        $builder = new StatementOfChangesInEquityBuilder();
        $ss = $builder->build(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            openingBalances: [
                SsSectionCode::CapitalStock->value     => '50000000.0000',
                SsSectionCode::RetainedEarnings->value => '100000000.0000',
            ],
            adjustments: [
                $this->makeAdj(SsSectionCode::CapitalStock, SsChangeType::NewIssue, '5000000.0000', 0, 'New'),
                $this->makeAdj(SsSectionCode::RetainedEarnings, SsChangeType::Dividend, '-3000000.0000', 1, 'Divvy'),
            ],
            netIncome: '10000000.0000',
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z'),
        );
        $totals = $ss->totals();
        // opening = 50M + 100M = 150M
        self::assertSame('150000000.0000', $totals['opening']);
        // ending = 55M + 107M = 162M
        self::assertSame('162000000.0000', $totals['ending']);
        // total change = 12M
        self::assertSame('12000000.0000', $totals['totalChange']);
    }

    private function makeAdj(
        SsSectionCode $section,
        SsChangeType $type,
        string $amount,
        int $sortOrder,
        string $label,
    ): SsManualAdjustment {
        static $seq = 0;
        $seq++;
        $id = sprintf('01HAAAAAAAAAAAAAAAAAAAAA%02d', $seq);
        return new SsManualAdjustment(
            id: $id,
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: $section,
            changeType: $type,
            amount: $amount,
            label: $label,
            sortOrder: $sortOrder,
            notes: null,
        );
    }
}
