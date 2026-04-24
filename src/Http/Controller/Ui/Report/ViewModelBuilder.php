<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Report;

use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Section;

/**
 * View-model helpers shared by the PL / BS HTML view controllers.
 *
 * Keeps the presentation mapping (Section → array suitable for Smarty) in
 * exactly one place so the controllers remain thin. The output shape is
 * intentionally kept close to {@see \Rucaro\Infrastructure\FinancialStatement\DompdfFinancialStatementGenerator}
 * so future consolidation is straightforward.
 */
final class ViewModelBuilder
{
    /**
     * @param array<string, Section> $sections
     * @return array<string, array{code: string, label: string, subtotal: string, lines: list<array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}>}>
     */
    public static function sectionMap(array $sections): array
    {
        $out = [];
        foreach ($sections as $code => $section) {
            $out[$code] = [
                'code'     => $section->code,
                'label'    => $section->label,
                'subtotal' => self::formatAmount($section->subtotal),
                'lines'    => array_map(self::lineToArray(...), $section->lines),
            ];
        }
        return $out;
    }

    /**
     * @return array{label: string, code: ?string, amount: string, depth: int, isSubtotal: bool}
     */
    public static function lineToArray(FinancialStatementLine $line): array
    {
        return [
            'label'      => $line->label,
            'code'       => $line->accountTitleCode,
            'amount'     => self::formatAmount($line->amount),
            'depth'      => $line->depth,
            'isSubtotal' => $line->isSubtotal,
        ];
    }

    /**
     * @param array<string, string> $totals
     * @return array<string, string>
     */
    public static function formatTotals(array $totals): array
    {
        $out = [];
        foreach ($totals as $k => $v) {
            $out[$k] = self::formatAmount($v);
        }
        return $out;
    }

    /**
     * @return list<array{code: string, label: string, isSubtotal: bool, isTotal: bool}>
     */
    public static function plOrder(): array
    {
        return [
            ['code' => 'operating_revenue',     'label' => '売上高',              'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'cost_of_sales',         'label' => '売上原価',            'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'gross_profit',          'label' => '売上総利益',          'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'sga',                   'label' => '販売費及び一般管理費', 'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'operating_income',      'label' => '営業利益',            'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'non_operating_revenue', 'label' => '営業外収益',          'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'non_operating_expense', 'label' => '営業外費用',          'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'ordinary_income',       'label' => '経常利益',            'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'extraordinary_gain',    'label' => '特別利益',            'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'extraordinary_loss',    'label' => '特別損失',            'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'pretax_income',         'label' => '税引前当期純利益',    'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'income_tax',            'label' => '法人税、住民税及び事業税', 'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'net_income',            'label' => '当期純利益',          'isSubtotal' => true,  'isTotal' => true],
        ];
    }

    /**
     * Canonical section ordering for the CS (indirect-method) view. Mirrors
     * {@see \Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionCode} so the
     * HTML view stays in lock-step with the PDF generator.
     *
     * @return list<array{code: string, label: string, isSubtotal: bool, isTotal: bool, indent: int}>
     */
    public static function csOrder(): array
    {
        return [
            ['code' => 'operating_cf',            'label' => 'I. 営業活動によるキャッシュフロー', 'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'operating_pretax_income', 'label' => '税引前当期純利益',                 'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'depreciation',            'label' => '減価償却費',                       'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'provision',               'label' => '引当金の増減額',                   'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'wc_receivables',          'label' => '売上債権の増減額',                 'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'wc_inventory',            'label' => '棚卸資産の増減額',                 'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'wc_payables',             'label' => '仕入債務の増減額',                 'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'operating_cf_subtotal',   'label' => '小計',                             'isSubtotal' => true,  'isTotal' => false, 'indent' => 1],
            ['code' => 'interest_received',       'label' => '利息の受取額',                     'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'interest_paid',           'label' => '利息の支払額',                     'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'tax_paid',                'label' => '法人税等の支払額',                 'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'operating_cf_total',      'label' => '営業活動によるキャッシュフロー計', 'isSubtotal' => true,  'isTotal' => true,  'indent' => 0],

            ['code' => 'investing_cf',            'label' => 'II. 投資活動によるキャッシュフロー', 'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'investing_ppe_purchase',     'label' => '有形固定資産の取得',              'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_ppe_sale',         'label' => '有形固定資産の売却',              'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_securities_purchase', 'label' => '有価証券の取得',              'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_securities_sale',     'label' => '有価証券の売却',              'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_loan_given',     'label' => '貸付による支出',                    'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_loan_received',  'label' => '貸付回収による収入',                'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_cf_total',      'label' => '投資活動によるキャッシュフロー計', 'isSubtotal' => true,  'isTotal' => true,  'indent' => 0],

            ['code' => 'financing_cf',            'label' => 'III. 財務活動によるキャッシュフロー', 'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'financing_debt_proceeds',    'label' => '借入による収入',                   'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_debt_repayment',   'label' => '借入金の返済',                    'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_equity_proceeds',  'label' => '株式発行による収入',              'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_dividends_paid',   'label' => '配当金の支払額',                  'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_cf_total',      'label' => '財務活動によるキャッシュフロー計', 'isSubtotal' => true,  'isTotal' => true,  'indent' => 0],

            ['code' => 'net_change_in_cash',      'label' => '現金及び現金同等物の増減額',       'isSubtotal' => true,  'isTotal' => false, 'indent' => 0],
            ['code' => 'beginning_cash',          'label' => '現金及び現金同等物の期首残高',     'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'ending_cash',             'label' => '現金及び現金同等物の期末残高',     'isSubtotal' => true,  'isTotal' => true,  'indent' => 0],
        ];
    }

    /**
     * @return array{assetGroups: list<array{code: string, label: string}>, liabilityGroups: list<array{code: string, label: string}>, equityGroups: list<array{code: string, label: string}>}
     */
    public static function bsOrder(): array
    {
        return [
            'assetGroups' => [
                ['code' => 'current_asset',    'label' => '流動資産'],
                ['code' => 'noncurrent_asset', 'label' => '固定資産'],
                ['code' => 'deferred_asset',   'label' => '繰延資産'],
            ],
            'liabilityGroups' => [
                ['code' => 'current_liability',    'label' => '流動負債'],
                ['code' => 'noncurrent_liability', 'label' => '固定負債'],
            ],
            'equityGroups' => [
                ['code' => 'shareholders_equity',     'label' => '株主資本'],
                ['code' => 'valuation_adjustments',   'label' => '評価・換算差額等'],
                ['code' => 'stock_acquisition_rights','label' => '新株予約権'],
            ],
        ];
    }

    /**
     * JPY-only integer formatting with thousands separators and parentheses
     * for negatives. Callers that need another currency should build their
     * own formatter rather than layering branches here.
     */
    public static function formatAmount(string $raw): string
    {
        if ($raw === '' || !is_numeric($raw)) {
            return '0';
        }
        $num = (float) $raw;
        if ($num === 0.0) {
            return '0';
        }
        $formatted = number_format(abs($num), 0, '.', ',');
        return $num < 0 ? '(' . $formatted . ')' : $formatted;
    }
}
