-- =============================================================
-- Rucaro Accounting - 0001: Users and authentication
-- =============================================================
-- Creates:
--   - users            : application users
--   - api_tokens       : REST API bearer tokens (opaque, hashed)
-- =============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- -------------------------------------------------------------
-- users
-- -------------------------------------------------------------
CREATE TABLE users (
  id              BINARY(16)     NOT NULL COMMENT 'ULID (16 bytes)',
  login_id        VARCHAR(64)    NOT NULL COMMENT 'ログイン ID',
  display_name    VARCHAR(128)   NOT NULL COMMENT '表示名',
  email           VARCHAR(255)   NOT NULL COMMENT 'メールアドレス',
  password_hash   VARCHAR(255)   NOT NULL COMMENT 'Argon2id ハッシュ',
  is_active       BOOLEAN        NOT NULL DEFAULT TRUE,
  last_login_at   TIMESTAMP(6)   NULL DEFAULT NULL,
  created_at      TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at      TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  deleted_at      TIMESTAMP(6)   NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users__login_id (login_id),
  UNIQUE KEY uq_users__email (email),
  KEY idx_users__active (is_active, deleted_at)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='アプリケーションユーザ';

-- -------------------------------------------------------------
-- api_tokens
-- -------------------------------------------------------------
CREATE TABLE api_tokens (
  id            BINARY(16)   NOT NULL COMMENT 'ULID',
  user_id       BINARY(16)   NOT NULL COMMENT 'users.id への FK',
  token_hash    CHAR(64)     NOT NULL COMMENT 'opaque token の SHA-256 hex',
  token_prefix  CHAR(8)      NOT NULL COMMENT '可視プレフィックス（ログ照合用）',
  scopes        VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'space-separated スコープ',
  issued_at     TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  expires_at    TIMESTAMP(6) NOT NULL,
  revoked_at    TIMESTAMP(6) NULL DEFAULT NULL,
  last_used_at  TIMESTAMP(6) NULL DEFAULT NULL,
  created_at    TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at    TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id),
  UNIQUE KEY uq_api_tokens__hash (token_hash),
  KEY idx_api_tokens__user (user_id, revoked_at, expires_at),
  CONSTRAINT fk_api_tokens__user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT='REST API Bearer トークン（opaque, DB 保管）';
