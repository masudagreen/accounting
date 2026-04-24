<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Application\Journal\SearchJournalUseCase;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalStatus;
use Rucaro\Domain\Journal\ValueObject\JournalDate;
use Rucaro\Tests\Support\Fake\InMemoryJournalRepository;

#[CoversClass(SearchJournalUseCase::class)]
final class SearchJournalUseCaseTest extends TestCase
{
    public function testEntityFilterScopesResults(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->journal('J1', 'ENT1', '2026-04-10'));
        $repo->save($this->journal('J2', 'ENT1', '2026-04-15'));
        $repo->save($this->journal('J3', 'ENT2', '2026-04-15'));

        $out = (new SearchJournalUseCase($repo))->execute(
            new JournalSearchCriteria(entityId: 'ENT1'),
        );

        self::assertSame(2, $out->total);
        self::assertCount(2, $out->items);
    }

    public function testDateRangeFilter(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->journal('J1', 'ENT1', '2026-04-01'));
        $repo->save($this->journal('J2', 'ENT1', '2026-04-15'));
        $repo->save($this->journal('J3', 'ENT1', '2026-05-01'));

        $out = (new SearchJournalUseCase($repo))->execute(
            new JournalSearchCriteria(
                entityId: 'ENT1',
                from: JournalDate::fromString('2026-04-10'),
                to: JournalDate::fromString('2026-04-30'),
            ),
        );

        self::assertSame(1, $out->total);
        self::assertSame('J2', $out->items[0]->id);
    }

    public function testStatusFilter(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->journal('J1', 'ENT1', '2026-04-01'));
        $repo->save(
            $this->journal('J2', 'ENT1', '2026-04-02')->approve(new DateTimeImmutable('2026-04-03T00:00:00Z'), 'U1'),
        );

        $out = (new SearchJournalUseCase($repo))->execute(
            new JournalSearchCriteria(entityId: 'ENT1', status: JournalStatus::Approved),
        );

        self::assertSame(1, $out->total);
        self::assertSame('J2', $out->items[0]->id);
    }

    public function testAccountTitleFilter(): void
    {
        $repo = new InMemoryJournalRepository();
        $repo->save($this->journal('J1', 'ENT1', '2026-04-01', 'A_DEBIT', 'A_CREDIT'));
        $repo->save($this->journal('J2', 'ENT1', '2026-04-02', 'X_DEBIT', 'X_CREDIT'));

        $out = (new SearchJournalUseCase($repo))->execute(
            new JournalSearchCriteria(entityId: 'ENT1', accountTitleId: 'A_DEBIT'),
        );

        self::assertSame(1, $out->total);
        self::assertSame('J1', $out->items[0]->id);
    }

    public function testPaginationSplitsResults(): void
    {
        $repo = new InMemoryJournalRepository();
        for ($i = 1; $i <= 5; $i++) {
            $repo->save($this->journal(sprintf('J%d', $i), 'ENT1', '2026-04-01'));
        }
        $out = (new SearchJournalUseCase($repo))->execute(
            new JournalSearchCriteria(entityId: 'ENT1', page: 2, pageSize: 2),
        );

        self::assertSame(5, $out->total);
        self::assertCount(2, $out->items);
    }

    private function journal(
        string $id,
        string $entityId,
        string $date,
        string $debitAccount = '01HW7K9B2QV7C8Y4ZACCTTL001',
        string $creditAccount = '01HW7K9B2QV7C8Y4ZACCTTL002',
    ): Journal {
        $lines = [
            new JournalLine(
                id: $id . '_D',
                lineNo: 1,
                side: 'debit',
                accountTitleId: $debitAccount,
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new DateTimeImmutable($date . 'T12:00:00Z'),
            ),
            new JournalLine(
                id: $id . '_C',
                lineNo: 2,
                side: 'credit',
                accountTitleId: $creditAccount,
                subAccountTitleId: null,
                amount: '100.0000',
                taxRatePercent: '0.00',
                taxAmount: '0.0000',
                isTaxReduced: false,
                memo: '',
                bookedAt: new DateTimeImmutable($date . 'T12:00:00Z'),
            ),
        ];
        return new Journal(
            id: $id,
            entityId: $entityId,
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable($date),
            bookedAt: new DateTimeImmutable($date . 'T12:00:00Z'),
            summary: $id,
            totalAmount: '100.0000',
            currencyCode: 'JPY',
            status: 'draft',
            source: 'manual',
            sourceReceiptId: null,
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            approvedBy: null,
            approvedAt: null,
            createdAt: new DateTimeImmutable($date . 'T12:00:00Z'),
            updatedAt: new DateTimeImmutable($date . 'T12:00:00Z'),
            deletedAt: null,
            lines: $lines,
        );
    }
}
