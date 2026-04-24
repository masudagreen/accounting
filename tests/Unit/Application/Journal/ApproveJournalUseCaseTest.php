<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\ApproveJournalUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;

#[CoversClass(ApproveJournalUseCase::class)]
final class ApproveJournalUseCaseTest extends TestCase
{
    public function testDraftIsApproved(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft('01HW7K9B2QV7C8Y4ZJRNL000001'));

        $useCase = new ApproveJournalUseCase($repo, new FrozenClock());
        $approved = $useCase->execute('01HW7K9B2QV7C8Y4ZJRNL000001', '01HW7K9B2QV7C8Y4ZUSER000002');

        self::assertSame(JournalStatus::Approved, $approved->statusEnum());
    }

    public function testMissingJournalRaises(): void
    {
        $useCase = new ApproveJournalUseCase(new InMemoryJournalRepository(), new FrozenClock());
        $this->expectException(EntityNotFoundException::class);
        $useCase->execute('01HW7K9B2QV7C8Y4ZJRNLMISSING', 'U1');
    }

    public function testAlreadyPostedCannotBeApproved(): void
    {
        $repo = new InMemoryJournalRepository();
        $posted = $this->draft('01HW7K9B2QV7C8Y4ZJRNL000001')
            ->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1')
            ->post(new DateTimeImmutable('2026-04-21T12:20:00Z'), 'U1');
        $repo->save($posted);

        $useCase = new ApproveJournalUseCase($repo, new FrozenClock());
        $this->expectException(InvariantViolationException::class);
        $useCase->execute($posted->id, 'U1');
    }

    private function draft(string $id): Journal
    {
        $lines = [
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE00001',
                lineNo: 1,
                side: 'debit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
                subAccountTitleId: null,
                amount: '500.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            ),
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZLINE00002',
                lineNo: 2,
                side: 'credit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL002',
                subAccountTitleId: null,
                amount: '500.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            ),
        ];
        return new Journal(
            id: $id,
            entityId: '01HW7K9B2QV7C8Y4ZENTITY0001',
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21'),
            bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            summary: 'Draft',
            totalAmount: '500.0000',
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
}
