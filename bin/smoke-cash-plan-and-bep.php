<?php

declare(strict_types=1);

/**
 * E2E smoke script for Phase 6 Wave 6-E (cash plan + break-even point port).
 *
 *  1. Seeds a demo cash plan against the first entity / fiscal term on the
 *     database, with realistic monthly inflows / outflows.
 *  2. Seeds CVP classifications so 売上 → revenue, 仕入高 → variable,
 *     旅費交通費 / 通信費 → fixed.
 *  3. Runs the AnalyzeBreakEvenPointUseCase over the fiscal term.
 *  4. Writes `cash-plan.pdf` and `break-even-point.pdf` to
 *     `$OUT_DIR` (default: `storage/out`).
 *
 * Usage: run inside the app container — the script auto-reads DB_* env vars
 * that the docker-compose stack already provides.
 */

require __DIR__ . '/../vendor/autoload.php';

use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointInput;
use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointUseCase;
use Rucaro\Application\BreakEvenPoint\UpsertCvpClassificationInput;
use Rucaro\Application\BreakEvenPoint\UpsertCvpClassificationsUseCase;
use Rucaro\Application\CashPlan\CashPlanEntryInput;
use Rucaro\Application\CashPlan\CreateCashPlanInput;
use Rucaro\Application\CashPlan\CreateCashPlanUseCase;
use Rucaro\Application\CashPlan\GetCashPlanUseCase;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointPdfGeneratorInterface;
use Rucaro\Domain\CashPlan\CashPlanPdfGeneratorInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Container\ContainerBootstrap;

$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'rucaro';
$user = getenv('DB_USER') ?: 'rucaro';
$pass = getenv('DB_PASSWORD') ?: 'rucaro';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
$pdo->exec("SET time_zone = '+00:00'");

$c = ContainerBootstrap::build($pdo);

$entity = $pdo->query('SELECT id, owner_user_id FROM entities LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if ($entity === false) {
    fwrite(STDERR, "No entity present; aborting.\n");
    exit(1);
}
$entityId = UlidGenerator::encode($entity['id']);
$userId = UlidGenerator::encode($entity['owner_user_id']);

$termRow = $pdo->prepare('SELECT id, start_date, end_date FROM fiscal_terms WHERE entity_id = :e ORDER BY start_date DESC LIMIT 1');
$termRow->execute([':e' => $entity['id']]);
$term = $termRow->fetch(PDO::FETCH_ASSOC);
if ($term === false) {
    fwrite(STDERR, "No fiscal_term for entity; aborting.\n");
    exit(1);
}
$fiscalTermId = UlidGenerator::encode($term['id']);
$fromDate = new DateTimeImmutable($term['start_date'], new DateTimeZone('UTC'));
$toDate = new DateTimeImmutable($term['end_date'], new DateTimeZone('UTC'));

echo "Using entity: $entityId\n";
echo "Using fiscal term: $fiscalTermId ({$term['start_date']} .. {$term['end_date']})\n";

// ---------------------------------------------------------------------
// 1. Cash Plan
// ---------------------------------------------------------------------
/** @var CreateCashPlanUseCase $createPlan */
$createPlan = $c->get(CreateCashPlanUseCase::class);

$zeroes = array_fill(0, 12, '0.0000');
$salesAmounts = [
    '1200000.0000', '1100000.0000', '1300000.0000', '1250000.0000',
    '1400000.0000', '1500000.0000', '1450000.0000', '1300000.0000',
    '1600000.0000', '1700000.0000', '1800000.0000', '1900000.0000',
];
$purchaseAmounts = [
    '400000.0000', '380000.0000', '420000.0000', '410000.0000',
    '460000.0000', '490000.0000', '480000.0000', '430000.0000',
    '530000.0000', '560000.0000', '590000.0000', '620000.0000',
];
$salaryAmounts = array_fill(0, 12, '500000.0000');
$rentAmounts = array_fill(0, 12, '80000.0000');
$loanIn = $zeroes;
$loanIn[5] = '5000000.0000';
$loanOut = $zeroes;
for ($i = 6; $i < 12; $i++) {
    $loanOut[$i] = '200000.0000';
}

