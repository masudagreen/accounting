<?php

declare(strict_types=1);

namespace App\Infrastructure\LegacyBridge;

use App\Application\Service\FinancialStatementService;
use App\Application\Service\TrialBalanceService;
use App\Infrastructure\Persistence\Mariadb\MariadbAccountTreeRepository;
use App\Infrastructure\Persistence\Mariadb\MariadbJournalRepository;
use PDO;

/**
 * 既存 PHP UI (グローバル PDO ベース) から新ドメインを呼ぶための
 * 簡易 DI コンテナ.
 *
 * 使い方 (既存 UI 側):
 *
 *   require_once $pathTop . '/vendor/autoload.php';
 *   $facade = \App\Infrastructure\LegacyBridge\BridgeContainer::trialBalanceFacade($pdo);
 *   $dto    = $facade->build(pdo: $pdo, idEntity: $idEntity, numFiscalPeriod: $numFiscalPeriod);
 *
 * このクラスは static ファクトリのみを持つ. 状態を持たない.
 */
final class BridgeContainer
{
    /**
     * LegacyTrialBalanceFacade を生成して返す.
     */
    public static function trialBalanceFacade(PDO $pdo): LegacyTrialBalanceFacade
    {
        $journalRepo = new MariadbJournalRepository($pdo);
        $treeRepo    = new MariadbAccountTreeRepository($pdo);
        $service     = new TrialBalanceService($journalRepo, $treeRepo);

        return new LegacyTrialBalanceFacade($service);
    }

    /**
     * LegacyFinancialStatementFacade を生成して返す.
     */
    public static function financialStatementFacade(PDO $pdo): LegacyFinancialStatementFacade
    {
        $journalRepo = new MariadbJournalRepository($pdo);
        $treeRepo    = new MariadbAccountTreeRepository($pdo);
        $service     = new FinancialStatementService($journalRepo, $treeRepo);

        return new LegacyFinancialStatementFacade($service);
    }
}
