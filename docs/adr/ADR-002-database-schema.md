# ADR-002: 新 DB スキーマ方針

- ステータス: 提案中 (2026-04-20)
- 決定者: (ユーザ承認待ち)
- 関連: PLAN.md, ADR-001, ADR-003

---

## 1. 文脈

Rucaro Accounting は 2012 年前後に設計された MySQL + PHP 5 時代のスキーマを 2026 年現在まで引きずっており、モダナイゼーション計画 (`docs/PLAN.md`) の Phase 1.2 で **新 DB を新規構築する** 方針が確定している。旧 DB は温存して参照・バックアップ用途に残し、新アプリ (`src/`) は MariaDB 10.11 LTS + utf8mb4 既定 + PHP 8.3 の組合せで白紙から立ち上げる。

Phase 1.1.a `docs/phase1/legacy-schema.md` §7（設計リスク / 癖）および §9（ADR-002 入力）で洗い出されたレガシースキーマの問題点を総括すると、以下の通りで、これらを **新スキーマでは継承しない** ことが本 ADR の出発点となる。

- **外部キー制約がゼロ**。`idAccount`, `idEntity`, `idLog`, `idAccountTitle` など全ての参照整合性がアプリ層任せで、孤児レコードが構造的に溜まる。
- **PRIMARY KEY を持たないテーブルが 8 本存在**（`baseSession`, `baseLoginSecond`, `baseToken`, `baseLoginPassword`, `baseLoginIdLogin`, `baseLoginMiss`, `baseAccountId`, `accountingAccountId`）。行数増加で線形スキャンに陥る。
- **索引が事実上 PK のみ**。59 テーブルの DDL 全件 grep で `CREATE INDEX` / `ADD INDEX` が 0 件。`accountingLog(idEntity, numFiscalPeriod, stampBook)` のような実運用のクエリパスに索引が無い。
- **論理削除が `flagRemove` + `stampRemove` の 2 列**。仕訳・ファイル・銀行ログ・固定資産ログ全てに複製され、物理削除せず肥大化し続ける。
- **日時カラムが全て `BIGINT` UNIX エポック秒**。タイムゾーン情報を保持せず、アプリ側で TZ=9 を固定。TZ 跨ぎと夏時間国の扱いに脆い。ミリ秒以下精度もない。
- **文字コード / collation が DDL で固定されていない**。サーバ既定依存で、MySQL → MariaDB / バージョン差異で文字化けや UNIQUE 判定の揺れが発生しうる。PDO 層で `charset=utf8` を指定しているだけで utf8mb4 未対応、絵文字を入れると破損。
- **連番 AUTO_INCREMENT PK**。順序が推測可能で、ID がそのまま URL に出ると件数が露見する。複数環境を跨いだマージや分散も難しい。
- **JSON カラムへの過度な集約**。仕訳 1 行が debit/credit 明細 + 承認履歴 + 版管理 + 消費税情報を 10+ 個の `longtext` JSON に分けて格納（`accountingLog`, `accountingLogCash`, `accountingLogFixedAssetsJpn`）。検索・集計・部分更新・FK が事実上不可能。
- **`arrCommaX` / `arrSpaceX` による CSV シリアライズ**。`LIKE '%,id,%'` 検索に頼らざるを得ず遅い。正規化できていない多対多を無理やり 1 列に押し込めている。
- **承認ワークフローが全テーブルに複製**（`flagApply` + `idAccountApply` + `arrCommaIdAccountPermit` + `jsonPermitHistory`）。DRY 違反。
- **可逆暗号が Blowfish CBC + md5 鍵派生 + 決定的 IV + Zero Padding**（`encrypted-columns.md` 参照）。AEAD でないため改ざん検知できず、同一平文が同一暗号文になる。
- **`ip varchar(15)`** で IPv6 が格納できない。
- **destructive migration**（`DROP TABLE IF EXISTS → CREATE TABLE`）により、新規インストールパスを誤って踏むと全データ消失するリスク。

