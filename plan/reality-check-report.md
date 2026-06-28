# Phase A リアリティチェック実施記録

**目的**: renewal を本格利用する前に、コード/環境/データ取り込み/業務動作の各レイヤで「実際に動くか」を検証する
**前提**:
- 作業ディレクトリ: `/Users/np_202212_11/projects/accounting`（renewal）
- master worktree: `/Users/np_202212_11/projects/accounting-master`
- 計画書: `plan/MIGRATION_PLAN.md` §3 RC-0 〜 RC-9

| # | 検証項目 | 状態 | 詳細 |
|---|---|---|---|
| RC-0 | `composer install` 完走 | ✓ | image build 中に composer install 成功（87 packages funded）。PHP 8.3.31 / Composer 2.9.7 / vendor 50 dirs |
| RC-1 | `docker compose up -d --build` 完走 | ✓ | app + db ともに `healthy`。app は 9080→80、db は 3307→3306 |
| RC-2 | 全 migration 適用成功 | △ | 18 primary + 5 seeds 適用 → 35 テーブル。ただし `MigrationRunner` の glob で `*_seed.sql` が 4 桁 version を上書きする bug を発見（ad-hoc runner で回避） |
| RC-3 | `vendor/bin/phpunit` 結果（pass/skip/fail） | △ | Tests: 963 / Assertions: 2110 / Errors: 56 / Failures: 1 / Skipped: 12。Errors 56 件のうち 52 件は MigrationRunner glob bug 由来、4 件は `Unknown database 'rucaro'` / migrations dir not found / ULID 不正など |
| RC-3.1 | `phpstan` / `psalm` / `cs-check` 結果 | △ | phpstan: ✓ No errors / psalm: ✗ 613 errors + 153 info / php-cs-fixer: ✗ 607/804 files に差分 |
| RC-4 | `/ui/login` → `/ui/dashboard` 動作 | ✓ | admin user を Argon2id で手動 INSERT、CSRF (`_csrf`) 込みで POST → 303 → ダッシュボード 200。HEAD は 404 を返すルーティング不整合あり |
| RC-5 | LegacyImport 小規模データ取り込み | △ | master DB 全 1,701 仕訳 → renewal に 1,666 inserted (35 件は legacy 側で `accountingLogCalcJpn` 行が存在しない空ヘッダ。意図通り skip)。journal_entry_lines 3,950 と calc 行数完全一致、貸借合計 ¥425,938,307 一致。**but**: importer の `--truncate-target` が `legacy_id_mapping` テーブル不在時に `SQLSTATE[42S02]` で落ちる bug あり。手動 DELETE で回避 |
| RC-6 | TrialBalance 数値一致 | ✓ | 全 entity x 全期 (15 組合せ) で `QueryTrialBalanceUseCase` 結果と raw SQL の SUM が完全一致。entity=2/pd=8 の 4 科目、entity=1/pd=20 の 22 科目を legacy `accountingLogCalcJpn` と直接比較しても **すべての科目で 1 円も差異無し** |
| RC-7 | 試算表応答時間実測 | ✓ | 全 15 期合計 70 ms (avg 4.7 ms / 期、最大 6.8 ms)。最大期 (entity=1/pd=20, 434 lines, 22 科目) を 100 回反復: avg 6.44 ms / p95 6.73 ms / p99 6.94 ms / max 6.94 ms。**ユーザー目標 1 秒の 145 倍以下**。全体的に CPU bound ではなく PDO ラウンドトリップが支配的 |
| RC-8 | 複数会計期 期首残高一致 | ✗ | **H-3 確定**。`ZeroOpeningBalanceRepository` がデフォルト wired (ContainerBootstrap.php:415) のため、各期 TB は **その期内の dr/cr** だけを返し、前期末残高を引き継がない。例: 株式会社アクセク 現金は legacy 累計 期 14→20 = ¥5,490,437 のところ renewal 期 20 TB は ¥1,593,200 (差 ¥3,897,237 ≒ 期 14〜19 の累計が消失)。普通預金は同様に約 ¥5.18M ぶん欠落。**多年度 import の本番投入は H-3 解消が必須** |
| RC-9 | `/ui/journals/new` 仕訳投稿 → 帳票反映 | △ | UI 経由で entity / fiscal_term / 2 科目を seed → 仕訳 1 本投稿成功 (303→detail)。但し UI に「draft → posted」遷移ボタンが無く、帳票反映には DB 直 UPDATE が必要だった。`/ui/trial-balance` は **404**（ルート未登録、既知問題） |

---

## 詳細記録

（各 RC の実施結果はこの下にセクション追加）

---

## RC-0: `composer install` 完走 — ✓ pass

**実行コマンド**:
```bash
docker compose build app
docker compose up -d app db
docker compose exec -T app php -v
docker compose exec -T app composer --version
docker compose exec -T app ls vendor/
```

**結果サマリ**:
- builder stage で `composer install --no-progress --no-scripts --prefer-dist --optimize-autoloader` が成功
- 成果物 vendor/ が final stage に COPY、named volume `app_vendor` でバインドマウントから保護
- PHP 8.3.31 / Composer 2.9.7 / vendor 直下 50 ディレクトリ
- `back/class/else/` も Dockerfile が COPY しており autoload は破綻せず（warning は 1 件: `Code_Else_Plugin_Accounting_Jpn_CalcBanks_Japannetbank` の二重定義 — レガシー由来、機能影響なし）

**所見**:
- `back/` ディレクトリは renewal にも残っており `composer.json` の classmap も解決可能。autoload 破綻の懸念は払拭。
- `Ambiguous class resolution` warning は `exclude-from-classmap` の設定で消せるが現時点の検証目的では無害。

