<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Multi;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementInput;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;
use Rucaro\Infrastructure\FinancialStatement\Multi\MultiPeriodRowBuilder;
use Rucaro\Tests\Support\Fake\FrozenClock;

#[CoversClass(GenerateMultiPeriodFinancialStatementUseCase::class)]
#[CoversClass(MultiPeriodFinancialStatement::class)]
#[CoversClass(MultiPeriodRowBuilder::class)]
final class GenerateMultiPeriodFinancialStatementUseCaseTest extends TestCase
{
    private const ENT = 'ENT';

    public function testBundlesTwoPeriodsInAscendingStartDateOrder(): void
    {
        $provider = new StubFinancialStatementProvider();
        $provider->seed('TERM_2025', pl: ['operating_revenue' => '1000.0000', 'net_income' => '200.0000']);
        $provider->seed('TERM_2026', pl: ['operating_revenue' => '1500.0000', 'net_income' => '400.0000']);

        $terms = new InMemoryFiscalTermMetadataRepository();
        // Seed in reverse order — use case must re-sort internally.
        $terms->seed('TERM_2026', 2, '2026-04-01', '2027-03-31');
        $terms->seed('TERM_2025', 1, '2025-04-01', '2026-03-31');

        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: $provider,
            fiscalTerms: $terms,
            clock: new FrozenClock(),
        );

