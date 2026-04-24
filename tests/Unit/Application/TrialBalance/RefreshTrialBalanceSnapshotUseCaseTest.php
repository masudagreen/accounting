<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\TrialBalance;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\TrialBalance\RefreshTrialBalanceSnapshotUseCase;
use Rucaro\Application\TrialBalance\RefreshTrialBalanceSnapshotUseCaseInput;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Decimal\Decimal;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(RefreshTrialBalanceSnapshotUseCase::class)]
final class RefreshTrialBalanceSnapshotUseCaseTest extends TestCase
{
    public function testDeletesExistingMonthBeforeSavingNewRows(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        $repo = new InMemoryTrialBalanceSnapshotRepository();

        $useCase = new RefreshTrialBalanceSnapshotUseCase(
            query: $query,
            snapshots: $repo,
            ulids: $this->deterministicUlids(new FrozenClock()),
            clock: new FrozenClock(),
        );

        $count = $useCase->execute(new RefreshTrialBalanceSnapshotUseCaseInput(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            monthStartDate: new DateTimeImmutable('2026-04-01'),
            monthEndDate: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertSame(2, $count);
        self::assertSame('delete:2026-04-30', $repo->operationLog[0]);
        self::assertSame('save:2', $repo->operationLog[1]);
    }

    public function testIsIdempotentWhenRunMultipleTimesForSameMonth(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        $repo = new InMemoryTrialBalanceSnapshotRepository();

        $useCase = new RefreshTrialBalanceSnapshotUseCase(
            query: $query,
            snapshots: $repo,
            ulids: $this->deterministicUlids(new FrozenClock()),
            clock: new FrozenClock(),
        );

        $input = new RefreshTrialBalanceSnapshotUseCaseInput(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            monthStartDate: new DateTimeImmutable('2026-04-01'),
            monthEndDate: new DateTimeImmutable('2026-04-30'),
        );
        $useCase->execute($input);
        $useCase->execute($input);

        // Final state: only one month's worth of rows remain.
        self::assertCount(2, $repo->saved);

        $totalDebit = '0.0000';
        $totalCredit = '0.0000';
        foreach ($repo->saved as $s) {
            $totalDebit = Decimal::add($totalDebit, $s->debitTotal);
            $totalCredit = Decimal::add($totalCredit, $s->creditTotal);
        }
        self::assertSame('5000.0000', $totalDebit);
        self::assertSame('5000.0000', $totalCredit);
    }

    public function testWritesSnapshotDateAsGivenMonthEnd(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        $repo = new InMemoryTrialBalanceSnapshotRepository();

        $useCase = new RefreshTrialBalanceSnapshotUseCase(
            query: $query,
            snapshots: $repo,
            ulids: $this->deterministicUlids(new FrozenClock()),
            clock: new FrozenClock(),
        );

        $useCase->execute(new RefreshTrialBalanceSnapshotUseCaseInput(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            monthStartDate: new DateTimeImmutable('2026-04-01'),
            monthEndDate: new DateTimeImmutable('2026-04-30'),
        ));

        foreach ($repo->saved as $s) {
            self::assertSame('2026-04-30', $s->snapshotDate->format('Y-m-d'));
            self::assertSame('ENT', $s->entityId);
            self::assertSame('TRM', $s->fiscalTermId);
        }
    }

    /**
     * Deterministic {@see UlidGenerator} via a {@see FrozenClock}.
     */
    private function deterministicUlids(ClockInterface $clock): UlidGenerator
    {
        return new UlidGenerator($clock);
    }

    private function seedApril(InMemoryTrialBalanceQuery $q): void
    {
        $q->addLine('ENT', 'TRM', new DateTimeImmutable('2026-04-10'), 'ACC_CASH',  '101', '現金', 'asset',   'debit',  'debit',  '5000.0000');
        $q->addLine('ENT', 'TRM', new DateTimeImmutable('2026-04-10'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '5000.0000');
    }
}
