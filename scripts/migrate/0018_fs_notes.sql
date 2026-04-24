-- =============================================================
-- Rucaro Accounting - 0018: Financial Statement Notes (Phase 6 Wave 6-H-3)
-- =============================================================
-- Creates:
--   - fs_note_templates : standard templates shipped by the platform
--                         (keyed by `code`, seeded by 0018_fs_notes_seed.sql)
--   - fs_notes          : per-(entity, fiscal_term) note rows; body is
--                         free-form markdown/plain text. May reference a
--                         template by code, or be fully custom.
--
-- Ports the legacy Jpn_NotesFS / Jpn_NotesFSEditor / Jpn_NotesFSOutput
-- triple into a ports-and-adapters shape. See ADR-018 for the design
-- rationale.
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE TABLE fs_note_templates (
    id            BINARY(16)   NOT NULL,
    code          VARCHAR(32)  NOT NULL,
    category      VARCHAR(32)  NOT NULL,
    label         VARCHAR(128) NOT NULL,
    default_body  TEXT         NOT NULL,
    sort_order    INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_fnt__code (code),
    CONSTRAINT chk_fnt__category CHECK (category IN ('accounting_policy','balance_sheet_notes','pl_notes','ss_notes','related_party','contingent_liability','other'))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='注記表の標準テンプレート';

CREATE TABLE fs_notes (
    id                BINARY(16)   NOT NULL,
    entity_id         BINARY(16)   NOT NULL,
    fiscal_term_id    BINARY(16)   NOT NULL,
    template_code     VARCHAR(32)  NULL COMMENT 'テンプレ由来の場合は紐付',
    category          VARCHAR(32)  NOT NULL,
    label             VARCHAR(128) NOT NULL,
    body              TEXT         NOT NULL,
    sort_order        INT UNSIGNED NOT NULL DEFAULT 0,
    is_active         TINYINT      NOT NULL DEFAULT 1,
    created_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    KEY idx_fn__entity_ft (entity_id, fiscal_term_id, sort_order),
    CONSTRAINT fk_fn__entity FOREIGN KEY (entity_id) REFERENCES entities (id) ON UPDATE CASCADE,
    CONSTRAINT fk_fn__ft     FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id) ON UPDATE CASCADE,
    CONSTRAINT chk_fn__category CHECK (category IN ('accounting_policy','balance_sheet_notes','pl_notes','ss_notes','related_party','contingent_liability','other'))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='注記表（エンティティ × 期別）';
