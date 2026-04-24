<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Application\Journal\UpdateJournalUseCase;
use Rucaro\Application\Journal\UpdateJournalUseCaseInput;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;

#[CoversClass(UpdateJournalUseCase::class)]
final class UpdateJournalUseCaseTest extends TestCase
{
    public function testUpdatesDraftLines(): void
    {
        $repo = new InMemoryJournalRepository();
        $existing = $this->draft();
        $repo->save($existing);

        $clock = new FrozenClock();
        $useCase = new UpdateJournalUseCase($repo, new UlidGenerator($clock), $clock);

        $updated = $useCase->execute(new UpdateJournalUseCaseInput(
            journalId: $existing->id,
            updatedBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            lines: [
                new JournalLineInput('debit', '01HW7K9B2QV7C8Y4ZACCTTL001', null, '2000.0000', '10.00', '0.0000', false, 'rev'),
                new JournalLineInput('credit', '01HW7K9B2QV7C8Y4ZACCTTL002', null, '2000.0000', '0.00', '0.0000', false, 'cash'),
            ],
            summary: 'Updated summary',
        ));

        self::assertSame('2000.0000', $updated->totalAmount);
        self::assertSame('Updated summary', $updated->summary);
    }

    public function testMissingJournalRaisesNotFound(): void
    {
        $clock = new FrozenClock();
        $useCase = new UpdateJournalUseCase(
            new InMemoryJournalRepository(),
            new UlidGenerator($clock),
            $clock,
        );

        $this->expectException(EntityNotFoundException::class);
        $useCase->execute(new UpdateJournalUseCaseInput(
            journalId: '01HW7K9B2QV7C8Y4ZJRNLMISSING',
            updatedBy: 'U1',
            lines: [
                new JournalLineInput('debit', 'A1', null, '100.0000', '0.00', '0.0000', false, ''),
                new JournalLineInput('credit', 'A2', null, '100.0000', '0.00', '0.0000', false, ''),
            ],
        ));
    }

    public function testApprovedJournalCannotBeUpdated(): void
    {
        $repo = new InMemoryJournalRepository();
        $existing = $this->draft()->approve(new DateTimeImmutable('2026-04-21T13:00:00Z'), 'U1');
        $repo->save($existing);

        $clock = new FrozenClock();
        $useCase = new UpdateJournalUseCase($repo, new UlidGenerator($clock), $clock);

        $this->expectException(InvariantViolationException::class);
        $useCase->execute(new UpdateJournalUseCaseInput(
            journalId: $existing->id,
            updatedBy: 'U1',
            lines: [
                new JournalLineInput('debit', 'A1', null, '100.0000', '0.00', '0.0000', false, ''),
                new JournalLineInput('credit', 'A2', null, '100.0000', '0.00', '0.0000', false, ''),
            ],
        ));
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
                amount: '1000.0000',
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
                amount: '1000.0000',
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
            summary: 'Initial',
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
}