Phase 4 で最初に刷新する中核モジュールは **Journal + TrialBalance** と確定しているため、本 ADR の「初期スキーマ」は **この 2 モジュールと Phase 3 の REST API 参考実装（`auth/login`, `entities`, `accountTitles`, `journals`）を支える最小セット** に絞る。それ以外の会計科目群（固定資産・青色申告・銀行連携・予算・家事按分）は Phase 4 以降で順次追加する。

---

## 2. 決定（新スキーマの方針）

### 2.1 テーブル命名規則

- **snake_case + 複数形**。例: `journal_entries`, `account_titles`, `fiscal_terms`, `bank_account_credentials`。
- 旧来のハンガリアン接頭辞（`id*`, `num*`, `str*`, `flag*`, `arr*`, `json*`, `stamp*`, `blob*`）は **全廃**。型はカラム定義で表現する。
- `Jpn` 接尾辞で nation を表す動的テーブル名生成はやめ、**`nation` / `country_code` 列** を標準化（`nation CHAR(3) NOT NULL DEFAULT 'JPN'` を該当テーブルに配置）。国ごとのカラム差分はサイドテーブルに分離（例: `entities` に共通属性、`entity_jpn_tax_profiles` に日本特有の税務属性）。
- モジュール名前空間は snake_case。旧 `base*` は共通基盤で接頭辞なし（`users`, `sessions`, `access_logs`）、旧 `accounting*` もモジュール色を外して会計ドメインのテーブルに素直な名を与える。

### 2.2 カラム命名規則

- 全カラム **snake_case**。略語は避ける（`num_fiscal_period` → `fiscal_period`, `stamp_book` → `booked_at`, `id_account_title` → `account_title_id`）。
- 真偽値は `is_*` / `has_*` / `can_*` プレフィックス + `BOOLEAN NOT NULL DEFAULT FALSE`。MariaDB の `BOOLEAN` は `TINYINT(1)` のエイリアスだが、DDL 上は意図を示すため `BOOLEAN` で書く。
- 外部キーは `<参照先単数>_id`（例: `journal_entries.entity_id` → `entities.id`）。
- 金額列は名詞単体で `amount`、種別が必要なら `debit_amount`, `credit_amount`, `tax_amount`。
- 日付列: 暦日は `xxx_date`（`journal_date`, `start_date`, `end_date`）、時刻込みは `xxx_at`（`booked_at`, `created_at`, `approved_at`, `expires_at`, `deleted_at`）。
- コード値は `_code` サフィックス（`nation_code`, `currency_code`, `tax_code`）。

### 2.3 主キー

- **ULID (26 文字 Crockford Base32, 先頭 48bit がミリ秒タイムスタンプ)** を採用し、**`BINARY(16)`** で格納。順序付き UUIDv7 互換の並び順を持つため B-tree 断片化が起きにくく、複数環境でも衝突しない。
- **アプリ側採番**。Composer で `robinvdvleuten/php-ulid` もしくは `symfony/uid` (`Ulid::generate()`) を導入し、INSERT 前に PHP で採番する。DB の `AUTO_INCREMENT` は使わない。
- クエリ時は `BIN_TO_UUID(id, 1)` 相当のアプリ側ヘルパで文字列化。API レスポンスでは 26 文字 ULID 文字列（`01HW7K9B2QV7C8Y4ZEXAMPLE00`）を返す。
- 連番 AUTO_INCREMENT を捨てる理由: 順序推測可能性を避ける、環境跨ぎのマージを容易化、ID そのものに時刻情報を埋め込む、という 3 点。

### 2.4 タイムスタンプ

- 全テーブルに共通:
  - `created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)`
  - `updated_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)`
- **ストレージは UTC 固定**。MariaDB の `TIMESTAMP` は内部 UTC 正規化されるため、接続時に `SET time_zone = '+00:00'` を宣言する（Phase 2 の `ConnectionFactory` で強制）。
- **表示は `Asia/Tokyo`**。PHP 側 `DateTimeImmutable` で `setTimezone(new DateTimeZone('Asia/Tokyo'))` を適用。
- マイクロ秒精度 `(6)` を持つのは、同一ミリ秒内に大量発行される操作ログ（AI 抽出→承認→仕訳生成）の順序を保つため。
- 旧 `stamp*` (BIGINT epoch 秒) は 継承しない。

