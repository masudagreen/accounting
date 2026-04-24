-- =============================================================
-- Rucaro Accounting - 0005: Trial balance monthly snapshots
-- =============================================================
-- Creates:
--   - trial_balance_snapshots : monthly SUM cache per account_title
--
-- Scope (ADR-007 §9.2 Wave 1 / Phase 4.3):
--   TrialBalance is a read-only projection over journal_entry_lines.
--   Exact-month aggregates are persisted here so the query path can
--   return cached rows for closed months and fall back to live SUM
--   only for the current (unclosed) month.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE TABLE trial_balance_snapshots (
    id                 BINARY(16)      NOT NULL COMMENT 'ULID',
    entity_id          BINARY(16)      NOT NULL,
    fiscal_term_id     BINARY(16)      NOT NULL,
    snapshot_date      DATE            NOT NULL COMMENT '月末締め日',
    account_title_id   BINARY(16)      NOT NULL,
    debit_total        DECIMAL(18, 4)  NOT NULL DEFAULT 0,
    credit_total       DECIMAL(18, 4)  NOT NULL DEFAULT 0,
    balance            DECIMAL(18, 4)  NOT NULL DEFAULT 0,
    line_count         INT UNSIGNED    NOT NULL DEFAULT 0,
    generated_at       TIMESTAMP(6)    NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_tb_snapshot (entity_id, fiscal_term_id, snapshot_date, account_title_id),
    KEY idx_tb_snapshot__entity_date (entity_id, snapshot_date),
    CONSTRAINT fk_tb_snapshot__entity
        FOREIGN KEY (entity_id) REFERENCES entities (id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_tb_snapshot__fiscal
        FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_tb_snapshot__account
        FOREIGN KEY (account_title_id) REFERENCES account_titles (id)
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='試算表の月次スナップショット（再計算キャッシュ）';