---

## RC-1: `docker compose up` 完走 — ✓ pass

**実行コマンド**:
```bash
docker compose up -d app db
docker compose ps
```

**結果サマリ**:
```
NAME                     STATUS                    PORTS
accounting_renewal_app   Up 5 seconds (healthy)    0.0.0.0:9080->80/tcp
accounting_renewal_db    Up 11 seconds (healthy)   0.0.0.0:3307->3306/tcp
```
- mariadb 10.11-jammy が pull → start → healthy までスムーズ
- app コンテナの healthcheck (`curl http://localhost/`) も healthy へ遷移
- phpmyadmin は profile `dev` のため起動せず（compose 設定通り）

**所見**:
- ポートの衝突なし（9080 / 3307 は空いていた）。プロジェクト名 `accounting-renewal` は他 worktree と隔離されている。

---

## RC-2: 全 migration 適用成功 — △ partial（runner bug を発見・回避済み）

**実行コマンド**:
```bash
# bootstrap (root で 0000 を流し schema_migrations を作成)
docker compose exec -T app sh -c 'cat scripts/migrate/0000_init_database.sql' \
  | docker compose exec -T db sh -c 'mariadb -uroot -proot'

# ad-hoc runner（primary を順次 → seed を順次）
docker compose exec -T app php scripts/migrate/_run_all.php
```

**結果サマリ**:
- 適用件数: primary 18 (0001〜0018) + seeds 5 (0008/0009/0011/0014/0018) = 計 23 ファイル
- テーブル件数: **35** (`schema_migrations` 含む。renewal ADR-002 の "約 30" を上回る)
- 失敗 SQL: 当初 `MigrationRunner::up()` を素直に呼ぶと `0008` で失敗
  ```
  SQLSTATE[42S02]: 1146 Table 'rucaro.fs_section_definitions' doesn't exist
  ```

**詳細 / 抜粋（テーブル一覧）**:
```
account_title_consumption_tax_defaults, account_title_cs_mappings,
account_title_cvp_classifications, account_title_fs_mappings, account_titles,
api_tokens, approval_tokens, blue_return_forms, budget_line_items, budgets,
cash_plan_entries, cash_plans, consumption_tax_categories,
consumption_tax_invoice_registrations, consumption_tax_periods,
consumption_tax_rates, entities, fiscal_terms, fixed_asset_categories,
fixed_asset_depreciation_schedules, fixed_assets, fs_cs_section_definitions,
fs_note_templates, fs_notes, fs_section_definitions, journal_entries,
journal_entry_lines, opening_balances, receipt_action_logs, receipts,
schema_migrations, ss_manual_adjustments, sub_account_titles,
trial_balance_snapshots, users
```

**所見（重要）**:
- **MigrationRunner glob bug**: `src/Infrastructure/Migration/MigrationRunner.php:144`
  ```php
  $upFiles = glob(... . '*.sql');
  // ...
  if (!preg_match('/^(\d{4})_([A-Za-z0-9_\-]+)\.sql$/', $basename, $m)) continue;
  $byVersion[$version] = new Migration(...);
  ```
  `0008_fs_mappings.sql` と `0008_fs_mappings_seed.sql` がともに version `0008` にマッチし、glob のアルファベット順で **後者が前者を上書き**。結果、テーブル定義が走る前に seed の `INSERT` が実行され失敗する。
- 影響: `migrate:status` も同様に seed 側だけ表示する。`bin/cowork migrate:up` 自体まだ未実装（`bin/cowork` は phase1 雛形でコマンド未登録）なので運用上は表面化していない。
- 対応案（後続フェーズで修正）:
  1. `MigrationRunner::discover()` の正規表現を `/^(\d{4})_(.+?)(_seed)?\.sql$/` にして seed を別カテゴリで扱う、または
  2. seeds をリネームして `0019_seed_fs_mappings.sql` のように独立 version とする、または
  3. seed ディレクトリを `scripts/seeds/` に分離する。
- ad-hoc runner: `scripts/migrate/_run_all.php` を作成（Phase A 検証専用、後で削除可）。

---

## RC-3: PHPUnit 実行 — △ partial（多数 errors はほぼ runner bug 起因）

**実行コマンド**:
```bash
docker compose exec -T \
  -e RUCARO_TEST_DB_HOST=db -e RUCARO_TEST_DB_PORT=3306 \
  -e RUCARO_TEST_DB_USER=rucaro -e RUCARO_TEST_DB_PASSWORD=rucaro \
  -e RUCARO_TEST_DB_PASS=rucaro -e RUCARO_TEST_DB_NAME=rucaro \
  -e RUCARO_TEST_DB_DSN='mysql:host=db;port=3306' \
  app vendor/bin/phpunit --colors=never --no-coverage
```

**結果サマリ**:
```
Tests: 963, Assertions: 2110, Errors: 56, Failures: 1, Skipped: 12
```
- **Errors 56 件の内訳**:
  - 52 件: `RuntimeException: Migration statement failed: SQLSTATE[42S02] ... fs_section_definitions doesn't exist`（RC-2 と同じ glob bug — 各 Integration test が `MigrationRunner::up()` を fixture で呼んでいるため毎回再現）
  - 4 件以上: `Unknown database 'rucaro'`（テスト側が `rucaro_test` ではなく `rucaro` を直に DROP/CREATE しようとして失敗）、`Migrations directory does not exist: /var/www/html/tests/scripts/migrate`（パス解決ミス）、`ULID string must be exactly 26 characters long`
