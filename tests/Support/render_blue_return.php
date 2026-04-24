<?php

declare(strict_types=1);

/**
 * Generates a demo 青色申告決算書 PDF using the DompdfBlueReturnGenerator.
 *
 * Usage (from repo root):
 *   docker run --rm -v "$PWD:/app" -w /app php:8.3-cli php tests/Support/render_blue_return.php /app/out.pdf
 */

use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Domain\BlueReturn\Service\BlueReturnBuilder;
use Rucaro\Infrastructure\BlueReturn\DompdfBlueReturnGenerator;

require __DIR__ . '/../../vendor/autoload.php';

$repoRoot = dirname(__DIR__, 2);
$templateDir = $repoRoot . '/storage/templates/blue_return';
$compileDir = $repoRoot . '/storage/cache/smarty_blue_return';
$fontDir = $repoRoot . '/storage/fonts';
@mkdir($compileDir, 0775, true);

$builder = new BlueReturnBuilder();
$snapshot = $builder->build(
    formType: BlueReturnFormType::General,
    revenueByAccount: ['売上高' => '12000000.0000', '雑収入' => '120000.0000'],
    costOfSalesByAccount: ['仕入高' => '3800000.0000', '期末棚卸' => '-420000.0000'],
    expensesByAccount: [
        '給料賃金'   => '2400000.0000',
        '地代家賃'   => '1200000.0000',
        '水道光熱費' => '240000.0000',
        '通信費'     => '180000.0000',
        '減価償却費' => '480000.0000',
    ],
    monthlyRows: [
        ['month' => 1,  'sales' => '980000.0000',  'purchase' => '310000.0000', 'salary' => '200000.0000'],
        ['month' => 2,  'sales' => '900000.0000',  'purchase' => '290000.0000', 'salary' => '200000.0000'],
        ['month' => 3,  'sales' => '1100000.0000', 'purchase' => '340000.0000', 'salary' => '200000.0000'],
        ['month' => 4,  'sales' => '1000000.0000', 'purchase' => '300000.0000', 'salary' => '200000.0000'],
        ['month' => 5,  'sales' => '960000.0000',  'purchase' => '300000.0000', 'salary' => '200000.0000'],
        ['month' => 6,  'sales' => '1080000.0000', 'purchase' => '330000.0000', 'salary' => '200000.0000'],
        ['month' => 7,  'sales' => '1020000.0000', 'purchase' => '310000.0000', 'salary' => '200000.0000'],
        ['month' => 8,  'sales' => '980000.0000',  'purchase' => '300000.0000', 'salary' => '200000.0000'],
        ['month' => 9,  'sales' => '1040000.0000', 'purchase' => '320000.0000', 'salary' => '200000.0000'],
        ['month' => 10, 'sales' => '1100000.0000', 'purchase' => '340000.0000', 'salary' => '200000.0000'],
        ['month' => 11, 'sales' => '940000.0000',  'purchase' => '290000.0000', 'salary' => '200000.0000'],
        ['month' => 12, 'sales' => '900000.0000',  'purchase' => '270000.0000', 'salary' => '200000.0000'],
    ],
    breakdown: [
        'depreciation' => [
            ['name' => '社用車 (軽)', 'acquisitionCost' => '1200000', 'method' => '定率法', 'usefulLifeYears' => '6', 'periodDepreciation' => '240000'],
            ['name' => 'PC',         'acquisitionCost' => '300000',  'method' => '定額法', 'usefulLifeYears' => '4', 'periodDepreciation' => '75000'],
        ],
        'allowance' => [
            ['label' => '貸倒引当金（一括評価）', 'amount' => '60000'],
        ],
        'rent' => [
            ['label' => '○○商会（店舗）', 'amount' => '1200000'],
        ],
        'interest' => [
            ['label' => '△△銀行（事業性融資）', 'amount' => '84000'],
        ],
        'taxAccountant' => [
            ['label' => '□□税理士事務所', 'amount' => '180000'],
        ],
    ],
    assetsByAccount: [
        '現金'       => '420000.0000',
        '普通預金'   => '1800000.0000',
        '売掛金'     => '860000.0000',
        '棚卸資産'   => '420000.0000',
        '車両運搬具' => '720000.0000',
        '工具器具備品' => '180000.0000',
    ],
    liabilitiesByAccount: [
        '買掛金'   => '340000.0000',
        '未払金'   => '120000.0000',
        '長期借入金' => '1800000.0000',
    ],
    equityByAccount: [
        '元入金' => '900000.0000',
    ],
);

$now = new \DateTimeImmutable('2026-04-21T12:00:00Z');
$form = new BlueReturnForm(
    id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
    entityId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
    fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
    formType: BlueReturnFormType::General,
    status: BlueReturnStatus::Draft,
    snapshot: $snapshot,
    finalizedAt: null,
    createdBy: '01HAAAAAAAAAAAAAAAAAAAAAB3',
    createdAt: $now,
    updatedAt: $now,
);

$generator = new DompdfBlueReturnGenerator(
    templateDir: $templateDir,
    compileDir: $compileDir,
    fontDir: $fontDir,
);

$pdf = $generator->render($form);

$outPath = $argv[1] ?? ($repoRoot . '/blue-return.pdf');
file_put_contents($outPath, $pdf);
echo "Wrote " . strlen($pdf) . " bytes to " . $outPath . PHP_EOL;
