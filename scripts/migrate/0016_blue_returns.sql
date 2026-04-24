-- =============================================================
-- Rucaro Accounting - 0016: Blue Return Forms (Phase 6 Wave 6-H-1)
-- =============================================================
-- 青色申告決算書 (individual-entrepreneur tax-authority filing).
--
-- Two changes:
--   1. `entities.is_corporate` (null-safe default=1) so the Blue Return
--      endpoints can reject requests for corporate entities with 422.
--   2. `blue_return_forms` — one row per (entity, fiscal_term) holding
--      the four-page form snapshot as JSON. JSON (not wide columns) is
--      chosen because tax-authority form layouts change every couple of
--      years (2014, 2015, 2016, 2017, 2018, 2019, 2020, ...) and we need
--      schema latitude to absorb new fields without a migration.
--
-- Ports the legacy
--   back/class/else/plugin/accounting/jpn/BlueSheet.php
--   back/class/else/plugin/accounting/jpn/BlueSheetOutput.php
--   back/class/else/plugin/accounting/jpn/2012/public/BlueSheet*.php
-- triple. See ADR-016 for the full rationale.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

ALTER TABLE entities
    ADD COLUMN is_corporate TINYINT NOT NULL DEFAULT 1 COMMENT '1=法人, 0=個人事業主'
    AFTER fiscal_start_mmdd;

CREATE TABLE blue_return_forms (
    id                BINARY(16)   NOT NULL,
    entity_id         BINARY(16)   NOT NULL,
    fiscal_term_id    BINARY(16)   NOT NULL,
    form_type         VARCHAR(16)  NOT NULL DEFAULT 'general' COMMENT 'general / agricultural / real_estate',
    snapshot_json     LONGTEXT     NOT NULL COMMENT '4 別紙の全データ JSON',
    status            VARCHAR(16)  NOT NULL DEFAULT 'draft',
    finalized_at      TIMESTAMP(6) NULL,
    created_by        BINARY(16)   NOT NULL,
    created_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at        TIMESTAMP(6) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_brf__entity_ft (entity_id, fiscal_term_id),
    CONSTRAINT fk_brf__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_brf__ft     FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id) ON UPDATE CASCADE,
    CONSTRAINT chk_brf__status CHECK (status IN ('draft','finalized')),
    CONSTRAINT chk_brf__form_type CHECK (form_type IN ('general','agricultural','real_estate'))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='青色申告決算書（個人事業主のみ）';