### 2.5 論理削除

- `deleted_at TIMESTAMP(6) NULL DEFAULT NULL` を採用。`flagRemove` + `stampRemove` の二重持ちは廃止。
- 会計データ (journal_entries, receipts) は **物理削除せず** `deleted_at` で隠す運用。
- MariaDB は PostgreSQL のような partial index を持たないため、生存行への B-tree 集中が必要な場合は **仮想カラム + index** で代替:
  ```sql
  is_alive TINYINT(1) GENERATED ALWAYS AS (deleted_at IS NULL) VIRTUAL,
  KEY idx_alive_by_entity (entity_id, is_alive, booked_at)
  ```
- ORM 層の `SoftDeleteRepositoryTrait` で `WHERE deleted_at IS NULL` を既定に付与し、明示的に `withTrashed()` を呼んだ時だけ含める。

### 2.6 外部キー制約

- **原則全張り**。`idX` が他テーブルを参照する箇所は例外なく FK に格上げ。
- 既定動作:
  - `ON DELETE RESTRICT`（子が残っていれば親を消せない）
  - `ON UPDATE CASCADE`（ULID を不変運用する前提なので実際は発火しないが形式上付ける）
- 会計上消してはいけない参照（勘定科目・会計期・entity）は `RESTRICT` で保護。
- どうしても cascade 削除したい関係（`journal_entries` と `journal_entry_lines`）は明示的に `ON DELETE CASCADE`。

### 2.7 JSON の扱い

- 旧 `jsonXxxx` は可能な限り **正規化テーブルに分解** する。例:
  - `accountingLog.arrCommaIdAccountTitleDebit` / `...Credit` → `journal_entry_lines`（後述）
  - `accountingLog.jsonPermitHistory` → `approval_events`
  - `accountingFSValueJpn.jsonConsumptionTaxDetail` → `tax_rate_snapshots`
- どうしても JSON を残す場合（ベンダー固有レスポンスなど）は:
  - 型は **`JSON` (= LONGTEXT + JSON_VALID 制約)**
  - 検索が必要なキーは `GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(payload, '$.vendor'))) VIRTUAL` 仮想カラム化
  - そこに `KEY` を張って関数インデックス相当を実現
- JSON を正規化せず BLOB 同等に使うのは禁止。

### 2.8 索引

- **FK には自動的にセカンダリインデックスが必要**（MariaDB は FK を張ると勝手に作る動作もあるが、意図を示すため `KEY` 句を明示）。
- 検索要件に応じた複合インデックス:
  - `journal_entries (entity_id, fiscal_term_id, booked_at)`
  - `journal_entries (entity_id, fiscal_term_id, is_alive, booked_at)`（soft-delete 透過用、仮想カラム経由）
  - `journal_entry_lines (entry_id, line_no)`
  - `journal_entry_lines (account_title_id, booked_at)`（試算表用読み側）
  - `approval_tokens (token_hash)` UNIQUE
  - `receipts (entity_id, status, created_at)`
- インデックス命名: `idx_<table>__<col1>_<col2>` / `uq_<table>__<col>`。

### 2.9 文字コード

- DB / テーブル / カラムの全レベルで **`CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`** を DDL に明示する。
- `ENGINE=InnoDB` も明示。
- MariaDB 10.11 では `utf8mb4_0900_ai_ci` は存在しない（MySQL 8.0 系のみ）ため、**unicode_ci を採用**。異体字・濁点分離の判定は unicode_ci で十分。

### 2.10 金額列

