<?php

declare(strict_types=1);

namespace App\Domain\Report\CorporateFinancialStatements;

use App\Domain\FinancialStatement\StatementOfEquity;
use App\Domain\FiscalPeriod\FiscalPeriod;

/**
 * 法人 株主資本等変動計算書 生成データ.
 */
final readonly class CorporateSsData
{
    public function __construct(
        public readonly string $companyName,
        public readonly FiscalPeriod $fiscalPeriod,
        public readonly StatementOfEquity $equity,
    ) {
    }
}
