<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\ListJournalsUseCase;
use Rucaro\Application\Journal\ListJournalsUseCaseInput;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;

#[CoversClass(ListJournalsUseCase::class)]
final class ListJournalsUseCaseTest extends TestCase
{
    public function testReturnsJournalsFilteredByEntity(): void
    {
        $repo = new InMemoryJournalRepo();
        $repo->save($this->journal('01HW7K9B2QV7C8Y4ZJRNL000001', 'ENT1'));
        $repo->save($this->journal('01HW7K9B2QV7C8Y4ZJRNL000002', 'ENT1'));
        $repo->save($this->journal('01HW7K9B2QV7C8Y4ZJRNL000003', 'ENT2'));

        $useCase = new ListJournalsUseCase($repo);

        $out = $useCase->execute(new ListJournalsUseCaseInput(
            entityId: 'ENT1',
            page: 1,
            pageSize: 10,
        ));

        self::assertSame(2, $out->total);
        self::assertCount(2, $out->items);
    }

    public function testPaginationSplitsResults(): void
    {
        $repo = new InMemoryJournalRepo();
        for ($i = 1; $i <= 5; $i++) {
            $repo->save($this->journal(sprintf('01HW7K9B2QV7C8Y4ZJRNL00000%d', $i), 'ENT1'));
        }
        $useCase = new ListJournalsUseCase($repo);

        $page1 = $useCase->execute(new ListJournalsUseCaseInput('ENT1', 1, 2));
        $page2 = $useCase->execute(new ListJournalsUseCaseInput('ENT1', 2, 2));

        self::assertSame(5, $page1->total);
        self::assertSame(5, $page2->total);
        self::assertCount(2, $page1->items);
        self::assertCount(2, $page2->items);
    }

    private function journal(string $id, string $entityId): Journal
    {
        $lines = [
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZJLINE00001',
                lineNo: 1,
                side: 'debit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL001',
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            ),
            new JournalLine(
                id: '01HW7K9B2QV7C8Y4ZJLINE00002',
                lineNo: 2,
                side: 'credit',
                accountTitleId: '01HW7K9B2QV7C8Y4ZACCTTL002',
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            ),
        ];
        return new Journal(
            id: $id,
            entityId: $entityId,
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21'),
            bookedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
            summary: 'Test',
            totalAmount: '100.0000',
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