        $multi = $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['TERM_2026', 'TERM_2025'],
            kind: FinancialStatementKind::ProfitAndLoss,
        ));

        self::assertSame(2, $multi->periodCount());
        self::assertSame('TERM_2025', $multi->periods[0]->fiscalTermId);
        self::assertSame('TERM_2026', $multi->periods[1]->fiscalTermId);
        self::assertSame('第 1 期', $multi->periods[0]->fiscalTermLabel);
        self::assertSame('第 2 期', $multi->periods[1]->fiscalTermLabel);
        self::assertSame(
            '2025-04-01',
            $multi->periods[0]->fromDate->format('Y-m-d'),
        );
        self::assertSame(
            '2027-03-31',
            $multi->periods[1]->toDate->format('Y-m-d'),
        );

        // The per-period single FS is produced by the provider.
        self::assertSame(
            '1000.0000',
            $multi->periods[0]->statement->pl['operating_revenue']->subtotal,
        );
        self::assertSame(
            '1500.0000',
            $multi->periods[1]->statement->pl['operating_revenue']->subtotal,
        );
    }

    public function testComparisonRowsIncludeVarianceAndVariancePercent(): void
    {
        $provider = new StubFinancialStatementProvider();
        $provider->seed('TERM_2025', pl: ['operating_revenue' => '1000.0000']);
        $provider->seed('TERM_2026', pl: ['operating_revenue' => '1500.0000']);

        $terms = new InMemoryFiscalTermMetadataRepository();
        $terms->seed('TERM_2025', 1, '2025-04-01', '2026-03-31');
        $terms->seed('TERM_2026', 2, '2026-04-01', '2027-03-31');

        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: $provider,
            fiscalTerms: $terms,
            clock: new FrozenClock(),
        );

        $multi = $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['TERM_2025', 'TERM_2026'],
            kind: FinancialStatementKind::ProfitAndLoss,
        ));

        $rows = MultiPeriodRowBuilder::buildPl($multi);
        self::assertCount(1, $rows);
        $row = $rows[0];
        self::assertSame('operating_revenue', $row->sectionCode);
        self::assertSame('1000.0000', $row->amounts['TERM_2025']);
        self::assertSame('1500.0000', $row->amounts['TERM_2026']);
        // Latest - previous = 1500 - 1000 = 500
        self::assertSame('500.0000', $row->variance);
        // 500 / 1000 * 100 = 50.00%
        self::assertSame('50.0000', $row->variancePercent);
    }

    public function testVariancePercentIsNullWhenPreviousPeriodIsZero(): void
    {
        $provider = new StubFinancialStatementProvider();
        // Previous period has zero amount → variance% undefined.
        $provider->seed('TERM_PRIOR', pl: ['operating_revenue' => '0.0000']);
        $provider->seed('TERM_CURR',  pl: ['operating_revenue' => '1500.0000']);

        $terms = new InMemoryFiscalTermMetadataRepository();
        $terms->seed('TERM_PRIOR', 1, '2025-04-01', '2026-03-31');
        $terms->seed('TERM_CURR',  2, '2026-04-01', '2027-03-31');

        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: $provider,
            fiscalTerms: $terms,
            clock: new FrozenClock(),
        );

        $multi = $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['TERM_PRIOR', 'TERM_CURR'],
            kind: FinancialStatementKind::ProfitAndLoss,
        ));

        $rows = MultiPeriodRowBuilder::buildPl($multi);
        self::assertCount(1, $rows);
        self::assertSame('1500.0000', $rows[0]->variance);
        self::assertNull($rows[0]->variancePercent);
    }

    public function testSinglePeriodProducesNoVariance(): void
    {
        $provider = new StubFinancialStatementProvider();
        $provider->seed('TERM_ONLY', pl: ['operating_revenue' => '2000.0000']);

        $terms = new InMemoryFiscalTermMetadataRepository();
        $terms->seed('TERM_ONLY', 1, '2026-04-01', '2027-03-31');

        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: $provider,
            fiscalTerms: $terms,
            clock: new FrozenClock(),
        );

        $multi = $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['TERM_ONLY'],
            kind: FinancialStatementKind::ProfitAndLoss,
        ));

        self::assertSame(1, $multi->periodCount());
        $rows = MultiPeriodRowBuilder::buildPl($multi);
        self::assertCount(1, $rows);
        self::assertNull($rows[0]->variance);
        self::assertNull($rows[0]->variancePercent);
        self::assertSame('2000.0000', $rows[0]->amounts['TERM_ONLY']);
    }

    public function testMoreThanFivePeriodsIsRejected(): void
    {
        $provider = new StubFinancialStatementProvider();
        $terms = new InMemoryFiscalTermMetadataRepository();
        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: $provider,
            fiscalTerms: $terms,
            clock: new FrozenClock(),
        );

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'],
            kind: FinancialStatementKind::All,
        ));
    }

    public function testEmptyFiscalTermIdsIsRejected(): void
    {
        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: new StubFinancialStatementProvider(),
            fiscalTerms: new InMemoryFiscalTermMetadataRepository(),
            clock: new FrozenClock(),
        );

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: [],
            kind: FinancialStatementKind::All,
        ));
    }

    public function testDuplicateFiscalTermIdsAreRejected(): void
    {
        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: new StubFinancialStatementProvider(),
            fiscalTerms: new InMemoryFiscalTermMetadataRepository(),
            clock: new FrozenClock(),
        );

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['T1', 'T1'],
            kind: FinancialStatementKind::All,
        ));
    }

    public function testUnresolvableFiscalTermIsRejected(): void
    {
        $terms = new InMemoryFiscalTermMetadataRepository();
        $terms->seed('TERM_KNOWN', 1, '2025-04-01', '2026-03-31');

        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: new StubFinancialStatementProvider(),
            fiscalTerms: $terms,
            clock: new FrozenClock(),
        );

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['TERM_KNOWN', 'TERM_MISSING'],
            kind: FinancialStatementKind::All,
        ));
    }

    public function testMissingCodeInOnePeriodTreatedAsZero(): void
    {
        $provider = new StubFinancialStatementProvider();
        // Prior period reports operating_revenue; current period also reports
        // non_operating_revenue which the prior period didn't have.
        $provider->seed('TERM_PRIOR', pl: ['operating_revenue' => '1000.0000']);
        $provider->seed('TERM_CURR',  pl: [
            'operating_revenue'     => '1200.0000',
            'non_operating_revenue' =>  '300.0000',
        ]);

        $terms = new InMemoryFiscalTermMetadataRepository();
        $terms->seed('TERM_PRIOR', 1, '2025-04-01', '2026-03-31');
        $terms->seed('TERM_CURR',  2, '2026-04-01', '2027-03-31');

        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: $provider,
            fiscalTerms: $terms,
            clock: new FrozenClock(),
        );

        $multi = $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['TERM_PRIOR', 'TERM_CURR'],
            kind: FinancialStatementKind::ProfitAndLoss,
        ));

        $rows = MultiPeriodRowBuilder::buildPl($multi);
        $byCode = [];
        foreach ($rows as $r) {
            $byCode[$r->sectionCode] = $r;
        }
        // non_operating_revenue in prior period is treated as 0.
        self::assertSame('0.0000', $byCode['non_operating_revenue']->amounts['TERM_PRIOR']);
        self::assertSame('300.0000', $byCode['non_operating_revenue']->amounts['TERM_CURR']);
        self::assertSame('300.0000', $byCode['non_operating_revenue']->variance);
        self::assertNull($byCode['non_operating_revenue']->variancePercent);
    }

    public function testGeneratedAtCarriesUtcTimezone(): void
    {
        $provider = new StubFinancialStatementProvider();
        $provider->seed('TERM_A', pl: []);
        $terms = new InMemoryFiscalTermMetadataRepository();
        $terms->seed('TERM_A', 1, '2025-04-01', '2026-03-31');

        $useCase = new GenerateMultiPeriodFinancialStatementUseCase(
            provider: $provider,
            fiscalTerms: $terms,
            clock: new FrozenClock('2026-04-21T12:00:00Z'),
        );

        $multi = $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
            entityId: self::ENT,
            fiscalTermIds: ['TERM_A'],
            kind: FinancialStatementKind::All,
        ));

        self::assertSame(
            'UTC',
            $multi->generatedAt->getTimezone()->getName(),
        );
        self::assertEquals(
            new DateTimeImmutable('2026-04-21T12:00:00Z', new DateTimeZone('UTC')),
            $multi->generatedAt,
        );
    }
}
