<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\TrialBalance;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshot;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Unit\Application\Ledger\InMemoryOpeningBalanceRepository;

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
            fiscalTermStartDate: new \DateTimeImmutable('2026-04-01'),
            asOf: new \DateTimeImmutable('2026-04-30'),
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
        $query->setLatestSnapshot(new \DateTimeImmutable('2026-04-30'));

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $snapshots->saved = [
            $this->snapshot('ACC_CASH', '900.0000', '0.0000', 'debit', '2026-04-30', 1),
            $this->snapshot('ACC_SALES', '0.0000', '900.0000', 'credit', '2026-04-30', 1),
        ];

        $useCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new \DateTimeImmutable('2026-04-01'),
            asOf: new \DateTimeImmutable('2026-04-30'),
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
        $query->addLine(self::ENT, self::TERM, new \DateTimeImmutable('2026-05-03'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '250.0000');
        $query->addLine(self::ENT, self::TERM, new \DateTimeImmutable('2026-05-03'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '250.0000');
        $query->setLatestSnapshot(new \DateTimeImmutable('2026-04-30'));

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $snapshots->saved = [
            $this->snapshot('ACC_CASH', '900.0000', '0.0000', 'debit', '2026-04-30', 1),
            $this->snapshot('ACC_SALES', '0.0000', '900.0000', 'credit', '2026-04-30', 1),
        ];

        $useCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new \DateTimeImmutable('2026-04-01'),
            asOf: new \DateTimeImmutable('2026-05-31'),
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
        $query->setLatestSnapshot(new \DateTimeImmutable('2026-05-31'));

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();

        $useCase = new QueryTrialBalanceUseCase($query, $snapshots, new FrozenClock());

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new \DateTimeImmutable('2026-04-01'),
            asOf: new \DateTimeImmutable('2026-04-30'),
        ));

        // Should go through the live path (snapshot > asOf → fall back).
        self::assertSame('5000.0000', $out->debitTotal());
    }

    // -----------------------------------------------------------------
    // Phase B-3b: opening (前期繰越) folded into BS rows
    // -----------------------------------------------------------------

    public function testFoldsOpeningBalanceIntoBalanceSheetRows(): void
    {
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        $query->setLatestSnapshot(null);

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $opening = new InMemoryOpeningBalanceRepository();
        // 現金 (asset, debit-normal) carries 3,897,237 forward from prior term
        $opening->set('ACC_CASH', '3897237.0000');
        // 売上 (revenue) opening is irrelevant — PL accounts must remain 0
        $opening->set('ACC_SALES', '999.0000');

        $useCase = new QueryTrialBalanceUseCase(
            $query,
            $snapshots,
            new FrozenClock(),
            $opening,
        );

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new \DateTimeImmutable('2026-04-01'),
            asOf: new \DateTimeImmutable('2026-04-30'),
        ));

        $byId = [];
        foreach ($out->rows as $row) {
            $byId[$row->accountTitleId] = $row;
        }
        self::assertArrayHasKey('ACC_CASH', $byId);
        self::assertArrayHasKey('ACC_SALES', $byId);

        // 現金: opening 3,897,237 + (period dr 5000 - cr 0) = 3,902,237
        self::assertSame('3897237.0000', $byId['ACC_CASH']->openingBalance);
        self::assertSame('3902237.0000', $byId['ACC_CASH']->balance);

        // 売上 (revenue): opening must NOT be applied even if the repo would
        // return a non-zero value. PL rows always carry opening = 0.
        self::assertSame('0.0000', $byId['ACC_SALES']->openingBalance);
        self::assertSame('5000.0000', $byId['ACC_SALES']->balance);
    }

    public function testReproducesRealityCheck8CashCarryForward(): void
    {
        // Mirror RC-8: idEntity=1 / 期20 / 現金
        // Period contributes dr 1,593,200 with cr 0; opening from prior 19
        // terms is 3,897,237. Renewal must surface the 5,490,437 cumulative.
        $query = new InMemoryTrialBalanceQuery();
        $query->addLine(
            self::ENT,
            self::TERM,
            new \DateTimeImmutable('2026-04-15'),
            'ACC_CASH',
            '101',
            '現金',
            'asset',
            'debit',
            'debit',
            '1593200.0000',
        );
        $query->addLine(
            self::ENT,
            self::TERM,
            new \DateTimeImmutable('2026-04-15'),
            'ACC_OFFSET',
            '102',
            'offset',
            'liability',
            'credit',
            'credit',
            '1593200.0000',
        );
        $query->setLatestSnapshot(null);

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $opening = new InMemoryOpeningBalanceRepository();
        $opening->set('ACC_CASH', '3897237.0000');

        $useCase = new QueryTrialBalanceUseCase(
            $query,
            $snapshots,
            new FrozenClock(),
            $opening,
        );

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new \DateTimeImmutable('2026-04-01'),
            asOf: new \DateTimeImmutable('2027-03-31'),
        ));

        $cash = null;
        foreach ($out->rows as $row) {
            if ($row->accountTitleId === 'ACC_CASH') {
                $cash = $row;
                break;
            }
        }
        self::assertNotNull($cash);
        self::assertSame('5490437.0000', $cash->balance);
    }

    public function testFoldsOpeningWhenServingFromSnapshot(): void
    {
        // Snapshot covers a fiscal-term boundary. Even though the snapshot
        // row already has period totals baked in, the use case is still
        // responsible for applying carry-forward from the prior term — the
        // monthly snapshot pipeline does NOT persist opening balances.
        $query = new InMemoryTrialBalanceQuery();
        $this->seedApril($query);
        $query->setLatestSnapshot(new \DateTimeImmutable('2026-04-30'));

        $snapshots = new InMemoryTrialBalanceSnapshotRepository();
        $snapshots->saved = [
            $this->snapshot('ACC_CASH', '900.0000', '0.0000', 'debit', '2026-04-30', 1),
            $this->snapshot('ACC_SALES', '0.0000', '900.0000', 'credit', '2026-04-30', 1),
        ];
        $opening = new InMemoryOpeningBalanceRepository();
        $opening->set('ACC_CASH', '100.0000');

        $useCase = new QueryTrialBalanceUseCase(
            $query,
            $snapshots,
            new FrozenClock(),
            $opening,
        );

        $out = $useCase->execute(new QueryTrialBalanceUseCaseInput(
            entityId: self::ENT,
            fiscalTermId: self::TERM,
            fiscalTermStartDate: new \DateTimeImmutable('2026-04-01'),
            asOf: new \DateTimeImmutable('2026-04-30'),
        ));

        $cash = null;
        foreach ($out->rows as $row) {
            if ($row->accountTitleId === 'ACC_CASH') {
                $cash = $row;
                break;
            }
        }
        self::assertNotNull($cash);
        // Snapshot debit 900 + opening 100 = 1000
        self::assertSame('100.0000', $cash->openingBalance);
        self::assertSame('1000.0000', $cash->balance);
    }

    /**
     * Populates one balanced April transaction: 5000 cash in ←→ 5000 sales out.
     */
    private function seedApril(InMemoryTrialBalanceQuery $q): void
    {
        $q->addLine(self::ENT, self::TERM, new \DateTimeImmutable('2026-04-10'), 'ACC_CASH', '101', '現金', 'asset', 'debit', 'debit', '5000.0000');
        $q->addLine(self::ENT, self::TERM, new \DateTimeImmutable('2026-04-10'), 'ACC_SALES', '401', '売上', 'revenue', 'credit', 'credit', '5000.0000');
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
            snapshotDate: new \DateTimeImmutable($date, new \DateTimeZone('UTC')),
            accountTitleId: $accountId,
            debitTotal: $row->debitTotal,
            creditTotal: $row->creditTotal,
            balance: $row->balance,
            lineCount: $row->lineCount,
            generatedAt: new \DateTimeImmutable('2026-04-30T23:59:59Z', new \DateTimeZone('UTC')),
        );
    }
}
