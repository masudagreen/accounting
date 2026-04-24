-- =============================================================
-- Rucaro Accounting - 0013: CVP classifications (Phase 6 Wave 6-E)
-- =============================================================
-- Creates:
--   - account_title_cvp_classifications : per-account variable/fixed type
--     for the Break-Even Point analyser.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE TABLE account_title_cvp_classifications (
    id                BINARY(16)   NOT NULL,
    entity_id         BINARY(16)   NOT NULL,
    account_title_id  BINARY(16)   NOT NULL,
    cost_type         VARCHAR(16)  NOT NULL COMMENT 'variable / fixed / semi_variable',
    variable_ratio    DECIMAL(5, 4) NOT NULL DEFAULT 1.0000 COMMENT 'semi_variable の変動費割合',
    notes             VARCHAR(255) NULL,
    created_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_atcvp__at (entity_id, account_title_id),
    CONSTRAINT fk_atcvp__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_atcvp__at FOREIGN KEY (account_title_id) REFERENCES account_titles (id) ON UPDATE CASCADE,
    CONSTRAINT chk_atcvp__type CHECK (cost_type IN ('variable','fixed','semi_variable')),
    CONSTRAINT chk_atcvp__ratio CHECK (variable_ratio BETWEEN 0 AND 1)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='勘定科目 → 固定費/変動費/準変動費 分類（CVP 分析用）';
