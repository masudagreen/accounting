<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Approval\Service;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\Service\JournalApprovalTarget;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;

#[CoversClass(JournalApprovalTarget::class)]
#[CoversClass(Journal::class)]
final class JournalApprovalTargetTest extends TestCase
{
    public function testKindAndIdReturnJournalUlid(): void
    {
        $target = new JournalApprovalTarget($this->journal(), new InMemoryJournalRepository());
        self::assertSame(ApprovalTargetKind::Journal, $target->kind());
        self::assertSame('01HW7K9B2QV7C8Y4ZJRNLMAIN00', $target->id());
    }

    public function testSummaryReturnsJournalSummary(): void
    {
        $target = new JournalApprovalTarget($this->journal('Office supplies'), new InMemoryJournalRepository());
        self::assertSame('Office supplies', $target->summary());
    }

    public function testSummaryFallsBackWhenBlank(): void
    {
        $target = new JournalApprovalTarget($this->journal(''), new InMemoryJournalRepository());
        self::assertStringContainsString('01HW7K9B2QV7C8Y4ZJRNLMAIN00', $target->summary());
    }

    public function testDetailsIncludeCoreJournalFields(): void
    {
        $target = new JournalApprovalTarget($this->journal('Lunch'), new InMemoryJournalRepository());
        $details = $target->details();

        self::assertSame('01HW7K9B2QV7C8Y4ZJRNLMAIN00', $details['journal_id']);
        self::assertSame('2026-04-21', $details['journal_date']);
        self::assertSame('JPY', $details['currency_code']);
        self::assertIsArray($details['lines']);
        self::assertCount(2, $details['lines']);
    }

    public function testApplyApprovalTransitionsStatusAndPersists(): void
    {
        $repo = new InMemoryJournalRepository();
        $journal = $this->journal('review me');
        $repo->save($journal);
        $target = new JournalApprovalTarget($journal, $repo);

        $at = new DateTimeImmutable('2026-04-22T00:00:00Z', new DateTimeZone('UTC'));
        $target->applyApproval('01HW7K9B2QV7C8Y4ZUSER000099', $at);

        $persisted = $repo->findById($journal->id);
        self::assertNotNull($persisted);
        self::assertSame('approved', $persisted->status);
        self::assertSame('01HW7K9B2QV7C8Y4ZUSER000099', $persisted->approvedBy);
        self::assertSame($at->format(DATE_ATOM), $persisted->approvedAt?->format(DATE_ATOM));
    }

    public function testApplyRejectionTransitionsStatusAndPersists(): void
    {
        $repo = new InMemoryJournalRepository();
        $journal = $this->journal('tentative');
        $repo->save($journal);
        $target = new JournalApprovalTarget($journal, $repo);

        $at = new DateTimeImmutable('2026-04-22T00:30:00Z', new DateTimeZone('UTC'));
        $target->applyRejection('01HW7K9B2QV7C8Y4ZUSER000099', $at, 'missing receipt');

        $persisted = $repo->findById($journal->id);
        self::assertNotNull($persisted);
        self::assertSame('rejected', $persisted->status);
        self::assertStringContainsString('[REJECTED:missing receipt]', $persisted->summary);
    }

    private function journal(string $summary = 'Test journal'): Journal
    {
        $tz = new DateTimeZone('UTC');
        $ts = new DateTimeImmutable('2026-04-21T12:00:00Z', $tz);
        $lines = [
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE0001AA',
                lineNo: 1,
                side: 'debit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: $ts,
            ),
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE0002BB',
                lineNo: 2,
                side: 'credit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL002',
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: $ts,
            ),
        ];

        return new Journal(
            id: '01HW7K9B2QV7C8Y4ZJRNLMAIN00',
            entityId: '01HW7K9B2QV7C8Y4ZENTITY0001',
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21', $tz),
            bookedAt: $ts,
            summary: $summary,
            totalAmount: '100.0000',
            currencyCode: 'JPY',
            status: 'draft',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedBy: null,
            approvedAt: null,
            createdAt: $ts,
            updatedAt: $ts,
            deletedAt: null,
            lines: $lines,
        );
    }
}
