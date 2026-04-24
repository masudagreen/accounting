<?php

declare(strict_types=1);

namespace Rucaro\Domain\StatementOfChangesInEquity;

/**
 * Section codes for the 株主資本等変動計算書 (Statement of Changes in
 * Equity). Each code maps to one column in the final table; the
 * "Total" (合計) column is derived and therefore not represented as
 * an enum case.
 *
 * Ported from the legacy `FinancialStatementSS` JGAAP column layout,
 * collapsed into ports-and-adapters friendly identifiers so the
 * storage schema ({@see ss_manual_adjustments.section_code}) and the
 * API payloads never drift apart.
 */
enum SsSectionCode: string
{
    case CapitalStock           = 'capital_stock';            // 資本金
    case CapitalSurplus         = 'capital_surplus';          // 資本剰余金
    case RetainedEarnings       = 'retained_earnings';        // 利益剰余金
    case TreasuryStock          = 'treasury_stock';           // 自己株式
    case ValuationAdjustment    = 'valuation_adjustment';     // 評価換算差額等
    case StockAcquisitionRight  = 'stock_acquisition_right';  // 新株予約権

    /**
     * Default display label for the column header.
     */
    public function label(): string
    {
        return match ($this) {
            self::CapitalStock          => '資本金',
            self::CapitalSurplus        => '資本剰余金',
            self::RetainedEarnings      => '利益剰余金',
            self::TreasuryStock         => '自己株式',
            self::ValuationAdjustment   => '評価換算差額等',
            self::StockAcquisitionRight => '新株予約権',
        };
    }

    /**
     * Deterministic column ordering used by both the PDF renderer and
     * JSON serializer. Mirrors the legacy `FinancialStatementSS`
     * column order so reviewers can cross-check period reports.
     *
     * @return list<self>
     */
    public static function ordered(): array
    {
        return [
            self::CapitalStock,
            self::CapitalSurplus,
            self::RetainedEarnings,
            self::TreasuryStock,
            self::ValuationAdjustment,
            self::StockAcquisitionRight,
        ];
    }
}
