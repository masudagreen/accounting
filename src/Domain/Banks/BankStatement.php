<?php

declare(strict_types=1);

namespace App\Domain\Banks;

use App\Domain\Cash\CashDirection;
use App\Domain\Money\Money;

/**
 * 銀行明細1件 (値オブジェクト).
 *
 * 銀行 Web 取込から得られる明細データを表す.
 * 入出金の方向は CashDirection で表現し、amount は常に正の値とする.
 *
 * 不変条件:
 *  - amount は正 (0 および負は不可)
 *
 * 将来の対応銀行:
 *  - Japan Net Bank (ジャパンネット銀行)
 *  - Japan Post Bank (ゆうちょ銀行)
 *  - Jibun Bank (じぶん銀行)
 *  - SumiSin Net Bank (住信SBIネット銀行)
 *  - Suruga Bank (スルガ銀行)
 * 各銀行は BankStatementImporter を実装し、BankAdapterRegistry に登録する.
 */
final readonly class BankStatement
{
    public function __construct(
        private \DateTimeImmutable $date,
        private string $description,
        private Money $amount,
        private CashDirection $direction,
        private Money $balanceAfter,
    ) {
        if (! $amount->isPositive()) {
            throw new \InvalidArgumentException('amount must be positive');
        }
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function direction(): CashDirection
    {
        return $this->direction;
    }

    public function balanceAfter(): Money
    {
        return $this->balanceAfter;
    }
}