- 既定 **`DECIMAL(18, 4)`**。軽減税率の端数計算（1.08 × 10 → 10.8 を丸めない）に小数 4 桁を確保。
- JPY でも外貨でも同じ型を使う。通貨は `currency_code CHAR(3)` と組で持つ（ISO 4217、既定 `'JPY'`）。
- 合計・税額計算は **DB 内 SUM を使わず PHP `BCMath`** で実行し、結果を再度 `DECIMAL(18,4)` に格納する（Phase 2 の `Amount` Value Object が担当）。

### 2.11 日付列

- 暦日: `DATE`（`journal_date`, `fiscal_start_date`）
- 時刻込み: `TIMESTAMP(6)`（`booked_at`, `created_at`）
- 期間型: `DATE` 2 本 `start_date` / `end_date`。

### 2.12 列挙型

- **MySQL ENUM は採用しない**。理由: DDL 変更に `ALTER TABLE` が必要で、値追加の履歴がスキーマ diff に埋もれ、ORM ライブラリごとの解釈も不安定。
- 代替:
  - 軽量な有限集合は `VARCHAR(32) NOT NULL CHECK (col IN ('pending', 'approved', 'rejected'))`（MariaDB 10.2+ の `CHECK` は実体的に動作する）。
  - 値に属性（表示名・並び順・有効期間）がある場合は **別マスタテーブル**（例: `journal_statuses`, `tax_rates`）。

---

## 3. 命名対応表（旧→新）

レガシー 59 テーブルのうち、Phase 4 以降で新 DB へ移植候補となる主要テーブルの命名マッピング。Phase 4 の個別 ADR で型・カラム構造は詳細化する。

| # | 旧テーブル | 新テーブル | 備考 |
|---|---|---|---|
| 1 | `baseAccount` | `users` | アカウント。パスワードは Argon2id 再ハッシュ |
| 2 | `baseLoginPassword` | `user_password_history` | 過去パスワード履歴 |
| 3 | `baseLoginMiss` | `login_failures` | **平文パスワード保存は廃止**、失敗カウントのみ |
| 4 | `baseSession` | `sessions` | PK なし → `id BINARY(16) PK` 付与 |
| 5 | `baseToken` | `api_tokens` | opaque token を bcrypt/sha256 ハッシュ化して格納 |
| 6 | `baseAccessLog` | `access_logs` | `ip` は `VARBINARY(16)` で IPv6 対応 |
| 7 | `baseAccessUnknown` | `access_denials` | 拒否ログ |
| 8 | `baseApiAccount` | `api_accounts` | 外部 API 連携用アカウント |
| 9 | `baseModule` | `installed_modules` | 機能モジュール有効化フラグ |
| 10 | `basePreference` | `user_preferences` | ユーザ設定 |
| 11 | `baseTerm` | `fiscal_terms` | 会計期。`numFiscalPeriod` → `fiscal_period INT` |
| 12 | `accountingEntity` | `entities` | 会社 / 事業主体 |
| 13 | `accountingEntityJpn` | `entity_jpn_tax_profiles` | 日本特有の税務属性（消費税区分等） |
| 14 | `accountingEntityDepartment` | `departments` | 部門 |
| 15 | `accountingAuthority` | `roles` | 権限ロール |
| 16 | `accountingAccess` | `role_permissions` | ロール × リソース権限 |
| 17 | `accountingAccountTitle` | `account_titles` | 勘定科目 |
| 18 | `accountingSubAccountTitle` | `sub_account_titles` | 補助科目 |
| 19 | `accountingFSJpn` | `financial_statement_templates` | 財務諸表ひな形 |
| 20 | `accountingFSValueJpn` | `financial_statement_values` | 財務諸表値スナップショット |
| 21 | `accountingLog` | `journal_entries` | 仕訳本体。明細は別テーブルへ分離 |
| 22 | （新規） | `journal_entry_lines` | 仕訳明細（借方 / 貸方 × 行） |
| 23 | `accountingLogCash` | `cash_book_entries` | 現金出納帳 |
| 24 | `accountingLogCalcJpn` | `account_title_balances` | 勘定科目別残高キャッシュ |
| 25 | `accountingLogFixedAssetsJpn` | `fixed_asset_schedules` | 固定資産台帳 |
| 26 | `accountingLogBanks` | `bank_transactions` | 銀行取引履歴 |
| 27 | `accountingLogBanksAccount` | `bank_account_credentials` | 銀行連携資格情報（AEAD 暗号化） |
| 28 | `accountingLogMailJpn` | `mail_import_sources` | メール取込設定（パスワードは AEAD 暗号化） |
| 29 | `accountingFile` | `file_import_sources` | FTP/IMAP ファイル取込設定 |
| 30 | `accountingBlueSheetJpn` | `blue_return_forms` | 青色申告決算書。`blobData` は LONGBLOB + 外部ストレージ検討 |
| 31 | `accountingBudget` | `budgets` | 予算 |
| 32 | `accountingConsumptionTaxJpn` | `tax_rates` | 消費税率マスタ（本則 + 軽減 + 履歴） |
| 33 | （新規） | `approval_tokens` | Phase 5 メール/メッセージ承認トークン |
| 34 | （新規） | `approval_events` | 承認イベントログ |
| 35 | （新規） | `receipts` | Phase 5 領収書ストレージ |
| 36 | （新規） | `receipt_action_logs` | 領収書の操作ログ |
| 37 | `accountingAdminMemo` | `admin_memos` | 管理メモ（旧 DDL 未定義だが運用上存在） |

