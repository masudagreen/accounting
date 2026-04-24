<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement;

/**
 * Identifies which financial statement the caller wants.
 *
 * `All` means the aggregate response should carry every available statement
 * (BS + PL + CS). The matching query parameter on the HTTP layer uses the
 * uppercase form (`BS`, `PL`, `CS`, `ALL`) to align with the OpenAPI spec.
 */
enum FinancialStatementKind: string
{
    case BalanceSheet = 'BS';
    case ProfitAndLoss = 'PL';
    case CashFlow = 'CS';
    case All = 'ALL';

    public static function fromQueryString(?string $raw): self
    {
        if ($raw === null || $raw === '') {
            return self::All;
        }
        $normalised = strtoupper($raw);
        return match ($normalised) {
            'BS', 'BALANCESHEET', 'BALANCE_SHEET' => self::BalanceSheet,
            'PL', 'PROFITANDLOSS', 'PROFIT_AND_LOSS' => self::ProfitAndLoss,
            'CS', 'CASHFLOW', 'CASH_FLOW' => self::CashFlow,
            default => self::All,
        };
    }

    public function includesBalanceSheet(): bool
    {
        return $this === self::BalanceSheet || $this === self::All;
    }

    public function includesProfitAndLoss(): bool
    {
        return $this === self::ProfitAndLoss || $this === self::All;
    }

    public function includesCashFlow(): bool
    {
        return $this === self::CashFlow || $this === self::All;
    }
}
