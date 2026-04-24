<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Port\Cs;

use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinition;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinitionRepositoryInterface;

/**
 * In-memory {@see CsSectionDefinitionRepositoryInterface} for unit tests.
 *
 * Auto-seeds the standard J-GAAP indirect-method CS skeleton on first access
 * so tests only need to focus on the mapping data that matters for each case.
 * Mirrors `scripts/migrate/0009_fs_cs_mappings_seed.sql`.
 */
final class InMemoryCsSectionDefinitionRepository implements CsSectionDefinitionRepositoryInterface
{
    /** @var list<CsSectionDefinition>|null */
    private ?array $defs = null;

    public function findAll(): array
    {
        $this->defs ??= self::jgaapStandard();
        return $this->defs;
    }

    /**
     * @return list<CsSectionDefinition>
     */
    public static function jgaapStandard(): array
    {
        return [
            // I. 営業活動
            new CsSectionDefinition('operating_cf',             null,           'I. 営業活動によるキャッシュフロー', 10,  false, false, null),
            new CsSectionDefinition('operating_pretax_income',  'operating_cf', '税引前当期純利益',                   11,  false, false, null),
            new CsSectionDefinition('depreciation',             'operating_cf', '減価償却費',                         12,  false, false, null),
            new CsSectionDefinition('provision',                'operating_cf', '引当金繰入額',                       13,  false, false, null),
            new CsSectionDefinition('wc_receivables',           'operating_cf', '売上債権の増減額',                   14,  false, false, null),
            new CsSectionDefinition('wc_inventory',             'operating_cf', '棚卸資産の増減額',                   15,  false, false, null),
            new CsSectionDefinition('wc_payables',              'operating_cf', '仕入債務の増減額',                   16,  false, false, null),
            new CsSectionDefinition('operating_cf_subtotal',    null,           '小計',                                19,  true,  false, '+operating_cf'),
            new CsSectionDefinition('interest_received',        null,           '利息の受取額',                       20,  false, false, null),
            new CsSectionDefinition('interest_paid',            null,           '利息の支払額',                       21,  false, false, null),
            new CsSectionDefinition('tax_paid',                 null,           '法人税等の支払額',                   22,  false, false, null),
            new CsSectionDefinition('operating_cf_total',       null,           '営業活動によるキャッシュフロー',       29,  false, true,  '+operating_cf_subtotal+interest_received-interest_paid-tax_paid'),

            // II. 投資活動
            new CsSectionDefinition('investing_cf',             null,           'II. 投資活動によるキャッシュフロー', 100, false, false, null),
            new CsSectionDefinition('investing_ppe_purchase',   'investing_cf', '有形固定資産の取得による支出',         101, false, false, null),
            new CsSectionDefinition('investing_ppe_sale',       'investing_cf', '有形固定資産の売却による収入',         102, false, false, null),
            new CsSectionDefinition('investing_securities_purchase','investing_cf','投資有価証券の取得による支出',      103, false, false, null),
            new CsSectionDefinition('investing_securities_sale','investing_cf', '投資有価証券の売却による収入',         104, false, false, null),
            new CsSectionDefinition('investing_loan_given',     'investing_cf', '貸付による支出',                       105, false, false, null),
            new CsSectionDefinition('investing_loan_received',  'investing_cf', '貸付金の回収による収入',               106, false, false, null),
            new CsSectionDefinition('investing_cf_total',       null,           '投資活動によるキャッシュフロー',       199, false, true,  '+investing_cf'),

            // III. 財務活動
            new CsSectionDefinition('financing_cf',             null,           'III. 財務活動によるキャッシュフロー', 200, false, false, null),
            new CsSectionDefinition('financing_debt_proceeds',  'financing_cf', '借入による収入',                       201, false, false, null),
            new CsSectionDefinition('financing_debt_repayment', 'financing_cf', '借入金の返済による支出',               202, false, false, null),
            new CsSectionDefinition('financing_equity_proceeds','financing_cf', '株式の発行による収入',                 203, false, false, null),
            new CsSectionDefinition('financing_dividends_paid', 'financing_cf', '配当金の支払額',                       204, false, false, null),
            new CsSectionDefinition('financing_cf_total',       null,           '財務活動によるキャッシュフロー',       299, false, true,  '+financing_cf'),

            new CsSectionDefinition('net_change_in_cash',       null,           '現金及び現金同等物の増減額',           390, true,  false, '+operating_cf_total+investing_cf_total+financing_cf_total'),
            new CsSectionDefinition('beginning_cash',           null,           '現金及び現金同等物の期首残高',         391, false, false, null),
            new CsSectionDefinition('ending_cash',              null,           '現金及び現金同等物の期末残高',         399, false, true,  '+net_change_in_cash+beginning_cash'),
        ];
    }
}