---

## 4. 初期スキーマ（最小）

Phase 4 の Journal + TrialBalance と Phase 3 の参考実装 5 エンドポイント（login / entities / accountTitles / journals GET / POST）を支える最小 10 テーブル。

すべて MariaDB 10.11 LTS 前提。そのまま `mariadb < schema.sql` で流せる形。

```sql
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
  COMMENT 'アプリケーションユーザ';

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
  COMMENT 'REST API Bearer トークン（opaque, DB 保管）';

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
  COMMENT '会計主体（個人事業 / 法人）';

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
  COMMENT '会計期（旧 baseTerm）';

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
  COMMENT '勘定科目（Chart of Accounts）';

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
  COMMENT '補助科目';

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
    status IN ('draft', 'pending_approval', 'approved', 'rejected', 'posted')
  ),
  CONSTRAINT chk_journal__source CHECK (
    source IN ('manual', 'ai_receipt', 'bank_import', 'mail_import')
  ),
  CONSTRAINT chk_journal__total_nonnegative CHECK (total_amount >= 0)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT '仕訳本体（旧 accountingLog）。明細は journal_entry_lines';

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
  COMMENT '仕訳明細（借方 / 貸方 × 行）';

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
  CONSTRAINT chk_approval_tokens__channel
    CHECK (channel IN ('email', 'line', 'slack', 'discord')),
  CONSTRAINT chk_approval_tokens__response
    CHECK (response IS NULL OR response IN ('approved', 'rejected')),
  CONSTRAINT chk_approval_tokens__target
    CHECK (journal_entry_id IS NOT NULL OR receipt_id IS NOT NULL)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci
  COMMENT 'メール / メッセージ承認トークン（Phase 5）';

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
  COMMENT '領収書（Phase 5）。バイナリは content-addressed storage へ';

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
  COMMENT '領収書操作の運用ログ（法令準拠ではない）';
```

上記 10 本で Phase 3 の参考実装 5 エンドポイントと Phase 4 の Journal + TrialBalance が稼働する。試算表は `journal_entry_lines (account_title_id, booked_at)` に sum 集計をかけるだけで 10 万仕訳 < 2 秒の目標を達成しやすい。残る科目群（fixed_assets, banks, budgets, blue_return, departments 等）は Phase 4.x および Phase 5 で順次追加する。

---

## 5. マイグレーション戦略

### 5.1 方式

