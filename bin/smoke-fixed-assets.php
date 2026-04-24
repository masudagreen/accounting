<?php

declare(strict_types=1);

/**
 * E2E smoke script for Phase 6 Wave 6-D (fixed assets port).
 *
 * Seeds three sample fixed assets against the existing demo entity,
 * generates a depreciation schedule for the current fiscal term, posts
 * the resulting journal entries, and renders the asset ledger PDF to
 * `storage/out/fixed-assets.pdf`.
 *
 * Usage: run inside the app container or with a PHP 8.3 CLI pointed at
 * the Rucaro codebase.
 */

require __DIR__ . '/../vendor/autoload.php';

use Rucaro\Application\FixedAsset\CreateFixedAssetInput;
use Rucaro\Application\FixedAsset\CreateFixedAssetUseCase;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleInput;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleUseCase;
use Rucaro\Application\FixedAsset\GetFixedAssetLedgerInput;
use Rucaro\Application\FixedAsset\GetFixedAssetLedgerUseCase;
use Rucaro\Domain\FixedAsset\FixedAssetLedgerGeneratorInterface;
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

/** Helper to bridge DateTimeImmutable when running without `use` imports. */
function _dt(string $iso): \DateTimeImmutable
{
    return new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
}

$c = ContainerBootstrap::build($pdo);

// Look up the demo entity + fiscal term + user.
$entity = $pdo->query('SELECT id, owner_user_id FROM entities LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if ($entity === false) {
    fwrite(STDERR, "No entity present; aborting.\n");
    exit(1);
}
$entityId = UlidGenerator::encode($entity['id']);
$userId = UlidGenerator::encode($entity['owner_user_id']);
$term = $pdo->query("SELECT id, start_date, end_date FROM fiscal_terms WHERE entity_id = UNHEX('" . bin2hex($entity['id']) . "') ORDER BY start_date DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($term === false) {
    fwrite(STDERR, "No fiscal_term for entity; aborting.\n");
    exit(1);
}
$fiscalTermId = UlidGenerator::encode($term['id']);
$termStart = _dt($term['start_date']);
$termEnd   = _dt($term['end_date']);

echo "Using entity: $entityId\n";
echo "Using fiscal term: $fiscalTermId ($term[start_date] .. $term[end_date])\n";

/** @var CreateFixedAssetUseCase $createUseCase */
$createUseCase = $c->get(CreateFixedAssetUseCase::class);

$samples = [
    [
        'assetCode' => 'SL-001',
        'assetName' => '本社ビル 内装設備',
        'categoryCode' => 'building_fixtures',
        'acquisitionDate' => $termStart,
        'serviceStartDate' => $termStart,
        'acquisitionCost' => '3000000.0000',
        'residualValue' => '0.0000',
        'usefulLifeYears' => 15,
        'method' => 'straight_line',
    ],
    [
        'assetCode' => 'DB-001',
        'assetName' => '製造機械 NC-550',
        'categoryCode' => 'machinery',
        'acquisitionDate' => $termStart,
        'serviceStartDate' => $termStart,
        'acquisitionCost' => '5000000.0000',
        'residualValue' => '0.0000',
        'usefulLifeYears' => 10,
        'method' => 'declining_balance_2012',
    ],
    [
        'assetCode' => 'OS-001',
        'assetName' => 'PC (即時償却)',
        'categoryCode' => 'one_shot_small',
        'acquisitionDate' => $termStart,
        'serviceStartDate' => $termStart,
        'acquisitionCost' => '180000.0000',
        'residualValue' => '0.0000',
        'usefulLifeYears' => 1,
        'method' => 'one_shot',
    ],
];

foreach ($samples as $s) {
    try {
        $input = new CreateFixedAssetInput(
            entityId: $entityId,
            assetCode: $s['assetCode'],
            assetName: $s['assetName'],
            categoryCode: $s['categoryCode'],
            assetAccountTitleId: null,
            accumulatedDepreciationAccountTitleId: null,
            depreciationExpenseAccountTitleId: null,
            acquisitionDate: $s['acquisitionDate'],
            serviceStartDate: $s['serviceStartDate'],
            acquisitionCost: $s['acquisitionCost'],
            residualValue: $s['residualValue'],
            usefulLifeYears: $s['usefulLifeYears'],
            method: $s['method'],
            quantity: 1,
            departmentCode: null,
            note: null,
            createdBy: $userId,
        );
        $out = $createUseCase->execute($input);
        echo " - created {$s['assetCode']} id={$out->asset->id}\n";
    } catch (\Rucaro\Domain\Exception\ValidationException $e) {
        // Duplicate on re-run is fine.
        echo " - skipped {$s['assetCode']}: " . $e->getMessage() . "\n";
    }
}

/** @var GenerateDepreciationScheduleUseCase $gen */
$gen = $c->get(GenerateDepreciationScheduleUseCase::class);
$schedule = $gen->execute(new GenerateDepreciationScheduleInput(
    entityId: $entityId,
    fiscalTermId: $fiscalTermId,
    fiscalTermStart: $termStart,
    fiscalTermEnd: $termEnd,
));
foreach ($schedule->entries as $e) {
    echo " - schedule asset={$e->fixedAssetId} dep={$e->depreciationAmount} closing={$e->closingBookValue}\n";
}

/** @var GetFixedAssetLedgerUseCase $getLedger */
$getLedger = $c->get(GetFixedAssetLedgerUseCase::class);
$ledger = $getLedger->execute(new GetFixedAssetLedgerInput(
    entityId: $entityId,
    fiscalTermId: $fiscalTermId,
));

/** @var FixedAssetLedgerGeneratorInterface $gen2 */
$gen2 = $c->get(FixedAssetLedgerGeneratorInterface::class);
$pdf = $gen2->render($ledger);
$outDir = getenv('OUT_DIR') ?: dirname(__DIR__) . '/storage/out';
if (!is_dir($outDir)) {
    @mkdir($outDir, 0775, true);
}
$path = $outDir . '/fixed-assets.pdf';
file_put_contents($path, $pdf);
echo "Wrote $path (" . strlen($pdf) . " bytes)\n";
