# RUCARO モダナイゼーション 実装計画書（確定版）

> 策定日: 2026-04-20
> **Phase 6 完了: 2026-04-22**（ADR-020 で旧 UI 正式退役）
> 対象: PHP5/MySQL5 時代から段階移行中の日本語会計アプリ RUCARO
> 方針: 旧アプリ+旧DBはバックアップ兼参照用として温存、新アプリは PHP 8.3 + MariaDB 10 で構築
>
> **全 6 フェーズ完走**: Phase 1（基盤）→ 2（現代化）→ 3（API + 解析）→ 4（ドメイン刷新）→ 5（承認基盤）→ 6（既存システム最新化） ✅
>
> 最終状態: Unit 729 tests / PHPStan level 6 green / デスクトップ 17 PDF / ADR 20 本

---

## 0. 確定事項（ユーザ回答済み）

| 項目 | 確定内容 |
|---|---|
| データ移行 | 旧アプリ・旧DB温存、**再暗号化バッチ不要**。新DBを新規構築し、旧データは必要に応じて手動/半自動でインポート |
| PHP バージョン | **8.3 固定**（今後こまめメンテ） |
| 法令対応（電帳法等） | **不要**（ローカル使用前提） |
| 誤仕訳対策 | 登録前に**メール/メッセージ**でユーザ承認を経る（Web UI では承認しない） |
| 承認チャネル詳細設計 | planner 裁量（Phase 5 設計時に提案） |
| セキュリティ要件 | ローカル使用のため過度な強度は不要（ただし最低限の健全性は維持） |
| 期限 | **なし**。じっくり取り組む |
| Bearer token 形式 | planner 裁量（opaque + DB 保管を想定） |
| 最初に刷新する中核モジュール | Journal + TrialBalance |
| Claude モデル | Sonnet 4.6（抽出）+ Opus 4.7（仕訳ドラフト）を既定 |
| フロント (`front/else/`) | 本計画スコープ外 |

---

## 1. フェーズ構成

旧アプリは温存するため、新アプリを**グリーンフィールドで構築**し、必要に応じて旧DBからデータをインポートする方針。

```
Phase 1: 新基盤整備 (PHP 8.3 + MariaDB10 + Composer + PSR-4 + テスト基盤)
    │
    ├─→ Phase 2: 現代化 (PHP 8.3 イディオム + 依存ライブラリ整備 + Rector/PHPStan/Psalm)
    │       │
    │       ├─→ Phase 3: 内部解析ドキュメント + REST API
    │       │       │
    │       │       └─→ Phase 4: ドメイン刷新 (Journal + TrialBalance 再設計)
    │       │               │
    │       │               └─→ Phase 5: 領収書 AI パイプライン + メール/メッセージ承認
```

---

## 2. フェーズ詳細

### Phase 1: 新基盤整備（約 40〜45 h）

**ゴール**: 旧コードを修正するのではなく、**隣に新しい基盤を立てる**。旧アプリは読み取り専用で温存。

#### 1.1 調査（explore, 並列）
- 1.1.a 旧DBスキーマ全量 `SHOW CREATE TABLE` 収集 → `docs/phase1/legacy-schema.md`
- 1.1.b 暗号化列一覧（blobDetail / strPassword 等）と保存フォーマット → `docs/phase1/encrypted-columns.md`
- 1.1.c 旧ルーティング・クラス命名規則の整理 → `docs/phase1/legacy-routing.md`
- 1.1.d Docker / PHP / DB 現状構成 → `docs/phase1/current-stack.md`

#### 1.2 設計
- ADR-001: 新ディレクトリ構成（`src/`, `tests/`, `config/`, `public/`, `bin/`, `storage/`, `scripts/`）
- ADR-002: 新DB スキーマ方針（utf8mb4 既定、snake_case、ID は ULID/UUIDv7 採用を検討）
- ADR-003: 暗号化方針（AES-256-GCM、nonce 12 バイト、AAD に列名）

#### 1.3 実装
- PHP 8.3 Docker イメージへ差し替え（`docker/Dockerfile`）
- `composer.json` 整備（require: smarty, dotenv, monolog, guzzle, dompdf, symfony/console, PSR-4 mapping）
- `.env.example` 作成、`config/*.php` に typed config
- `src/` 下に `Domain/`, `Application/`, `Infrastructure/`, `Http/` スケルトン作成
- `tests/` スケルトン + `phpunit.xml.dist` + `phpstan.neon` + `psalm.xml` + `.php-cs-fixer.dist.php`
- CI: `.github/workflows/ci.yml` (PHP 8.3 + MariaDB 10.11)
- ~~`back/class/else/lib/Smarty/` 死蔵ディレクトリ削除~~ → **見送り**。`Batch14310.php:89` が require しているため旧アプリ整合性のため温存。新アプリは vendor/smarty/smarty を参照する

