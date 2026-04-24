-- =============================================================
-- Rucaro Accounting - 0003: Journal
-- =============================================================
-- Creates:
--   - journal_entries       : journal entry headers
--   - journal_entry_lines   : journal entry lines (debit/credit)
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- journal_entries
-- -------------------------------------------------------------
CREATE TABLE journal_entries (
  id               BINARY(16)      NOT NULL COMMENT 'ULID',
  entity_id        BINARY(16)      NOT NULL,
  fiscal_term_id   BINARY(16)      NOT NULL,
  journal_date     DATE            NOT NULL COMMENT '計上日',
  booked_at        TIMESTAMP(6)    NOT NULL COMMENT '記帳タイムスタンプ',
  summary          VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '摘要',
  total_amount     DECIMAL(18, 4)  NOT NULL COMMENT '借方合計（= 貸方合計）',
  currency_code    CHAR(3)         NOT NULL DEFAULT 'JPY',
  status           VARCHAR(16)     NOT NULL DEFAULT 'draft'
                                   COMMENT 'draft / pending_approval / approved / rejected / posted',
  source           VARCHAR(16)     NOT NULL DEFAULT 'manual'
                                   COMMENT 'manual / ai_receipt / bank_import / mail_import',
  source_receipt_id BINARY(16)     NULL DEFAULT NULL COMMENT 'receipts.id（該当する場合）',
  created_by       BINARY(16)      NOT NULL COMMENT 'users.id',
  approved_by      BINARY(16)      NULL DEFAULT NULL,
  approved_at      TIMESTAMP(6)    NULL DEFAULT NULL,
  created_at       TIMESTAMP(6)    NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at       TIMESTAMP(6)    NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  deleted_at       TIMESTAMP(6)    NULL DEFAULT NULL,
  is_alive         TINYINT(1) GENERATED ALWAYS AS (deleted_at IS NULL) VIRTUAL,
  PRIMARY KEY (id),
  KEY idx_journal__entity_term_date (entity_id, fiscal_term_id, booked_at),
  KEY idx_journal__alive (entity_id, fiscal_term_id, is_alive, booked_at),
  KEY idx_journal__status (entity_id, status, booked_at),
  KEY idx_journal__receipt (source_receipt_id),
  CONSTRAINT fk_journal__entity
    FOREIGN KEY (entity_id) REFERENCES entities (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_journal__term
    FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_journal__created_by
    FOREIGN KEY (created_by) REFERENCES users (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_journal__approved_by
    FOREIGN KEY (approved_by) REFERENCES users (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_journal__status CHECK (
    status IN (
      'draft', 'pending_approval', 'approved', 'rejected', 'posted',
      'reversed', 'voided', 'canceled'
    )
  ),
  CONSTRAINT chk_journal__source CHECK (
    source IN ('manual', 'ai_receipt', 'bank_import', 'mail_import')
  ),
  CONSTRAINT chk_journal__total_nonnegative CHECK (total_amount >= 0)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='仕訳本体（旧 accountingLog）。明細は journal_entry_lines';

-- -------------------------------------------------------------
-- journal_entry_lines
-- -------------------------------------------------------------
CREATE TABLE journal_entry_lines (
  id                  BINARY(16)     NOT NULL COMMENT 'ULID',
  entry_id            BINARY(16)     NOT NULL,
  line_no             SMALLINT       NOT NULL COMMENT '行番号（1 起番）',
  side                VARCHAR(6)     NOT NULL COMMENT 'debit / credit',
  account_title_id    BINARY(16)     NOT NULL,
  sub_account_title_id BINARY(16)    NULL DEFAULT NULL,
  amount              DECIMAL(18, 4) NOT NULL,
  tax_rate_percent    DECIMAL(5, 2)  NOT NULL DEFAULT 0 COMMENT '軽減 8%, 標準 10% 等',
  tax_amount          DECIMAL(18, 4) NOT NULL DEFAULT 0,
  is_tax_reduced      BOOLEAN        NOT NULL DEFAULT FALSE COMMENT '軽減税率対象',
  memo                VARCHAR(255)   NOT NULL DEFAULT '',
  booked_at           TIMESTAMP(6)   NOT NULL COMMENT 'journal_entries.booked_at のコピー（試算表索引用）',
  created_at          TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at          TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id),
  UNIQUE KEY uq_journal_lines__entry_line (entry_id, line_no),
  KEY idx_journal_lines__account_booked (account_title_id, booked_at),
  KEY idx_journal_lines__sub (sub_account_title_id),
  CONSTRAINT fk_journal_lines__entry
    FOREIGN KEY (entry_id) REFERENCES journal_entries (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_journal_lines__account
    FOREIGN KEY (account_title_id) REFERENCES account_titles (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_journal_lines__sub
    FOREIGN KEY (sub_account_title_id) REFERENCES sub_account_titles (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_journal_lines__side CHECK (side IN ('debit', 'credit')),
  CONSTRAINT chk_journal_lines__amount CHECK (amount >= 0),
  CONSTRAINT chk_journal_lines__tax_amount CHECK (tax_amount >= 0)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='仕訳明細（借方 / 貸方 × 行）';
