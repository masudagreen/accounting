#!/usr/bin/env php
<?php

/**
 * 帳票出力 CLI ツール.
 *
 * 使用例:
 *   php bin/print-report.php blue-return 1 14 > report.html
 *   php bin/print-report.php blue-return 1 14 --format=pdf > report.pdf
 *   php bin/print-report.php corporate-pl 1 14 > corporate-pl.html
 *   php bin/print-report.php corporate-bs 1 14 > corporate-bs.html
 *   php bin/print-report.php corporate-ss 1 14 > corporate-ss.html
 *   php bin/print-report.php deposits 1 14 > deposits.html
 *   php bin/print-report.php receivables 1 14 > receivables.html
 *   php bin/print-report.php payables 1 14 > payables.html
 *   php bin/print-report.php loans 1 14 > loans.html
 *
 * 環境変数 (DB接続時):
 *   GOLDEN_DB_HOST, GOLDEN_DB_PORT, GOLDEN_DB_USER, GOLDEN_DB_PASS, GOLDEN_DB_NAME
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Application\Dto\BalanceSheetDto;
use App\Application\Dto\ProfitAndLossDto;
use App\Domain\FinancialStatement\EquitySection;
use App\Domain\FinancialStatement\StatementOfEquity;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Report\BlueReturn\BlueReturnData;
use App\Domain\Report\BlueReturn\BlueReturnRenderer;
use App\Domain\Report\CorporateFinancialStatements\CorporateBsData;
use App\Domain\Report\CorporateFinancialStatements\CorporateBsRenderer;
use App\Domain\Report\CorporateFinancialStatements\CorporatePlData;
use App\Domain\Report\CorporateFinancialStatements\CorporatePlRenderer;
use App\Domain\Report\CorporateFinancialStatements\CorporateSsData;
use App\Domain\Report\CorporateFinancialStatements\CorporateSsRenderer;
use App\Domain\Report\DetailedAccount\AccountBreakdownRow;
use App\Domain\Report\DetailedAccount\AccountsPayableBreakdown;
use App\Domain\Report\DetailedAccount\AccountsReceivableBreakdown;
use App\Domain\Report\DetailedAccount\DepositsBreakdown;
use App\Domain\Report\DetailedAccount\DetailedAccountRenderer;
use App\Domain\Report\DetailedAccount\LoanRow;
use App\Domain\Report\DetailedAccount\LoansPayableBreakdown;
use App\Infrastructure\Pdf\DompdfPdfRenderer;

// -----------------------------------------------------------------------
// 引数パース
// -----------------------------------------------------------------------

$args = $argv;
array_shift($args); // スクリプト名を除去

$reportType = $args[0] ?? null;
$format     = 'html';

// --format=pdf オプションの解析
$filteredArgs = [];
foreach ($args as $arg) {
    if (str_starts_with($arg, '--format=')) {
        $format = substr($arg, strlen('--format='));
    } else {
        $filteredArgs[] = $arg;
    }
}

if ($reportType === null || $reportType === '--help' || $reportType === '-h') {
    fwrite(STDERR, <<<HELP
    使用法: php bin/print-report.php <様式> [オプション]

    様式:
      blue-return     青色申告決算書（損益計算書 + 貸借対照表）
      corporate-pl    法人 損益計算書
      corporate-bs    法人 貸借対照表
      corporate-ss    法人 株主資本等変動計算書
      deposits        預貯金等の内訳書
      receivables     売掛金の内訳書
      payables        買掛金の内訳書
      loans           借入金の内訳書

    オプション:
      --format=html   HTML 出力（デフォルト）
      --format=pdf    PDF 出力（dompdf 使用）

    HELP);
    exit(1);
}

// -----------------------------------------------------------------------
// スタブデータ (DB未接続時のデモ用)
// -----------------------------------------------------------------------

$period = FiscalPeriod::of(2024, 1, 12, 14);

$plDto = new ProfitAndLossDto(
    sales: 12_500_000,
    costOfSales: 5_000_000,
    grossProfit: 7_500_000,
    sellingAndAdmin: 3_000_000,
    operatingIncome: 4_500_000,
    nonOperatingIncome: 150_000,
    nonOperatingExpenses: 80_000,
    ordinaryIncome: 4_570_000,
    extraordinaryIncome: 0,
    extraordinaryLosses: 0,
    incomeBeforeTax: 4_570_000,
    tax: 0,
    netIncome: 4_570_000,
);

$bsSimpleDto = new BalanceSheetDto(
    totalAssets: 20_000_000,
    totalLiabilities: 8_000_000,
    totalEquity: 12_000_000,
);

$bsData = new CorporateBsData(
    companyName: '株式会社サンプル',
    fiscalPeriod: $period,
    currentAssets: 12_000_000,
    fixedAssets: 8_000_000,
    totalAssets: 20_000_000,
    currentLiabilities: 5_000_000,
    fixedLiabilities: 3_000_000,
    totalLiabilities: 8_000_000,
    capitalStock: 5_000_000,
    retainedEarnings: 7_000_000,
    totalEquity: 12_000_000,
);

$equity = new StatementOfEquity(
    openingBalances: [
        EquitySection::CapitalStock->value     => Money::ofYen(5_000_000),
        EquitySection::RetainedEarnings->value => Money::ofYen(2_430_000),
    ],
    changes: [],
    closingBalances: [
        EquitySection::CapitalStock->value     => Money::ofYen(5_000_000),
        EquitySection::RetainedEarnings->value => Money::ofYen(7_000_000),
    ],
    totalEquityOpening: Money::ofYen(7_430_000),
    totalChange: Money::ofYen(4_570_000),
    totalEquityClosing: Money::ofYen(12_000_000),
);

$depositRows = [
    new AccountBreakdownRow('東京銀行 渋谷支店', '東京都渋谷区', 5_000_000, '普通預金'),
    new AccountBreakdownRow('大阪信用金庫 梅田支店', '大阪府大阪市', 3_200_000, '当座預金'),
];

$receivableRows = [
    new AccountBreakdownRow('株式会社得意先A', '東京都千代田区', 2_500_000, ''),
    new AccountBreakdownRow('有限会社得意先B', '神奈川県横浜市', 800_000, ''),
];

$payableRows = [
    new AccountBreakdownRow('株式会社仕入先X', '埼玉県さいたま市', 1_200_000, ''),
];

$loanRows = [
    new LoanRow('日本政策金融公庫', '東京都千代田区', 8_000_000, 96_000, '1.20'),
];

// -----------------------------------------------------------------------
// レンダリング
// -----------------------------------------------------------------------

$html = match ($reportType) {
    'blue-return' => (new BlueReturnRenderer())->render(
        new BlueReturnData('テスト商店', $period, $plDto, $bsSimpleDto),
    ),
    'corporate-pl' => (new CorporatePlRenderer())->render(
        new CorporatePlData('株式会社サンプル', $period, $plDto),
    ),
    'corporate-bs' => (new CorporateBsRenderer())->render($bsData),
    'corporate-ss' => (new CorporateSsRenderer())->render(
        new CorporateSsData('株式会社サンプル', $period, $equity),
    ),
    'deposits' => (new DetailedAccountRenderer())->renderDeposits(
        new DepositsBreakdown('株式会社サンプル', $period, $depositRows),
    ),
    'receivables' => (new DetailedAccountRenderer())->renderReceivables(
        new AccountsReceivableBreakdown('株式会社サンプル', $period, $receivableRows),
    ),
    'payables' => (new DetailedAccountRenderer())->renderPayables(
        new AccountsPayableBreakdown('株式会社サンプル', $period, $payableRows),
    ),
    'loans' => (new DetailedAccountRenderer())->renderLoans(
        new LoansPayableBreakdown('株式会社サンプル', $period, $loanRows),
    ),
    default => throw new \InvalidArgumentException(sprintf('Unknown report type: %s', $reportType)),
};

// -----------------------------------------------------------------------
// 出力
// -----------------------------------------------------------------------

if ($format === 'pdf') {
    $pdf = (new DompdfPdfRenderer())->render($html);
    fwrite(STDOUT, $pdf);
} else {
    fwrite(STDOUT, $html);
}
