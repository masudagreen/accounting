-- =============================================================
-- Rucaro Accounting - 0014: Consumption tax (down)
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION';

DROP TABLE IF EXISTS consumption_tax_periods;
DROP TABLE IF EXISTS consumption_tax_invoice_registrations;
DROP TABLE IF EXISTS account_title_consumption_tax_defaults;
DROP TABLE IF EXISTS consumption_tax_categories;
DROP TABLE IF EXISTS consumption_tax_rates;