- **真の Failure 1 件** (`testGoldenFromSpec`):
  ```
  Rucaro\Tests\Unit\Domain\BreakEvenPoint\BreakEvenPointCalculatorTest::testGoldenFromSpec
  --- Expected      +++ Actual
  - '0.9879'        + '0.9878'
  ```
  丸め差 1 ulp の純粋な計算 fail（DB 非依存・glob bug 非依存）
- **Skipped 12 件**: 全て `LegacyBlowfishDecryptor` 関連（OpenSSL legacy provider が未有効、Bookworm/Trixie ベースの php:8.3-apache では既定で `bf-cbc` 不可）

**失敗 / エラーになったテスト（top 10、抜粋）**:
1. `Integration\Infrastructure\CashPlan\PdoCashPlanRepositoryTest::testDeleteIsSoftDelete`
2. `Integration\Infrastructure\CashPlan\PdoCashPlanRepositoryTest::testSaveRoundTripWithEntries`
3. `Integration\Infrastructure\CashPlan\PdoCashPlanRepositoryTest::testSaveReplacesEntriesAtomically`
4. `Integration\Infrastructure\StatementOfChangesInEquity\PdoSsManualAdjustmentRepositoryTest` 4 件
5. `Integration\Infrastructure\BreakEvenPoint\PdoCvpClassificationRepositoryTest` 2 件
6. `Integration\Infrastructure\TrialBalance\PdoTrialBalanceQueryServiceTest` 4 件
7. `Integration\Infrastructure\ConsumptionTax\PdoConsumptionTaxRepositoryTest` 6 件
8. `Integration\Infrastructure\Budget\PdoBudgetRepositoryTest` 4 件
9. `Integration\Infrastructure\FinancialStatementNotes\PdoFsNoteRepositoryTest` 6 件
10. `Integration\Api\V1\AccountTitlesListTest::testAccountTitlesListRequiresAuth`

**所見**:
- **誤検知の 52/56 errors は全て同一原因**。MigrationRunner の glob bug を直せば一気に通る見込み。
- 残り 4 件の `Unknown database 'rucaro'` は test fixture の DB ライフサイクル設計（drop → create → migrate）に問題がありそう。Phase B で fixture の専用 DB 名（`rucaro_test`）+ 専用ユーザー権限を整理する必要あり。
- `BreakEvenPoint` の 1 ulp 差は **renewal 側コードの真のバグ候補**。CVP 計算の浮動小数点丸めを再検証すべき。
- Blowfish 12 件は `legacy_sect` を有効化した OpenSSL config を Dockerfile に追加すれば pass する（ADR-003 のレガシー暗号取り込み時に必要）。

---

## RC-3.1: 静的解析 — △ partial

### phpstan — ✓ pass

```bash
docker compose exec -T app vendor/bin/phpstan analyse --no-progress --memory-limit=1G
```

**結果**: `[OK] No errors`（`phpstan-baseline.neon` のおかげで現状ゼロ件）

### psalm — ✗ fail

```bash
docker compose exec -T app vendor/bin/psalm --no-progress --no-cache
```

**結果**: **613 errors + 153 other issues**（うち 439 件は psalm 自身が auto-fix 可能）

代表エラー:
- `MissingOverrideAttribute` 多数 (`tests/Unit/Support/Web/*Test.php` の `setUp/tearDown` に `#[\Override]` 付与が必要)
- `RedundantCondition` (`SessionStoreTest.php:71`)
- `ArgumentTypeCoercion` (`tests/bootstrap.php:34`, `date_default_timezone_set` に non-empty-string が要求される)
- "Psalm was able to infer types for 99.5867% of the codebase" — type coverage 自体は健全

### php-cs-fixer — ✗ fail

```bash
docker compose exec -T app vendor/bin/php-cs-fixer fix --dry-run --diff -v
```

**結果**: **804 ファイル中 607 ファイルに差分**（主に `binary_operator_spaces`, `concat_space`, `global_namespace_import`, `no_unused_imports`, `blank_line_before_statement`, `native_constant_invocation`）

**所見**:
- phpstan は baseline 運用で安定。新規コードの違反だけ警戒。
- psalm 613 errors は tests 側の `#[Override]` 欠落が大半。`vendor/bin/psalm --alter --issues=MissingOverrideAttribute,InvalidReturnType,MissingParamType` で 439 件は機械的に修正可能 → Phase B の最初のチケットに最適。
- php-cs-fixer 607/804 ファイルは `composer cs-fix` 一発で揃う。これも Phase B で先に走らせるべき（その後の差分レビューを楽にする）。

---

## 次のアクション（Phase B/C/D へ送るべき項目）

1. **Migration runner の glob bug 修正**（`src/Infrastructure/Migration/MigrationRunner.php:152` の正規表現を `_seed` を考慮した形にする、または seed をディレクトリ分離）。これだけで PHPUnit Errors 56→4 程度まで激減する見込み。
2. **`bin/cowork` に `migrate:up / migrate:status / migrate:down` を実装**（現在は雛形のみ）。README が前提とするコマンドが未提供。
3. **Test fixture の DB ライフサイクル整理**（`rucaro_test` 専用 DB / 専用ユーザー / GRANT を docker-compose に追加）。
4. **BreakEvenPointCalculator 0.9879 vs 0.9878 の丸め差**を Phase B でドメインバグとして調査。
5. **psalm 439 件は `--alter` で機械修正、php-cs-fixer は `composer cs-fix` で 607 ファイル一括整形** を Phase B 最初のクリーンアップとして実施。
6. RC-4 / RC-9 (UI スモーク) と RC-5〜RC-8 (master データ取り込み) は本セッション未実施。app は healthy なので curl で `/ui/login` のレスポンスは取得可能だが、本タスクのスコープ外。

---

## RC-4: ログイン → ダッシュボード — ✓ pass

