<?php

declare(strict_types=1);

namespace App\Domain\Report\DetailedAccount;

use App\Domain\FiscalPeriod\FiscalPeriod;

/**
 * 借入金及び支払利子の内訳書データ.
 */
final readonly class LoansPayableBreakdown
{
    /**
     * @param list<LoanRow> $rows
     */
    public function __construct(
        public readonly string $companyName,
        public readonly FiscalPeriod $fiscalPeriod,
        public readonly array $rows,
    ) {
    }

    public function totalBalance(): int
    {
        return array_sum(array_map(
            static fn (LoanRow $r) => $r->closingBalance,
            $this->rows,
        ));
    }

    public function totalInterestPaid(): int
    {
        return array_sum(array_map(
            static fn (LoanRow $r) => $r->interestPaid,
            $this->rows,
        ));
    }
}
