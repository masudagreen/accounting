<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * 株主資本等変動計算書 (Statement of Shareholders' Equity).
 *
 * 不変条件:
 *   closingBalance(section) = openingBalance(section) + 当該セクションの変動合計
 *   totalEquityClosing()    = totalEquityOpening() + totalChange()
 */
final readonly class StatementOfEquity
{
    /**
     * @param array<string, Money>       $openingBalances  section->value => 期首残高
     * @param list<EquityChange>         $changes          当期変動の全件
     * @param array<string, Money>       $closingBalances  section->value => 期末残高 (導出済み)
     * @param Money                      $totalEquityOpening 純資産合計期首
     * @param Money                      $totalChange        変動合計
     * @param Money                      $totalEquityClosing 純資産合計期末
     */
    public function __construct(
        private array $openingBalances,
        private array $changes,
        private array $closingBalances,
        private Money $totalEquityOpening,
        private Money $totalChange,
        private Money $totalEquityClosing,
    ) {
    }

    public function openingBalance(EquitySection $section): Money
    {
        return $this->openingBalances[$section->value] ?? Money::zero();
    }

    public function closingBalance(EquitySection $section): Money
    {
        return $this->closingBalances[$section->value] ?? Money::zero();
    }

    public function totalEquityOpening(): Money
    {
        return $this->totalEquityOpening;
    }

    public function totalChange(): Money
    {
        return $this->totalChange;
    }

    public function totalEquityClosing(): Money
    {
        return $this->totalEquityClosing;
    }

    /**
     * 指定セクションの当期変動一覧.
     *
     * @return list<EquityChange>
     */
    public function changesForSection(EquitySection $section): array
    {
        return array_values(
            array_filter(
                $this->changes,
                static fn (EquityChange $c) => $c->section() === $section,
            ),
        );
    }

    /**
     * 全変動一覧.
     *
     * @return list<EquityChange>
     */
    public function allChanges(): array
    {
        return $this->changes;
    }
}
