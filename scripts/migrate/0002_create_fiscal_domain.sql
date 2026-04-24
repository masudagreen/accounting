-- =============================================================
-- Rucaro Accounting - 0002: Fiscal domain
-- =============================================================
-- Creates:
--   - entities            : accounting entities (individual / corporate)
--   - fiscal_terms        : fiscal periods per entity
--   - account_titles      : chart of accounts (hierarchical)
--   - sub_account_titles  : sub-accounts under account_titles
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- entities
-- -------------------------------------------------------------
CREATE TABLE entities (
  id                BINARY(16)    NOT NULL COMMENT 'ULID',
  owner_user_id     BINARY(16)    NOT NULL COMMENT 'users.id への FK',
  name              VARCHAR(128)  NOT NULL COMMENT '屋号 / 会社名',
  nation_code       CHAR(3)       NOT NULL DEFAULT 'JPN' COMMENT 'ISO 3166-1 alpha-3',
  currency_code     CHAR(3)       NOT NULL DEFAULT 'JPY' COMMENT 'ISO 4217',
  fiscal_start_mmdd CHAR(4)       NOT NULL DEFAULT '0101' COMMENT '会計年度開始 MMDD',
  is_active         BOOLEAN       NOT NULL DEFAULT TRUE,
  created_at        TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at        TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  deleted_at        TIMESTAMP(6)  NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_entities__owner (owner_user_id, is_active, deleted_at),
  CONSTRAINT fk_entities__owner
    FOREIGN KEY (owner_user_id) REFERENCES users (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='会計主体（個人事業 / 法人）';

-- -------------------------------------------------------------
-- fiscal_terms
-- -------------------------------------------------------------
CREATE TABLE fiscal_terms (
  id             BINARY(16)   NOT NULL COMMENT 'ULID',
  entity_id      BINARY(16)   NOT NULL,
  fiscal_period  INT          NOT NULL COMMENT '期番号（1 期 / 2 期 …）',
  start_date     DATE         NOT NULL,
  end_date       DATE         NOT NULL,
  is_closed      BOOLEAN      NOT NULL DEFAULT FALSE COMMENT '決算締切後 TRUE',
  closed_at      TIMESTAMP(6) NULL DEFAULT NULL,
  created_at     TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at     TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id),
  UNIQUE KEY uq_fiscal_terms__entity_period (entity_id, fiscal_period),
  KEY idx_fiscal_terms__dates (entity_id, start_date, end_date),
  CONSTRAINT fk_fiscal_terms__entity
    FOREIGN KEY (entity_id) REFERENCES entities (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_fiscal_terms__dates CHECK (end_date >= start_date)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='会計期（旧 baseTerm）';

-- -------------------------------------------------------------
-- account_titles
-- -------------------------------------------------------------
CREATE TABLE account_titles (
  id              BINARY(16)    NOT NULL COMMENT 'ULID',
  entity_id       BINARY(16)    NOT NULL,
  code            VARCHAR(16)   NOT NULL COMMENT '勘定科目コード',
  name            VARCHAR(128)  NOT NULL COMMENT '勘定科目名',
  category        VARCHAR(32)   NOT NULL COMMENT 'asset / liability / equity / revenue / expense',
  normal_side     VARCHAR(6)    NOT NULL COMMENT 'debit / credit',
  parent_id       BINARY(16)    NULL DEFAULT NULL COMMENT '階層（null なら最上位）',
  sort_order      INT           NOT NULL DEFAULT 0,
  is_active       BOOLEAN       NOT NULL DEFAULT TRUE,
  created_at      TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at      TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  deleted_at      TIMESTAMP(6)  NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_account_titles__entity_code (entity_id, code),
  KEY idx_account_titles__parent (parent_id),
  KEY idx_account_titles__entity_active (entity_id, is_active, deleted_at, sort_order),
  CONSTRAINT fk_account_titles__entity
    FOREIGN KEY (entity_id) REFERENCES entities (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_account_titles__parent
    FOREIGN KEY (parent_id) REFERENCES account_titles (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_account_titles__category
    CHECK (category IN ('asset', 'liability', 'equity', 'revenue', 'expense')),
  CONSTRAINT chk_account_titles__normal_side
    CHECK (normal_side IN ('debit', 'credit'))
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='勘定科目（Chart of Accounts）';

-- -------------------------------------------------------------
-- sub_account_titles
-- -------------------------------------------------------------
CREATE TABLE sub_account_titles (
  id                BINARY(16)    NOT NULL COMMENT 'ULID',
  account_title_id  BINARY(16)    NOT NULL,
  code              VARCHAR(16)   NOT NULL,
  name              VARCHAR(128)  NOT NULL,
  sort_order        INT           NOT NULL DEFAULT 0,
  is_active         BOOLEAN       NOT NULL DEFAULT TRUE,
  created_at        TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at        TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  deleted_at        TIMESTAMP(6)  NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sub_account_titles__parent_code (account_title_id, code),
  KEY idx_sub_account_titles__active (account_title_id, is_active, deleted_at, sort_order),
  CONSTRAINT fk_sub_account_titles__account
    FOREIGN KEY (account_title_id) REFERENCES account_titles (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='補助科目';