- **番号付きマイグレーション** を `scripts/migrate/<YYYYMMDD_HHMMSS>_<slug>.sql`（または `.php`）で管理。
- `.sql` は直線的なスキーマ変更に使う。
- `.php` は「暗号化列の再処理」「JSON 正規化」「データ再計算」など手続きが必要な時だけ使う。`Rucaro\Infrastructure\Migration\MigrationInterface` を実装し、`up()` / `down()` を PDO コンテキスト付きで呼び出す。
- 各マイグレーションは **冪等** に書く。トランザクション内で実行（MariaDB の DDL は暗黙 commit するため、構造変更は各ファイル 1 件に絞るのが原則）。
- 旧来 `BatchXXXXX.php` の `drop → create` destructive パターンは **採用しない**。

### 5.2 CLI ツール

- Symfony Console 製 `bin/cowork.php` にサブコマンド:
  - `migrate:status` — 未適用マイグレーション一覧
  - `migrate:up [--target=<timestamp>]` — 適用
  - `migrate:down [--steps=1]` — 直近から N 件ロールバック
  - `migrate:make <slug> [--php]` — スケルトン生成
- 実行履歴は `schema_migrations(version CHAR(15) PK, applied_at TIMESTAMP(6), checksum CHAR(64))` テーブルに記録。`checksum` を取ることで「一度適用後にファイル内容を書き換えた」ケースを検知して停止させる。
- CI では `migrate:up` を空 DB に対して実行し、成功することをテストする。

### 5.3 初期データと旧 DB 取込

- **初期スタートは空 DB**。旧 DB からのデータインポートはユーザ要望時のみ個別にバッチ作成する。
- 旧データ取込が必要になった時は:
  1. 旧 `strPassword` / `blobDetail` / `blobData` は `encrypted-columns.md` のレシピで復号
  2. 新スキーマへ正規化して変換（`journal_entries` + `journal_entry_lines` への分解など）
  3. `scripts/import/legacy_<table>.php` として書く
  4. dry-run モード必須、実行ログは `storage/logs/import/*.log`

### 5.4 シードとフィクスチャ

- マスタデータ（日本の標準勘定科目 3 桁コード、消費税率履歴 0%/5%/8%/10%/軽減 8%）は `scripts/seed/*.sql` で用意。
- テスト用フィクスチャは `tests/Fixtures/*.sql` に分離し、本番マイグレーションと混ざらないようにする。

---

## 6. 結果

### 6.1 Pros

- FK と CHECK で **参照整合性とドメイン制約を DB レベルで保証**できる。孤児レコードは構造的に発生しない。
- ULID + `BINARY(16)` で **ID の順序推測を塞ぎつつ** ストレージ効率（文字列 26 バイトより 10 バイト節約）と B-tree 局所性を確保。
- `TIMESTAMP(6)` UTC 統一で TZ 越境 / DST / マイクロ秒順序問題がまとめて解消。
- snake_case + `_id` 命名で **一般的な PHP ORM / クエリビルダとの親和性が上がる**（Laravel, Doctrine, Cycle など将来的な差し替えが容易）。
- JSON を正規化したことで、試算表・残高集計が `JOIN` + `GROUP BY` で自然に書け、**10 万仕訳 < 2 秒** の目標（PLAN.md §2 Phase 4.3）を達成しやすい。
- utf8mb4 固定で絵文字・機種依存文字対応。
- ENUM を避けたことで、値追加時に `ALTER TABLE` が不要となり、マスタテーブルの CRUD で済む。

### 6.2 Cons

- 旧スキーマと命名が完全に変わるため、旧コードからのコピペ移行ができない（= Phase 4 以降は書き直し前提）。
- `BINARY(16)` は `mysql` CLI から見ると生バイナリで読みづらい。デバッグ時に `HEX(id)` を都度書く必要がある。ビュー `v_*_readable` を用意する運用か、ORM 層が取り扱う前提にする。
- ULID 採番をアプリ側に置いたため、**DB だけで INSERT を打つと ID が入らない**。手動 INSERT 時は `UNHEX(REPLACE(...))` ヘルパーを使う。
- MariaDB 10.11 の `CHECK` 制約は強制されるが、`JSON_VALID` は 10.4.3 以降、`GENERATED ALWAYS AS ... VIRTUAL` のインデックスも仕様上は動くが、バージョン固定運用が必要（10.11 LTS で固定して回避）。
- FK を RESTRICT で張ったため、削除順序にアプリが気を遣う必要がある（CASCADE で逃げない）。

