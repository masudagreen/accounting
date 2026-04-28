<?php

declare(strict_types=1);

namespace App\Domain\Journal;

use App\Domain\Money\Money;

/**
 * 仕訳明細1行。借方または貸方のいずれかとして使う。
 *
 * 不変条件:
 *  - 勘定科目IDは非空文字
 *  - 金額は0以上 (マイナス金額は別仕訳で表現)
 */
final readonly class JournalLine
{
    private function __construct(
        private string $accountTitleId,
        private Money $amount,
        private ?string $departmentId = null,
        private ?string $subAccountTitleId = null,
    ) {
    }

    public static function of(
        string $accountTitleId,
        Money $amount,
        ?string $departmentId = null,
        ?string $subAccountTitleId = null,
    ): self {
        if ($accountTitleId === '') {
            throw new \InvalidArgumentException('accountTitleId must not be empty');
        }
        if ($amount->isNegative()) {
            throw new \InvalidArgumentException('amount must be zero or positive');
        }
        return new self($accountTitleId, $amount, $departmentId, $subAccountTitleId);
    }

    public function accountTitleId(): string
    {
        return $this->accountTitleId;
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
