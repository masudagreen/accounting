# Rucaro Accounting - Internal Architecture

> **読者**: 「旧 Rucaro Accounting（PHP 5 時代設計）」と「新 Rucaro v2（PHP 8.3 + REST API）」が 1 つのリポジトリに並走する構造のメタ索引。新規参加者・保守担当・運用担当が把握すべき要点を集約。
>
> **最終更新**: 2026-04-22（**Phase 6 完了時点**、旧 UI 正式退役）

---

## 0. TL;DR

- **Phase 6 完了**: 旧 `back/class/else/plugin/accounting/jpn/` 223 PHP ファイルを新 `src/**` へ段階移植完了。旧 UI は**正式退役**（ADR-020）、Web 到達不能のまま温存。
- 新アプリ（`src/**`、PSR-4 `Rucaro\` 名前空間）は **Layered + Hexagonal** で 5 層（Domain / Application / Infrastructure / Http / Support）、**474 ファイル**稼働。
- DB は MariaDB 10.11、**utf8mb4** / **BINARY(16) ULID PK** / **TIMESTAMP(6) UTC** / **外部キー完備**。新スキーマで約 30 テーブル稼働中（Migration 0000〜0018）。
- 新 REST API `/api/v1/*` は **Bearer opaque token** 認証で、全会計機能を提供（仕訳/試算表/決算書BS-PL-CS/総勘定元帳/固定資産/資金繰/BEP/消費税/予算/青色申告/株主資本変動/注記/複数期比較）。
- デスクトップ（`C:\Users\yusuk\OneDrive\デスクトップ\rucaro-out\`）に **17 PDF** が生成され視覚検証済、日本語（IPAex Gothic）埋め込み。
- テスト **Unit 729 / PHPStan level 6 No errors**（baseline 未変更）。CI（lint / phpstan / psalm / phpunit）設定済。
- Docker は PHP 8.3-apache + MariaDB 10.11-jammy + phpmyadmin(dev プロファイル)。`docker compose up -d` で起動。

---

## 1. リポジトリの全体像

```
accounting/
├── back/                      # [旧] レガシーコード（一切変更せず温存）
│   ├── class/else/core/       # 旧 Core 35 クラス（Init, Attest, Access, Login）
│   ├── class/else/plugin/     # 旧 Plugin 368 クラス（accounting/jpn/）
│   ├── class/else/lib/        # 旧 Lib 16 クラス（Db, Crypte, Smarty3 etc.）
│   ├── dat/                   # 旧データファイル・マイグレーション履歴・鍵
│   └── tpl/                   # 旧 Smarty テンプレート
├── src/                       # [新] PSR-4 `Rucaro\`
│   ├── Domain/                # 集約・値オブジェクト・ドメインサービス
│   ├── Application/           # ユースケース（CQRS-lite）
│   ├── Infrastructure/        # Pdo* リポジトリ・Crypto・Logging・Ulid
│   ├── Http/                  # Kernel / ApiKernel / Controller / Middleware
│   └── Support/               # Clock / Container / Decimal / Result / Validation
├── tests/
│   ├── Unit/                  # 181 tests
│   ├── Integration/           # DB 必須、未設定時は skip
│   └── E2E/                   # Playwright 予定（Phase 4 以降）
├── public/                    # Apache DocumentRoot
│   ├── index.php              # 新 Kernel エントリ
│   ├── api/v1/index.php       # 新 REST API エントリ
│   └── .htaccess              # Authorization ヘッダ転送・routing rewrite
├── scripts/migrate/           # 番号付き DDL + rollback SQL
├── docs/                      # 本体
│   ├── PLAN.md                # マスタープラン（5フェーズ）
│   ├── adr/                   # 意思決定ログ（ADR-001〜003）
│   ├── phase1/                # Phase 1 で作った旧アプリ解析
│   ├── internal/              # Phase 3 で作った内部解析
│   └── api/openapi.yaml       # OpenAPI 3.1
├── config/                    # typed PHP config（app / database / logging / crypto）
├── bin/cowork                 # Symfony Console CLI（Phase 5 拡張予定）
├── storage/                   # ログ・キャッシュ・アップロード・領収書
├── docker/                    # Dockerfile / apache / php.ini
├── composer.json              # PSR-4 + classmap（旧コードも autoload 可）
├── docker-compose.yml         # app / db / phpmyadmin
└── .env.example               # 環境変数テンプレ
```

---

## 2. ドキュメントマップ

### 企画・計画

| ドキュメント | 内容 | 備考 |
|---|---|---|
| [`PLAN.md`](PLAN.md) | 5 フェーズ全体計画・工数見積・ユーザ確定事項 | 各フェーズ着手時の基準 |

### 意思決定記録（ADR）

| ADR | テーマ | 要点 |
|---|---|---|
| [`ADR-001`](adr/ADR-001-directory-layout.md) | 新ディレクトリ構成と PSR-4 マッピング | 5 層構成、旧 back/ と並走 |
| [`ADR-002`](adr/ADR-002-database-schema.md) | 新 DB スキーマ方針 | utf8mb4 / BINARY(16) ULID / TIMESTAMP(6) UTC / FK 完備 |
| [`ADR-003`](adr/ADR-003-crypto-strategy.md) | 暗号化戦略 | AES-256-GCM + HKDF-SHA256、旧 Blowfish CBC 互換復号も用意 |
| [`ADR-005`](adr/ADR-005-layered-architecture.md) | Layered Architecture | Domain←Application←Http の依存方向、Infrastructure は Port 実装 |
| [`ADR-006`](adr/ADR-006-ports-and-adapters.md) | Ports & Adapters | Primary/Secondary ポート、Adapter 命名規則 |
| [`ADR-007`](adr/ADR-007-strangler-fig-migration.md) | Strangler Fig 移行計画 | Wave 構成・退役順序 |
| [`ADR-008`](adr/ADR-008-approval-workflow.md) | 承認ワークフロー | メール/メッセージ経由の仕訳承認、トークン管理 |
| [`ADR-009`](adr/ADR-009-fs-port.md) | FS (BS/PL) 移植 | 階層セクション、勘定科目 → FS 項目マッピング |
| [`ADR-010`](adr/ADR-010-cs-port.md) | CS（キャッシュフロー計算書）移植 | 間接法、営業/投資/財務 CF |
| [`ADR-011`](adr/ADR-011-ledger-port.md) | 総勘定元帳移植 | 期首残高 + 時系列エントリ + 残高推移 |
| [`ADR-012`](adr/ADR-012-fixed-assets-port.md) | 固定資産・減価償却移植 | 9 償却方式、平成 19/24/28 年改正対応 |
| [`ADR-013`](adr/ADR-013-cash-plan-and-bep-port.md) | 資金繰り + 損益分岐点移植 | 月次 12 列 + CVP 分析 |
| [`ADR-014`](adr/ADR-014-consumption-tax-port.md) | 消費税・インボイス移植 | 標準/軽減/旧/非課税/免税、原則/簡易/2割特例 |
| [`ADR-015`](adr/ADR-015-budget-port.md) | 予算管理移植 | 予算策定・予実対比・消化率 |
| [`ADR-016`](adr/ADR-016-blue-return-port.md) | 青色申告決算書移植 | 個人事業主専用、4 ページ snapshot_json 方式 |
| [`ADR-017`](adr/ADR-017-statement-of-changes-in-equity-port.md) | 株主資本等変動計算書移植 | 期首→当期変動→期末、journal 自動検出 + 手動調整 |
| [`ADR-018`](adr/ADR-018-financial-statement-notes-port.md) | 注記表移植 | テンプレート + entity override の 2 段構え |
| [`ADR-019`](adr/ADR-019-multi-period-fs-port.md) | 複数期比較決算書移植 | 単期 FS 再利用、最大 5 期横並び |
| [`ADR-020`](adr/ADR-020-legacy-ui-retirement.md) | **旧 UI 退役判断**（承認済） | Phase 6 完了宣言、退役条件満足 |

### Phase 1 解析（旧アプリ現状）

| ドキュメント | 内容 |
|---|---|
| [`phase1/legacy-schema.md`](phase1/legacy-schema.md) | 旧 DB 59 テーブルの DDL 全量、命名規則、設計リスク |
| [`phase1/legacy-routing.md`](phase1/legacy-routing.md) | 旧 URL → クラス解決規則、4 エントリポイント分析 |
| [`phase1/encrypted-columns.md`](phase1/encrypted-columns.md) | 旧暗号化 4 カラムの保存フォーマットと復号互換レシピ |
| [`phase1/current-stack.md`](phase1/current-stack.md) | 旧 Docker / PHP / DB 現状と新スタックへの差分 |

### Phase 3 解析（再実装向け）

| ドキュメント | 内容 |
|---|---|
| [`internal/class-table-matrix.md`](internal/class-table-matrix.md) | 431 クラス × 59 テーブル × 958 参照の対応表 / Phase 4 移行優先度 Top 10 |
| [`internal/auth-flow.md`](internal/auth-flow.md) | 旧セッション/API/2FA 認証フロー、**平文パスワード保存等の重大問題** |
| [`internal/external-integrations.md`](internal/external-integrations.md) | 5 銀行連携は CSV パーサのみ、実スクレイピングは外部プロキシ `rucaro.org/banks.php` |

### Phase 3 設計（新 API）

| ドキュメント | 内容 |
|---|---|
| [`api/openapi.yaml`](api/openapi.yaml) | REST API v1 仕様 34 操作 / 25 パス / 53 スキーマ |

---

## 3. 旧アプリの到達経路

旧 `back/**` は **Web から直接到達不能**（DocumentRoot が `public/` に切替済、`.htaccess` で `back/` 拒否）。参照方法は以下:

1. **コード閲覧**: `back/class/else/` を直接 Read / Grep
2. **Composer autoload**: `Code_Else_*` クラスは `composer.json` の `classmap` に登録済。`require "vendor/autoload.php"` すれば新コードから旧クラスを new できる（ただし旧 DB/cookie/session 依存のため、そのままでは動かない）
3. **旧 DB 参照**: 旧 DB ダンプを別スキーマ（例: `rucaro_legacy`）にリストアして、新 API から read-only でクエリする想定（Phase 4〜5 の移行時に必要になったら実装）

旧 `index.php` / `api.php` / `output.php` / `confirm.php` を稼働させたい場合は、DocumentRoot を戻すか別 vhost を立てる必要がある（本計画では扱わない）。

---

## 4. 新アプリの内部構造

### 4.1 レイヤ責務

| レイヤ | 責務 | 依存先 |
|---|---|---|
| `Http/` | ルーティング / ミドルウェア / Controller / Response | `Application/` |
| `Application/` | ユースケース（1 操作 = 1 クラス）、入出力 DTO、Port 定義 | `Domain/` |
| `Domain/` | エンティティ / 値オブジェクト / 集約 / ドメイン例外 / リポジトリ Interface | 他レイヤに依存しない |
| `Infrastructure/` | Pdo リポジトリ実装 / Crypto / Logging / Ulid / 外部 API クライアント | `Domain/` の Port を実装 |
| `Support/` | Result / Clock / Decimal / Container / Validation 等の横断ユーティリティ | なし |

### 4.2 エントリ・ルーティング

```
HTTP Request
   │
   ▼
public/{index,api/v1/index}.php                 ← Autoload + Dotenv + ContainerBootstrap
   │
   ▼
Rucaro\Http\{Kernel,ApiKernel}                  ← FastRoute でパスマッチ
   │
   ▼
Rucaro\Http\Controller\**                       ← Bearer 認証 → UseCase 呼出 → Envelope 化
   │
   ▼
Rucaro\Application\**UseCase                    ← 入出力 DTO、集約の不変条件検証
   │
   ▼
Rucaro\Infrastructure\**\Pdo*Repository         ← PDO prepared statement、BINARY(16) ↔ ULID 変換
   │
   ▼
MariaDB 10.11 (utf8mb4)
```

### 4.3 認証

- `POST /api/v1/auth/login` → 32 バイトランダム token を生成、`hash('sha256', $token)` を `api_tokens.token_hash`（VARCHAR CHAR(64)）に保存、平文 token をクライアントに返す
- 以降の全 API は `Authorization: Bearer <token>` を要求、`AuthenticateBearer` middleware が SHA-256 ハッシュで DB 照合
- `public/.htaccess` で `HTTP_AUTHORIZATION` を明示転送（mod_php の既定は Authorization ヘッダを落とす）
- password_hash は ARGON2ID

### 4.4 Journal（仕訳）集約の不変条件

- `Sum(debit_amount) = Sum(credit_amount)` が必須、違反時 `InvariantViolationException`
- `total_amount = Sum(debit_amount)` 一致も要件
- 新 `Journal` 集約は `Support\Decimal\Decimal`（bcmath 利用、未ロード環境では int64 固定小数点フォールバック）で端数ゼロを保証

---

## 5. DB スキーマ（Phase 6 完了時点）

`scripts/migrate/0000〜0018*.sql` で約 30 テーブル投入済。

### 5.1 Phase 1〜5 基盤（0000〜0007）
| カテゴリ | テーブル |
|---|---|
| 認証 | `users`, `api_tokens` |
| マスタ | `entities`, `fiscal_terms`, `account_titles`, `sub_account_titles` |
| 取引 | `journal_entries`, `journal_entry_lines` |
| Phase 5 承認 | `approval_tokens`, `receipts`, `receipt_action_logs` |
| システム | `schema_migrations` |

### 5.2 Phase 6 追加（0008〜0018）
| Migration | テーブル | Wave |
|---|---|---|
| 0008 | `fs_section_definitions`, `account_title_fs_mappings` | 6-A |
| 0009 | `fs_cs_section_definitions`, `account_title_cs_mappings` | 6-B |
| 0010 | `opening_balances` | 6-C |
| 0011 | `fixed_asset_categories`, `fixed_assets`, `fixed_asset_depreciation_schedules` | 6-D |
| 0012 | `cash_plans`, `cash_plan_entries` | 6-E |
| 0013 | `account_title_cvp_classifications` | 6-E |
| 0014 | `consumption_tax_rates`, `consumption_tax_categories`, `account_title_consumption_tax_defaults`, `consumption_tax_invoice_registrations`, `consumption_tax_periods` | 6-F |
| 0015 | `budgets`, `budget_line_items` | 6-G |
| 0016 | `blue_return_forms`, `entities.is_corporate` カラム追加 | 6-H-1 |
| 0017 | `ss_manual_adjustments` | 6-H-2 |
| 0018 | `fs_note_templates`, `fs_notes` | 6-H-3 |

全て `utf8mb4_unicode_ci` / InnoDB / BINARY(16) ULID PK / TIMESTAMP(6) UTC / FK 完備。

**注**: `approval_tokens` の「journal_entry_id / receipt_id いずれか NOT NULL」制約は MariaDB 10.11 で CHECK エラー 1901 が出たため **アプリ層で強制**（SQL 層では未設定）。

---

## 6. Phase 6 完了：旧→新モジュール対応（移植済）

| 旧モジュール群 | 新モジュール | 実装 Wave | PDF 生成 |
|---|---|---|---|
| `Log*`, `CalcAccountTitle*` | `Domain/Journal/*`, `Application/TrialBalance/*` | 4.2, 4.3 | — |
| `FinancialStatement*`, `AccountTitleFS*`, `CalcAccountTitleFS` | `Domain/FinancialStatement/Port/*` | 6-A, 6-A' | bs.pdf, pl.pdf, all.pdf |
| `FinancialStatementCS*`, `CalcAccountTitleFSCS` | `Domain/FinancialStatement/Port/Cs/*` | 6-B | cs.pdf |
| `Ledger*` | `Domain/Ledger/*` | 6-C | ledger.pdf |
| `FixedAssets*` (9 ファイル) | `Domain/FixedAsset/*` | 6-D | fixed-assets.pdf |
| `CashPlan*`, `CashAnalyze*` | `Domain/CashPlan/*` | 6-E | cash-plan.pdf |
| `BreakEvenPoint*`, `CalcBreakEvenPoint` | `Domain/BreakEvenPoint/*` | 6-E | break-even-point.pdf |
| `ConsumptionTax*`, `CalcConsumptionTax*` | `Domain/ConsumptionTax/*` | 6-F | consumption-tax-report.pdf |
| `Budget*` | `Domain/Budget/*` | 6-G | budget.pdf, budget-variance.pdf |
| `BlueSheet*` | `Domain/BlueReturn/*` | 6-H-1 | blue-return.pdf |
| `FinancialStatementSS*` | `Domain/StatementOfChangesInEquity/*` | 6-H-2 | statement-of-changes-in-equity.pdf |
| `NotesFS*` | `Domain/FinancialStatementNotes/*` | 6-H-3 | fs-notes.pdf |
| `FinancialStatementMulti*` | `Domain/FinancialStatement/Multi/*` | 6-I | multi-period-{bs,pl,all}.pdf |

詳細は [ADR-020](adr/ADR-020-legacy-ui-retirement.md) §2.2 を参照。

---

## 7. 未移植モジュール（Phase 6 スコープ外）

以下は将来 Phase で個別対応予定:

| 旧モジュール | 扱い |
|---|---|
| `Access*`, `Authority*`（RBAC UI） | 新 Bearer 認証で代替、UI 層は将来のフロントエンドで構築 |
| `calcBanks/`（5 銀行 CSV 取込） | 必要時に Infrastructure Adapter として追加 |
| `CalcLogImportMail`（IMAP メール取込） | 必要時に `ext-imap` を Dockerfile に追加 |
| `LogImport*`（汎用ファイル取込） | 必要時に Import Adapter として |
| `Portal`, `Preference`（管理 UI） | 将来のフロントエンドで新設計 |
| 旧 Smarty テンプレート `back/tpl/templates/**` | 参考資料として保持 |

5 銀行連携は旧実装が `rucaro.org/banks.php` プロキシ（**TLS 検証無効**）に依存するため、移植時は公式 API 切替前提で別計画。

---

## 8. 開発・運用

### 8.1 起動

```bash
cp .env.example .env           # 初回のみ
docker compose up -d --build   # DB + App 起動
# App: http://localhost:8080/
# API: http://localhost:8080/api/v1/healthz
# phpMyAdmin: docker compose --profile dev up -d   (http://localhost:8081/)
```

### 8.2 マイグレーション適用

```bash
for f in scripts/migrate/0000*.sql scripts/migrate/0001*.sql \
         scripts/migrate/0002*.sql scripts/migrate/0003*.sql \
         scripts/migrate/0004*.sql; do
  cat "$f" | docker exec -i accounting_db mariadb -u rucaro -prucaro rucaro
done
```

（`bin/cowork migrate:up` CLI は TODO、現状は手動）

### 8.3 テスト

```bash
# Unit テスト（Legacy bf-cbc 互換テストのために OPENSSL_CONF が必要）
docker run --rm -e OPENSSL_CONF=/app/tests/Unit/Infrastructure/Crypto/openssl-legacy.cnf \
  -v "$PWD:/app" -w /app php:8.3-cli \
  php vendor/bin/phpunit --testsuite=Unit

# PHPStan
docker run --rm -v "$PWD:/app" -w /app php:8.3-cli \
  php vendor/bin/phpstan analyse --memory-limit=512M

# Integration（DB 必須）
docker run --rm --network host \
  -e RUCARO_TEST_DB_HOST=127.0.0.1 -e RUCARO_TEST_DB_PORT=3307 \
  -e RUCARO_TEST_DB_NAME=rucaro -e RUCARO_TEST_DB_USER=rucaro \
  -e RUCARO_TEST_DB_PASSWORD=rucaro \
  -v "$PWD:/app" -w /app php:8.3-cli \
  php vendor/bin/phpunit --testsuite=Integration
```

### 8.4 CI

`.github/workflows/ci.yml` で 4 ジョブ: php-cs-fixer (dry-run) / phpstan / psalm / phpunit（MariaDB 10.11 サービス付き、PHP 8.3）。

---

## 9. 完了状況 (Phase 6 完走)

| Phase | 内容 | 状態 |
|---|---|---|
| Phase 1 | 新基盤整備（PHP 8.3/MariaDB 10/Docker/Composer/PSR-4/テスト基盤） | ✅ 完了 |
| Phase 2 | 現代化（共通インフラ：暗号/DB/ロガー/例外/VO） | ✅ 完了 |
| Phase 3 | 内部解析 + REST API（OpenAPI 3.1、Bearer 認証、5 参考エンドポイント） | ✅ 完了 |
| Phase 4 | ドメイン刷新（Journal + TrialBalance、DDD + Hexagonal） | ✅ 完了 |
| Phase 5 | メール/メッセージ承認基盤（トークン発行 + 通知 + 1 回応答制限） | ✅ 完了 |
| Phase 6 | 既存システム最新化（Wave 6-A〜6-J、旧 UI 退役） | ✅ 完了（ADR-020） |

### Phase 6 で追加された機能

- 決算書 BS/PL/CS（日本基準段階計算、ADR-009, ADR-010）
- 総勘定元帳（ADR-011）
- 固定資産台帳・減価償却（9 償却方式、ADR-012）
- 資金繰り表・損益分岐点（ADR-013）
- 消費税・インボイス・軽減税率（ADR-014）
- 予算管理・予実対比（ADR-015）
- 青色申告決算書（ADR-016）
- 株主資本等変動計算書（ADR-017）
- 注記表（ADR-018）
- 複数期比較決算書（ADR-019）
- 旧 UI 正式退役宣言（ADR-020）

---

## 10. 残 TODO（次フェーズ候補）

- **bin/cowork CLI** の本格実装（migrate / crypto:generate-key / approvals:resend / approvals:expire / receipts:ingest 等）
- **RBAC**（Role-Based Access Control）の新 UI + API 本格化
- **フロントエンド**（Web UI）の新設計・構築（React/Vue/Next.js 等、別リポジトリ推奨）
- **旧 DB インポータ**（旧 59 テーブル → 新 30 テーブルのワンショット変換 CLI、必要時）
- **銀行取込 Adapter**（`calcBanks/` 5 行の正式 API 版、各行の公式 API 切替時）
- **メール取込 Adapter**（`ext-imap` 追加 + `CalcLogImportMail` 相当）
- **OpenAPI 仕様の全エンドポイント追記**（現状 Wave 6-I 分まで未追記）
- **Integration test の seed スクリプト**（fixtures 自動化）
- **CI における `RUCARO_TEST_DB_*` 環境変数の設定**（現状 Integration は skip）
- **将来の AI 連携**（領収書 OCR → 仕訳ドラフト、Claude API 経由）— 別 Phase で改めて仕様検討

---

## 11. 関連リンク

- [PLAN.md](PLAN.md) — 全フェーズ計画
- [ADR ディレクトリ](adr/) — 20 本の意思決定記録（ADR-001〜020、ADR-004 は欠番）
- [phase1/](phase1/) — Phase 1 解析レポート 4 本
- [internal/](internal/) — Phase 3 解析レポート 3 本（class-table-matrix / auth-flow / external-integrations）
- [api/openapi.yaml](api/openapi.yaml) — REST API 仕様
- **Phase 6 成果物（デスクトップ）**: `C:\Users\yusuk\OneDrive\デスクトップ\rucaro-out\*.pdf`（17 ファイル）
