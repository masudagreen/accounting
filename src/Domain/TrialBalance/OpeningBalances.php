<?php

declare(strict_types=1);

namespace App\Domain\TrialBalance;

use App\Domain\Money\Money;

/**
 * 期首残高. 科目ID → 通常残高方向の金額 (絶対値).
 *
 * 元実装の `accountingFSValueJpn.jsonJgaapAccountTitleBS`/`jsonJgaapFSBS` 等の `sumPrev` に対応.
 */
final readonly class OpeningBalances
{
    /**
     * @param array<string, Money> $balances
     */
    private function __construct(
        private array $balances,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @param array<string, Money> $balances
     */
    public static function of(array $balances): self
    {
        return new self($balances);
    }

    public function amountFor(string $accountTitleId): Money
    {
        return $this->balances[$accountTitleId] ?? Money::zero();
    }

    /**
     * @return array<string, Money>
     */
    public function all(): array
    {
        return $this->balances;
    }
}
