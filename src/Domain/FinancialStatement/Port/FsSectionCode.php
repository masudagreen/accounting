<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port;

/**
 * Canonical section-code registry.
 *
 * These codes are seeded into {@see fs_section_definitions} by
 * `scripts/migrate/0008_fs_mappings_seed.sql`. Keep this file in sync
 * whenever a new canonical section is added to the seed.
 *
 * Using a `final class` with constants instead of an enum because the set
 * is open to entity-specific extensions — an entity may introduce custom
 * codes that don't appear here, and we don't want `from()` to throw.
 */
final class FsSectionCode
{
    // --- BS: asset side ------------------------------------------------
    public const BS_ASSET                  = 'asset';
    public const BS_CURRENT_ASSET          = 'current_asset';
    public const BS_NONCURRENT_ASSET       = 'noncurrent_asset';
    public const BS_TANGIBLE_ASSET         = 'tangible_asset';
    public const BS_INTANGIBLE_ASSET       = 'intangible_asset';
    public const BS_INVESTMENT_ASSET       = 'investment_asset';
    public const BS_DEFERRED_ASSET         = 'deferred_asset';
    public const BS_ASSET_TOTAL            = 'asset_total';

    // --- BS: liability side -------------------------------------------
    public const BS_LIABILITY              = 'liability';
    public const BS_CURRENT_LIABILITY      = 'current_liability';
    public const BS_NONCURRENT_LIABILITY   = 'noncurrent_liability';
    public const BS_LIABILITY_TOTAL        = 'liability_total';

    // --- BS: equity side ----------------------------------------------
    public const BS_EQUITY                 = 'equity';
    public const BS_SHAREHOLDERS_EQUITY    = 'shareholders_equity';
    public const BS_CAPITAL                = 'capital';
    public const BS_CAPITAL_SURPLUS        = 'capital_surplus';
    public const BS_RETAINED_EARNINGS      = 'retained_earnings';
    public const BS_VALUATION_ADJUSTMENTS  = 'valuation_adjustments';
    public const BS_STOCK_ACQUISITION_RIGHTS = 'stock_acquisition_rights';
    public const BS_EQUITY_TOTAL           = 'equity_total';
    public const BS_LIABILITY_EQUITY_TOTAL = 'liability_equity_total';

    // --- PL -----------------------------------------------------------
    public const PL_OPERATING_REVENUE      = 'operating_revenue';
    public const PL_COST_OF_SALES          = 'cost_of_sales';
    public const PL_GROSS_PROFIT           = 'gross_profit';
    public const PL_SGA                    = 'sga';
    public const PL_OPERATING_INCOME       = 'operating_income';
    public const PL_NON_OPERATING_REVENUE  = 'non_operating_revenue';
    public const PL_NON_OPERATING_EXPENSE  = 'non_operating_expense';
    public const PL_ORDINARY_INCOME        = 'ordinary_income';
    public const PL_EXTRAORDINARY_GAIN     = 'extraordinary_gain';
    public const PL_EXTRAORDINARY_LOSS     = 'extraordinary_loss';
    public const PL_PRETAX_INCOME          = 'pretax_income';
    public const PL_INCOME_TAX             = 'income_tax';
    public const PL_NET_INCOME             = 'net_income';

    private function __construct()
    {
        // non-instantiable
    }
}