#### 1.4 成果物・テスト
- 新規ファイル: `composer.json`, `.env.example`, `config/*`, `src/**`, `tests/**`, CI 設定
- テスト: 空のサンプル Unit test が PHPUnit で走ること、PHPStan level 6 green、CI green

---

### Phase 2: 現代化（約 60 h）

**ゴール**: 新基盤の上に PHP 8.3 イディオム準拠の共通基盤（DB 接続、暗号化、ロガー、バリデーション、例外階層）を据える。

#### 2.1 共通インフラ実装（tdd-guide + code-reviewer）
- `Rucaro\Infrastructure\Database\ConnectionFactory`（PDO, utf8mb4, 例外モード、prepared statement）
- `Rucaro\Infrastructure\Crypto\AesGcmCipher`（AES-256-GCM）
- `Rucaro\Infrastructure\Logging\LoggerFactory`（Monolog）
- `Rucaro\Domain\Exception\DomainException` 階層
- `Rucaro\Support\Validation\*`（Value Object パターン）

#### 2.2 PHP 8.3 イディオム徹底
- typed properties, readonly, constructor promotion, enum, match, first-class callable
- Rector ルール: `Php82Sets`, `CodeQualitySet` を段階適用

#### 2.3 静的解析 baseline 確立
- PHPStan level 6 → 8 へ段階引き上げ
- Psalm level 3
- PHP-CS-Fixer (PSR-12 + Symfony risky rules)
- `rector.php` 設定

#### 2.4 成果物
- `src/Infrastructure/**`, `src/Support/**`
- Unit test カバレッジ ≥ 80%
- `phpstan.neon` baseline ゼロ件

---

### Phase 3: 内部解析ドキュメント + REST API（約 60 h）

**ゴール**: 旧アプリの挙動を完全に文書化し、新 REST API（`/api/v1/*`）を Bearer token 認証で提供。

#### 3.1 旧アプリ解析（explore 並列、Phase 2 と並行可）
- 3.1.a ルーティング網羅 → `docs/internal/routing.md`
- 3.1.b クラス↔SQL テーブル対応 → `docs/internal/class-table-matrix.md`
- 3.1.c ERD → `docs/internal/erd.md`（mermaid + dbdiagram DSL）
- 3.1.d 認証フロー → `docs/internal/auth-flow.md`
- 3.1.e 外部連携（5 銀行 + メール取込） → `docs/internal/external-integrations.md`
- 統合: `docs/INTERNAL_ARCHITECTURE.md`

#### 3.2 新 API 設計
- `docs/api/openapi.yaml`（OpenAPI 3.1）
- ルータ: `nikic/fast-route`
- エントリ: `public/api/v1/index.php`
- 認証: `POST /api/v1/auth/login` → opaque token（DB 保管、ハッシュ化、期限あり）
- 共通レスポンス: `{success, data, error, meta}`
- リソース: `/auth`, `/entities`, `/accountTitles`, `/subAccountTitles`, `/journals`, `/trialBalance`, `/financialStatements`, `/fixedAssets`, `/receipts`（Phase 5）

#### 3.3 参考実装（5 エンドポイント）
1. `POST /api/v1/auth/login`
2. `GET /api/v1/entities`
3. `GET /api/v1/accountTitles`
4. `GET /api/v1/journals`
5. `POST /api/v1/journals`

#### 3.4 契約テスト
- `schemathesis` または `Dredd` で OpenAPI 契約テスト、CI 組込

---

### Phase 4: ドメイン刷新（約 88 h）

**ゴール**: Journal + TrialBalance を DDD + ヘキサゴナルで再実装。

#### 4.1 ADR
- ADR-005: Layered Architecture (HTTP → Application → Domain → Infrastructure)
- ADR-006: Ports & Adapters（DB, 外部銀行 API, AI を adapter 化）
- ADR-007: Strangler Fig 移行計画

#### 4.2 Journal ドメイン（tdd-guide）
- `Rucaro\Domain\Journal\Journal`（集約ルート、不変条件「借方合計 = 貸方合計」）
- Value Object: `Amount`, `AccountTitleId`, `JournalDate`, `TaxRate`
- Repository port: `JournalRepositoryInterface`
- UseCase: `CreateJournalUseCase`, `UpdateJournalUseCase`, `DeleteJournalUseCase`, `SearchJournalUseCase`
- Infrastructure: `PdoJournalRepository`
- Controller: `Http\Api\V1\JournalController`

#### 4.3 TrialBalance ドメイン
- `TrialBalance` = Journal の射影（read model）
- `TrialBalanceQueryService`
- 月次スナップショットキャッシュ: `trial_balance_snapshot` テーブル
- パフォーマンス: 10 万仕訳で < 2 秒

