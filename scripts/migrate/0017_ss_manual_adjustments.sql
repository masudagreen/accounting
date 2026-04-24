-- =============================================================
-- Rucaro Accounting - 0017: Statement of Changes in Equity
--                           manual adjustments (Phase 6 Wave 6-H-2)
-- =============================================================
-- Creates:
--   - ss_manual_adjustments : per-(entity, fiscal_term) rows that
--                             describe changes in equity sections
--                             that cannot be derived automatically
--                             from posted journals (dividends,
--                             new issues, treasury stock moves,
--                             valuation/translation adjustments, ...).
--
-- Ports the 株主資本等変動計算書 view from the legacy Jpn stack
-- (Jpn_FinancialStatementSS / Jpn_FinancialStatementSSOutput).
-- See ADR-017 for the rationale behind the section_code /
-- change_type_code domain enums and the decision to keep the
-- section totals derived rather than stored.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE TABLE ss_manual_adjustments (
    id                BINARY(16)   NOT NULL,
    entity_id         BINARY(16)   NOT NULL,
    fiscal_term_id    BINARY(16)   NOT NULL,
    section_code      VARCHAR(32)  NOT NULL COMMENT 'capital_stock / capital_surplus / retained_earnings / treasury_stock / valuation_adjustment / stock_acquisition_right',
    change_type_code  VARCHAR(32)  NOT NULL COMMENT 'dividend / new_issue / treasury_purchase / treasury_dispose / other',
    amount            DECIMAL(18, 4) NOT NULL,
    label             VARCHAR(128) NOT NULL,
    sort_order        INT UNSIGNED NOT NULL DEFAULT 0,
    notes             VARCHAR(255) NULL,
    created_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    KEY idx_ssma__entity_ft (entity_id, fiscal_term_id, sort_order),
    CONSTRAINT fk_ssma__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_ssma__ft     FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='株主資本等変動計算書の手動調整項目（Journal で検出できない変動）';
