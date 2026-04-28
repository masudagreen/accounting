<?php

declare(strict_types=1);

namespace App\Domain\TrialBalance;

use App\Domain\AccountTitle\AccountTitle;
use App\Domain\AccountTitle\NormalBalance;
use App\Domain\Money\Money;

/**
 * 試算表の1行. 科目別の期首/当期発生/期末.
 */
final readonly class TrialBalanceRow
{
    public function __construct(
        private AccountTitle $accountTitle,
        private Money $opening,
        private Money $periodDebits,
        private Money $periodCredits,
        private Money $closing,
    ) {
    }

    public function accountTitle(): AccountTitle
    {
        return $this->accountTitle;
    }

    public function opening(): Money
    {
        return $this->opening;
    }

    public function periodDebits(): Money
    {
        return $this->periodDebits;
    }

    public function periodCredits(): Money
    {
        return $this->periodCredits;
    }

    public function closing(): Money
    {
        return $this->closing;
    }

    public function normalBalance(): NormalBalance
    {
        return $this->accountTitle->normalBalance();
    }
}
