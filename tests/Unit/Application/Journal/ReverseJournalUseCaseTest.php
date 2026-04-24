<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\ReverseJournalUseCase;
use Rucaro\Application\Journal\ReverseJournalUseCaseInput;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Domain\Journal\Service\JournalReverser;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;

#[CoversClass(ReverseJournalUseCase::class)]
final class ReverseJournalUseCaseTest extends TestCase
{
    public function testPostedJournalIsReversedAndReversalIsPersisted(): void
    {
        $repo = new InMemoryJournalRepository();
        $posted = $this->draft()
            ->approve(new DateTimeImmutable('2026-04-21T12:10:00Z'), 'U1')
            ->post(new DateTimeImmutable('2026-04-21T12:20:00Z'), 'U1');
        $repo->save($posted);

        $clock = new FrozenClock('2026-04-22T09:00:00.000Z');
        $useCase = new ReverseJournalUseCase(
            $repo,
            new JournalReverser(new UlidGenerator($clock)),
            $clock,
        );

        $out = $useCase->execute(new ReverseJournalUseCaseInput(
            journalId: $posted->id,
            reversedBy: 'U1',
            reason: 'typo',
        ));

        self::assertSame(JournalStatus::Reversed, $out->source->statusEnum());
        self::assertSame(JournalStatus::Posted, $out->reversal->statusEnum());
        self::assertCount(2, $out->reversal->lines);
        self::assertSame('credit', $out->reversal->lines[0]->side);
        self::assertSame('debit', $out->reversal->lines[1]->side);
        self::assertNotEquals($posted->id, $out->reversal->id);
        self::assertCount(2, $repo->byId);
    }

    public function testMissingJournalRaises(): void
    {
        $clock = new FrozenClock();
        $useCase = new ReverseJournalUseCase(
            new InMemoryJournalRepository(),
            new JournalReverser(new UlidGenerator($clock)),
            $clock,
        );
        $this->expectException(EntityNotFoundException::class);
        $useCase->execute(new ReverseJournalUseCaseInput('01HW7K9B2QV7C8Y4ZJRNLMISSING', 'U1', 'r'));
    }

    public function testDraftJournalCannotBeReversed(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->draft());

        $clock = new FrozenClock();
        $useCase = new ReverseJournalUseCase(
            $repo,
            new JournalReverser(new UlidGenerator($clock)),
            $clock,
        );
        $this->expectException(InvariantViolationException::class);
        $useCase->execute(new ReverseJournalUseCaseInput('01HW7K9B2QV7C8Y4ZJRNL000001', 'U1', 'x'));
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