### 環境セットアップ

- 本セッション開始時、`db_data` ボリュームが空 (`/var/lib/mysql` 内に `rucaro/` ディレクトリ無し) で `/ui/login` が **503**（"初期化に失敗しました。DB 接続設定や .env を確認してください。"）。前回 RC-2 後にコンテナを再生成した際にスキーマが消失。
- 復旧手順:
  1. `mariadb -uroot -proot -e "CREATE DATABASE rucaro ...; GRANT ALL ON rucaro.* TO 'rucaro'@'%'"` で DB 再作成
  2. `docker compose exec -T app php scripts/migrate/_run_all.php` を再実行 → 18 primary + 5 seeds → 35 テーブル復旧
- users テーブルは空。**user 作成用 CLI が存在しない**（`bin/cowork` は phase1 雛形でコマンド未登録、`scripts/dev/` には PDF レンダラのみ、`scripts/import/` には legacy importer のみ）ので、PHP one-liner で Argon2id ハッシュ + ULID(BINARY(16)) を生成して直接 INSERT:
  ```php
  $bin = (new UlidGenerator())->binary();
  $hash = (new PasswordHasher())->hash('admin123');
  // INSERT INTO users (id, login_id, display_name, email, password_hash, is_active)
  // VALUES (BINARY(16), 'admin', '管理者', 'admin@example.com', $hash, 1)
  ```
  → `email=admin@example.com / password=admin123` で seed 完了

### curl 実行ログ抜粋

```
$ curl -sI http://localhost:9080/ui/login    # HEAD → HTTP/1.1 404 Not Found  (※ ルーティングのバグ。GET は 200)
$ curl -s -o /tmp/login.html  http://localhost:9080/ui/login -w "%{http_code}\n"
200
$ grep 'name="_csrf"' /tmp/login.html
<input type="hidden" name="_csrf" value="ac0519...c30325">

$ curl -b /tmp/cookies -X POST http://localhost:9080/ui/login \
    -d "_csrf=...&email=admin@example.com&password=admin123" -w "%{http_code}"
HTTP/1.1 303 See Other
Location: /ui/dashboard

$ curl -b /tmp/cookies -L http://localhost:9080/ui/dashboard -w "%{http_code}"
200
# title=<title>Rucaro Accounting</title>, ナビに「ダッシュボード」「ログアウト」「/ui/journals」「/ui/pl」「/ui/bs」等あり
```

### 所見

- ログイン → セッション (cookie `rucaro_ui_sid`) → ダッシュボード描画は完全に動作。CSRF (`_csrf` フォームフィールド) も期待通り。
- **HEAD /ui/login が 404** を返す（GET は 200）。WebKernel の routing が HEAD を独立に登録していない FastRoute 由来の問題。実害は無いがヘルスチェック等で踏みうる。
- **ユーザー seed 用 CLI 未提供** が Phase B のブロッカー。`bin/cowork user:create --login=admin --email=...` 相当のコマンドが必要（社内オペレーション・運用 README にも欠落）。
- bootstrap 失敗時のレスポンス本文は `503 ... 初期化に失敗しました` のみで詳細無し → APP_DEBUG=true でも追加情報無し。Phase C で **error reporter のテレメトリ強化**（DB 接続失敗時のスタックトレースをログに出す or 開発環境では画面表示）を要検討。

---

## RC-9: 仕訳投稿 → 帳票反映 — △ partial

### 試したこと

ダッシュボードからナビゲートして以下を順に作成（全て UI POST 経由、CSRF 込み）:

| 手順 | URL | 状態 |
|---|---|---|
| 1 | `POST /ui/masters/entities/new` (name=テスト株式会社, fiscal_start_mmdd=0401) | 303 → 一覧、entity 1 件作成 |
| 2 | navbar の auto-select で entity がセッションに入る（明示 `/ui/entity/switch` POST 不要） | OK |
| 3 | `POST /ui/masters/fiscal-terms/new` (期番号=1, 2025-04-01〜2026-03-31) | 303 → 一覧、fiscal_terms 1 件 |
| 4 | `POST /ui/masters/account-titles/new` ×2 (1110 現金 asset/debit, 4110 売上高 revenue/credit) | 303 / 303、account_titles 2 件 |
| 5 | `GET /ui/journals/new` で fiscal_term ・科目セレクトが populate されたのを確認 | OK |
| 6 | `POST /ui/journals/new` (journal_date=2025-09-15, summary=テスト売上, lines[0]=借方/現金/10000, lines[1]=貸方/売上/10000) | 303 → `/ui/journals/01KR8KAW3SMN4CMBNJSDC7729E` |
| 7 | `GET /ui/journals` で一覧に "テスト売上" が出現 | OK |
| 8 | `GET /ui/ledger`, `/ui/pl`, `/ui/bs` を確認 | **当初は数値ゼロ**（理由は↓） |
| 9 | DB 直 `UPDATE journal_entries SET status='posted' WHERE ...` で承認済みに昇格 | OK |
| 10 | 再度 `/ui/ledger`, `/ui/pl`, `/ui/bs` を取得 → 全て `10,000` が反映 | OK |
| 11 | `GET /ui/trial-balance` | **HTTP 404**（ルート未登録） |

### 詰まった箇所

1. **draft → posted 遷移 UI が存在しない**: `POST /ui/journals/new` で作成された仕訳は `status='draft'`。詳細画面 `/ui/journals/{id}` は単に編集フォームを再表示するだけで、「投稿/承認」ボタンが無い。`Http/WebKernel.php` の routes にも `/ui/journals/{id}/post` 等は無い。
   - 一方 `PdoTrialBalanceQueryService.php:49` / `PdoLedgerQueryService.php:73` は `WHERE je.status IN ('posted','approved')` で集計するため、UI 経由で投入した draft 仕訳は永久に帳票に出ない。
   - 暫定対応として DB 直接 `UPDATE journal_entries SET status='posted'` で promote → 全帳票に正しく反映。
