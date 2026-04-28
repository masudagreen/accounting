<?php

declare(strict_types=1);

namespace App\Domain\Report\DetailedAccount;

/**
 * 勘定科目内訳書の1行 (預貯金・売掛金・買掛金 共通).
 */
final readonly class AccountBreakdownRow
{
    public function __construct(
        public readonly string $counterpartyName,
        public readonly string $location,
        public readonly int $closingBalance,
        public readonly string $note,
    ) {
    }
}
