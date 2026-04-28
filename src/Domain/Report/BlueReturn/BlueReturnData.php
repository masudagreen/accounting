<?php

declare(strict_types=1);

namespace App\Domain\Report\BlueReturn;

use App\Application\Dto\BalanceSheetDto;
use App\Application\Dto\ProfitAndLossDto;
use App\Domain\FiscalPeriod\FiscalPeriod;

/**
 * 青色申告決算書を生成するのに必要なデータ集約.
 */
final readonly class BlueReturnData
{
    public function __construct(
        public readonly string $businessName,
        public readonly FiscalPeriod $fiscalPeriod,
        public readonly ProfitAndLossDto $pl,
        public readonly BalanceSheetDto $bs,
    ) {
    }
}
