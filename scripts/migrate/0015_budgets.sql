-- =============================================================
-- Rucaro Accounting - 0015: Budgets (Phase 6 Wave 6-G)
-- =============================================================
-- Creates:
--   - budgets             : header per (entity, fiscal term, name)
--   - budget_line_items   : 12-month wide row per (account_title, sub_account_title)
--
-- Ports the legacy Jpn_Budget / Jpn_BudgetEditor / Jpn_BudgetOutput
-- triple into a ports-and-adapters shape. See ADR-015 for the design
-- rationale.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE TABLE budgets (
    id                BINARY(16)   NOT NULL,
    entity_id         BINARY(16)   NOT NULL,
    fiscal_term_id    BINARY(16)   NOT NULL,
    name              VARCHAR(128) NOT NULL,
    status            VARCHAR(16)  NOT NULL DEFAULT 'draft' COMMENT 'draft / approved / locked',
    approved_by       BINARY(16)   NULL,
    approved_at       TIMESTAMP(6) NULL,
    notes             TEXT         NULL,
    created_by        BINARY(16)   NOT NULL,
    created_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at        TIMESTAMP(6) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_b__entity_ft_name (entity_id, fiscal_term_id, name),
    CONSTRAINT fk_b__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_b__ft     FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id) ON UPDATE CASCADE,
    CONSTRAINT fk_b__approver FOREIGN KEY (approved_by) REFERENCES users (id) ON UPDATE CASCADE,
    CONSTRAINT chk_b__status CHECK (status IN ('draft','approved','locked'))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT='予算ヘッダ';

CREATE TABLE budget_line_items (
    id                   BINARY(16)   NOT NULL,
    budget_id            BINARY(16)   NOT NULL,
    account_title_id     BINARY(16)   NOT NULL,
    sub_account_title_id BINARY(16)   NULL,
    sort_order           INT UNSIGNED NOT NULL DEFAULT 0,
    month_1              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_2              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_3              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_4              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_5              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_6              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_7              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_8              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_9              DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_10             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_11             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    month_12             DECIMAL(18, 4) NOT NULL DEFAULT 0,
    memo                 VARCHAR(255) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_bli__budget_at (budget_id, account_title_id, sub_account_title_id),
    KEY idx_bli__budget (budget_id, sort_order),
    CONSTRAINT fk_bli__budget FOREIGN KEY (budget_id) REFERENCES budgets (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_bli__at FOREIGN KEY (account_title_id) REFERENCES account_titles (id) ON UPDATE CASCADE,
    CONSTRAINT fk_bli__sat FOREIGN KEY (sub_account_title_id) REFERENCES sub_account_titles (id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT='予算明細（月次）';
