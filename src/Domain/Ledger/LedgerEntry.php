<?php

declare(strict_types=1);

namespace App\Domain\Ledger;

use App\Domain\AccountTitle\NormalBalance;
use App\Domain\Money\Money;

/**
 * 元帳の1行 (仕訳明細を借方/貸方サイドの片側に展開したもの).
 *
 * 元実装の `accountingLogCalcJpn` の1行に対応.
 *  - flagDebit (1=借方, 0=貸方) → side
 *  - idAccountTitle / idDepartment / idSubAccountTitle
 *  - numValue → amount
 */
final readonly class LedgerEntry
{
    public function __construct(
        private \DateTimeImmutable $date,
        private string $accountTitleId,
        private NormalBalance $side,
        private Money $amount,
        private ?string $departmentId = null,
        private ?string $subAccountTitleId = null,
    ) {
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function accountTitleId(): string
    {
        return $this->accountTitleId;
    }

    public function side(): NormalBalance
    {
        return $this->side;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function departmentId(): ?string
    {
        return $this->departmentId;
    }

    public function subAccountTitleId(): ?string
    {
        return $this->subAccountTitleId;
    }
}