2. **`/ui/trial-balance` ルート未登録**: WebKernel.php の routes 配列に `/ui/trial-balance` が無く 404。`/ui/ledger /ui/pl /ui/bs /ui/cs` は存在する。
3. seed 用エンティティ/科目数: 最小（科目 2 件）でも帳票は描画される。consumption_tax / fs_mappings の seed (5 ファイル) が migration で入っているため税区分エラーは出なかった。

### 所見

- **仕訳投稿フローは技術的には完動**（CSRF, バリデーション, 二重エントリの貸借一致チェック, 一覧表示, 詳細表示）。
- ただし **業務フローとして決定的に未完成**:
  - draft 仕訳を確定する手段が UI に無い（API 側にも `JournalPostController` 相当の存在を src/ 全体で grep したが見当たらない）
  - `JournalEntry` の `status` 列は migration 0006 で `draft|posted|approved|locked|deleted` まで拡張されているが、Application 層に状態遷移 UseCase が無い疑い
- **Phase C 監査で確認すべき具体的不足点**:
  1. `src/Application/Journal/` に `PostJournalEntryUseCase` / `ApproveJournalEntryUseCase` が存在するか、存在するならなぜ Web 層に紐づいていないのか
  2. `bin/cowork` に最低限 `user:create`, `entity:create`, `migrate:up` を実装するチケット
  3. `/ui/trial-balance` ルートの追加 + ナビ整合性（dashboard ナビには `/ui/trial-balance` リンク無し → ナビとルートの両方が欠落）
  4. HEAD メソッド対応（FastRoute の dispatcher を HEAD→GET フォールバックさせる middleware）
  5. bootstrap 503 時の診断情報強化（現状は本文 1 行のみで debug 困難）

### 残置データ（次セッション RC-5 で truncate 必要）

| テーブル | 件数 |
|---|---|
| `users` | 1 (admin@example.com / admin123) |
| `entities` | 1 (テスト株式会社) |
| `fiscal_terms` | 1 (第1期 2025-04-01〜2026-03-31) |
| `account_titles` | 2 (1110 現金 / 4110 売上高) |
| `journal_entries` | 1 (status=posted, 2025-09-15 テスト売上 ¥10,000) |
| `journal_entry_lines` | 2 |

RC-5 で master データを取り込む前に上記を `TRUNCATE` するか、entity ごと `DELETE` する想定。

---

## RC-5: LegacyImport (master 1,701 仕訳 / 15 期 / 2 エンティティ) — △ partial

### 環境構築

master DB は別 compose プロジェクト (`accounting-db-1`, port 3306) に存在。importer は **単一 DB host 前提** なので、master を renewal の MariaDB に `rucaro_legacy` として注入する戦略を取った。

```bash
# 1) master を dump
docker exec accounting-db-1 sh -c \
  'mariadb-dump -uroot -proot --single-transaction --routines --triggers rucaro > /tmp/master_dump.sql'
# 24,711,551 bytes (約 24 MB)

# 2) renewal DB に rucaro_legacy DB を作成
docker compose exec -T db sh -c \
  'mariadb -uroot -proot -e "DROP DATABASE IF EXISTS rucaro_legacy; CREATE DATABASE rucaro_legacy CHARACTER SET utf8mb4; GRANT ALL ON rucaro_legacy.* TO \"rucaro\"@\"%\"; GRANT ALL ON rucaro_legacy.* TO \"root\"@\"%\";"'

# 3) docker cp で dump を移動 → 流し込み
docker cp accounting-db-1:/tmp/master_dump.sql /tmp/master_dump.sql
docker cp /tmp/master_dump.sql accounting_renewal_db:/tmp/master_dump.sql
docker compose exec -T db sh -c 'mariadb -uroot -proot rucaro_legacy < /tmp/master_dump.sql'
# accountingLog 1,701, accountingLogCalcJpn 3,950, accountingEntity 2, baseAccount 1 を確認
```

### dry-run

```bash
docker compose exec -T app php scripts/import/legacy_to_v2.php --dry-run \
  --source-db=rucaro_legacy --target-db=rucaro \
  --db-host=db --db-port=3306 --db-user=root --db-password=root \
  --stage=all --placeholder-password='changeme-placeholder-2026'
```

| stage | read | inserted | skipped | notes |
|---|---:|---:|---:|---|
| users | 1 | 1 | 0 | admin (masudagreen@gmail.com) |
| entities | 2 | 2 | 0 | 株式会社アクセク (corp), 個人 (personal) |
| fiscal_terms | 15 | 15 | 0 | 1-1〜1-7, 1-14〜1-20, 2-1〜2-8 |
| account_titles | 48 | 48 | 0 | L0001〜L0048 (Crockford Base32 連番) |
| sub_accounts | 0 | 0 | 0 | (master に行なし) |
| journals | 1666 | 1666 | 0 | journal_entry_lines inserted: 3950 |
| fixed_assets | 0 | 0 | 0 | (master `accountingFixedAssetsJpn` 未参照 or 空) |
| fs_mappings | 48 | 48 | 0 | account_title_fs_mappings |

**注**: dry-run の "journals read=1666" は importer 側で `accountingLog` を直接見るのではなく、`accountingLogCalcJpn` から有効なヘッダだけを SELECT JOIN しているため。`accountingLog` 1,701 - 35 (calc 行ゼロの空ヘッダ) = 1,666。

