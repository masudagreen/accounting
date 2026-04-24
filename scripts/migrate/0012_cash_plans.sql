-- =============================================================
-- Rucaro Accounting - 0012: Cash plans (Phase 6 Wave 6-E)
-- =============================================================
-- Creates:
--   - cash_plans         : header per (entity, fiscal term, name)
--   - cash_plan_entries  : 12-month wide row per line (category × label)
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE TABLE cash_plans (
    id                  BINARY(16)   NOT NULL,
    entity_id           BINARY(16)   NOT NULL,
    fiscal_term_id      BINARY(16)   NOT NULL,
    name                VARCHAR(128) NOT NULL,
    opening_balance     DECIMAL(18, 4) NOT NULL DEFAULT 0,
    currency_code       CHAR(3)      NOT NULL DEFAULT 'JPY',
    notes               TEXT         NULL,
    created_by          BINARY(16)   NOT NULL,
    created_at          TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at          TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at          TIMESTAMP(6) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cp__entity_ft_name (entity_id, fiscal_term_id, name),
    CONSTRAINT fk_cp__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_cp__ft FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='資金繰り計画のヘッダ';

CREATE TABLE cash_plan_entries (
    id                  BINARY(16)   NOT NULL,
    cash_plan_id        BINARY(16)   NOT NULL,
    category            VARCHAR(24)  NOT NULL,
    label               VARCHAR(128) NOT NULL,
    sort_order          INT UNSIGNED NOT NULL DEFAULT 0,
    month_1             DECIMAL(18, 4) NOT NULL DEFAULT 0 COMMENT '期首月',
    month_2             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_3             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_4             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_5             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_6             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_7             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_8             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_9             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_10            DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_11            DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_12            DECIMAL(18, 4) NOT NULL DEFAULT 0,
    memo                VARCHAR(255) NULL,
    PRIMARY KEY (id),
    KEY idx_cpe__plan (cash_plan_id, sort_order),
    CONSTRAINT fk_cpe__plan FOREIGN KEY (cash_plan_id) REFERENCES cash_plans (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_cpe__category CHECK (category IN ('operating_in','operating_out','investing_in','investing_out','financing_in','financing_out'))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='資金繰り計画の月次明細';