---

## 7. 代替案と却下理由

| 代替 | 却下理由 |
|---|---|
| **連番 `BIGINT UNSIGNED AUTO_INCREMENT` PK** を踏襲 | 順序推測可能で件数や成長速度がそのまま URL / JSON に露見。スケールアウト時の衝突回避も困難。会計 SaaS 化の余地を残すため ULID を選ぶ。 |
| **UUIDv4**（ランダム） | 順序性なしで B-tree 末尾 insert が成立せず、InnoDB primary page 断片化が進み、大量仕訳で劣化する。ULID / UUIDv7 の時系列性が必要。 |
| **UUIDv7** | ULID と同等の特性を持つがライブラリ / DB 関数対応が MariaDB 10.11 にまだ薄く、PHP 側も `symfony/uid 7.x` が必要。ULID は `symfony/uid` 6.x から安定しており Composer 要件が軽い。将来 v7 に切替可能なよう、ID 採番を `IdFactory` インタフェース経由に抽象化しておく。 |
| **TIMESTAMP を使わず BIGINT UNIX 秒を継続** | TZ / DST / うるう秒 / ミリ秒 / DB 関数（`DATE_FORMAT`, `BETWEEN`）の扱いで長期的に苦労する。Phase 4 の金額×期間分析で SQL が膨らむ。`TIMESTAMP(6)` + UTC 接続で解決。 |
| **JSON 維持（旧通り `jsonXxxx` を LONGTEXT に詰める）** | 検索・部分更新・FK が全て破綻する。仮想カラム索引で誤魔化しても正規化機会を逃し、Phase 4 で結局書き直す。ここで正規化してしまう。 |
| **PostgreSQL 移行**（MariaDB → PG） | Phase 1 時点ではインフラ影響が大きすぎる。旧 DB 温存方針と整合せず、MariaDB 10.11 + utf8mb4 で要件を満たせる。PG への将来移行を閉ざさないため、PG 非互換機能（MariaDB 独自の `SEQUENCE`, `JSON` キャスト、`VARBINARY` 比較演算）は避け、標準 SQL に寄せて書く。 |
| **ENUM 型採用**（`status ENUM('draft', ...)`） | 値追加に `ALTER TABLE` が必須、順序情報がスキーマに張り付く、ORM ライブラリの解釈が揺れる。`CHECK IN (...)` もしくは別マスタで代替可能。 |
| **テーブル命名でハンガリアン接頭辞継続**（`idAccount`, `flagRemove`） | 新規エンジニア学習コスト高、型と命名の二重管理、PHP 8.3 typed property との齟齬。旧との可読性的橋渡しは対応表（§3）で担保する。 |

---

## 8. 実装チェックリスト

Phase 1.3 と Phase 4 で実施する具体タスク。

### Phase 1.3（新基盤整備）

1. [ ] `composer require symfony/uid` を追加し、`Rucaro\Domain\Shared\Id\IdFactory` インタフェースと `UlidIdFactory` 実装を追加。
2. [ ] `Rucaro\Infrastructure\Database\ConnectionFactory` で PDO 接続時に `SET NAMES utf8mb4`, `SET time_zone = '+00:00'`, `SET sql_mode = 'STRICT_ALL_TABLES,...'` を強制。
3. [ ] `scripts/migrate/20260420_120000_init_schema.sql` を作成し、§4 の 10 テーブル DDL を収容。
4. [ ] `bin/cowork.php migrate:up / down / status / make` を Symfony Console で実装。`schema_migrations` テーブルの自動作成を含む。
5. [ ] `docker/Dockerfile` に MariaDB 10.11 LTS を採用し、`utf8mb4` デフォルトの `my.cnf` を同梱。
6. [ ] `tests/Integration/Database/SchemaSmokeTest.php` で「空 DB → migrate:up → 全テーブル存在 → FK count が期待数以上」を検査。
7. [ ] `tests/Integration/Database/Utf8mb4Test.php` で絵文字と漢字結合を挿入・取得しラウンドトリップ確認。

