<?php

declare(strict_types=1);

namespace App\Infrastructure\LegacyBridge;

use App\Application\Service\FinancialStatementService;
use App\Domain\TrialBalance\OpeningBalances;

/**
 * PL / BS を既存 Smarty テンプレートへ渡しやすい配列で返すファサード.
 *
 * 呼び出し例 (back/class/else/plugin/accounting/jpn/FinancialStatement.php):
 *
 *   $facade = BridgeContainer::financialStatementFacade($classDb->getHandle());
 *   $pl = $facade->buildProfitAndLoss(idEntity: $id, numFiscalPeriod: $num);
 *   $bs = $facade->buildBalanceSheet(idEntity: $id, numFiscalPeriod: $num);
 *   $classSmarty->assign('newPl', $pl);
 *   $classSmarty->assign('newBs', $bs);
 */
final class LegacyFinancialStatementFacade
{
    public function __construct(
        private readonly FinancialStatementService $service,
    ) {
    }

    /**
     * 損益計算書 DTO を配列で返す.
     *
     * @return array<string, int>
     */
    public function buildProfitAndLoss(
        int $idEntity,
        int $numFiscalPeriod,
        ?OpeningBalances $opening = null,
    ): array {
        return $this->service->buildProfitAndLoss(
            idEntity: $idEntity,
            numFiscalPeriod: $numFiscalPeriod,
            opening: $opening ?? OpeningBalances::empty(),
        );
    }

    /**
     * 貸借対照表 DTO を配列で返す.
     *
     * @return array<string, int>
     */
    public function buildBalanceSheet(
        int $idEntity,
        int $numFiscalPeriod,
        ?OpeningBalances $opening = null,
    ): array {
        return $this->service->buildBalanceSheet(
            idEntity: $idEntity,
            numFiscalPeriod: $numFiscalPeriod,
            opening: $opening ?? OpeningBalances::empty(),
        );
    }
}
