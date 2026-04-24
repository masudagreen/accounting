-- =============================================================
-- Rucaro Accounting - 0011: Fixed assets ledger (Phase 6 Wave 6-D)
-- =============================================================
-- Creates:
--   - fixed_asset_categories             : master of standard categories
--   - fixed_assets                       : asset book (legacy accountingLogFixedAssetsJpn)
--   - fixed_asset_depreciation_schedules : depreciation schedule per (asset, fiscal term)
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- fixed_asset_categories : standard asset category master
-- -------------------------------------------------------------
CREATE TABLE fixed_asset_categories (
    id                        BINARY(16)   NOT NULL,
    code                      VARCHAR(32)  NOT NULL,
    label                     VARCHAR(128) NOT NULL,
    parent_code               VARCHAR(32)  NULL,
    sort_order                INT UNSIGNED NOT NULL DEFAULT 0,
    is_tangible               TINYINT      NOT NULL DEFAULT 1,
    is_depreciable            TINYINT      NOT NULL DEFAULT 1,
    default_useful_life_years INT UNSIGNED NOT NULL DEFAULT 0,
    default_method            VARCHAR(32)  NOT NULL DEFAULT 'straight_line',
    PRIMARY KEY (id),
    UNIQUE KEY uq_fac__code (code)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='固定資産の標準区分マスタ';

-- -------------------------------------------------------------
-- fixed_assets : per-asset book
-- -------------------------------------------------------------
CREATE TABLE fixed_assets (
    id                                        BINARY(16)   NOT NULL,
    entity_id                                 BINARY(16)   NOT NULL,
    asset_code                                VARCHAR(32)  NOT NULL,
    asset_name                                VARCHAR(255) NOT NULL,
    category_code                             VARCHAR(32)  NOT NULL,
    asset_account_title_id                    BINARY(16)   NULL,
    accumulated_depreciation_account_title_id BINARY(16)   NULL,
    depreciation_expense_account_title_id     BINARY(16)   NULL,
    acquisition_date                          DATE         NOT NULL,
    service_start_date                        DATE         NOT NULL,
    disposal_date                             DATE         NULL,
    acquisition_cost                          DECIMAL(18, 4) NOT NULL,
    residual_value                            DECIMAL(18, 4) NOT NULL DEFAULT 0,
    useful_life_years                         INT UNSIGNED NOT NULL,
    method                                    VARCHAR(32)  NOT NULL,
    quantity                                  INT UNSIGNED NOT NULL DEFAULT 1,
    department_code                           VARCHAR(64)  NULL,
    note                                      TEXT         NULL,
    created_by                                BINARY(16)   NOT NULL,
    created_at                                TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at                                TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at                                TIMESTAMP(6) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_fa__entity_code (entity_id, asset_code),
    KEY idx_fa__entity_date (entity_id, acquisition_date),
    CONSTRAINT fk_fa__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT chk_fa__method CHECK (method IN (
        'straight_line',
        'declining_balance',
        'declining_balance_2007',
        'declining_balance_2012',
        'declining_balance_2016',
        'old_straight_line',
        'old_declining_balance',
        'one_shot',
        'three_year_equal',
        'none'
    ))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='固定資産台帳（旧 accountingLogFixedAssetsJpn 相当）';

-- -------------------------------------------------------------
-- fixed_asset_depreciation_schedules
-- -------------------------------------------------------------
CREATE TABLE fixed_asset_depreciation_schedules (
    id                       BINARY(16)   NOT NULL,
    fixed_asset_id           BINARY(16)   NOT NULL,
    fiscal_term_id           BINARY(16)   NOT NULL,
    period_number            INT UNSIGNED NOT NULL,
    period_start_date        DATE         NOT NULL,
    period_end_date          DATE         NOT NULL,
    months_in_service        INT UNSIGNED NOT NULL DEFAULT 12,
    opening_book_value       DECIMAL(18, 4) NOT NULL,
    depreciation_amount      DECIMAL(18, 4) NOT NULL,
    accumulated_depreciation DECIMAL(18, 4) NOT NULL,
    closing_book_value       DECIMAL(18, 4) NOT NULL,
    is_posted                TINYINT      NOT NULL DEFAULT 0,
    posted_journal_entry_id  BINARY(16)   NULL,
    generated_at             TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_fads__asset_period (fixed_asset_id, fiscal_term_id),
    KEY idx_fads__asset (fixed_asset_id, period_number),
    CONSTRAINT fk_fads__asset FOREIGN KEY (fixed_asset_id) REFERENCES fixed_assets (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_fads__ft    FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='期別減価償却スケジュール（資産 × 期）';
