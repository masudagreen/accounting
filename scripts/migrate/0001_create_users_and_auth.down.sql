-- Rollback for 0001_create_users_and_auth.sql
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS api_tokens;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;
