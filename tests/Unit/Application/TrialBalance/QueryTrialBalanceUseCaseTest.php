<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\TrialBalance;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshot;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(QueryTrialBalanceUseCase::class)]
final class QueryTrialBalanceUseCaseTest extends TestCase
{
    private const ENT = 'ENT';
    private const TERM = 'TRM';

    public function testFallsBackToLiveAggregationWhenNoSnapshotExists(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        $query->setLatestSnapshot(null);

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $useCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertCount(2, $out->rows);
        self::assertSame('5000.0000', $out->debitTotal());
        self::assertSame('5000.0000', $out->creditTotal());
        self::assertTrue($out->isBalanced());
    }

    public function testUsesSnapshotWhenAllRequestedDatesAreCovered(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        $query->setLatestSnapshot(new DateTimeImmutable('2026-04-30'));

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $snapshots->saved = [
            $this->snapshot('ACC_CASH', '900.0000', '0.0000', 'debit', '2026-04-30', 1),
            $this->snapshot('ACC_SALES', '0.0000', '900.0000', 'credit', '2026-04-30', 1),
        ];

        $useCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        // Snapshot values are used verbatim (not the live in-memory 5000 figures).
        self::assertSame('900.0000', $out->debitTotal());
        self::assertSame('900.0000', $out->creditTotal());
    }

    public function testMergesSnapshotWithLiveTailWhenAsOfIsAfterLatestSnapshot(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        // May line that will be included only via the live tail path.
        $query->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-05-03'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '250.0000');
        $query->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-05-03'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '250.0000');
        $query->setLatestSnapshot(new DateTimeImmutable('2026-04-30'));

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $snapshots->saved = [
            $this->snapshot('ACC_CASH', '900.0000', '0.0000', 'debit', '2026-04-30', 1),
            $this->snapshot('ACC_SALES', '0.0000', '900.0000', 'credit', '2026-04-30', 1),
        ];

        $useCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-05-31'),
        ));

        // Snapshot debit 900 + May live debit 250 = 1150, same on credit
        self::assertSame('1150.0000', $out->debitTotal());
        self::assertSame('1150.0000', $out->creditTotal());
    }

    public function testIgnoresSnapshotWhenAsOfIsBeforeLatestSnapshotMonth(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        // Latest snapshot is from May, but caller asks for an April-only view
        $query->setLatestSnapshot(new DateTimeImmutable('2026-05-31'));

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();

        $useCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        // Should go through the live path (snapshot > asOf → fall back).
        self::assertSame('5000.0000', $out->debitTotal());
    }

    /**
     * Populates one balanced April transaction: 5000 cash in ←→ 5000 sales out.
     */
    private function seedApril(InMemoryTrialBalanceQuery $q): void
    {
        $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '5000.0000');
        $q->addLine(self::ENT, self::TERM, new DateTimeImmutable('2026-04-10'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '5000.0000');
    }

    private function snapshot(
        string $accountId,
        string $debit,
        string $credit,
        string $normalSide,
        string $date,
        int $lineCount,
    ): TrialBalanceSnapshot {
        // Compute the expected balance the same way the domain would.
        $row = TrialBalanceRow::compute(
            accountTitleId: $accountId,
            accountTitleCode: '',
            accountTitleName: '',
            accountCategory: '',
            normalSide: $normalSide,
            debitTotal: $debit,
            creditTotal: $credit,
            lineCount: $lineCount,
        );
        return new TrialBalanceSnapshot(
            id: '01HW7K9B2QV7C8Y4ZSNPSHT0001',
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            snapshotDate: new DateTimeImmutable($date, new DateTimeZone('UTC')),
            accountTitleId: $accountId,
            debitTotal: $row->debitTotal,
            creditTotal: $row->creditTotal,
            balance: $row->balance,
            lineCount: $row->lineCount,
            generatedAt: new DateTimeImmutable('2026-04-30T23:59:59Z', new DateTimeZone('UTC')),
        );
    }
}
