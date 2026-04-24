<?php

declare(strict_types=1);

namespace Rucaro\Domain\CashPlan;

/**
 * Cash-flow bucket for a {@see CashPlanEntry}.
 *
 * Groups the legacy Jpn_CashPlan categories into the six canonical
 * operating / investing / financing × in / out buckets used by the new
 * 資金繰り表 UI. A `*_in` entry always increases the running balance,
 * a `*_out` entry always decreases it.
 */
enum CashPlanCategory: string
{
    case OperatingIn = 'operating_in';
    case OperatingOut = 'operating_out';
    case InvestingIn = 'investing_in';
    case InvestingOut = 'investing_out';
    case FinancingIn = 'financing_in';
    case FinancingOut = 'financing_out';

    public function isInflow(): bool
    {
        return match ($this) {
            self::OperatingIn,
            self::InvestingIn,
            self::FinancingIn => true,
            self::OperatingOut,
            self::InvestingOut,
            self::FinancingOut => false,
        };
    }

    /**
     * +1 for inflows, -1 for outflows — used when summing signed monthly
     * deltas into a running balance.
     */
    public function sign(): int
    {
        return $this->isInflow() ? 1 : -1;
    }

    public function group(): string
    {
        return match ($this) {
            self::OperatingIn, self::OperatingOut => 'operating',
            self::InvestingIn, self::InvestingOut => 'investing',
            self::FinancingIn, self::FinancingOut => 'financing',
        };
    }
}
