<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Port;

use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Domain\FinancialStatement\Port\FsSectionDefinition;
use Rucaro\Domain\FinancialStatement\Port\FsSectionDefinitionRepositoryInterface;

/**
 * In-memory {@see FsSectionDefinitionRepositoryInterface} for unit tests.
 *
 * Auto-seeds the standard J-GAAP BS / PL skeleton on first access so tests
 * only need to focus on the mapping data that matters for each case.
 */
final class InMemoryFsSectionDefinitionRepository implements FsSectionDefinitionRepositoryInterface
{
    /** @var array<string, list<FsSectionDefinition>>|null */
    private ?array $byKind = null;

    public function findAllByKind(FsKind $kind): array
    {
        $this->byKind ??= self::jgaapStandard();
        return $this->byKind[$kind->value] ?? [];
    }

    /**
     * @return array<string, list<FsSectionDefinition>>
     */
    public static function jgaapStandard(): array
    {
        $bs = [
            new FsSectionDefinition(FsKind::BalanceSheet, 'asset',                  null,                 '資産の部',       1, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'current_asset',          'asset',              '流動資産',       10, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'noncurrent_asset',       'asset',              '固定資産',       20, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'tangible_asset',         'noncurrent_asset',   '有形固定資産',   21, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'intangible_asset',       'noncurrent_asset',   '無形固定資産',   22, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'investment_asset',       'noncurrent_asset',   '投資その他の資産', 23, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'deferred_asset',         'asset',              '繰延資産',       30, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'asset_total',            null,                 '資産合計',       99, false, true, '+asset'),
            new FsSectionDefinition(FsKind::BalanceSheet, 'liability',              null,                 '負債の部',       100, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'current_liability',      'liability',          '流動負債',       110, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'noncurrent_liability',   'liability',          '固定負債',       120, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'liability_total',        null,                 '負債合計',       199, false, true, '+liability'),
            new FsSectionDefinition(FsKind::BalanceSheet, 'equity',                 null,                 '純資産の部',     200, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'shareholders_equity',    'equity',             '株主資本',       210, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'capital',                'shareholders_equity','資本金',         211, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'capital_surplus',        'shareholders_equity','資本剰余金',     212, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'retained_earnings',      'shareholders_equity','利益剰余金',     213, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'valuation_adjustments',  'equity',             '評価・換算差額等', 220, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'stock_acquisition_rights','equity',            '新株予約権',     230, false, false, null),
            new FsSectionDefinition(FsKind::BalanceSheet, 'equity_total',           null,                 '純資産合計',     299, false, true, '+equity'),
            new FsSectionDefinition(FsKind::BalanceSheet, 'liability_equity_total', null,                 '負債純資産合計', 399, false, true, '+liability+equity'),
        ];
        $pl = [
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'operating_revenue',     null, '売上高',              10, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'cost_of_sales',         null, '売上原価',            20, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'gross_profit',          null, '売上総利益',          30, true, false, '+operating_revenue-cost_of_sales'),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'sga',                   null, '販売費及び一般管理費', 40, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'operating_income',      null, '営業利益',            50, true, false, '+gross_profit-sga'),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'non_operating_revenue', null, '営業外収益',          60, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'non_operating_expense', null, '営業外費用',          70, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'ordinary_income',       null, '経常利益',            80, true, false, '+operating_income+non_operating_revenue-non_operating_expense'),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'extraordinary_gain',    null, '特別利益',            90, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'extraordinary_loss',    null, '特別損失',           100, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'pretax_income',         null, '税引前当期純利益',   110, true, false, '+ordinary_income+extraordinary_gain-extraordinary_loss'),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'income_tax',            null, '法人税等',           120, false, false, null),
            new FsSectionDefinition(FsKind::ProfitAndLoss, 'net_income',            null, '当期純利益',         130, true, true,  '+pretax_income-income_tax'),
        ];
        return [
            FsKind::BalanceSheet->value   => $bs,
            FsKind::ProfitAndLoss->value  => $pl,
        ];
    }
}
