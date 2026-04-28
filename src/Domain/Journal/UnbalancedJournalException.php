<?php

declare(strict_types=1);

namespace App\Domain\Journal;

use App\Domain\Money\Money;

/**
 * 借方合計と貸方合計が一致しない場合に投げる。
 */
final class UnbalancedJournalException extends \DomainException
{
    public static function of(Money $totalDebits, Money $totalCredits): self
    {
        return new self(sprintf(
            'Journal entry is unbalanced: debits=%s credits=%s',
            $totalDebits->toString(),
            $totalCredits->toString(),
        ));
    }
}
