-- =============================================================
-- Rucaro Accounting - 0004: Receipts and approvals
-- =============================================================
-- Creates:
--   - receipts              : uploaded receipts (Phase 5)
--   - receipt_action_logs   : operational log on receipts
--   - approval_tokens       : email/message approval tokens
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- receipts
-- -------------------------------------------------------------
CREATE TABLE receipts (
  id              BINARY(16)     NOT NULL COMMENT 'ULID',
  entity_id       BINARY(16)     NOT NULL,
  uploader_id     BINARY(16)     NOT NULL COMMENT 'users.id',
  content_sha256  CHAR(64)       NOT NULL COMMENT 'ファイル SHA-256 hex',
  filename        VARCHAR(255)   NOT NULL,
  mime_type       VARCHAR(64)    NOT NULL,
  byte_size       BIGINT         NOT NULL,
  storage_path    VARCHAR(512)   NOT NULL COMMENT 'storage/receipts/YYYY/MM/<sha256>.<ext>',
  status          VARCHAR(16)    NOT NULL DEFAULT 'uploaded'
                                 COMMENT 'uploaded / extracting / drafted / approved / rejected / journaled',
  extracted_json  JSON           NULL DEFAULT NULL COMMENT 'Claude Sonnet が返す構造化抽出結果',
  draft_journal_id BINARY(16)    NULL DEFAULT NULL COMMENT 'Claude Opus 生成の draft journal_entries.id',
  uploaded_at     TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  extracted_at    TIMESTAMP(6)   NULL DEFAULT NULL,
  drafted_at      TIMESTAMP(6)   NULL DEFAULT NULL,
  journaled_at    TIMESTAMP(6)   NULL DEFAULT NULL,
  created_at      TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at      TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  deleted_at      TIMESTAMP(6)   NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_receipts__entity_sha (entity_id, content_sha256),
  KEY idx_receipts__entity_status (entity_id, status, created_at),
  KEY idx_receipts__draft_journal (draft_journal_id),
  CONSTRAINT fk_receipts__entity
    FOREIGN KEY (entity_id) REFERENCES entities (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_receipts__uploader
    FOREIGN KEY (uploader_id) REFERENCES users (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_receipts__draft_journal
    FOREIGN KEY (draft_journal_id) REFERENCES journal_entries (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT chk_receipts__status CHECK (
    status IN ('uploaded', 'extracting', 'drafted', 'approved', 'rejected', 'journaled')
  )
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='領収書（Phase 5）。バイナリは content-addressed storage へ';

-- -------------------------------------------------------------
-- receipt_action_logs
-- -------------------------------------------------------------
CREATE TABLE receipt_action_logs (
  id            BINARY(16)    NOT NULL COMMENT 'ULID',
  receipt_id    BINARY(16)    NOT NULL,
  actor_user_id BINARY(16)    NULL DEFAULT NULL COMMENT 'ユーザ起因の場合のみ',
  action        VARCHAR(32)   NOT NULL
                              COMMENT 'uploaded / extracted / drafted / approval_requested / approved / rejected / journaled / retry_requested',
  detail        JSON          NULL DEFAULT NULL,
  occurred_at   TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  created_at    TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id),
  KEY idx_receipt_logs__receipt_time (receipt_id, occurred_at),
  KEY idx_receipt_logs__actor (actor_user_id, occurred_at),
  CONSTRAINT fk_receipt_logs__receipt
    FOREIGN KEY (receipt_id) REFERENCES receipts (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_receipt_logs__actor
    FOREIGN KEY (actor_user_id) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT chk_receipt_logs__action CHECK (
    action IN (
      'uploaded', 'extracted', 'drafted',
      'approval_requested', 'approved', 'rejected',
      'journaled', 'retry_requested'
    )
  )
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='領収書操作の運用ログ（法令準拠ではない）';

-- -------------------------------------------------------------
-- approval_tokens
-- -------------------------------------------------------------
CREATE TABLE approval_tokens (
  id               BINARY(16)    NOT NULL COMMENT 'ULID',
  journal_entry_id BINARY(16)    NULL DEFAULT NULL COMMENT '承認対象仕訳（nullable: 領収書ドラフト承認にも使う）',
  receipt_id       BINARY(16)    NULL DEFAULT NULL COMMENT '承認対象領収書',
  token_hash       CHAR(64)      NOT NULL COMMENT 'SHA-256 hex（受信 URL の比較用）',
  channel          VARCHAR(16)   NOT NULL COMMENT 'email / line / slack / discord',
  recipient        VARCHAR(255)  NOT NULL COMMENT '宛先メール / チャンネル ID',
  issued_at        TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  expires_at       TIMESTAMP(6)  NOT NULL,
  responded_at     TIMESTAMP(6)  NULL DEFAULT NULL,
  response         VARCHAR(16)   NULL DEFAULT NULL COMMENT 'approved / rejected',
  response_detail  VARCHAR(512)  NOT NULL DEFAULT '',
  created_at       TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at       TIMESTAMP(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id),
  UNIQUE KEY uq_approval_tokens__hash (token_hash),
  KEY idx_approval_tokens__entry (journal_entry_id),
  KEY idx_approval_tokens__receipt (receipt_id),
  KEY idx_approval_tokens__open (responded_at, expires_at),
  CONSTRAINT fk_approval_tokens__entry
    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_approval_tokens__receipt
    FOREIGN KEY (receipt_id) REFERENCES receipts (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_approval_tokens__channel
    CHECK (channel IN ('email', 'line', 'slack', 'discord')),
  CONSTRAINT chk_approval_tokens__response
    CHECK (response IS NULL OR response IN ('approved', 'rejected'))
  -- NOTE: 「journal_entry_id と receipt_id の少なくとも片方は NOT NULL」制約は
  -- MariaDB 10.11 が FK 列を CHECK で参照するとエラー 1901 を返すため DB 層では
  -- 設けず、アプリ層（Phase 5 で実装）で強制する
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='メール / メッセージ承認トークン（Phase 5）';
