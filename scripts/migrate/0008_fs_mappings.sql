-- =============================================================
-- Rucaro Accounting - 0008: FS mappings and section definitions
-- =============================================================
-- Creates:
--   - fs_section_definitions     : BS/PL の階層的セクション定義（マスタ）
--   - account_title_fs_mappings  : 勘定科目 → FS 項目のマッピング（旧 accountingFSJpn 相当）
--
-- Phase 6-A: 旧 back/class/else/plugin/accounting/jpn/FinancialStatement*.php
--            で JSON カラム (jsonJgaapFSBS / jsonJgaapFSPL) に保持していた
--            決算書階層を正規化スキーマへ移植する。
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- fs_section_definitions
-- -------------------------------------------------------------
--   日本基準 BS / PL の標準階層（売上高 → 売上原価 → 売上総利益 → ...）。
--   parent_code で親子関係を表す（NULL なら最上位ノード）。
--   is_subtotal = 1 : 小計（売上総利益, 営業利益, 経常利益, 税引前, 等）
--   is_total    = 1 : 合計（資産合計, 当期純利益）
--   formula     : 計算式。NULL のときは配下科目の sign 付き SUM。
-- -------------------------------------------------------------
CREATE TABLE fs_section_definitions (
    id            BINARY(16)   NOT NULL COMMENT 'ULID',
    fs_kind       VARCHAR(8)   NOT NULL COMMENT 'bs / pl',
    code          VARCHAR(64)  NOT NULL COMMENT 'current_asset, operating_revenue, etc.',
    parent_code   VARCHAR(64)  NULL DEFAULT NULL COMMENT '親セクションコード（階層）',
    label         VARCHAR(128) NOT NULL,
    sort_order    INT UNSIGNED NOT NULL DEFAULT 0,
    is_subtotal   TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '小計行なら 1',
    is_total      TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '合計行なら 1',
    formula       VARCHAR(255) NULL DEFAULT NULL COMMENT '計算式（+code/-code の並び）',
    PRIMARY KEY (id),
    UNIQUE KEY uq_fs_sec__kind_code (fs_kind, code),
    KEY idx_fs_sec__parent (fs_kind, parent_code, sort_order),
    CONSTRAINT chk_fs_sec__kind CHECK (fs_kind IN ('bs', 'pl')),
    CONSTRAINT chk_fs_sec__bool CHECK (is_subtotal IN (0,1) AND is_total IN (0,1))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='決算書（BS / PL）の階層セクション定義（日本基準）';

-- -------------------------------------------------------------
-- account_title_fs_mappings
-- -------------------------------------------------------------
--   勘定科目 ID → FS セクションコード への紐付け。
--   sign = +1 は加算、-1 は減算（控除項目、例: 貸倒引当金）。
--   旧実装では flagDebit (0/1) をもって符号を判定していたが、
--   新実装では sign を明示することで意図を保守しやすくする。
-- -------------------------------------------------------------
CREATE TABLE account_title_fs_mappings (
    id                BINARY(16)   NOT NULL COMMENT 'ULID',
    entity_id         BINARY(16)   NOT NULL,
    account_title_id  BINARY(16)   NOT NULL,
    fs_kind           VARCHAR(8)   NOT NULL COMMENT 'bs / pl',
    fs_section_code   VARCHAR(64)  NOT NULL COMMENT 'fs_section_definitions.code の参照',
    sort_order        INT UNSIGNED NOT NULL DEFAULT 0,
    display_label     VARCHAR(128) NULL DEFAULT NULL COMMENT 'NULL の場合は account_title.name',
    sign              TINYINT      NOT NULL DEFAULT 1 COMMENT '+1 加算、-1 減算',
    created_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_fs_map__at_kind (entity_id, account_title_id, fs_kind),
    KEY idx_fs_map__section (entity_id, fs_kind, fs_section_code, sort_order),
    CONSTRAINT fk_fs_map__entity FOREIGN KEY (entity_id) REFERENCES entities (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_fs_map__at FOREIGN KEY (account_title_id) REFERENCES account_titles (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_fs_map__kind CHECK (fs_kind IN ('bs', 'pl')),
    CONSTRAINT chk_fs_map__sign CHECK (sign IN (-1, 1))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  COMMENT='勘定科目 → 決算書表示項目のマッピング（旧 accountingFSJpn 相当）';
