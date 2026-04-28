<?php

declare(strict_types=1);

namespace App\Domain\Report\DetailedAccount;

use App\Domain\FiscalPeriod\FiscalPeriod;

/**
 * 預貯金等の内訳書データ.
 */
final readonly class DepositsBreakdown
{
    /**
     * @param list<AccountBreakdownRow> $rows
     */
    public function __construct(
        public readonly string $companyName,
        public readonly FiscalPeriod $fiscalPeriod,
        public readonly array $rows,
    ) {
    }

    public function total(): int
    {
        return array_sum(array_map(
            static fn (AccountBreakdownRow $r) => $r->closingBalance,
            $this->rows,
        ));
    }
}
