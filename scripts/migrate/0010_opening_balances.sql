-- =============================================================
-- Rucaro Accounting - 0010: Opening balances
-- =============================================================
-- Creates:
--   - opening_balances : per-account opening balance for a fiscal term
--
-- Scope (Phase 6 Wave 6-C — Ledger port):
--   The general ledger needs a 前期繰越 (opening balance) per account so
--   running balances roll forward correctly. Until a close-fiscal-term
--   workflow is introduced (later Wave) this table stays empty and the
--   ZeroOpeningBalanceRepository is wired by default, returning 0 for
--   every account. The table shape is finalised here so schema churn
--   does not block subsequent waves.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE TABLE opening_balances (
    id                BINARY(16)     NOT NULL COMMENT 'ULID',
    entity_id         BINARY(16)     NOT NULL,
    fiscal_term_id    BINARY(16)     NOT NULL,
    account_title_id  BINARY(16)     NOT NULL,
    amount            DECIMAL(18, 4) NOT NULL DEFAULT 0,
    currency_code     CHAR(3)        NOT NULL DEFAULT 'JPY',
    created_at        TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at        TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_ob__at_ft (entity_id, fiscal_term_id, account_title_id),
    CONSTRAINT fk_ob__entity
        FOREIGN KEY (entity_id) REFERENCES entities (id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_ob__ft
        FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_ob__at
        FOREIGN KEY (account_title_id) REFERENCES account_titles (id)
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='期首残高（勘定科目別）。Ledger と BS の期首値算出に使用。';
