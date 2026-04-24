-- =============================================================
-- Rucaro Accounting - 0014: Consumption tax seed data
-- =============================================================
-- Populates the rate and category masters with the canonical values
-- used by the legacy plugin (CalcLogConsumptionTax.php).
-- IDs are deterministic ULIDs so seeds can be re-applied idempotently.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- Rates: historical → current, plus exempt / untaxed / non-taxable / export
INSERT INTO consumption_tax_rates (id, code, label, rate_percent, effective_from, effective_until, is_taxable, is_reduced, sort_order)
VALUES
  (UNHEX('01A00000000000000000000001'), 'old_3',       '旧税率 3%',     3.00,  '1989-04-01', '1997-03-31', 1, 0, 90),
  (UNHEX('01A00000000000000000000002'), 'old_5',       '旧税率 5%',     5.00,  '1997-04-01', '2014-03-31', 1, 0, 80),
  (UNHEX('01A00000000000000000000003'), 'old_8',       '旧税率 8%',     8.00,  '2014-04-01', '2019-09-30', 1, 0, 70),
  (UNHEX('01A00000000000000000000004'), 'standard_10', '標準税率 10%',  10.00, '2019-10-01', NULL,         1, 0, 10),
  (UNHEX('01A00000000000000000000005'), 'reduced_8',   '軽減税率 8%',   8.00,  '2019-10-01', NULL,         1, 1, 20),
  (UNHEX('01A00000000000000000000006'), 'exempt',      '免税（輸出）',  0.00,  '1989-04-01', NULL,         1, 0, 30),
  (UNHEX('01A00000000000000000000007'), 'non_taxable', '非課税',        0.00,  '1989-04-01', NULL,         0, 0, 40),
  (UNHEX('01A00000000000000000000008'), 'untaxed',     '不課税',        0.00,  '1989-04-01', NULL,         0, 0, 50)
ON DUPLICATE KEY UPDATE label = VALUES(label), rate_percent = VALUES(rate_percent), is_taxable = VALUES(is_taxable), is_reduced = VALUES(is_reduced), sort_order = VALUES(sort_order);

-- Categories: sales side
INSERT INTO consumption_tax_categories (id, code, label, side, deductible, sort_order)
VALUES
  (UNHEX('01B00000000000000000000001'), 'taxable_sales',                       '課税売上',                 'sales',    0, 10),
  (UNHEX('01B00000000000000000000002'), 'non_taxable_sales',                   '非課税売上',               'sales',    0, 20),
  (UNHEX('01B00000000000000000000003'), 'exempt_sales',                        '免税売上（輸出）',         'sales',    0, 30),
  (UNHEX('01B00000000000000000000004'), 'untaxed_sales',                       '不課税売上',               'sales',    0, 40),
  (UNHEX('01B00000000000000000000005'), 'taxable_purchase',                    '課税仕入（インボイス）',   'purchase', 1, 50),
  (UNHEX('01B00000000000000000000006'), 'taxable_purchase_non_registered',     '課税仕入（非登録事業者）', 'purchase', 1, 60),
  (UNHEX('01B00000000000000000000007'), 'non_taxable_purchase',                '非課税仕入',               'purchase', 0, 70),
  (UNHEX('01B00000000000000000000008'), 'exempt_purchase',                     '免税仕入',                 'purchase', 0, 80),
  (UNHEX('01B00000000000000000000009'), 'untaxed_purchase',                    '不課税仕入',               'purchase', 0, 90)
ON DUPLICATE KEY UPDATE label = VALUES(label), side = VALUES(side), deductible = VALUES(deductible), sort_order = VALUES(sort_order);
