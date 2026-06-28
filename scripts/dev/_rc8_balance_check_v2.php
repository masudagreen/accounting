<?php

declare(strict_types=1);

/**
 * RC-8 acceptance check (Phase B-3 verification).
 *
 * Asserts that QueryTrialBalanceUseCase, after the OpeningBalanceRepository
 * is wired (B-3a), the use case folds opening (B-3b), and the importer
 * populates `opening_balances` (B-3c), reports the cumulative cash balance
 * for idEntity=1, 期20 as ¥5,490,437.
 */

use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Container\ContainerBootstrap;

require __DIR__ . '/../../vendor/autoload.php';

$pdo = new PDO(
    'mysql:host=db;port=3306;dbname=rucaro;charset=utf8mb4',
    'root',
    'root',
    [
        PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
);
$pdo->exec("SET time_zone = '+00:00'");

$container = ContainerBootstrap::build($pdo);
/** @var QueryTrialBalanceUseCase $uc */
$uc = $container->getTyped(QueryTrialBalanceUseCase::class);

// Resolve entity / fiscal term ULIDs for legacy idEntity=1 / period 20
$stmt = $pdo->prepare("SELECT id, name FROM entities WHERE name='株式会社アクセク'");
$stmt->execute();
$entityRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($entityRow === false) {
    fwrite(STDERR, "entity not found\n");
    exit(2);
}
$entityBin = $entityRow['id'];

$stmt = $pdo->prepare(
    'SELECT id, fiscal_period, start_date, end_date
       FROM fiscal_terms
      WHERE entity_id = :e AND fiscal_period = 20'
);
$stmt->bindValue(':e', $entityBin, PDO::PARAM_LOB);
$stmt->execute();
$termRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($termRow === false) {
    fwrite(STDERR, "fiscal term 20 not found\n");
    exit(2);
}

$input = new QueryTrialBalanceUseCaseInput(
    entityId: UlidGenerator::encode($entityBin),
    fiscalTermId: UlidGenerator::encode($termRow['id']),
    fiscalTermStartDate: new DateTimeImmutable($termRow['start_date']),
    asOf: new DateTimeImmutable($termRow['end_date']),
);

$tb = $uc->execute($input);

$cash = null;
foreach ($tb->rows as $row) {
    if ($row->accountTitleCode === 'L0004') {
        $cash = $row;
        break;
    }
}

if ($cash === null) {
    fwrite(STDERR, "cash row (L0004) not found in TB\n");
    exit(3);
}

printf("entity        : %s\n", $entityRow['name']);
printf("fiscal_period : %d (%s..%s)\n", $termRow['fiscal_period'], $termRow['start_date'], $termRow['end_date']);
printf("account       : %s %s (%s, %s-normal)\n", $cash->accountTitleCode, $cash->accountTitleName, $cash->accountCategory, $cash->normalSide);
printf("opening       : %s\n", number_format((float) $cash->openingBalance));
printf("period dr     : %s\n", number_format((float) $cash->debitTotal));
printf("period cr     : %s\n", number_format((float) $cash->creditTotal));
printf("balance       : %s\n", number_format((float) $cash->balance));

$expected = 5490437.0;
$actual   = (float) $cash->balance;
$delta    = abs($actual - $expected);

printf("\nexpected      : %s\n", number_format($expected));
printf("delta         : %s\n", number_format($delta));

if ($delta <= 1.0) {
    print "RC-8 PASS\n";
    exit(0);
}
print "RC-8 FAIL\n";
exit(1);