#### 4.4 テスト
- Property-based test で借貸一致を検証
- Golden test: 旧アプリと新アプリで試算表数値が一致

---

### Phase 5: メール / メッセージ承認基盤（約 45〜55 h）

**ゴール**: **仕訳ドラフト → ユーザ承認 → 確定投稿** の承認基盤を、Phase 6 の領収書 AI パイプラインが乗る土台として先に完成させる。AI 連携は **Phase 6 へ分離**。

#### 5.1 DB / ドメイン設計
- 既存 `approval_tokens` テーブル活用（Phase 1.3 で作成済）
- 必要なら `approval_tokens` にカラム追加（`issued_by_user_id`, `target_kind`）
- Domain: `ApprovalToken` 集約、`ApprovalChannel` enum、`ApprovalDecision` VO、`ApprovalTarget` Interface（journal / (phase6) receipt 等で実装）
- ADR-008「Approval Workflow」を起草

#### 5.2 承認フロー
1. `POST /api/v1/journals`（status=`draft`）
2. `POST /api/v1/journals/{id}/request-approval` → トークン発行 + メール送信
3. 受信者がリンククリック → `GET /api/v1/approvals/{token}` で詳細表示（HTML or JSON）
4. 受信者が承認/却下 → `POST /api/v1/approvals/{token}` で状態遷移
5. 承認 → 対象 Journal を `approved` or `posted` へ遷移
6. 却下 → 差し戻し、理由を `response_detail` に記録

#### 5.3 通知チャネル抽象
- `MailSenderInterface` + `SymfonyMailSender`（`symfony/mailer` + SMTP）+ `InMemoryMailSender`（テスト用）
- `MessagingChannelInterface` + `NullMessagingChannel`（Phase 5 では NoOp、実 LINE/Slack は Phase 6 以降）
- `ApprovalNotifierInterface` + `DefaultApprovalNotifier`（チャネル選択を束ねる）
- `.env` で `MAIL_MAILER=smtp|null` / `APPROVAL_CHANNEL=mail|null` 切替

#### 5.4 セキュリティ
- トークンは 32 バイトランダム、DB には SHA-256 ハッシュのみ保存（既存 `api_tokens` と同じ方式）
- 有効期限 72 時間（`.env` で調整可）
- 1 トークン 1 回のみ応答可（responded_at セット後は 410 Gone）
- ローカル運用のため IP 制限は不要、HTTPS もローカルでは不要

#### 5.5 エンドポイント
- `POST /api/v1/journals/{id}/request-approval` — トークン発行 + 通知送信
- `GET /api/v1/approvals/{token}` — 承認待ち詳細を返す（JSON、簡易 HTML は Phase 6 で）
- `POST /api/v1/approvals/{token}` — 承認/却下を記録
- `POST /api/v1/approvals/{token}/resend` — 再送

#### 5.6 メールテンプレート
- `storage/templates/mail/approval-request.ja.txt` / `.html`（Smarty 5 利用）
- 件名・本文に仕訳ヘッダ情報 + 承認 URL + 却下理由入力リンク

#### 5.7 CLI
- `bin/cowork approvals:resend <tokenPrefix>` — 期限内トークンの再送
- `bin/cowork approvals:expire` — 期限切れトークン一括失効（cron 想定、Phase 5 は手動）

---

### Phase 6: 既存システム最新化（旧 Plugin Accounting の新 Domain への段階移植）

**ゴール**: 旧 `back/class/else/plugin/accounting/jpn/` の機能を全て新 `src/Rucaro/**` へ段階移植し、旧 UI なしで実運用可能な状態を作る。

> **方針変更（2026-04-22）**: ユーザ指示により「領収書 AI パイプライン」は本計画から外す。Phase 6 は純粋に **現行システムの最新化完了** のみを目的とする。旧コードの計算ロジック・勘定科目マッピングを正式移植（B-direct）する。AI 連携は将来の別フェーズで別途検討。

#### Phase 6 の Wave 構成（ADR-007 Strangler Fig 準拠）

