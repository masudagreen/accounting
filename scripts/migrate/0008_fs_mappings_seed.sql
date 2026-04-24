-- =============================================================
-- Rucaro Accounting - 0008: seed standard J-GAAP BS/PL sections
-- =============================================================
-- 日本基準の標準決算書階層を fs_section_definitions へ投入する。
-- Entity 固有の拡張・上書きは将来 0008_fs_mappings_seed_entity.sql などで追加予定。
-- =============================================================

SET NAMES utf8mb4;

-- ------------------ BS ------------------
INSERT INTO fs_section_definitions (id, fs_kind, code, parent_code, label, sort_order, is_subtotal, is_total, formula) VALUES
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'asset',                 NULL,       '資産の部',            1,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'current_asset',         'asset',    '流動資産',            10, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'noncurrent_asset',      'asset',    '固定資産',            20, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'tangible_asset',        'noncurrent_asset', '有形固定資産', 21, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'intangible_asset',      'noncurrent_asset', '無形固定資産', 22, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'investment_asset',      'noncurrent_asset', '投資その他の資産', 23, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'deferred_asset',        'asset',    '繰延資産',            30, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'asset_total',           NULL,       '資産合計',            99, 0, 1, '+asset'),

    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'liability',             NULL,       '負債の部',            100,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'current_liability',     'liability','流動負債',            110,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'noncurrent_liability',  'liability','固定負債',            120,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'liability_total',       NULL,       '負債合計',            199,0, 1, '+liability'),

    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'equity',                NULL,       '純資産の部',          200,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'shareholders_equity',   'equity',   '株主資本',            210,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'capital',               'shareholders_equity', '資本金',   211,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'capital_surplus',       'shareholders_equity', '資本剰余金',212,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'retained_earnings',     'shareholders_equity', '利益剰余金',213,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'valuation_adjustments', 'equity',   '評価・換算差額等',    220,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'stock_acquisition_rights','equity', '新株予約権',          230,0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'equity_total',          NULL,       '純資産合計',          299,0, 1, '+equity'),
    (UNHEX(REPLACE(UUID(), '-', '')), 'bs', 'liability_equity_total',NULL,       '負債純資産合計',      399,0, 1, '+liability+equity');

-- ------------------ PL ------------------
INSERT INTO fs_section_definitions (id, fs_kind, code, parent_code, label, sort_order, is_subtotal, is_total, formula) VALUES
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'operating_revenue',        NULL, '売上高',              10,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'cost_of_sales',            NULL, '売上原価',            20,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'gross_profit',             NULL, '売上総利益',          30,  1, 0, '+operating_revenue-cost_of_sales'),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'sga',                      NULL, '販売費及び一般管理費',40,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'operating_income',         NULL, '営業利益',            50,  1, 0, '+gross_profit-sga'),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'non_operating_revenue',    NULL, '営業外収益',          60,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'non_operating_expense',    NULL, '営業外費用',          70,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'ordinary_income',          NULL, '経常利益',            80,  1, 0, '+operating_income+non_operating_revenue-non_operating_expense'),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'extraordinary_gain',       NULL, '特別利益',            90,  0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'extraordinary_loss',       NULL, '特別損失',            100, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'pretax_income',            NULL, '税引前当期純利益',    110, 1, 0, '+ordinary_income+extraordinary_gain-extraordinary_loss'),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'income_tax',               NULL, '法人税、住民税及び事業税', 120, 0, 0, NULL),
    (UNHEX(REPLACE(UUID(), '-', '')), 'pl', 'net_income',               NULL, '当期純利益',          130, 1, 1, '+pretax_income-income_tax');