### apply

`--truncate-target` を付けると、まだ存在しない `legacy_id_mapping` テーブルを参照する DELETE が走り即落ちる:

```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'rucaro.legacy_id_mapping' doesn't exist
```

→ `LegacyToV2Command::truncateTarget()` (LegacyToV2Command.php:163-187) は `legacy_id_mapping` の存在を **前提** にしているが、初回 import 時にはまだ無い。`IdMapping::truncate()` が呼ばれた瞬間に CREATE される設計と矛盾。

回避策: 手動 DELETE で renewal の RC-9 残置データをクリアし、`--truncate-target` 抜きで apply。

```bash
docker compose exec -T db mariadb -uroot -proot rucaro -e "
  SET FOREIGN_KEY_CHECKS=0;
  DELETE FROM journal_entry_lines; DELETE FROM journal_entries;
  DELETE FROM account_title_fs_mappings;
  DELETE FROM account_title_consumption_tax_defaults;
  DELETE FROM account_title_cs_mappings;
  DELETE FROM account_title_cvp_classifications;
  DELETE FROM sub_account_titles; DELETE FROM account_titles;
  DELETE FROM fiscal_terms; DELETE FROM opening_balances;
  DELETE FROM entities; DELETE FROM users;
  DELETE FROM api_tokens; DELETE FROM approval_tokens;
  SET FOREIGN_KEY_CHECKS=1;"

docker compose exec -T app php scripts/import/legacy_to_v2.php --apply \
  --source-db=rucaro_legacy --target-db=rucaro \
  --db-host=db --db-port=3306 --db-user=root --db-password=root \
  --stage=all --placeholder-password='changeme-placeholder-2026'
```

結果: dry-run と全く同じ件数 (read 1666 / inserted 1666 / skipped 0)。所要時間は数秒（体感）。

### renewal DB 検証

```sql
SELECT 'users' AS t, COUNT(*) FROM users
UNION ALL SELECT 'entities', COUNT(*) FROM entities
UNION ALL SELECT 'fiscal_terms', COUNT(*) FROM fiscal_terms
UNION ALL SELECT 'account_titles', COUNT(*) FROM account_titles
UNION ALL SELECT 'sub_account_titles', COUNT(*) FROM sub_account_titles
UNION ALL SELECT 'journal_entries', COUNT(*) FROM journal_entries
UNION ALL SELECT 'journal_entry_lines', COUNT(*) FROM journal_entry_lines
UNION ALL SELECT 'account_title_fs_mappings', COUNT(*) FROM account_title_fs_mappings
UNION ALL SELECT 'opening_balances', COUNT(*) FROM opening_balances
UNION ALL SELECT 'legacy_id_mapping', COUNT(*) FROM legacy_id_mapping;
```

| table | n | 備考 |
|---|---:|---|
| users | 1 | OK |
| entities | 2 | OK (株式会社アクセク, 個人) |
| fiscal_terms | 15 | OK |
| account_titles | 48 | OK |
| sub_account_titles | 0 | (master 元データなし) |
| journal_entries | 1666 | 1,701 - 35 空ヘッダ = 1,666 (期待通り) |
| journal_entry_lines | 3950 | calc 行と完全一致 |
| account_title_fs_mappings | 48 | OK |
| opening_balances | 0 | **重要**: RC-8 の主因 (importer が期首残高を生成しない) |
| legacy_id_mapping | 1732 | 1+2+15+48+1666 = 1732 (累計マッピング) |

```sql
-- 全 lines 貸借バランス
SELECT COUNT(*) AS lines_total,
       SUM(CASE WHEN side='debit'  THEN amount ELSE 0 END) AS dr_sum,
       SUM(CASE WHEN side='credit' THEN amount ELSE 0 END) AS cr_sum
FROM journal_entry_lines;
-- → 3950 / 425,938,307 / 425,938,307  (完全一致)
```

### 主要発見 (RC-5)

1. **金額は完全一致** (¥425,938,307 dr=cr)。importer の数値忠実度は実証済み。
2. **空ヘッダ 35 件は意図的に skip**。renewal 側で `journal_entries.id` の連続性が壊れる懸念は無いが、master 側の `idLog` をユニーク参照キーに使う運用なら **対応表が不可逆に消失** する点に注意。
3. **`--truncate-target` の bootstrap bug**: 初回 import で必ず踏む。Phase B チケット候補。
4. **`opening_balances` テーブルが空**: importer は B/S 期首を生成しない設計。`stage=all` でも同様。これが RC-8 の根本原因。
5. **fixed_assets は 0**: master の `accountingFixedAssetsJpn` を `LegacyFixedAssetImporter` が読みに行くが本データセットには 0 行 (master 側で運用していない可能性が高い)。
6. **sub_account_titles も 0**: 同上。master のデータ実態として補助科目を使っていないだけ。

---

## RC-6: TrialBalance 数値一致 — ✓ pass

### 検証スクリプト

`scripts/dev/_rc6_tb_check.php` (Phase A 検証専用、削除可) を作成し、`ContainerBootstrap::build($pdo)` 経由で `QueryTrialBalanceUseCase` を直接 instantiate → 全 entity x 全 fiscal_term の組合せで execute → raw SQL の SUM と突合。

### 結果

