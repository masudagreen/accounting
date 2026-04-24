-- =============================================================
-- Rucaro Accounting - 0014: Consumption tax (Phase 6 Wave 6-F)
-- =============================================================
-- Ports the legacy 消費税 plumbing from
--   back/class/else/plugin/accounting/jpn/ConsumptionTax*.php
-- and introduces five first-class tables:
--   - consumption_tax_rates                    : 税率マスタ（施行日付つき）
--   - consumption_tax_categories               : 税区分マスタ（課税/非課税/免税/不課税）
--   - account_title_consumption_tax_defaults   : 勘定科目ごとの既定区分
--   - consumption_tax_invoice_registrations    : 取引先インボイス登録状況
--   - consumption_tax_periods                  : 課税期間と計算方法
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- consumption_tax_rates
-- -------------------------------------------------------------
CREATE TABLE consumption_tax_rates (
    id               BINARY(16)   NOT NULL,
    code             VARCHAR(32)  NOT NULL COMMENT '例: standard_10, reduced_8, old_8, old_5, old_3, exempt, non_taxable, untaxed, export',
    label            VARCHAR(64)  NOT NULL,
    rate_percent     DECIMAL(5, 2) NOT NULL COMMENT '10.00, 8.00, 5.00, 3.00, 0.00',
    effective_from   DATE         NOT NULL,
    effective_until  DATE         NULL,
    is_taxable       TINYINT      NOT NULL DEFAULT 1 COMMENT '課税対象か',
    is_reduced       TINYINT      NOT NULL DEFAULT 0 COMMENT '軽減税率か',
    sort_order       INT UNSIGNED NOT NULL DEFAULT 0,
    created_at       TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at       TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_ctr__code_from (code, effective_from),
    KEY idx_ctr__effective (effective_from, effective_until)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='消費税率マスタ（施行日付・軽減フラグつき）';

-- -------------------------------------------------------------
-- consumption_tax_categories
-- -------------------------------------------------------------
CREATE TABLE consumption_tax_categories (
    id            BINARY(16)   NOT NULL,
    code          VARCHAR(32)  NOT NULL COMMENT '例: taxable_sales, non_taxable_sales, exempt_sales, untaxed, taxable_purchase, taxable_purchase_non_registered',
    label         VARCHAR(64)  NOT NULL,
    side          VARCHAR(8)   NOT NULL COMMENT 'sales / purchase',
    deductible    TINYINT      NOT NULL DEFAULT 0 COMMENT '仕入税額控除可能か',
    sort_order    INT UNSIGNED NOT NULL DEFAULT 0,
    created_at    TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at    TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_ctc__code (code),
    CONSTRAINT chk_ctc__side CHECK (side IN ('sales', 'purchase', 'both'))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='消費税の取引区分マスタ';

-- -------------------------------------------------------------
-- account_title_consumption_tax_defaults
-- -------------------------------------------------------------
CREATE TABLE account_title_consumption_tax_defaults (
    id                    BINARY(16)   NOT NULL,
    entity_id             BINARY(16)   NOT NULL,
    account_title_id      BINARY(16)   NOT NULL,
    default_category_code VARCHAR(32)  NOT NULL,
    default_rate_code     VARCHAR(32)  NULL,
    created_at            TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at            TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_atctd__at (entity_id, account_title_id),
    CONSTRAINT fk_atctd__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_atctd__at FOREIGN KEY (account_title_id) REFERENCES account_titles (id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='勘定科目の既定消費税区分（仕訳入力時の初期値）';

-- -------------------------------------------------------------
-- consumption_tax_invoice_registrations
-- -------------------------------------------------------------
CREATE TABLE consumption_tax_invoice_registrations (
    id                  BINARY(16)   NOT NULL,
    entity_id           BINARY(16)   NOT NULL,
    counterparty_name   VARCHAR(255) NOT NULL,
    registration_number VARCHAR(32)  NULL COMMENT 'T1234567890123 形式',
    is_registered       TINYINT      NOT NULL DEFAULT 0,
    registered_from     DATE         NULL,
    registered_until    DATE         NULL,
    notes               VARCHAR(255) NULL,
    created_at          TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at          TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    KEY idx_ctir__entity_name (entity_id, counterparty_name),
    KEY idx_ctir__reg_no (registration_number),
    CONSTRAINT fk_ctir__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='取引先のインボイス登録状況（2023-10-01〜）';

-- -------------------------------------------------------------
-- consumption_tax_periods
-- -------------------------------------------------------------
CREATE TABLE consumption_tax_periods (
    id                            BINARY(16)   NOT NULL,
    entity_id                     BINARY(16)   NOT NULL,
    fiscal_term_id                BINARY(16)   NOT NULL,
    period_from                   DATE         NOT NULL,
    period_to                     DATE         NOT NULL,
    calculation_method            VARCHAR(16)  NOT NULL COMMENT 'principle / simplified / two_percent',
    simplified_business_category  TINYINT      NULL COMMENT '簡易課税の事業区分 1-6',
    is_interim                    TINYINT      NOT NULL DEFAULT 0,
    settlement_status             VARCHAR(16)  NOT NULL DEFAULT 'pending' COMMENT 'pending / calculated / filed / paid',
    settled_at                    TIMESTAMP(6) NULL,
    created_at                    TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at                    TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_ctp__entity_from (entity_id, period_from),
    KEY idx_ctp__fiscal_term (fiscal_term_id),
    CONSTRAINT fk_ctp__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_ctp__ft FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id) ON UPDATE CASCADE,
    CONSTRAINT chk_ctp__method CHECK (calculation_method IN ('principle', 'simplified', 'two_percent')),
    CONSTRAINT chk_ctp__status CHECK (settlement_status IN ('pending', 'calculated', 'filed', 'paid')),
    CONSTRAINT chk_ctp__period CHECK (period_to >= period_from),
    CONSTRAINT chk_ctp__simplified_range CHECK (simplified_business_category IS NULL OR (simplified_business_category BETWEEN 1 AND 6))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='消費税の課税期間';
