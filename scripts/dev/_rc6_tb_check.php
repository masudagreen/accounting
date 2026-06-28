<?php

declare(strict_types=1);

// RC-6 / RC-7: TrialBalance の数値一致 + 応答時間実測
// インラインで全 entity x 全期 をループし、UseCase を呼んで集計と時間を出す。

use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Container\ContainerBootstrap;

require __DIR__ . '/../../vendor/autoload.php';

$pdo = new PDO(
    'mysql:host=db;port=3306;dbname=rucaro;charset=utf8mb4',
    'root',
    'root',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false],
);
$pdo->exec("SET time_zone = '+00:00'");

$container = ContainerBootstrap::build($pdo);
/** @var QueryTrialBalanceUseCase $uc */
$uc = $container->getTyped(QueryTrialBalanceUseCase::class);

// fetch all (entity_id, fiscal_term_id, fiscal_period, start_date, end_date)
$rows = $pdo->query("
    SELECT
        e.id AS eid_bin, e.name AS ename,
        ft.id AS ftid_bin, ft.fiscal_period, ft.start_date, ft.end_date
    FROM fiscal_terms ft
    JOIN entities e ON e.id = ft.entity_id
    ORDER BY e.name, ft.fiscal_period
")->fetchAll(PDO::FETCH_ASSOC);

printf("entities x periods: %d combos\n", count($rows));
printf("%-22s %-4s %-12s %-12s %12s %12s %12s %8s\n",
    'entity', 'pd', 'start', 'asOf', 'tb_dr', 'tb_cr', 'tb_rows', 'ms');

$totalMs = 0.0;
$mismatches = [];
foreach ($rows as $r) {
    $entityIdHex = UlidGenerator::encode($r['eid_bin']);
    $ftIdHex = UlidGenerator::encode($r['ftid_bin']);
    $start = new DateTimeImmutable($r['start_date']);
    $asOf = new DateTimeImmutable($r['end_date']);

    $input = new QueryTrialBalanceUseCaseInput(
        entityId: $entityIdHex,
        fiscalTermId: $ftIdHex,
        fiscalTermStartDate: $start,
        asOf: $asOf,
    );

    $t0 = microtime(true);
    try {
        $tb = $uc->execute($input);
    } catch (Throwable $e) {
        printf("EXCEPTION at entity=%s pd=%d: %s\n  %s:%d\n",
            $r['ename'], $r['fiscal_period'], $e->getMessage(), $e->getFile(), $e->getLine());
        continue;
    }
    $elapsedMs = (microtime(true) - $t0) * 1000;
    $totalMs += $elapsedMs;

    $sumDr = 0.0;
    $sumCr = 0.0;
    foreach ($tb->rows as $row) {
        $sumDr += (float) $row->debitTotal;
        $sumCr += (float) $row->creditTotal;
    }

    printf("%-22s %-4d %-12s %-12s %12s %12s %12d %8.1f\n",
        mb_substr($r['ename'], 0, 18),
        (int) $r['fiscal_period'],
        $r['start_date'], $r['end_date'],
        number_format($sumDr, 0),
        number_format($sumCr, 0),
        count($tb->rows),
        $elapsedMs,
    );

    // legacy 側との比較
    $stmt = $pdo->prepare("
        SELECT SUM(amount) AS dr
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.entry_id
        WHERE je.entity_id = :ent AND je.fiscal_term_id = :ft AND jel.side='debit' AND je.status IN ('posted','approved')
    ");
    $stmt->bindValue(':ent', $r['eid_bin'], PDO::PARAM_LOB);
    $stmt->bindValue(':ft', $r['ftid_bin'], PDO::PARAM_LOB);
    $stmt->execute();
    $rawDr = (float) ($stmt->fetchColumn() ?: 0);

    if (abs($sumDr - $rawDr) > 0.01) {
        $mismatches[] = sprintf("entity=%s pd=%d UseCase_dr=%s vs raw_dr=%s",
            $r['ename'], $r['fiscal_period'], number_format($sumDr), number_format($rawDr));
    }
}

printf("\ntotal time: %.1f ms (avg %.1f ms / period)\n",
    $totalMs, $totalMs / max(count($rows), 1));

if ($mismatches) {
    print "MISMATCHES:\n";
    foreach ($mismatches as $m) print "  $m\n";
} else {
    print "All UseCase totals match raw SQL totals.\n";
}
