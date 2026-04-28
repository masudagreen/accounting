-- Migration 0001 rollback: Drop all tables created in 0001_initial_schema.up.sql
-- Order: reverse of creation (dependent tables first)

DROP TABLE IF EXISTS schema_migrations;
DROP TABLE IF EXISTS accountingLogFixedAssetsJpn;
DROP TABLE IF EXISTS accountingFixedAssetsJpn;
DROP TABLE IF EXISTS accountingSubAccountTitleJpn;
DROP TABLE IF EXISTS accountingLogCalcJpn;
DROP TABLE IF EXISTS accountingLog;
DROP TABLE IF EXISTS accountingFSValueJpn;
DROP TABLE IF EXISTS accountingFSJpn;
DROP TABLE IF EXISTS accountingEntityJpn;
DROP TABLE IF EXISTS accountingEntity;
DROP TABLE IF EXISTS baseToken;
DROP TABLE IF EXISTS baseSession;
DROP TABLE IF EXISTS baseAccount;
DROP TABLE IF EXISTS basePreference;