| Wave | 対象旧クラス | 新実装 | 規模 |
|---|---|---|---|
| **6-A（今回）** | `FinancialStatement.php` + `FinancialStatementOutput.php` + `CalcAccountTitleFS.php` + `AccountTitleFS.php` | `Rucaro\Domain\FinancialStatement\*`（正式 BS/PL 段階計算） | 約 20 h |
| **6-B** | `FinancialStatementCS.php` + `FinancialStatementCSOutput.php` + `CalcAccountTitleFSCS.php` | CS（間接法 営業/投資/財務） | 約 12 h |
| **6-C** | `Ledger.php` + `LedgerOutput.php` | 総勘定元帳 | 約 10 h |
| **6-D** | `FixedAssets*.php` 全 9 ファイル | 固定資産台帳・減価償却計算 | 約 25 h |
| **6-E** | `CashPlan.php` / `CashAnalyze.php` / `BreakEvenPoint*.php` | 資金繰り・損益分岐点 | 約 15 h |
| **6-F** | `ConsumptionTax*.php` | 消費税計算（軽減税率・インボイス） | 約 15 h |
| **6-G** | `Budget*.php` | 予算管理 | 約 8 h |
| **6-H** | `BlueSheet.php` + `BlueSheetEditor.php` + `FinancialStatementSS.php` + `NotesFS*.php` | 青色申告表・株主資本等変動計算書・注記表 | 約 20 h |
| **6-I** | `FinancialStatementMulti*.php` | 複数期比較決算書 | 約 8 h |
| **6-J** | 旧 UI 退役判断・旧 `index.php` / `api.php` の停止 | — | 約 4 h |

**合計**: 約 137 h

各 Wave は独立に着手可能。Wave 6-A/B は連続でやるのが自然。

#### 移植方針（全 Wave 共通）

1. 旧クラスの **計算アルゴリズム** を抽出（認証・ルーティングボイラープレートは捨てる）
2. 新 `src/Domain/*` に集約として再実装（readonly、不変条件付き）
3. 新 `src/Application/*` にユースケース配置
4. 新 `src/Infrastructure/*` に Pdo リポジトリ / テンプレレンダ
5. 新 `src/Http/*` に Controller、OpenAPI も更新
6. Unit 80%+ カバレッジ
7. **Golden test**: 旧実装に同じ仕訳データを食わせて出力した CSV と、新実装の出力を比較して数値一致を保証（旧コードが動く環境が必要だが、せめて「よく使う仕訳パターン 10 ケース」の期待値は旧で手動作成）

#### 旧スキーマ関連テーブルの扱い

旧 UI が使う `accountingFSJpn`（勘定科目 → FS 項目マッピング設定）は、新 DB にも `account_title_fs_mappings` として移植が必要。Wave 6-A 着手時にマイグレーション 0008 で追加する。

#### 旧 UI の退役（Wave 6-J）

Wave 6-A〜6-I 完了後、旧 `index.php` / `api.php` / `output.php` / `confirm.php` を DocumentRoot から外す（既に外しているが、旧コードは `back/` に温存）。退役条件:
- 新 API / UI で旧機能相当が動作
- Golden test が全主要パターンで数値一致
- ユーザ最終 OK

---

---

## 3. リスク総覧（修正版）

| # | リスク | フェーズ | 対策 |
|---|---|---|---|
| R1 | 新DBスキーマの設計ミス | 1-2 | 旧アプリを稼働させたまま golden test で数値一致確認 |
| R2 | Strangler Fig 切替時の挙動差分 | 4 | feature flag、golden test、旧経路は並走 |
| R3 | Claude API コスト | 5 | prompt caching、`.env` の月額上限、モデル切替可能に |
| R4 | 誤仕訳 | 5 | **メール/メッセージ承認必須**、信頼度 threshold 未満は自動承認不可 |
| R5 | メール配送失敗 | 5 | 再送、別チャネル fallback、配送ログ |
| R6 | 旧・新並走期間のデータ乖離 | 4-5 | どちらを master とするか明確化（新アプリ稼働開始日以降は新が master） |

---

## 4. 工数・スケジュール

| フェーズ | 工数 |
|---|---|
| 1 | 40〜45 h |
| 2 | 60 h |
| 3 | 60 h |
| 4 | 88 h |
| 5 | 75〜85 h |
| **合計** | **約 325〜340 h（約 41〜43 人日）** |

期限なし、じっくり取り組む方針。

---

## 5. サブエージェント運用方針

各サブタスクに以下を割当:

| エージェント | 主用途 |
|---|---|
| Explore | 現状コード調査、grep 網羅 |
| ecc:architect | アーキ決定、ADR 起草 |
| ecc:planner | サブプラン細分化 |
| ecc:tdd-guide | RED-GREEN-REFACTOR |
| ecc:code-reviewer | PR レビュー |
| ecc:security-reviewer | 認証・暗号・トークン（ローカル前提で程々に） |
| ecc:database-reviewer | スキーマ、インデックス、migration |
| ecc:build-error-resolver | PHP 起動失敗、autoload 崩壊 |
| ecc:refactor-cleaner | dead code 除去 |
| ecc:e2e-runner | Playwright スモーク |
| ecc:doc-updater | OpenAPI・ADR・README |

独立タスクは parallel Task execution で同時起動。

---

## 6. 進捗管理

TaskCreate で Phase 1〜5 をトップレベルタスクとして登録、各フェーズ内のサブタスクは開始時に細分化して追加する。
