<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\Journal;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Journal\CreateJournalUseCase;
use Rucaro\Application\Journal\CreateJournalUseCaseInput;
use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Application\Journal\JournalSearchResult;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

#[CoversClass(CreateJournalUseCase::class)]
final class CreateJournalUseCaseTest extends TestCase
{
    public function testBalancedPayloadIsPersisted(): void
    {
        $repo = new class implements JournalRepositoryInterface {
            public ?Journal $saved = null;
            public function save(Journal $journal): void
            {
                $this->saved = $journal;
            }
            public function findById(string $id): ?Journal
            {
                return null;
            }
            public function findByCriteria(JournalSearchCriteria $criteria): JournalSearchResult
            {
                return new JournalSearchResult([], 0, $criteria->page, $criteria->pageSize);
            }
            public function delete(string $id, DateTimeImmutable $at, string $deletedBy): void
            {
                throw new EntityNotFoundException('not used');
            }
            public function searchByEntity(
                string $entityId,
                int $page,
                int $pageSize,
                ?string $fiscalTermId = null,
                ?string $from = null,
                ?string $to = null,
                ?string $status = null,
                ?string $source = null,
                ?string $search = null,
                bool $includeTrashed = false,
            ): array {
                return [];
            }
            public function countByEntity(
                string $entityId,
                ?string $fiscalTermId = null,
                ?string $from = null,
                ?string $to = null,
                ?string $status = null,
                ?string $source = null,
                ?string $search = null,
                bool $includeTrashed = false,
            ): int {
                return 0;
            }
        };

        $useCase = $this->makeUseCase($repo);

        $journal = $useCase->execute($this->makeInput([
            new JournalLineInput('debit', '01HW7K9B2QV7C8Y4ZACCTTL001', null, '1100.0000', '10.00', '100.0000', false, 'purchase'),
            new JournalLineInput('credit', '01HW7K9B2QV7C8Y4ZACCTTL002', null, '1100.0000', '0.00', '0.0000', false, 'cash'),
        ]));

        self::assertNotNull($repo->saved);
        self::assertSame('1100.0000', $journal->totalAmount);
        self::assertSame('draft', $journal->status);
        self::assertCount(2, $journal->lines);
        self::assertSame(1, $journal->lines[0]->lineNo);
        self::assertSame(2, $journal->lines[1]->lineNo);
    }

    public function testUnbalancedPayloadThrowsInvariantViolation(): void
    {
        $useCase = $this->makeUseCase(new InMemoryJournalRepo());

        $this->expectException(InvariantViolationException::class);
        $this->expectExceptionMessageMatches('/must_balance/');

        $useCase->execute($this->makeInput([
            new JournalLineInput('debit', '01HW7K9B2QV7C8Y4ZACCTTL001', null, '1000.0000', '0.00', '0.0000', false, ''),
            new JournalLineInput('credit', '01HW7K9B2QV7C8Y4ZACCTTL002', null, '999.0000', '0.00', '0.0000', false, ''),
        ]));
    }

    public function testRejectsFewerThanTwoLines(): void
    {
        $useCase = $this->makeUseCase(new InMemoryJournalRepo());

        $this->expectException(ValidationException::class);

        $useCase->execute($this->makeInput([
            new JournalLineInput('debit', '01HW7K9B2QV7C8Y4ZACCTTL001', null, '100.0000', '0.00', '0.0000', false, ''),
        ]));
    }

    /**
     * @param list<JournalLineInput> $lines
     */
    private function makeInput(array $lines): CreateJournalUseCaseInput
    {
        return new CreateJournalUseCaseInput(
            entityId: '01HW7K9B2QV7C8Y4ZENTITY0001',
            fiscalTermId: '01HW7K9B2QV7C8Y4ZFTTERM0001',
            journalDate: new DateTimeImmutable('2026-04-21'),
            summary: 'Test',
            source: 'manual',
            sourceReceiptId: null,
            currencyCode: 'JPY',
            createdBy: '01HW7K9B2QV7C8Y4ZUSER000001',
            lines: $lines,
        );
    }

    private function makeUseCase(JournalRepositoryInterface $repo): CreateJournalUseCase
    {
        $clock = new class implements ClockInterface {
            public function getCurrentTime(): DateTimeImmutable
            {
                return new DateTimeImmutable('2026-04-21T12:00:00.000Z', new DateTimeZone('UTC'));
            }
        };
        return new CreateJournalUseCase(
            journals: $repo,
            ulids: new UlidGenerator($clock),
            clock: $clock,
        );
    }
}