### Phase 2（共通インフラ）

8. [ ] `Rucaro\Domain\Shared\Id\Ulid` Value Object を実装（`BINARY(16)` ↔ 26 文字 Crockford Base32 相互変換）。
9. [ ] `Rucaro\Support\Time\Clock` インタフェース + `SystemClock` 実装。アプリ内 `now()` は全てここ経由。UTC で返し、`withJstDisplay()` 派生を持つ。
10. [ ] `Rucaro\Domain\Shared\Amount` Value Object（`BCMath` ベース、`DECIMAL(18, 4)` に対応）。`add` / `sub` / `mul` / `equals` / `isNegative` を網羅。
11. [ ] `Rucaro\Infrastructure\Migration\MigrationRunner`（SQL / PHP 両対応、チェックサム検証、トランザクション境界）。
12. [ ] `phpstan level 6` で `BINARY(16)` ↔ `Ulid` の型整合を確認、`mixed` の漏れをゼロに。

### Phase 4（Journal + TrialBalance）

13. [ ] `Rucaro\Domain\Journal\JournalEntry` 集約ルート（借方合計 = 貸方合計を不変条件として強制）。
14. [ ] `Rucaro\Domain\Journal\JournalEntryLine` Value Object。`side`（debit / credit）、`amount`、`taxRate` を含む。
15. [ ] `Rucaro\Domain\Journal\JournalRepositoryInterface` ポート定義。`save`, `findById`, `searchByEntityAndPeriod`, `delete` を最小 API に。
16. [ ] `Rucaro\Infrastructure\Persistence\PdoJournalRepository` 実装。`journal_entries` と `journal_entry_lines` へのトランザクション write を行う。
17. [ ] `Rucaro\Application\Journal\CreateJournalUseCase` を tdd-guide で RED → GREEN → REFACTOR。AAA パターンのユニットテスト 10 本以上。
18. [ ] `Rucaro\Application\TrialBalance\TrialBalanceQueryService`。`journal_entry_lines (account_title_id, booked_at)` インデックスを使った `SUM(amount) FILTER` 相当の SQL で集計。
19. [ ] `tests/Property/JournalBalanceTest.php` — 借貸一致を Property-based test（`eris` もしくは手書き乱数）で検証。
20. [ ] `tests/Integration/JournalRepositoryTest.php` — MariaDB 10.11 コンテナ上で実 SQL を走らせ、FK 違反時に `RESTRICT` が効くことを確認。
21. [ ] `tests/Performance/TrialBalancePerfTest.php` — 10 万仕訳シードで 2 秒以内を確認。CI では `@group perf` で分離。
22. [ ] Golden test: 旧アプリと新アプリに同一仕訳を投入し、試算表数値が一致する（Phase 4.4）。

### 横断（Phase 1〜4 通し）

23. [ ] `.env.example` に `DB_HOST`, `DB_PORT=3306`, `DB_NAME=rucaro_new`, `DB_USER`, `DB_PASSWORD`, `DB_CHARSET=utf8mb4` を記載し、`APP_TIMEZONE=Asia/Tokyo` を分離。
24. [ ] `config/database.php` を typed config 化（`readonly class DatabaseConfig`）、起動時に必須値を検証。
25. [ ] CI (`.github/workflows/ci.yml`) で MariaDB 10.11 + PHP 8.3 の matrix を回し、`migrate:up` が green であることをゲート条件にする。
26. [ ] `docs/internal/erd.md` を mermaid で自動生成する `bin/cowork.php db:erd` を用意。
27. [ ] `security-reviewer` サブエージェントに `api_tokens.token_hash` / `approval_tokens.token_hash` / `users.password_hash` の 3 箇所の扱いを最終確認させる。

---

（ADR-002 ここまで）
