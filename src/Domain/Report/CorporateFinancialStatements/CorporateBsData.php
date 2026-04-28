<?php

declare(strict_types=1);

namespace App\Domain\Report\CorporateFinancialStatements;

use App\Domain\FiscalPeriod\FiscalPeriod;

/**
 * 法人 貸借対照表 生成データ.
 *
 * 中小企業が必要とする主要科目を保持する.
 * 詳細な科目内訳は DetailedAccount 層に委ねる.
 */
final readonly class CorporateBsData
{
    public function __construct(
        public readonly string $companyName,
        public readonly FiscalPeriod $fiscalPeriod,
        public readonly int $currentAssets,
        public readonly int $fixedAssets,
        public readonly int $totalAssets,
        public readonly int $currentLiabilities,
        public readonly int $fixedLiabilities,
        public readonly int $totalLiabilities,
        public readonly int $capitalStock,
        public readonly int $retainedEarnings,
        public readonly int $totalEquity,
    ) {
    }
}
