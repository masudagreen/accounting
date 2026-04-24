-- =============================================================
-- Rucaro Accounting - 0000: Database bootstrap
-- =============================================================
-- Create database with utf8mb4 default and set up the
-- schema_migrations history table used by MigrationRunner.
-- =============================================================

CREATE DATABASE IF NOT EXISTS rucaro
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE rucaro;

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- schema_migrations: migration history
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS schema_migrations (
  version     VARCHAR(32)  NOT NULL COMMENT 'ファイル名の連番プレフィックス（例: 0001）',
  applied_at  TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  checksum    CHAR(64)     NULL DEFAULT NULL COMMENT '適用時の SQL SHA-256（差替え検知用）',
  PRIMARY KEY (version)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='適用済みマイグレーション履歴（MigrationRunner が管理）';
