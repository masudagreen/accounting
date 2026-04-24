-- Rollback for 0003_create_journal_tables.sql
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS journal_entry_lines;
DROP TABLE IF EXISTS journal_entries;
SET FOREIGN_KEY_CHECKS = 1;
