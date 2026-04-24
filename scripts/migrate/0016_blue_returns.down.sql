-- =============================================================
-- Rucaro Accounting - 0016: Blue Return Forms (rollback)
-- =============================================================
SET NAMES utf8mb4;

DROP TABLE IF EXISTS blue_return_forms;
ALTER TABLE entities DROP COLUMN is_corporate;
