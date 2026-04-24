<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Domain\Journal\ValueObject\FiscalPeriod;
use Rucaro\Domain\Journal\ValueObject\JournalDate;

#[CoversClass(Journal::class)]
final class JournalLifecycleTest extends TestCase
{
    public function testApproveFromDraft(): void
    {
        $j = $this->draft();
        $at = new DateTimeImmutable('2026-04-21T13:00:00Z');
        $approved = $j->approve($at, '01HW7K9B2QV7C8Y4ZUSER000002');

        self::assertSame(JournalStatus::Approved, $approved->statusEnum());
        self::assertSame('01HW7K9B2QV7C8Y4ZUSER000002', $approved->approvedBy);
        self::assertEquals($at, $approved->approvedAt);
    }

    public function testApproveFromPostedRaises(): void
    {
        $posted = $this->draft()
            ->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1')
            ->post(new DateTimeImmutable('2026-04-21T12:20:00Z'), 'U1');

        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/cannot_approve_from_status/');
        $posted->approve(new DateTimeImmutable('2026-04-21T13:00:00Z'), 'U2');
    }

    public function testPostFromApproved(): void
    {
        $approved = $this->draft()->approve(
            new DateTimeImmutable('2026-04-21T13:00:00Z'),
            '01HW7K9B2QV7C8Y4ZUSER000002',
        );
        $posted = $approved->post(
            new DateTimeImmutable('2026-04-21T13:30:00Z'),
            '01HW7K9B2QV7C8Y4ZUSER000002',
        );
        self::assertSame(JournalStatus::Posted, $posted->statusEnum());
    }

    public function testPostFromDraftRaises(): void
    {
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/cannot_post_from_status/');
        $this->draft()->post(new DateTimeImmutable('2026-04-21T13:00:00Z'), 'U1');
    }

    public function testReverseRequiresPosted(): void
    {
        $draft = $this->draft();
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/cannot_reverse_from_status/');
        $draft->reverse(new DateTimeImmutable('2026-04-22T09:00:00Z'), 'U1', 'bad');
    }

    public function testReverseRequiresReason(): void
    {
        $posted = $this->draft()
            ->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1')
            ->post(new DateTimeImmutable('2026-04-21T12:20:00Z'), 'U1');

        $this->expectException(ValidationException::class);
        $posted->reverse(new DateTimeImmutable('2026-04-22T09:00:00Z'), 'U1', '   ');
    }

    public function testReverseFlipsStatus(): void
    {
        $posted = $this->draft()
            ->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1')
            ->post(new DateTimeImmutable('2026-04-21T12:20:00Z'), 'U1');

        $reversed = $posted->reverse(new DateTimeImmutable('2026-04-22T09:00:00Z'), 'U1', 'typo');
        self::assertSame(JournalStatus::Reversed, $reversed->statusEnum());
        self::assertStringContainsString('[REVERSED:typo]', $reversed->summary);
    }

    public function testVoidOnlyFromDraft(): void
    {
        $voided = $this->draft()->void(new DateTimeImmutable('2026-04-21T12:30:00Z'), 'U1', 'mistaken');
        self::assertSame(JournalStatus::Voided, $voided->statusEnum());
        self::assertNotNull($voided->deletedAt);
        self::assertStringContainsString('[VOIDED:mistaken]', $voided->summary);
    }

    public function testVoidFromApprovedRaises(): void
    {
        $approved = $this->draft()->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1');
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/cannot_void_from_status/');
        $approved->void(new DateTimeImmutable('2026-04-21T12:30:00Z'), 'U1', 'oops');
    }

    public function testWithLinesRecomputesTotal(): void
    {
        $draft = $this->draft();
        $newLines = [
            $this->line(1, 'debit', '2000.0000'),
            $this->line(2, 'credit', '2000.0000'),
        ];
        $updated = $draft->withLines($newLines);
        self::assertSame('2000.0000', $updated->totalAmount);
    }

    public function testWithLinesRejectedAfterApproval(): void
    {
        $approved = $this->draft()->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1');
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/immutable_after_draft/');
        $approved->withLines([
            $this->line(1, 'debit', '2000.0000'),
            $this->line(2, 'credit', '2000.0000'),
        ]);
    }

    public function testAssertWithinFiscalPeriodSuccess(): void
    {
        $draft = $this->draft();
        $period = new FiscalPeriod(
            fiscalTermId: $draft->fiscalTermId,
            startDate: JournalDate::fromString('2026-04-01'),
            endDate: JournalDate::fromString('2027-03-31'),
        );
        $draft->assertWithinFiscalPeriod($period);
        self::assertTrue(true);
    }

    public function testAssertWithinFiscalPeriodOutOfRangeRaises(): void
    {
        $draft = $this->draft();
        $period = new FiscalPeriod(
            fiscalTermId: $draft->fiscalTermId,
            startDate: JournalDate::fromString('2027-04-01'),
            endDate: JournalDate::fromString('2028-03-31'),
        );
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/entry_date_out_of_fiscal_period/');
        $draft->assertWithinFiscalPeriod($period);
    }

    public function testAssertWithinFiscalPeriodMismatchedTermIdRaises(): void
    {
        $draft = $this->draft();
        $period = new FiscalPeriod(
            fiscalTermId: 'different_term',
            startDate: JournalDate::fromString('2026-04-01'),
            endDate: JournalDate::fromString('2027-03-31'),
        );
        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/fiscal_term_mismatch/');
        $draft->assertWithinFiscalPeriod($period);
    }

    public function testSoftDeleteMarksDeletedAt(): void
    {
        $draft = $this->draft();
        $at = new DateTimeImmutable('2026-04-21T15:00:00Z');
        $deleted = $draft->softDelete($at);
        self::assertEquals($at, $deleted->deletedAt);
    }

    public function testSoftDeleteAfterApprovalRaises(): void
    {
        $approved = $this->draft()->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1');
        $this->expectException(InvariantViolationException::class);
        $approved->softDelete(new DateTimeImmutable('2026-04-21T15:00:00Z'));
    }

    private function draft(): Journal
    {
        $lines = [
            $this->line(1, 'debit', '1000.0000'),
            $this->line(2, 'credit', '1000.0000'),
        ];
        return new Journal(
            id: '01HW7K9B2QV7C8Y4ZJRNL000001',
            entityId: '01HW7K9B2QV7C8Y4ZENTITY0001',
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21'),
            bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            summary: 'Test',
            totalAmount: '1000.0000',
            currencyCode: 'JPY',
            status: 'draft',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedBy: null,
            approvedAt: null,
            createdAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            updatedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            deletedAt: null,
            lines: $lines,
        );
    }

    private function line(int $no, string $side, string $amount): JournalLine
    {
        return new JournalLine(
            id: sprintf('01HW7K9B2QV7C8Y4ZLINE%05d', $no),
            lineNo: $no,
            side: $side,
            accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
            subAccountTitleId: null,
            amount: $amount,
            taxRatePercent: '0.00',
            taxAmount: '0.0000',
            isTaxReduced: false,
            memo: '',
            bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
        );
    }
}
