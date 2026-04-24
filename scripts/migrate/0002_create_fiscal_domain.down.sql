-- Rollback for 0002_create_fiscal_domain.sql
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS sub_account_titles;
DROP TABLE IF EXISTS account_titles;
DROP TABLE IF EXISTS fiscal_terms;
DROP TABLE IF EXISTS entities;
SET FOREIGN_KEY_CHECKS = 1;
