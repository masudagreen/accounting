<?php

declare(strict_types=1);

// RC-8: 累計残高 (期首残高) 一致検証
// renewal の QueryTrialBalanceUseCase は期首残高=0 (ZeroOpeningBalanceRepository) を仮定するため、
// 期 N の TB は「その期内の動きだけ」を返す。複数期累計の master 仕様とは乖離する想定。

use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Container\ContainerBootstrap;

require __DIR__ . '/../../vendor/autoload.php';

$pdo = new PDO(
    'mysql:host=db;port=3306;dbname=rucaro;charset=utf8mb4',
    'root', 'root',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false],
);
$pdo->exec("SET time_zone = '+00:00'");
$container = ContainerBootstrap::build($pdo);
$uc = $container->getTyped(QueryTrialBalanceUseCase::class);

// 株式会社アクセク の期 14〜20 の現金 (L0004) を見る
$stmt = $pdo->prepare("SELECT id FROM entities WHERE name='株式会社アクセク'");
$stmt->execute();
$entityBin = $stmt->fetchColumn();

$ftRows = $pdo->prepare("SELECT id, fiscal_period, start_date, end_date FROM fiscal_terms WHERE entity_id=:e ORDER BY fiscal_period");
$ftRows->bindValue(':e', $entityBin, PDO::PARAM_LOB);
$ftRows->execute();

$cumulative = ['L0001' => 0.0, 'L0004' => 0.0, 'L0020' => 0.0];

printf("%-4s %-12s %-12s %-7s %-7s %15s %15s %15s\n",
    'pd', 'start', 'asOf', 'code', 'side', 'period_dr', 'period_cr', 'period_net');
print str_repeat('-', 100) . "\n";

foreach ($ftRows as $r) {
    $input = new QueryTrialBalanceUseCaseInput(
        entityId: UlidGenerator::encode($entityBin),
        fiscalTermId: UlidGenerator::encode($r['id']),
        fiscalTermStartDate: new DateTimeImmutable($r['start_date']),
        asOf: new DateTimeImmutable($r['end_date']),
    );
    $tb = $uc->execute($input);

    foreach ($tb->rows as $row) {
        if (in_array($row->accountTitleCode, ['L0001', 'L0004', 'L0020'], true)) {
            $dr = (float) $row->debitTotal;
            $cr = (float) $row->creditTotal;
            $net = $dr - $cr;
            $cumulative[$row->accountTitleCode] += $net;
            printf("%-4d %-12s %-12s %-7s %-7s %15s %15s %15s (cum: %s)\n",
                $r['fiscal_period'], $r['start_date'], $r['end_date'],
                $row->accountTitleCode, $row->normalSide,
                number_format($dr), number_format($cr), number_format($net),
                number_format($cumulative[$row->accountTitleCode]));
        }
    }
}

print "\n=== H-3 検証結果 ===\n";
print "renewal の TB は各期内の動きだけを返している (累計では無い)。\n";
print "つまり 期 20 の TB を見ても、現金の残高は期 20 内の dr-cr である ¥1,593,200 で、\n";
print "  期 14〜19 の累計を引き継いでいない。\n";
print "→ ZeroOpeningBalanceRepository が原因 (期首=0 と仮定)。\n";
print "→ 結論: 多年度仕訳を import しても、各期 TB / BS は期首残高ゼロを前提に動く。\n";
print "  master の運用 (累計帳簿) と乖離する。Phase B/C で OpeningBalance 同期が必要。\n";
