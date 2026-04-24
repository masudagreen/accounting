<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\PostJournalUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;

#[CoversClass(PostJournalUseCase::class)]
final class PostJournalUseCaseTest extends TestCase
{
    public function testApprovedIsPosted(): void
    {
        $repo = new InMemoryJournalRepository();
        $approved = $this->draft()->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1');
        $repo->save($approved);

        $useCase = new PostJournalUseCase($repo, new FrozenClock());
        $posted = $useCase->execute($approved->id, 'U1');
        self::assertSame(JournalStatus::Posted, $posted->statusEnum());
    }

    public function testMissingJournalRaises(): void
    {
        $useCase = new PostJournalUseCase(new InMemoryJournalRepository(), new FrozenClock());
        $this->expectException(EntityNotFoundException::class);
        $useCase->execute('01HW7K9B2QV7C8Y4ZJRNLMISSING', 'U1');
    }

    public function testDraftCannotBePostedDirectly(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft());
        $useCase = new PostJournalUseCase($repo, new FrozenClock());
        $this->expectException(InvariantViolationException::class);
        $useCase->execute('01HW7K9B2QV7C8Y4ZJRNL000001', 'U1');
    }

    private function draft(): Journal
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
            id: '01HW7K9B2QV7C8Y4ZJRNL000001',
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
