<?php

declare(strict_types=1);

namespace App\Infrastructure\LegacyBridge;

use App\Application\Service\TrialBalanceService;
use App\Domain\TrialBalance\OpeningBalances;

/**
 * 試算表を既存 Smarty テンプレートへ渡しやすい配列で返すファサード.
 *
 * 呼び出し例 (back/class/else/plugin/accounting/jpn/TrialBalance.php):
 *
 *   $facade = BridgeContainer::trialBalanceFacade($classDb->getHandle());
 *   $result = $facade->build(
 *       idEntity: $idEntityCurrent,
 *       numFiscalPeriod: $numFiscalPeriodCurrent,
 *   );
 *   $classSmarty->assign('newTrialBalance', $result);
 */
final class LegacyTrialBalanceFacade
{
    public function __construct(
        private readonly TrialBalanceService $service,
    ) {
    }

    /**
     * 試算表データを array<string, mixed> で返す.
     *
     * @return array{rows: array<string, array<string, mixed>>}
     */
    public function build(
        int $idEntity,
        int $numFiscalPeriod,
        ?OpeningBalances $opening = null,
    ): array {
        return $this->service->build(
            idEntity: $idEntity,
            numFiscalPeriod: $numFiscalPeriod,
            opening: $opening ?? OpeningBalances::empty(),
        );
    }
}
