<?php

declare(strict_types=1);

/**
 * Phase 6 Wave 6-I smoke-test harness.
 *
 * Builds a representative {@see MultiPeriodFinancialStatement} with two
 * periods (2025 prior, 2026 current) and renders one PDF per kind (BS, PL,
 * ALL) into the rucaro-out desktop folder.
 *
 * Not used at runtime — strictly an operator aid.
 */

use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadata;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadataRepositoryInterface;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementInput;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Infrastructure\FinancialStatement\Multi\DompdfMultiPeriodFinancialStatementGenerator;
use Rucaro\Tests\Unit\Application\FinancialStatement\Multi\StubFinancialStatementProvider;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$repoRoot = dirname(__DIR__, 2);
$templateDir = $repoRoot . '/storage/templates/fs_multi';
$compileDir = $repoRoot . '/storage/cache/smarty_fs_multi';
$fontDir = $repoRoot . '/storage/fonts';
if (!is_dir($compileDir)) {
    mkdir($compileDir, 0775, true);
}
if (!is_dir($fontDir)) {
    mkdir($fontDir, 0775, true);
}

// --- seed fake prior (2025) + current (2026) statements ---
$provider = new StubFinancialStatementProvider();

// 2025 fiscal term: empty (no journals booked).
$provider->seed(
    fiscalTermId: 'TERM_2025',
    bs: [
        'current_asset'     => '0.0000',
        'current_liability' => '0.0000',
        'capital'           => '1000000.0000',
        'asset_total'       => '1000000.0000',
        'equity_total'      => '1000000.0000',
    ],
    pl: [
        'operating_revenue' => '0.0000',
        'cost_of_sales'     => '0.0000',
        'gross_profit'      => '0.0000',
        'operating_income'  => '0.0000',
        'pretax_income'     => '0.0000',
        'net_income'        => '0.0000',
    ],
    cs: [
        'operating_cf_total' => '0.0000',
        'investing_cf_total' => '0.0000',
        'financing_cf_total' => '0.0000',
        'net_change_in_cash' => '0.0000',
        'ending_cash'        => '0.0000',
    ],
    totals: ['net_income' => '0.0000'],
);

// 2026 fiscal term: realistic demo numbers.
$provider->seed(
    fiscalTermId: 'TERM_2026',
    bs: [
        'current_asset'     => '2583000.0000',
        'current_liability' =>  '200000.0000',
        'capital'           => '1000000.0000',
        'retained_earnings' => '1383000.0000',
        'asset_total'       => '2583000.0000',
        'liability_total'   =>  '200000.0000',
        'equity_total'      => '2383000.0000',
    ],
    pl: [
        'operating_revenue' => '2000000.0000',
        'cost_of_sales'     =>  '617000.0000',
        'gross_profit'      => '1383000.0000',
        'operating_income'  => '1383000.0000',
        'pretax_income'     => '1383000.0000',
        'net_income'        => '1383000.0000',
    ],
    cs: [
        'operating_cf_total' => '1383000.0000',
        'investing_cf_total' =>       '0.0000',
        'financing_cf_total' =>       '0.0000',
        'net_change_in_cash' => '1383000.0000',
        'ending_cash'        => '1383000.0000',
    ],
    totals: ['net_income' => '1383000.0000'],
);

$terms = new class () implements FiscalTermMetadataRepositoryInterface {
    public function findByIds(array $ids): array
    {
        $tz = new DateTimeZone('UTC');
        $all = [
            'TERM_2025' => new FiscalTermMetadata(
                id: 'TERM_2025',
                label: '第 1 期 (2025)',
                startDate: new DateTimeImmutable('2025-04-01', $tz),
                endDate: new DateTimeImmutable('2026-03-31', $tz),
            ),
            'TERM_2026' => new FiscalTermMetadata(
                id: 'TERM_2026',
                label: '第 2 期 (2026)',
                startDate: new DateTimeImmutable('2026-04-01', $tz),
                endDate: new DateTimeImmutable('2027-03-31', $tz),
            ),
        ];
        $out = [];
        foreach ($ids as $id) {
            if (isset($all[$id])) {
                $out[] = $all[$id];
            }
        }
        return $out;
    }
};

$useCase = new GenerateMultiPeriodFinancialStatementUseCase(
    provider: $provider,
    fiscalTerms: $terms,
);

$generator = new DompdfMultiPeriodFinancialStatementGenerator(
    templateDir: $templateDir,
    compileDir: $compileDir,
    fontDir: $fontDir,
);

$outDir = getenv('OUT_DIR');
if (!is_string($outDir) || $outDir === '') {
    $outDir = '/c/out';
}
if (!is_dir($outDir)) {
    @mkdir($outDir, 0775, true);
}

$entityId = '01KPRT14PHN0R6YGTG9WSRHG95';
$ids = ['TERM_2025', 'TERM_2026'];

foreach ([
    'bs'  => FinancialStatementKind::BalanceSheet,
    'pl'  => FinancialStatementKind::ProfitAndLoss,
    'all' => FinancialStatementKind::All,
] as $slug => $kind) {
    $multi = $useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
        entityId: $entityId,
        fiscalTermIds: $ids,
        kind: $kind,
    ));
    $bytes = $generator->render($multi);
    $path = rtrim($outDir, '/\\') . '/multi-period-' . $slug . '.pdf';
    file_put_contents($path, $bytes);
    printf("Wrote %s (%d bytes)\n", $path, strlen($bytes));
}