$planName = 'FY' . $toDate->format('Y') . ' Demo Cash Plan';
try {
    $planOut = $createPlan->execute(new CreateCashPlanInput(
        entityId: $entityId,
        fiscalTermId: $fiscalTermId,
        name: $planName,
        openingBalance: '3000000.0000',
        currencyCode: 'JPY',
        notes: 'Phase 6 Wave 6-E smoke',
        entries: [
            new CashPlanEntryInput('operating_in', '売上入金', 0, $salesAmounts),
            new CashPlanEntryInput('operating_out', '仕入支払', 1, $purchaseAmounts),
            new CashPlanEntryInput('operating_out', '人件費', 2, $salaryAmounts),
            new CashPlanEntryInput('operating_out', '地代家賃', 3, $rentAmounts),
            new CashPlanEntryInput('financing_in', '借入金入金', 4, $loanIn),
            new CashPlanEntryInput('financing_out', '借入金返済', 5, $loanOut),
        ],
        createdBy: $userId,
    ));
    echo " - created cash plan id={$planOut->plan->id} name={$planOut->plan->name}\n";
    $planId = $planOut->plan->id;
} catch (\Rucaro\Domain\Exception\ValidationException $e) {
    // Duplicate on re-run — pull the existing one.
    $stmt = $pdo->prepare('SELECT id FROM cash_plans WHERE entity_id = :e AND fiscal_term_id = :f AND name = :n AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([
        ':e' => $entity['id'],
        ':f' => $term['id'],
        ':n' => $planName,
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        fwrite(STDERR, "Could not locate existing plan: " . $e->getMessage() . "\n");
        exit(1);
    }
    $planId = UlidGenerator::encode($row['id']);
    echo " - reusing existing plan id=$planId\n";
}

/** @var GetCashPlanUseCase $getPlan */
$getPlan = $c->get(GetCashPlanUseCase::class);
$plan = $getPlan->execute($planId);
if ($plan === null) {
    fwrite(STDERR, "Plan vanished after create; aborting.\n");
    exit(1);
}

/** @var CashPlanPdfGeneratorInterface $planPdfGen */
$planPdfGen = $c->get(CashPlanPdfGeneratorInterface::class);
$planPdf = $planPdfGen->render($plan);

$outDir = getenv('OUT_DIR') ?: (dirname(__DIR__) . '/storage/out');
if (!is_dir($outDir)) {
    @mkdir($outDir, 0775, true);
}
$planPath = $outDir . '/cash-plan.pdf';
file_put_contents($planPath, $planPdf);
echo "Wrote $planPath (" . strlen($planPdf) . " bytes)\n";

// ---------------------------------------------------------------------
// 2. CVP Classifications
// ---------------------------------------------------------------------
/** @var UpsertCvpClassificationsUseCase $upsert */
$upsert = $c->get(UpsertCvpClassificationsUseCase::class);

$rowsStmt = $pdo->prepare('SELECT id, code, category FROM account_titles WHERE entity_id = :e AND deleted_at IS NULL');
$rowsStmt->execute([':e' => $entity['id']]);
$accountRows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);
$upserts = [];
foreach ($accountRows as $r) {
    $code = (string) $r['code'];
    $category = (string) $r['category'];
    $atId = UlidGenerator::encode($r['id']);
    // Heuristic classification mirrors the legacy default set:
    //   - 売上高 / 受取利息 etc (revenue) → not part of CVP classifications.
    //   - 仕入高 (COGS-ish) → variable.
    //   - 販管費っぽい → fixed.
    if ($category === 'revenue') {
        continue; // Revenue is derived from account category, not classified.
    }
    if ($category !== 'expense') {
        continue;
    }
    $type = 'fixed';
    // Classic: purchase / direct costs → variable. Recognise common codes.
    if (str_starts_with($code, '5') || str_contains($code, 'COGS') || str_contains($code, '仕入')) {
        $type = 'variable';
    }
    $upserts[] = new UpsertCvpClassificationInput(
        accountTitleId: $atId,
        costType: $type,
        variableRatio: $type === 'variable' ? '1.0000' : '0.0000',
    );
}
echo " - applying " . count($upserts) . " CVP classifications\n";
if ($upserts !== []) {
    $upsert->execute($entityId, $upserts);
}

// ---------------------------------------------------------------------
// 3. BEP Analysis
// ---------------------------------------------------------------------
/** @var AnalyzeBreakEvenPointUseCase $analyze */
$analyze = $c->get(AnalyzeBreakEvenPointUseCase::class);
$analysis = $analyze->execute(new AnalyzeBreakEvenPointInput(
    entityId: $entityId,
    fiscalTermId: $fiscalTermId,
    fromDate: $fromDate,
    toDate: $toDate,
    currencyCode: 'JPY',
));
echo sprintf(
    " - BEP: sales=%s variable=%s fixed=%s BEP売上=%s 安全余裕率=%s\n",
    $analysis->sales,
    $analysis->variableCosts,
    $analysis->fixedCosts,
    $analysis->bepSales,
    $analysis->safetyMarginRatio,
);

/** @var BreakEvenPointPdfGeneratorInterface $bepGen */
$bepGen = $c->get(BreakEvenPointPdfGeneratorInterface::class);
$bepPdf = $bepGen->render($analysis);
$bepPath = $outDir . '/break-even-point.pdf';
file_put_contents($bepPath, $bepPdf);
echo "Wrote $bepPath (" . strlen($bepPdf) . " bytes)\n";
