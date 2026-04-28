<?php

declare(strict_types=1);

namespace App\Domain\Report\DetailedAccount;

/**
 * 借入金内訳書の1行.
 */
final readonly class LoanRow
{
    public function __construct(
        public readonly string $lenderName,
        public readonly string $location,
        public readonly int $closingBalance,
        public readonly int $interestPaid,
        public readonly string $interestRate,
    ) {
    }
}