| entity | pd | start | asOf | tb_dr | tb_cr | rows | ms |
|---|---:|---|---|---:|---:|---:|---:|
| 個人 | 1 | 2019-01-01 | 2019-12-31 | 625,928 | 625,928 | 8 | 2.3 |
| 個人 | 2 | 2020-01-01 | 2020-12-31 | 1,240,029 | 1,240,029 | 9 | 1.1 |
| 個人 | 3 | 2021-01-01 | 2021-12-31 | 303,700 | 303,700 | 4 | 0.8 |
| 個人 | 4 | 2022-01-01 | 2022-12-31 | 5,639,684 | 5,639,684 | 6 | 0.8 |
| 個人 | 5 | 2023-01-01 | 2023-12-31 | 3,021,927 | 3,021,927 | 10 | 5.9 |
| 個人 | 6 | 2024-01-01 | 2024-12-31 | 3,868,085 | 3,868,085 | 14 | 6.0 |
| 個人 | 7 | 2025-01-01 | 2025-12-31 | 887,803 | 887,803 | 12 | 6.0 |
| 個人 | 8 | 2026-01-01 | 2026-12-31 | 114,277 | 114,277 | 4 | 0.8 |
| 株式会社アクセク | 14 | 2019-07-01 | 2020-06-30 | 68,254,700 | 68,254,700 | 19 | 6.6 |
| 株式会社アクセク | 15 | 2020-07-01 | 2021-06-30 | 46,154,362 | 46,154,362 | 21 | 6.5 |
| 株式会社アクセク | 16 | 2021-07-01 | 2022-06-30 | 52,592,691 | 52,592,691 | 22 | 6.8 |
| 株式会社アクセク | 17 | 2022-07-01 | 2023-06-30 | 63,735,892 | 63,735,892 | 21 | 6.7 |
| 株式会社アクセク | 18 | 2023-07-01 | 2024-06-30 | 63,408,113 | 63,408,113 | 23 | 6.6 |
| 株式会社アクセク | 19 | 2024-07-01 | 2025-06-30 | 58,704,870 | 58,704,870 | 22 | 6.6 |
| 株式会社アクセク | 20 | 2025-07-01 | 2026-06-30 | 57,386,246 | 57,386,246 | 22 | 6.6 |

**全 15 期で UseCase 結果と raw SQL SUM が完全一致**（"All UseCase totals match raw SQL totals."）。

### 科目別比較サンプル

**個人 / 期 8** (4 科目):
```
legacy:  cash 0/114277, correspondenceExpenses 14502/0, suppliesExpenses 63775/0, taxesAndDues 36000/0
renewal: L0005 現金 0/114277, L0009 通信費 14502/0, L0018 消耗品費 63775/0, L0019 租税公課 36000/0
```

**株式会社アクセク / 期 20** (22 科目): すべて legacy `accountingLogCalcJpn` の SUM(idAccountTitle) と完全一致 (例: accountsReceivable 12,436,875/12,441,000, ordinaryDeposit 12,445,299/13,692,631, netSales 0/12,436,875, directorsCompensations 9,600,000/0)。

### 主要発見 (RC-6)

- **数値忠実度は完璧**。`PdoTrialBalanceQueryService` は `WHERE je.status IN ('posted','approved')` でフィルタするが、importer が全件 `status='posted'` で投入する設計と整合。
- ただし **これは「累計でない単一期 TB」の話**。多年度を跨ぐ累計表示は RC-8 で破綻する。
- `TrialBalanceQueryInterface::queryByPeriod()` の単純 SUM ロジックは正しいが、`OpeningBalanceRepositoryInterface` が **TB UseCase に渡されていない**点（QueryTrialBalanceUseCase の constructor を見ると snapshots と clock しか取らない）も今後の論点。

---

## RC-7: 試算表応答時間実測 — ✓ pass

### 計測方法

1. `_rc6_tb_check.php` 内で `microtime(true)` 前後測定 (15 期、cold + warm 混在)
2. 株式会社アクセク 期 20 (最大規模 = 22 科目 / 434 lines / dr 5736万円) を対象に **100 回反復**

### 結果

**1) 全 15 期通し** (cold start を含む)

| 指標 | 値 |
|---|---|
| 合計時間 | 70.0 ms |
| 平均 / 期 | 4.7 ms |
| 最小 | 0.8 ms (個人 / 期 3 = 4 科目のみ) |
| 最大 | 6.8 ms (株式会社アクセク / 期 16) |

**2) 単一期 (株式会社アクセク pd=20) を 100 回**

| 指標 | 値 |
|---|---|
| N | 100 |
| avg | 6.44 ms |
| p50 | 6.46 ms |
| p95 | 6.73 ms |
| p99 | 6.94 ms |
| max | 6.94 ms |

### 主要発見 (RC-7)

- **ユーザー目標 1 秒以内を 145 倍以上下回る**。本データセット規模 (~3,950 lines) では性能懸念無し。
- 1 期あたりの SQL は 1 本 (`PdoTrialBalanceQueryService::queryByPeriod` の `GROUP BY je.entity_id, jel.account_title_id, at.code, at.name, at.category, at.normal_side`) → ラウンドトリップ最小化済み。
- スナップショット未生成のため `latestSnapshotDate` は毎回 NULL を返し、live SUM パスが選ばれている。スナップショット導入後の性能変化は別途検証要 (RC-7.1)。
- 本番データが 10 年 / 50,000 仕訳規模に拡張しても線形なら ~140ms/期 で収まる試算 (3,950 lines / 7ms = 550 lines/ms スループット)。

---

## RC-8: 複数会計期 期首残高一致 — ✗ FAIL (H-3 確定)

### 検証

`scripts/dev/_rc8_balance_check.php` で株式会社アクセクの **L0001 売掛金 / L0004 現金 / L0020 普通預金** を期 14〜20 にわたり TB UseCase を呼び、各期の dr/cr/net と擬似累計を出力。

### 結果 (抜粋)

