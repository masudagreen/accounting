<?php

declare(strict_types=1);

namespace App\Domain\Report\CorporateFinancialStatements;

use App\Application\Dto\ProfitAndLossDto;
use App\Domain\FiscalPeriod\FiscalPeriod;

/**
 * 法人 損益計算書 生成データ.
 */
final readonly class CorporatePlData
{
    public function __construct(
        public readonly string $companyName,
        public readonly FiscalPeriod $fiscalPeriod,
        public readonly ProfitAndLossDto $pl,
    ) {
    }
}
