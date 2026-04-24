<?php

declare(strict_types=1);

/**
 * Phase 6 Wave 6-H-2 smoke-test harness.
 *
 * Builds a representative {@see StatementOfChangesInEquity} and writes
 * the PDF bytes to `/c/Users/yusuk/OneDrive/デスクトップ/rucaro-out/statement-of-changes-in-equity.pdf`.
 *
 * Not used at runtime — strictly an operator aid.
 */

use Rucaro\Domain\StatementOfChangesInEquity\Service\StatementOfChangesInEquityBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Infrastructure\StatementOfChangesInEquity\DompdfStatementOfChangesInEquityGenerator;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$repoRoot = dirname(__DIR__, 2);
$templateDir = $repoRoot . '/storage/templates/ss';
$compileDir = $repoRoot . '/storage/cache/smarty_ss';
$fontDir = $repoRoot . '/storage/fonts';
if (!is_dir($compileDir)) {
    mkdir($compileDir, 0775, true);
}
if (!is_dir($fontDir)) {
    mkdir($fontDir, 0775, true);
}

$builder = new StatementOfChangesInEquityBuilder();
$adjustments = [
    new SsManualAdjustment(
        id: '01HAAAAAAAAAAAAAAAAAAAAA01',
        entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
        fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
        sectionCode: SsSectionCode::CapitalStock,
        changeType: SsChangeType::NewIssue,
        amount: '5000000.0000',
        label: '新株発行 (募集株式)',
        sortOrder: 0,
        notes: null,
    ),
    new SsManualAdjustment(
        id: '01HAAAAAAAAAAAAAAAAAAAAA02',
        entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
        fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
        sectionCode: SsSectionCode::CapitalSurplus,
        changeType: SsChangeType::NewIssue,
        amount: '3000000.0000',
        label: '新株発行 (募集株式)',
        sortOrder: 1,
        notes: null,
    ),
    new SsManualAdjustment(
        id: '01HAAAAAAAAAAAAAAAAAAAAA03',
        entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
        fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
        sectionCode: SsSectionCode::RetainedEarnings,
        changeType: SsChangeType::Dividend,
        amount: '-12000000.0000',
        label: '剰余金の配当',
        sortOrder: 2,
        notes: null,
    ),
    new SsManualAdjustment(
        id: '01HAAAAAAAAAAAAAAAAAAAAA04',
        entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
        fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
        sectionCode: SsSectionCode::TreasuryStock,
        changeType: SsChangeType::TreasuryPurchase,
        amount: '-3000000.0000',
        label: '自己株式の取得',
        sortOrder: 3,
        notes: null,
    ),
];

$ss = $builder->build(
    entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
    fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
    fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
    toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
    currencyCode: 'JPY',
    openingBalances: [
        SsSectionCode::CapitalStock->value          => '50000000.0000',
        SsSectionCode::CapitalSurplus->value        => '20000000.0000',
        SsSectionCode::RetainedEarnings->value      => '180000000.0000',
        SsSectionCode::TreasuryStock->value         => '-2000000.0000',
        SsSectionCode::ValuationAdjustment->value   => '1500000.0000',
        SsSectionCode::StockAcquisitionRight->value => '800000.0000',
    ],
    adjustments: $adjustments,
    netIncome: '45000000.0000',
    generatedAt: new DateTimeImmutable('2026-04-21T12:00:00Z'),
);

$generator = new DompdfStatementOfChangesInEquityGenerator(
    templateDir: $templateDir,
    compileDir: $compileDir,
    fontDir: $fontDir,
);
$pdf = $generator->render($ss);

$outPath = $argv[1] ?? '/tmp/statement-of-changes-in-equity.pdf';
file_put_contents($outPath, $pdf);

echo 'Wrote ' . strlen($pdf) . " bytes to $outPath\n";
