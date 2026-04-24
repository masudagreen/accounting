-- =============================================================
-- Rucaro Accounting - 0009: CS (Cash Flow Statement) mappings and section definitions
-- =============================================================
-- Creates:
--   - fs_cs_section_definitions     : CS (間接法) のセクション構造（マスタ）
--   - account_title_cs_mappings     : 勘定科目 → CS 区分のマッピング
--
-- Phase 6 Wave 6-B: 旧 back/class/else/plugin/accounting/jpn/FinancialStatementCS*.php
--                    / CalcAccountTitleFSCS.php / AccountTitleFSCS.php
--                    が保持していた CS 階層と勘定科目マッピングを
--                    正規化スキーマへ移植する。間接法（indirect method）を採用。
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- fs_cs_section_definitions
-- -------------------------------------------------------------
--   CS (キャッシュフロー計算書・間接法) の階層セクション定義。
--   parent_code で親子関係を表す（NULL なら最上位ノード）。
--   is_subtotal = 1 : 小計（営業活動CF小計 等）
--   is_total    = 1 : 合計（営業活動CF, 投資活動CF, 財務活動CF, 期首残高, 期末残高）
--   formula     : 計算式。NULL のときは配下科目の sign 付き SUM。
-- -------------------------------------------------------------
CREATE TABLE fs_cs_section_definitions (
    id            BINARY(16)   NOT NULL COMMENT 'ULID',
    code          VARCHAR(64)  NOT NULL COMMENT 'operating_cf_total, investing_cf_total, etc.',
    parent_code   VARCHAR(64)  NULL DEFAULT NULL COMMENT '親セクションコード（階層）',
    label         VARCHAR(128) NOT NULL,
    sort_order    INT UNSIGNED NOT NULL DEFAULT 0,
    is_subtotal   TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '小計行なら 1',
    is_total      TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '合計行なら 1',
    formula       VARCHAR(255) NULL DEFAULT NULL COMMENT '計算式（+code/-code の並び）',
    PRIMARY KEY (id),
    UNIQUE KEY uq_fs_cs_sec__code (code),
    KEY idx_fs_cs_sec__parent (parent_code, sort_order),
    CONSTRAINT chk_fs_cs_sec__bool CHECK (is_subtotal IN (0,1) AND is_total IN (0,1))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='CS（キャッシュフロー計算書・間接法）の階層セクション定義';

-- -------------------------------------------------------------
-- account_title_cs_mappings
-- -------------------------------------------------------------
--   勘定科目 ID → CS セクションコード への紐付け。
--   sign = +1 は加算、-1 は減算。
--   flow_category は operating / investing / financing のいずれか。
--   is_working_capital = 1 は運転資本の増減（売掛金、棚卸資産、買掛金 等）で、
--     CS 計算時に「期中増減額」として符号反転を伴って扱われる。
-- -------------------------------------------------------------
CREATE TABLE account_title_cs_mappings (
    id                 BINARY(16)   NOT NULL COMMENT 'ULID',
    entity_id          BINARY(16)   NOT NULL,
    account_title_id   BINARY(16)   NOT NULL,
    cs_section_code    VARCHAR(64)  NOT NULL COMMENT 'fs_cs_section_definitions.code の参照',
    sort_order         INT UNSIGNED NOT NULL DEFAULT 0,
    display_label      VARCHAR(128) NULL DEFAULT NULL COMMENT 'NULL の場合は account_title.name',
    sign               TINYINT      NOT NULL DEFAULT 1 COMMENT '+1 加算、-1 減算',
    flow_category      VARCHAR(16)  NOT NULL COMMENT 'operating / investing / financing',
    is_working_capital TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '運転資本増減なら 1',
    created_at         TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at         TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_atcs__at (entity_id, account_title_id),
    KEY idx_atcs__section (entity_id, cs_section_code, sort_order),
    CONSTRAINT fk_atcs__entity FOREIGN KEY (entity_id) REFERENCES entities (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_atcs__at FOREIGN KEY (account_title_id) REFERENCES account_titles (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_atcs__flow CHECK (flow_category IN ('operating', 'investing', 'financing')),
    CONSTRAINT chk_atcs__sign CHECK (sign IN (-1, 1)),
    CONSTRAINT chk_atcs__wc CHECK (is_working_capital IN (0, 1))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='勘定科目 → CS（キャッシュフロー計算書）区分のマッピング';
