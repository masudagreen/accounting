<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\StatementOfChangesInEquity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\StatementOfChangesInEquity\SsChange;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;

#[CoversClass(SsManualAdjustment::class)]
final class SsManualAdjustmentTest extends TestCase
{
    public function testRejectsEmptyLabel(): void
    {
        $this->expectException(ValidationException::class);
        new SsManualAdjustment(
            id: '01HAAAAAAAAAAAAAAAAAAAAA01',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: SsSectionCode::CapitalStock,
            changeType: SsChangeType::NewIssue,
            amount: '1000.0000',
            label: '',
            sortOrder: 0,
            notes: null,
        );
    }

    public function testRejectsNegativeSortOrder(): void
    {
        $this->expectException(ValidationException::class);
        new SsManualAdjustment(
            id: '01HAAAAAAAAAAAAAAAAAAAAA01',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: SsSectionCode::CapitalStock,
            changeType: SsChangeType::NewIssue,
            amount: '1000.0000',
            label: 'x',
            sortOrder: -1,
            notes: null,
        );
    }

    public function testWithReturnsFreshInstanceWithUpdatedFields(): void
    {
        $original = new SsManualAdjustment(
            id: '01HAAAAAAAAAAAAAAAAAAAAA01',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: SsSectionCode::CapitalStock,
            changeType: SsChangeType::NewIssue,
            amount: '1000000.0000',
            label: 'First',
            sortOrder: 0,
            notes: null,
        );
        $updated = $original->with(amount: '2000000.0000', label: 'Second');
        self::assertSame('2000000.0000', $updated->amount);
        self::assertSame('Second', $updated->label);
        // Immutable fields preserved.
        self::assertSame($original->id, $updated->id);
        self::assertSame($original->entityId, $updated->entityId);
    }

    public function testToSsChangeMapsSourceToManual(): void
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
            notes: null,
        );
        $change = $adj->toSsChange();
        self::assertSame(SsChangeType::Dividend, $change->changeType);
        self::assertSame(SsChange::SOURCE_MANUAL, $change->source);
        self::assertSame('-5000000.0000', $change->amount);
    }
}
