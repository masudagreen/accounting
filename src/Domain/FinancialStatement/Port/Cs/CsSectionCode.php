<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Cs;

/**
 * Canonical CS section-code registry.
 *
 * These codes are seeded into {@see fs_cs_section_definitions} by
 * `scripts/migrate/0009_fs_cs_mappings_seed.sql`. Keep this file in sync
 * whenever a new canonical section is added to the seed.
 *
 * Using a `final class` with constants instead of an enum because the set
 * is open to entity-specific extensions — an entity may introduce custom
 * codes that don't appear here, and we don't want `from()` to throw.
 */
final class CsSectionCode
{
    // --- I. Operating activities -----------------------------------------
    public const OPERATING_CF               = 'operating_cf';
    public const OPERATING_PRETAX_INCOME    = 'operating_pretax_income';
    public const DEPRECIATION               = 'depreciation';
    public const PROVISION                  = 'provision';
    public const WC_RECEIVABLES             = 'wc_receivables';
    public const WC_INVENTORY               = 'wc_inventory';
    public const WC_PAYABLES                = 'wc_payables';
    public const OPERATING_CF_SUBTOTAL      = 'operating_cf_subtotal';
    public const INTEREST_RECEIVED          = 'interest_received';
    public const INTEREST_PAID              = 'interest_paid';
    public const TAX_PAID                   = 'tax_paid';
    public const OPERATING_CF_TOTAL         = 'operating_cf_total';

    // --- II. Investing activities ----------------------------------------
    public const INVESTING_CF               = 'investing_cf';
    public const INVESTING_PPE_PURCHASE     = 'investing_ppe_purchase';
    public const INVESTING_PPE_SALE         = 'investing_ppe_sale';
    public const INVESTING_SECURITIES_PURCHASE = 'investing_securities_purchase';
    public const INVESTING_SECURITIES_SALE     = 'investing_securities_sale';
    public const INVESTING_LOAN_GIVEN       = 'investing_loan_given';
    public const INVESTING_LOAN_RECEIVED    = 'investing_loan_received';
    public const INVESTING_CF_TOTAL         = 'investing_cf_total';

    // --- III. Financing activities ---------------------------------------
    public const FINANCING_CF               = 'financing_cf';
    public const FINANCING_DEBT_PROCEEDS    = 'financing_debt_proceeds';
    public const FINANCING_DEBT_REPAYMENT   = 'financing_debt_repayment';
    public const FINANCING_EQUITY_PROCEEDS  = 'financing_equity_proceeds';
    public const FINANCING_DIVIDENDS_PAID   = 'financing_dividends_paid';
    public const FINANCING_CF_TOTAL         = 'financing_cf_total';

    // --- Reconciliation of cash ------------------------------------------
    public const NET_CHANGE_IN_CASH         = 'net_change_in_cash';
    public const BEGINNING_CASH             = 'beginning_cash';
    public const ENDING_CASH                = 'ending_cash';

    private function __construct()
    {
        // non-instantiable
    }
}