| pd | code | period_dr | period_cr | period_net | 累計 (renewal が返さない値) |
|---:|---|---:|---:|---:|---:|
| 14 | L0004 現金 | 1,000,000 | 428,542 | +571,458 | 571,458 |
| 15 | L0004 現金 | 500,000 | 1,337,279 | -837,279 | -265,821 |
| 16 | L0004 現金 | 500,000 | 595,598 | -95,598 | -361,419 |
| 17 | L0004 現金 | 2,000,000 | 530,900 | +1,469,100 | 1,107,681 |
| 18 | L0004 現金 | 4,000,000 | 1,574,851 | +2,425,149 | 3,532,830 |
| 19 | L0004 現金 | 2,800,000 | 2,435,593 | +364,407 | 3,897,237 |
| 20 | L0004 現金 | 9,000,000 | 7,406,800 | **+1,593,200** | **5,490,437** |
| 14 | L0020 普通預金 | 15,854,066 | 20,335,111 | -4,481,045 | -4,481,045 |
| 20 | L0020 普通預金 | 12,445,299 | 13,692,631 | **-1,247,332** | **-6,431,714** |

renewal の **期 20 TB** で得られる現金額は **¥1,593,200**。
master 側の運用 (累計帳簿) では **¥5,490,437** が正解。
**差額 ¥3,897,237 = 期 14〜19 の累計が消失**。

普通預金は更に深刻で、期 20 単体 -¥1,247,332 vs 真の累計 -¥6,431,714 → **約 ¥5.18M 過少**。

### 根本原因

`src/Support/Container/ContainerBootstrap.php:415`:

```php
$c->set(
    OpeningBalanceRepositoryInterface::class,
    static fn (Container $c): OpeningBalanceRepositoryInterface => new ZeroOpeningBalanceRepository(),
);
```

`ZeroOpeningBalanceRepository::findForFiscalTerm()` は常に空を返す。importer は `opening_balances` テーブルに行を作らない (RC-5 の検証で行数 0 を確認)。

加えて、`QueryTrialBalanceUseCase` の constructor は **`OpeningBalanceRepositoryInterface` を取らない**:

```php
public function __construct(
    private TrialBalanceQueryInterface $query,
    private TrialBalanceSnapshotRepositoryInterface $snapshots,
    private ClockInterface $clock = new SystemClock(),
) {}
```

→ TB は構造的に「期内 dr-cr のみ」を返す設計。BS / Ledger 側 (`QueryLedgerUseCase` は OpeningBalanceRepositoryInterface を取る) と仕様が乖離している。

### 主要発見 (RC-8)

1. **多年度 import は H-3 を解消するまで本番投入不可**。BS / TB / 月次推移すべてに影響。
2. **importer のスコープ拡張が必須**: master の各期末残高を計算 → 翌期 `opening_balances` に INSERT する処理が必要。または専用 `legacy:seed-opening-balances` ステージ追加。
3. **TB UseCase の API 改修も必要**: `OpeningBalanceRepository` を注入し、`accountTitleId` ごとに `dr += opening (debit-side)` / `cr += opening (credit-side)` を加算する。
4. 試算表 UI (`/ui/trial-balance` も未登録ながら) を直しても、UseCase 側の数値が間違っている限り修正は無意味。
5. **暫定対策**: 現データセットを単一期 (期 20 のみ) で運用するか、master の B/S 期末残高 PDF を別途参照する運用フローに留めるしか無い。

---

## 次のアクション (Phase B/C/D 送り) — RC-5/6/7/8 由来

### Phase B: importer & opening balance

1. **`legacy_id_mapping` の lazy create**: `LegacyToV2Command::truncateTarget()` 冒頭で `IdMapping::ensureTable()` を呼ぶ (または `truncateTarget` の頭で `tableExists()` チェック)。**最優先**。
2. **新ステージ `legacy:opening-balances`**: master の `accountingLogCalcJpn` から各期末の科目別累計を計算 → renewal の `opening_balances` に INSERT。term_id ごとに 1 行 / account_title_id ごとに 1 行を `(entity_id, fiscal_term_id, account_title_id, debit_amount, credit_amount)` で持つ想定。
3. **`PdoOpeningBalanceRepository` を default wire** (ZeroOpeningBalanceRepository は test fixture / 初回 entity 専用に留める)。
4. **`QueryTrialBalanceUseCase` に `OpeningBalanceRepositoryInterface` を注入**し、TB の dr/cr に加算。`TrialBalanceRow::compute` の `lineCount` と整合する形 (lineCount に opening は含めない) を保つ。

### Phase C: 監査 / 検証強化

5. **TB スナップショット自動化**: 月末に `RefreshTrialBalanceSnapshotUseCase` を回す cron / コマンド (`bin/cowork tb:refresh-snapshots`)。RC-7 の性能余裕を考えると Phase D まで保留可。
6. **importer の冪等性テスト**: `--apply` を 2 回連続で走らせると `legacy_id_mapping` 経由で重複 INSERT しないか (`getOrCreate` 経由なら問題ないはず) を Integration test に追加。
7. **`accountingLog` の空ヘッダ 35 件は import レポートで明示**: 現状は単に skipped++ されるが、idLog のリストを出力 / dry-run の notes に列挙して legacy 利用者が把握できるようにする。

### Phase D: UI / 帳票

8. **`/ui/trial-balance` 復活** (RC-9 既知欠落)。これだけでは数値が間違っているので H-3 修正後に。
9. **BS / PL に「累計表示モード」と「当期分表示モード」のトグル**: 法人用途では基本累計が正、税務的には当期分が必要。
10. **multi-entity ナビ**: 現在 entity が 2 件あるのに UI のセレクタの実装状況を再確認 (RC-9 では 1 entity しか試していない)。

