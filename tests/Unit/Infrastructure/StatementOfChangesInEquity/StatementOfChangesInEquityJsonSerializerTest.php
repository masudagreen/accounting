<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\StatementOfChangesInEquity;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\StatementOfChangesInEquity\Service\StatementOfChangesInEquityBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Infrastructure\StatementOfChangesInEquity\StatementOfChangesInEquityJsonSerializer;

#[CoversClass(StatementOfChangesInEquityJsonSerializer::class)]
final class StatementOfChangesInEquityJsonSerializerTest extends TestCase
{
    public function testSerializesStatementShape(): void
    {
        $builder = new StatementOfChangesInEquityBuilder();
        $ss = $builder->build(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            openingBalances: [SsSectionCode::CapitalStock->value => '10000000.0000'],
            adjustments: [new SsManualAdjustment(
                id: '01HAAAAAAAAAAAAAAAAAAAAA01',
                entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
                fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
                sectionCode: SsSectionCode::CapitalStock,
                changeType: SsChangeType::NewIssue,
                amount: '2000000.0000',
                label: 'Issuance',
                sortOrder: 0,
                notes: null,
            )],
            netIncome: '3000000.0000',
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z'),
        );

        $payload = StatementOfChangesInEquityJsonSerializer::statementToArray($ss);
        self::assertSame('01HAAAAAAAAAAAAAAAAAAAAAA1', $payload['entityId']);
        self::assertSame('2026-04-01', $payload['fromDate']);
        self::assertArrayHasKey('sections', $payload);
        self::assertArrayHasKey('totals', $payload);
        self::assertIsArray($payload['totals']);
        self::assertArrayHasKey('opening', $payload['totals']);
    }

    public function testAdjustmentSerializationRoundTripsKeys(): void
    {
        $adj = new SsManualAdjustment(
            id: '01HAAAAAAAAAAAAAAAAAAAAA01',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: SsSectionCode::RetainedEarnings,
            changeType: SsChangeType::Dividend,
            amount: '-5000000.0000',
            label: 'Interim dividend',
            sortOrder: 2,
            notes: 'Board approval 2026-09-15',
        );
        $arr = StatementOfChangesInEquityJsonSerializer::adjustmentToArray($adj);
        self::assertSame('retained_earnings', $arr['sectionCode']);
        self::assertSame('dividend', $arr['changeTypeCode']);
        self::assertSame('Interim dividend', $arr['label']);
        self::assertSame(2, $arr['sortOrder']);
        self::assertSame('Board approval 2026-09-15', $arr['notes']);
    }
}
