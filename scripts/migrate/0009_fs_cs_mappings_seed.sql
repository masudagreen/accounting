-- =============================================================
-- Rucaro Accounting - 0009: seed standard J-GAAP CS (indirect method) sections
-- =============================================================
-- 日本基準 間接法 CS の標準階層を fs_cs_section_definitions へ投入する。
--
-- 構造（間接法）:
--   I. 営業活動によるキャッシュフロー (operating)
--      税引前当期純利益 (PL から継承)
--      + 非資金項目の調整 (減価償却費, 引当金繰入 等)
--      + 運転資本の増減 (売掛金, 棚卸資産, 買掛金)
--      = 小計 (operating_cf_subtotal)
--      + 利息受取 / - 利息支払 / - 法人税等支払
--      = 営業活動CF (operating_cf_total)
--   II. 投資活動によるキャッシュフロー (investing)
--      有形固定資産の取得 / 売却
--      投資有価証券の取得 / 売却
--      貸付 / 貸付金の回収
--      = 投資活動CF (investing_cf_total)
--   III. 財務活動によるキャッシュフロー (financing)
--      短期/長期借入金の調達 / 返済
--      株式発行による収入
--      配当金支払
--      = 財務活動CF (financing_cf_total)
--   純増減 = 営業 + 投資 + 財務 (net_change_in_cash)
--   期首残高 (beginning_cash) + 純増減 = 期末残高 (ending_cash)
-- =============================================================

SET NAMES utf8mb4;

INSERT INTO fs_cs_section_definitions (id, code, parent_code, label, sort_order, is_subtotal, is_total, formula) VALUES
    -- I. 営業活動によるキャッシュフロー
    (UNHEX(REPLACE(UUID(), '-', '')), 'operating_cf',            NULL,          'I. 営業活動によるキャッシュフロー', 10,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'operating_pretax_income', 'operating_cf','税引前当期純利益',                   11,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'depreciation',            'operating_cf','減価償却費',                         12,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'provision',               'operating_cf','引当金繰入額',                       13,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'wc_receivables',          'operating_cf','売上債権の増減額',                   14,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'wc_inventory',            'operating_cf','棚卸資産の増減額',                   15,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'wc_payables',             'operating_cf','仕入債務の増減額',                   16,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'operating_cf_subtotal',   NULL,          '小計',                                19,  1, 0, '+operating_cf'),
    (UNHEX(REPLACE(UUID(), '-', '')), 'interest_received',       NULL,          '利息の受取額',                       20,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'interest_paid',           NULL,          '利息の支払額',                       21,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'tax_paid',                NULL,          '法人税等の支払額',                   22,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'operating_cf_total',      NULL,          '営業活動によるキャッシュフロー',       29,  0, 1, '+operating_cf_subtotal+interest_received-interest_paid-tax_paid'),

    -- II. 投資活動によるキャッシュフロー
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_cf',            NULL,          'II. 投資活動によるキャッシュフロー', 100, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_ppe_purchase',  'investing_cf','有形固定資産の取得による支出',         101, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_ppe_sale',      'investing_cf','有形固定資産の売却による収入',         102, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_securities_purchase','investing_cf','投資有価証券の取得による支出',   103, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_securities_sale','investing_cf','投資有価証券の売却による収入',        104, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_loan_given',    'investing_cf','貸付による支出',                       105, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_loan_received', 'investing_cf','貸付金の回収による収入',               106, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'investing_cf_total',      NULL,          '投資活動によるキャッシュフロー',       199, 0, 1, '+investing_cf'),

    -- III. 財務活動によるキャッシュフロー
    (UNHEX(REPLACE(UUID(), '-', '')), 'financing_cf',            NULL,          'III. 財務活動によるキャッシュフロー', 200, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'financing_debt_proceeds', 'financing_cf','借入による収入',                       201, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'financing_debt_repayment','financing_cf','借入金の返済による支出',               202, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'financing_equity_proceeds','financing_cf','株式の発行による収入',                 203, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'financing_dividends_paid','financing_cf','配当金の支払額',                       204, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'financing_cf_total',      NULL,          '財務活動によるキャッシュフロー',       299, 0, 1, '+financing_cf'),

    -- IV. 現金及び現金同等物の増減 / 期首 / 期末
    (UNHEX(REPLACE(UUID(), '-', '')), 'net_change_in_cash',      NULL,          '現金及び現金同等物の増減額',           390, 1, 0, '+operating_cf_total+investing_cf_total+financing_cf_total'),
    (UNHEX(REPLACE(UUID(), '-', '')), 'beginning_cash',          NULL,          '現金及び現金同等物の期首残高',         391, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'ending_cash',             NULL,          '現金及び現金同等物の期末残高',         399, 0, 1, '+net_change_in_cash+beginning_cash');
