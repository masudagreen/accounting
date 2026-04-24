-- =============================================================
-- Rucaro Accounting - 0011 seed: Fixed asset categories
-- =============================================================
-- Standard Japanese fixed asset categories.
-- =============================================================

SET NAMES utf8mb4;

INSERT INTO fixed_asset_categories
    (id, code, label, parent_code, sort_order, is_tangible, is_depreciable, default_useful_life_years, default_method)
VALUES
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000001','-','')), 'building',            '建物',           NULL, 10, 1, 1, 22, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000002','-','')), 'building_fixtures',   '建物附属設備',   NULL, 20, 1, 1, 15, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000003','-','')), 'structures',          '構築物',         NULL, 30, 1, 1, 20, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000004','-','')), 'machinery',           '機械装置',       NULL, 40, 1, 1, 10, 'declining_balance_2012'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000005','-','')), 'vehicles',            '車両運搬具',     NULL, 50, 1, 1,  6, 'declining_balance_2012'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000006','-','')), 'tools_furniture',     '工具器具備品',   NULL, 60, 1, 1,  5, 'declining_balance_2012'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000007','-','')), 'software',            'ソフトウェア',   NULL, 70, 0, 1,  5, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000008','-','')), 'lease_asset',         'リース資産',     NULL, 80, 1, 1,  5, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-000000000009','-','')), 'intangible_other',    '無形固定資産',   NULL, 90, 0, 1,  5, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-00000000000A','-','')), 'land',                '土地',           NULL,100, 1, 0,  0, 'none'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-00000000000B','-','')), 'deferred_asset',      '繰延資産',       NULL,110, 0, 1,  5, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-00000000000C','-','')), 'one_shot_small',      '少額減価償却資産（即時償却）', NULL,120, 1, 1, 1, 'one_shot'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-00000000000D','-','')), 'three_year_equal',    '一括償却資産（3年均等）',    NULL,130, 1, 1, 3, 'three_year_equal'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-00000000000E','-','')), 'goodwill',            'のれん',         NULL,140, 0, 1,  5, 'straight_line'),
    (UNHEX(REPLACE('01930000-0000-0000-0000-00000000000F','-','')), 'other',               'その他',         NULL,150, 1, 1, 10, 'straight_line');
