<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Golden\FinancialStatement;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\FinancialStatement\Port\GenerateFinancialStatementFromMappingUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Unit\Application\FinancialStatement\Port\InMemoryAccountTitleFsMappingRepository;
use Rucaro\Tests\Unit\Application\FinancialStatement\Port\InMemoryFsSectionDefinitionRepository;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceQuery;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceSnapshotRepository;

/**
 * Golden-table regression suite for the port-based FS calculation.
 *
 * Each scenario feeds a fixed journal book into the port use case and
 * asserts the resulting J-GAAP subtotals. Ten scenarios (as required by
 * Phase 6-A task spec) cover:
 *
 *   1. simple sales only
 *   2. sales + cost of sales
 *   3. sales + cost + SGA (operating income)
 *   4. plus non-operating revenue
 *   5. plus non-operating expense (ordinary income)
 *   6. plus extraordinary gain (pretax income +)
 *   7. plus extraordinary loss (pretax income -)
 *   8. plus income tax (net income)
 *   9. multi-entry aggregation (same account appears across several journals)
 *  10. sign-flip contra item on BS (allowance for doubtful accounts)
 */
#[CoversClass(GenerateFinancialStatementFromMappingUseCase::class)]
#[CoversClass(FinancialStatementBuilder::class)]
final class FinancialStatementGoldenTest extends TestCase
{
    /**
     * @return array<string, array{0: list<array{0:string, 1:string, 2:string, 3:string, 4:string, 5:string, 6:string}>, 1: FsKind, 2: array<string, string>}>
     */
    public static function plScenarios(): array
    {
        return [
            '1-simple-sales' => [
                [
                    ['S', '401', '売上', 'revenue', 'credit', 'credit', '10000'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'operating_revenue' => '10000.0000',
                    'cost_of_sales'     => '0.0000',
                    'gross_profit'      => '10000.0000',
                    'operating_income'  => '10000.0000',
                    'ordinary_income'   => '10000.0000',
                    'pretax_income'     => '10000.0000',
                    'net_income'        => '10000.0000',
                ],
            ],
            '2-sales-and-cost' => [
                [
                    ['S', '401', '売上', 'revenue', 'credit', 'credit', '20000'],
                    ['C', '501', '仕入', 'expense', 'debit',  'debit',  '12000'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'gross_profit'     => '8000.0000',
                    'operating_income' => '8000.0000',
                    'net_income'       => '8000.0000',
                ],
            ],
            '3-including-sga' => [
                [
                    ['S', '401', '売上', 'revenue', 'credit', 'credit', '30000'],
                    ['C', '501', '仕入', 'expense', 'debit',  'debit',  '15000'],
                    ['G', '502', '販管費', 'expense', 'debit', 'debit', '5000'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'gross_profit'     => '15000.0000',
                    'operating_income' => '10000.0000',
                    'net_income'       => '10000.0000',
                ],
            ],
            '4-with-non-op-revenue' => [
                [
                    ['S',  '401', '売上',    'revenue', 'credit', 'credit', '30000'],
                    ['C',  '501', '仕入',    'expense', 'debit',  'debit',  '15000'],
                    ['G',  '502', '販管費',  'expense', 'debit',  'debit',  '5000'],
                    ['NR', '411', '受取利息','revenue', 'credit', 'credit', '800'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'operating_income' => '10000.0000',
                    'ordinary_income'  => '10800.0000',
                    'net_income'       => '10800.0000',
                ],
            ],
            '5-with-non-op-expense' => [
                [
                    ['S',  '401', '売上',    'revenue', 'credit', 'credit', '30000'],
                    ['C',  '501', '仕入',    'expense', 'debit',  'debit',  '15000'],
                    ['G',  '502', '販管費',  'expense', 'debit',  'debit',  '5000'],
                    ['NR', '411', '受取利息','revenue', 'credit', 'credit', '800'],
                    ['NE', '511', '支払利息','expense', 'debit',  'debit',  '300'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'ordinary_income' => '10500.0000',
                    'net_income'      => '10500.0000',
                ],
            ],
            '6-with-extraordinary-gain' => [
                [
                    ['S',  '401', '売上',   'revenue', 'credit', 'credit', '30000'],
                    ['C',  '501', '仕入',   'expense', 'debit',  'debit',  '15000'],
                    ['EG', '412', '売却益', 'revenue', 'credit', 'credit', '2000'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'ordinary_income' => '15000.0000',
                    'pretax_income'   => '17000.0000',
                    'net_income'      => '17000.0000',
                ],
            ],
            '7-with-extraordinary-loss' => [
                [
                    ['S',  '401', '売上',     'revenue', 'credit', 'credit', '30000'],
                    ['C',  '501', '仕入',     'expense', 'debit',  'debit',  '15000'],
                    ['EL', '512', '災害損失', 'expense', 'debit',  'debit',  '1500'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'ordinary_income' => '15000.0000',
                    'pretax_income'   => '13500.0000',
                    'net_income'      => '13500.0000',
                ],
            ],
            '8-with-income-tax' => [
                [
                    ['S',  '401', '売上',   'revenue', 'credit', 'credit', '40000'],
                    ['C',  '501', '仕入',   'expense', 'debit',  'debit',  '18000'],
                    ['G',  '502', '販管費', 'expense', 'debit',  'debit',  '4000'],
                    ['TX', '591', '法人税等','expense','debit',  'debit',  '6000'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'pretax_income' => '18000.0000',
                    'net_income'    => '12000.0000',
                ],
            ],
            '9-multi-entry-aggregation' => [
                // Same account appears twice — amounts must aggregate.
                [
                    ['S', '401', '売上', 'revenue', 'credit', 'credit', '7000'],
                    ['S', '401', '売上', 'revenue', 'credit', 'credit', '3000'],
                    ['C', '501', '仕入', 'expense', 'debit',  'debit',  '2500'],
                    ['C', '501', '仕入', 'expense', 'debit',  'debit',  '2500'],
                ],
                FsKind::ProfitAndLoss,
                [
                    'operating_revenue' => '10000.0000',
                    'cost_of_sales'     => '5000.0000',
                    'gross_profit'      => '5000.0000',
                    'net_income'        => '5000.0000',
                ],
            ],
        ];
    }

    /**
     * @param list<array{0:string, 1:string, 2:string, 3:string, 4:string, 5:string, 6:string}> $journalLines
     * @param array<string, string> $expected
     */
    #[DataProvider('plScenarios')]
    public function testPlScenarios(array $journalLines, FsKind $kind, array $expected): void
    {
        self::assertSame(FsKind::ProfitAndLoss, $kind);
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();

        // Feed journal lines (id, code, name, category, normalSide, side, amount).
        foreach ($journalLines as [$id, $code, $name, $category, $normalSide, $side, $amount]) {
            $tb->addLine('ENT', 'TRM', new DateTimeImmutable('2026-04-05'), $id, $code, $name, $category, $normalSide, $side, $amount);
        }

        // Map every unique account id to a canonical PL section.
        $seeded = [];
        foreach ($journalLines as [$id, $code, $name, $category]) {
            if (isset($seeded[$id])) {
                continue;
            }
            $seeded[$id] = true;
            $sectionCode = self::sectionForId($id);
            $mappings->seed('ENT', $id, FsKind::ProfitAndLoss, $sectionCode, 1, 10);
            unset($code, $name, $category);
        }

        $port = new GenerateFinancialStatementFromMappingUseCase(
            trialBalance: new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock()),
            mappings: $mappings,
            definitions: $defs,
            builder: new FinancialStatementBuilder(),
            clock: new FrozenClock(),
        );
        $fs = $port->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::ProfitAndLoss,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        foreach ($expected as $code => $want) {
            self::assertArrayHasKey($code, $fs->pl, "Missing section $code");
            self::assertSame($want, $fs->pl[$code]->subtotal, "Section $code subtotal mismatch");
        }
    }

    public function testContraAssetIsSubtractedAtScenarioTen(): void
    {
        $tb = new InMemoryTrialBalanceQuery();
        $snap = new InMemoryTrialBalanceSnapshotRepository();
        $mappings = new InMemoryAccountTitleFsMappingRepository();
        $defs = new InMemoryFsSectionDefinitionRepository();

        // 売掛金 1000 / 貸倒引当金 40 → current_asset = 960.
        $tb->addLine('ENT', 'TRM', new DateTimeImmutable('2026-04-05'), 'AR',  '121', '売掛金',     'asset', 'debit', 'debit', '1000');
        $tb->addLine('ENT', 'TRM', new DateTimeImmutable('2026-04-05'), 'ALL', '129', '貸倒引当金', 'asset', 'debit', 'debit', '40');

        $mappings->seed('ENT', 'AR',  FsKind::BalanceSheet, 'current_asset', 1,  10);
        $mappings->seed('ENT', 'ALL', FsKind::BalanceSheet, 'current_asset', -1, 20);

        $port = new GenerateFinancialStatementFromMappingUseCase(
            trialBalance: new QueryTrialBalanceUseCase($tb, $snap, new FrozenClock()),
            mappings: $mappings,
            definitions: $defs,
            builder: new FinancialStatementBuilder(),
            clock: new FrozenClock(),
        );

        $fs = $port->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::BalanceSheet,
            fromDate: new DateTimeImmutable('2026-04-01'),
            asOf: new DateTimeImmutable('2026-04-30'),
        ));

        self::assertSame('960.0000', $fs->bs['current_asset']->subtotal);
        self::assertSame('960.0000', $fs->bs['asset']->subtotal);
        self::assertSame('960.0000', $fs->bs['asset_total']->subtotal);
    }

    private static function sectionForId(string $id): string
    {
        return match ($id) {
            'S'    => 'operating_revenue',
            'C'    => 'cost_of_sales',
            'G'    => 'sga',
            'NR'   => 'non_operating_revenue',
            'NE'   => 'non_operating_expense',
            'EG'   => 'extraordinary_gain',
            'EL'   => 'extraordinary_loss',
            'TX'   => 'income_tax',
            default => 'operating_revenue',
        };
    }
}
