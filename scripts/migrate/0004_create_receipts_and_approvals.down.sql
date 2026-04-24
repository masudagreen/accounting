-- Rollback for 0004_create_receipts_and_approvals.sql
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS approval_tokens;
DROP TABLE IF EXISTS receipt_action_logs;
DROP TABLE IF EXISTS receipts;
SET FOREIGN_KEY_CHECKS = 1;
