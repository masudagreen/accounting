# 移行計画: master → renewal

**作成日**: 2026-05-10
**改訂**: v4（v3 + Phase A リアリティチェック結果反映）
**対象リポジトリ**: `accounting`
**判定対象**: renewal ブランチを継続するか、別アプローチを取るか

---

## 0. エグゼクティブサマリ

**判定: `continue`（条件付き継続）**

renewal ブランチには既に相当な投資が存在する: src 579 ファイル / Web UI Controller 53 個 / SSR テンプレート 103 個 / Web ルート 60+ / test 186 ファイル / 845 テストメソッド / UseCase 123 個 / migration 18 個 / ADR 21 本。ユーザーゴール 5 項目（モダン UI / リアルタイム集計 / DB 簡素化 / テストファースト / テスタブル化）はすべて renewal の設計方針に組み込まれており、Web UI も「ログイン+ダッシュボードのみ」ではなく **業務画面の大半が既に実装済み**（journals/ledger/pl/bs/cs/fs/multi/masters/fixed-assets/budgets/cash-plans/consumption-tax/blue-return/ss/notes など）。

ただし以下の **三大ギャップ** がある（v4: Phase A リアリティチェックで全て検証済み）:
- **G-1 (RC で再確認)**: ADR-007 の「Strangler Fig + FEATURE_* フラグ 14 個」は **ドキュメントのみで実コード未実装**（grep 0 件）。renewal は実質 Greenfield + 片方向データ取り込みで動く構成。
- **G-2 (RC-8 で本番データ規模で確定)**: `ZeroOpeningBalanceRepository` がデフォルト配線で、`OpeningBalanceRepositoryInterface` が `QueryTrialBalanceUseCase` に注入されていない。**実測で本番データ（idEntity=1, 期 20）の現金 TB が真値 ¥5,490,437 に対して ¥1,593,200 と表示、¥3.9M が静かに欠落**。Phase B で 3 段同時対応必須:
  1. importer に `opening-balances` ステージ追加
  2. `PdoOpeningBalanceRepository` を default wire
  3. `QueryTrialBalanceUseCase` の API 改修（OpeningBalance 注入）
- **G-3 (RC-7 で否定)**: 性能目標「10 万仕訳 < 2 秒」は実データ 1,701 仕訳ではほぼ無関係。実測で **試算表応答 6.4 ms（avg）/ 6.9 ms（max）** = ユーザー目標 1 秒の **145 倍以下**。**性能対策は当面不要**。集計テーブル廃止後も都度集計で十分実用的。

**v4 の追加発見**:
- **importer の数値忠実度は完璧**: 1,701 master 仕訳 → 1,666 import（35 件は `accountingLog` に行があるが `accountingLogCalcJpn` に対応行が無い空ヘッダ、silent skip。要監査ログ化）、貸借合計 ¥425,938,307 完全一致、全 15 期 × 全科目で TB が 1 円差なし
- **importer `--truncate-target` の初回起動 bug**: `legacy_id_mapping` 未作成時に `42S02` で停止。本番運用で必ず踏む。Phase B の単発チケット（`IdMapping::ensureTable()` を冒頭で呼ぶだけ）
- **draft → posted 遷移 UI が renewal に存在しない**: 仕訳を draft で投稿後、UI では確定不可。DB 直 UPDATE すれば帳票反映されるが、**業務上の blocker**
- **`/ui/trial-balance` ルート未登録**: dashboard ナビからもアクセス不可
- **`bin/cowork` のサブコマンド未実装**（`migrate:up`, `user:create`, `entity:create` など）
- **MigrationRunner glob bug**: `_seed.sql` が 4 桁 version を上書き、PHPUnit で 52 件の整合性エラー誘発
- **psalm 613 errors / php-cs-fixer 607 ファイル差分**: ほぼ機械修正可能（`--alter` + `composer cs-fix`）

本計画では「継続を前提に、リアリティチェック → ギャップ埋め → 並行運用 → 切替」で進める。

棄却した代替案:
- **discard（ゼロから作り直し）**: 上記資産（特に Web UI 53 ctrl + tpl 103 + テスト 845）を捨てるコストが過大
- **partial-reuse（master 本体に新ロジックだけ移植）**: master は手続き的継承 + テストゼロ + UI 旧式で再生不能。partial-reuse は不可
- **partial-discard（renewal の使わない領域だけ捨てる）**: Budget / BEP / BlueReturn 等の運用しないモジュールの撤去は **continue の中の Phase F として残す**

---

## 1. master と renewal の差分

### 1.1 規模・構造

| 項目 | master | renewal | 備考 |
|---|---|---|---|
| 言語/PHP | PHP 7.x 系（plus PHP 8 互換修正済み） | PHP 8.3 固定 | renewal は最新基盤 |
| クラス数 | 577 PHP（back/class 配下） | 579 PHP（src/ 配下） | ほぼ同等規模、構造は別 |
| 命名/構造 | Base/Editor/Search/Choice の手続き的継承 | DDD: Domain/Application/Infrastructure/Http/Support の 5 層 | 別物 |
| autoload | 手作り include | Composer PSR-4 | renewal はモダン |
| フロント | prototype.js + scriptaculous | Smarty 5 + Bootstrap 5 SSR（Phase 7-1 のみ実装） | renewal はまだ最小 |
| DB テーブル | 49 テーブル（集計キャッシュ多数） | 約 30 テーブル（FK 全張り、ULID、TIMESTAMP(6)） | renewal は正規化済 |
| テスト | 0 | 186 ファイル / 845 メソッド | renewal が圧倒的 |
| CI | なし | phpstan L6 / psalm / php-cs-fixer / phpunit on MariaDB | renewal は整備済 |
| 起動 | docker compose up（apache+mariadb） | docker compose up（apache+mariadb+phpmyadmin） | 両方とも運用可能 |
| ドキュメント | なし | PLAN.md + INTERNAL_ARCHITECTURE.md + ADR x 21 + phase1 docs | renewal は文書化が分厚い |

### 1.2 ゴール整合性

| ユーザーゴール | master の状態 | renewal の達成度 | 根拠 |
|---|---|---|---|
| (1) モダン UI | prototype.js（保守不能） | ○ Controller 53 / テンプレート 103 / Web ルート 60+。journals/ledger/pl/bs/cs/fs/multi/masters/fixed-assets/budgets/cash-plans/consumption-tax/blue-return/ss/notes 実装済。**ただし `/ui/trial-balance` は未登録**、動作検証と E2E は未実施 | `src/Http/WebKernel.php:86-160`, `src/Http/Controller/Ui/`, `storage/templates/` |
| (2) リアルタイム集計 | 集計テーブル多用（CalcLog 等） | ○ journal から都度集計、TrialBalanceSnapshot は任意キャッシュ | ADR-002, `src/Domain/TrialBalance/` |
| (3) DB 簡素化（仕訳中心） | 49 テーブル中 8 個前後が廃止可能な集計キャッシュ | ○ 約 30 テーブルに正規化済 | ADR-002, `scripts/migrate/0000-0017_*.sql` |
| (4) テストファースト | 0 | △ 845 メソッド存在。**ただし Integration 系は `RUCARO_TEST_DB_*` 未設定で多数 skip**、カバレッジ未計測、Golden は新内部一致のみで master vs renewal の Golden 未実装 | `tests/`, `.github/workflows/ci.yml`, `tests/Unit/Golden/FinancialStatement/FinancialStatementGoldenTest.php` |
| (5) テスタブル化/モジュール化 | 不可能に近い | ○ Ports & Adapters + DI Container で確立 | ADR-005, `src/Support/Container/ContainerBootstrap.php` |

### 1.3 master の集計テーブル廃止候補（research-master.md §2 より整理）

| 旧テーブル | renewal での扱い | 廃止判定 |
|---|---|---|
| `accountingLogCalc` | 廃止、journal_entry_lines から SUM 導出 | ○ 廃止可 |
| `accountingSubAccountTitleValue` | 廃止 | ○ 廃止可 |
| `accountingFSValue` / `accountingFSId` | fs_mappings + 都度計算 | ○ 廃止可 |
| `accountingSummaryStatement` | trial_balance_snapshot（任意キャッシュ） | ○ 廃止可 |
| `accountingEntityDepartmentFSValue` | 部門別 FS は導出 | ○ 廃止可 |
| `accountingBudget` | budget マスター（独立、計画値） | × 維持 |
| `accountingBreakEvenPoint` | bep_settings マスター | × 維持 |
| `accountingConsumptionTax` | 区分マスター + 都度集計 | △ 性能次第 |
| `accountingFixedAssets` / `accountingLogFixedAssets` | fixed_assets マスター + 償却記録（仕訳と別管理） | × 維持（簿記外情報） |

---

## 2. 判定: continue（条件付き）

### 2.1 continue を選ぶ理由

1. **DB 設計が既にユーザーゴールに合致**（ADR-002）
   - ULID / DECIMAL(18,4) / TIMESTAMP(6) UTC / FK 全張り / JSON 正規化分解
   - 仕訳 (journals + journal_entry_lines) を中心に集計テーブルを廃止する方針が明文化済み

2. **テスタブル化の基盤が完成**（ADR-005）
   - Domain は外部依存なし、Application は Port 経由、Infrastructure が実装
   - PHPStan L6 + Psalm L3 で層間依存違反を機械検証

3. **段階移行戦略**（実態は ADR-021 legacy import 中心）
   - **重要**: ADR-007 の Strangler Fig + Feature flag 14 個（`FEATURE_JOURNAL_V2` 等）は **ADR 上の設計のみ**で、`grep -rnE "FEATURE_" src/` は 0 件、`.env.example` にも未定義
   - 現在の renewal は実質「**新 DB / 新エンドポイント / 新 UI で完結**」する Greenfield + 片方向 import
   - master との切替は片方向 import + 並行運用 (§5) で行う
   - 旧 INT 連番 → 新 ULID マッピングを冪等化する LegacyImport CLI（`scripts/import/legacy_to_v2.php` + 14 ファイル）が実装済
   - **ユーザー要求「master データ取り込み + 現新比較」と整合（フラグ切替ではなく並行運用ベース）**

4. **Golden test 戦略が用意されている**（ADR-007 §9.4）
   - 旧 DB から仕訳を持ち込み → 新ロジックで TB/PL/BS を計算 → 旧出力と一致確認
   - 「テスト書いてから移行」というユーザー方針と整合

5. **やり直しコスト**: 上記を再構築するには数ヶ月オーダー。renewal を捨てる正当性は薄い

### 2.2 ただし以下は要検証（次節 §3 で扱う）

- "Phase 1-6 完走" の自己申告に対する **実動作確認**
- Web UI Phase 7 の **完成度ギャップ**（業務画面が未実装）
- master データ import の **エンドツーエンド検証**
- master と新ロジックの **数値一致** の実測

---

## 3. リアリティチェック（着手前必須）

renewal を「使う」前に、以下を **1〜2 日で** 検証する。失敗すれば計画見直し。

| # | 検証項目 | 成功条件 | 失敗時の対応 |
|---|---|---|---|
| RC-0 | `composer install --no-progress` 完走、`vendor/` 生成 | autoloader OK、ext-* 不足は補い再実行 | PHP 拡張インストール |
| RC-1 | `docker compose up -d --build` が renewal で完走 | 3 コンテナが healthy | Dockerfile/compose 修正 |
| RC-2 | 全 migration 適用が成功 | `scripts/migrate` 全 SQL が apply、テーブル ~30 個 | 失敗 SQL を ADR-002 と突合し修正 |
| RC-3 | `vendor/bin/phpunit` が緑（または failure 件数把握） | 845 メソッドのうち pass / skip / fail を可視化、Integration 系は `RUCARO_TEST_DB_*` を docker-compose 環境変数経由でセット | 落ちているテストを Issue 化、修正コスト見積 |
| RC-3.1 | `composer phpstan` / `composer psalm` / `composer cs-check` 全 green | baseline 通過 | baseline 出して issue 化 |
| RC-4 | `/ui/login` → `/ui/dashboard` がブラウザで動く | ログインしてダッシュボード表示 | UI 層の実装欠落を Issue 化 |
| RC-5 | LegacyImport CLI で master の小規模データセットを import | journals + journal_entry_lines に 1 期分入る、count 一致 | importer のバグ修正、または renewal 側スキーマ調整 |
| RC-6 | import したデータで TrialBalance 計算 → master の試算表と一致 | 借方合計・貸方合計一致、各科目残高一致 | Golden 不一致箇所を Issue 化（ただし Golden test の機械化コードは現状未実装、新規開発が必要） |
| RC-7 | **実データ全件 (1,613 仕訳) で試算表応答時間を実測** | < 100 ms 程度（renewal 設計から想定） | 想定外に遅ければ §6 R-3 を発動、性能対策（snapshot 等）を Phase D で具体化 |
| RC-8 | **複数会計期データを import → 期首残高一致確認** | Phase A 段階: 主要科目（現金/預金/売掛/買掛/資本）のサンプリングで master の期首残高と renewal の期首残高が一致。Phase B-2 で Golden を機械化した後に「全科目 100% 自動一致」へ昇格 | `ZeroOpeningBalanceRepository` を `PdoOpeningBalanceRepository` に切替 + 期首残高シードを LegacyImport に追加 |
| RC-9 | `/ui/journals/new` で仕訳投稿 → `/ui/journals` 一覧に反映 → `/ui/ledger` `/ui/pl` `/ui/bs` 反映 | 一巡操作 OK、CSRF/Session/Flash 動作 | UI バグを issue 化 |

**Deliverable**: `plan/reality-check-report.md`（OK/NG とギャップリスト）。

**注**: RC-6 の Golden test は **現状コードに無く新規開発が必要**。`tests/Unit/Golden/FinancialStatement/FinancialStatementGoldenTest.php` は新ロジック内部のシナリオ表検証のみ。「master 出力と新ロジック出力の自動 diff」は Phase B-2 で機械化する。

---

## 4. ゴール 5 項目への移行計画

> 略号: M = master、R = renewal

### Phase A: 検証と土台固め（〜2 週間）

**目的**: renewal を本格利用する判断材料を揃える

- A-1. リアリティチェック（§3 RC-0〜RC-9）実施
- A-2. master データの完全 import スクリプトを成熟させる
  - 暗号化列（パスワード等）は ADR-021 §placeholder-password 方針を確認
  - **期首残高シード機構** の追加（`--seed-opening-balances` で master の最古期 BS から逆算、または期締め workflow 設計）
  - **`accountingLogCalcJpn` 依存** の明文化: importer は集計テーブルを「金額の正本」として読む（ADR-021 §3.4）。master retire 時は本テーブルも保管が必要
  - **import 失敗時のロールバック手順** を明記（最低限: `legacy_id_mapping` テーブルと renewal 側 stage 別テーブルを truncate、もしくは renewal DB 全体を import 直前の mysqldump から復元）
  - import 後の row count + sample diff レポートを自動化（`bin/legacy-diff` 新設）
- A-3. **「使われている master 機能」のリスト確定**（§7 Q-1a〜e ヒアリング）
  - 必須コア（推定）: 仕訳入力 / 元帳 / 試算表 / PL / BS / 消費税申告 / 固定資産（要確認）
  - 暗号化列を使う機能（要確認）: 銀行連携 (Banks*)、メール取込 (LogMailJpn)、青色申告書 (BlueSheet)
  - 想定不要: 損益分岐点 / 予算管理 / 部門別 / 複数会社（要確認）
  - 実利用機能だけを Phase C 以降のスコープに残す
- A-3.1. **master Blowfish キー所在確認**（A-3 と並走、Phase A 必須）
  - ADR-003 で「キー所在不明」とされている master の暗号化マスター秘密鍵を実際に探す（環境変数、ファイル、コードハードコード等）
  - 入手可: B-6 で AES-256-GCM 再暗号化計画
  - 入手不可 + 銀行/メール機能を使う: ユーザー再入力フローの設計（パスワード等の手動セットアップ画面）を Phase B-6 に追加
  - 入手不可 + 機能不使用: 該当機能を Phase F で renewal からも撤去（実装済モジュールを retire）
- A-4. renewal 実装の **ゴール別ギャップ表** を作る（PLAN.md・各 ADR と実コードの突合）
  - 特に: `/ui/trial-balance` ルート不在、`FEATURE_*` フラグ不在、`PdoOpeningBalanceRepository` 未配線、Golden 機械化コード不在 を整理
- A-5. **Strangler Fig 立場確定**: ADR-007 を「Deprecated」または「Greenfield + 片方向 import」に書き換えるか、フラグを実装するかを決定（推奨: Deprecated 化、本計画の §5 並行運用方式で代替）
- A-6. **バックアップ運用** を確立: master 日次 mysqldump + 7 日保持 + オフサイト退避（外付け SSD でも可）。renewal も同様
- A-7. `composer.json` の `classmap: ["back/class/else/"]` の扱い: 当面残すなら `phpstan.neon` の `excludePaths` に追加。最終的に Phase E-3 で除去

### Phase B: テスト基盤強化（〜2 週間、A と部分並行）

**目的**: 「テスト書いてから移行」を安全にできる土台を整える

- B-1. PHPUnit を CI と同等で local 実行できるようにする
  - `RUCARO_TEST_DB_*` を docker-compose env または `scripts/test/setup-local-db.sh` でセット
  - skip 件数を 0 に近づける
- B-2. **Golden test の機械化**（新規開発）
  - master DB から固定の仕訳セット（実データの一部 or 合成）を export
  - renewal 側で TrialBalance / PL / BS / 元帳を計算
  - master の対応出力（既存 `back/class/.../*Output.php` の結果）と diff
  - GitHub Actions で nightly 実行
- B-3. PHPUnit Code Coverage（Xdebug or PCOV）を導入、レポートを CI artifact に
- B-4. property-based test（借方 = 貸方 が常に一致）の導入確認
- B-5. Performance test（**実データ 1,613 仕訳** で試算表応答時間を実測）
  - 想定: 数 ms〜数十 ms。性能対策（snapshot 等）はおそらく不要
  - 想定外に遅ければ Phase D で snapshot 戦略を具体化
- B-6. **暗号化列移行設計**（A-3 で銀行連携/メール取込が「使う」と判明した場合のみ）
  - master の Blowfish キー入手可否確認（ADR-003 §キー所在は不明とされている）
  - 入手できなければユーザーに再入力依頼するフロー
  - 入手できれば `LegacyBlowfishDecryptor` で復号 → AES-256-GCM で再暗号化して renewal へ
- B-7. **PDF 出力の比較**（master と renewal の PDF を pixel diff or text 抽出で比較）
  - 完全一致は不要、項目漏れ・数値漏れ検出が目的

### Phase C: 既存 Web UI の検証と仕上げ（〜2〜4 週間）

**目的**: **既に実装済みの** Web UI（Controller 53 / テンプレート 103 / ルート 60+）が業務利用に耐えるかを検証し、不足を埋める

> **重要**: 当初想定の「ゼロから UI を作る」は事実誤認。`/ui/journals` `/ui/ledger` `/ui/pl` `/ui/bs` `/ui/cs` `/ui/fixed-assets` `/ui/budgets` `/ui/cash-plans` `/ui/consumption-tax` `/ui/blue-return` `/ui/ss` `/ui/notes` `/ui/masters/*` 等は既に登録済（`src/Http/WebKernel.php:86-160`）。

- C-1. **既存 UI の網羅監査**（A-4 のギャップ表ベース）
  - WebKernel ルート定義 vs Controller / Template の対応マトリクス
  - 既知不足: **`/ui/trial-balance` ルートが未登録**（実装または不要判定）
  - 各画面の入出力項目を master の同等画面と突合
- C-2. **手動操作試験**（RC-9 を縦方向に拡張）
  - ログイン → 仕訳投稿 → 各帳票表示 → 消費税集計 → PDF 出力 の一巡
  - master と並べて見比べる
- C-3. **E2E テスト**（Playwright）を Critical Path から実装
  - 仕訳投稿 / 試算表表示 / PL/BS 表示 / 消費税集計 / 固定資産登録
  - 完成までは renewal は **見るだけ**、入力は master 側継続
- C-4. **不足機能の実装**（C-1 で見つかった穴）
- C-5. **UI ブラッシュアップ**（Bootstrap テーマ調整、§7 Q-3a 確定後）
  - SPA 化（React 等）は §7 Q-3b で別判断（既存 Smarty 資産を捨てる選択になる）

### Phase D: 集計テーブル廃止と性能チューニング

**目的**: ゴール「リアルタイム集計」「DB 簡素化」の最終形

- D-1. trial_balance_snapshot のキャッシュ戦略を実測ベースで決定
  - 都度計算で性能 OK → snapshot 廃止
  - 性能 NG → snapshot は materialized view 的に運用、journal 更新で invalidate
- D-2. master の集計テーブル（CalcLog 等）に **対応する renewal 機能の不在を確認**
  - 不在ならその master 機能は廃止対象、ユーザー確認後 retire
- D-3. fs_mappings 等の設定テーブルは保持（仕訳から導出できない分類情報）

### Phase E: 並行運用 → 切替

**目的**: master を retire し、renewal を本番に

- E-1. ユーザー運用を一定期間「master + renewal 両方」にする
  - master で記帳 → 日次 or 週次 import → renewal で同じ画面が使えるか確認
  - **renewal で記帳 → master へリバース import は採らない**（双方向同期はリスク高）
- E-2. **切替判定 SLA**（§7 Q-5 で確定）
  - (a) 直近 90 日 master 登録の 100% が renewal でも数値一致して Golden test pass
  - (b) renewal の主要画面（仕訳 CRUD / TB / PL / BS / 消費税）を 1 会計期通して操作試験完了
  - (c) E2E テスト Critical Path 全 green
- E-3. **片方向同期に切替**: master を read-only に、renewal が primary
  - master の最終 mysqldump を **永久保持**
  - `composer.json` の `classmap: ["back/class/else/"]` を除去
- E-4. 一定期間後、master を archive（コードと最終 dump を別ストレージへ）

### Phase F: 機能削減・最終化

**目的**: 使わない機能の renewal 側からの削除（もし renewal に過剰実装があれば）

- F-1. A-3 で「不要」と確定した renewal モジュール（Budget / BEP 等）を retire
  - 既に実装されているが運用されないモジュールは負債
  - 削除によりテスト・保守コストを下げる

---

## 5. 開発フロー（master を使い続けながら）

### 5.1 ブランチと環境

- `master`: 業務で日常使用。docker は accounting_db / accounting_app（port 8080/3306）
- `renewal`: 開発。docker は accounting_renewal_db / accounting_renewal_app（port 9080/3307）
- 既に docker-compose を別名化済 → **2 環境並列起動可能**

### 5.2 master データの取り込み

毎日（または毎週）次を回す:
1. master の DB を `mysqldump`（read-only でも OK）
2. renewal 側 LegacyImport CLI で取り込み
3. Golden compare スクリプトで 主要レポート（試算表等）の差分検出
4. 差分があれば issue 化

### 5.3 renewal で開発した画面の利用

- ユーザーは master を使い続けるが、新画面ができたら renewal でも同じ操作を試す
- **renewal で記帳 → master へ反映** はリスク高（双方向同期は破綻しがち）→ Phase C 中は **見るだけ**、入力は master 側継続を推奨

### 5.4 別アイデア: もし master 並行が辛くなったら

- renewal を「primary」に上げて、**master の仕訳画面だけ legacy として保持**（読み取り専用 + emergency edit）
- ただし Phase C-1（仕訳入力 UI）が安定してから

---

## 6. リスクと対策

| # | リスク | 影響 | 対策 |
|---|---|---|---|
| R-1 | renewal の Phase 1-6 「完走」が実は紙の上だけで動かない | 計画全体破綻 | §3 リアリティチェック RC-0〜RC-9 で早期検知。NG なら「ギャップ埋め先行」に切替 |
| R-2 | master と renewal の数値が一致しない（複式簿記の解釈差） | 信頼喪失 | Golden test を Phase B-2 で機械化。差分を ADR で文書化（仕様明文化） |
| R-3 | Web UI の不足機能が多く Phase C 工数超過 | リリース遅延 | C-1 監査でスコープ確定後に再見積。実装よりも E2E テスト工数の方が大きいと予想 |
| R-4 | 双方向同期 (master ⇄ renewal) で整合性破綻 | データ破壊 | §5.3 のとおり片方向のみ。renewal 入力は Phase C 完了まで凍結 |
| R-5 | renewal の過剰実装（Budget/BEP/BlueReturn 等）の保守コスト | 認知負荷 | A-3 で要否確定、Phase F で不要部分を削除 |
| R-6 | テストカバレッジが 845 メソッドあるが品質が低い | リファクタ時に false confidence | B-3 でカバレッジ計測、B-2 で Golden test を「正しさの証拠」とする |
| R-7 | renewal docs/ADR との実装乖離 | 方針逸脱 | A-4 のギャップ表で可視化、ADR-007 等は Deprecated 化 or 実装に合わせ更新 |
| R-8 | LegacyImport が暗号化列を扱えずパスワード初期化 | ユーザー混乱 | Phase A-2 で「パスワード再設定」フローを明文化、UI に表示 |
| R-9 | データ消失（import 失敗、SQL 誤実行、ディスク故障） | 致命的 | Phase A-6 で日次バックアップ + 切替時 dump 永久保持 |
| R-10 | master の暗号化列（Banks/Mail/BlueSheet）を使う運用が判明 | renewal が完全置換できない | A-3 ヒアリングで早期検出、必要なら Phase B-6 で master Blowfish キー入手と再暗号化を設計 |
| R-11 | 複数会計期 import で期首残高がゼロになり数値破綻 | BS / Ledger の累計表示崩壊 | RC-8 で検出、`ZeroOpeningBalanceRepository` を `PdoOpeningBalanceRepository` に切替 + 期首残高シード機構を A-2 で実装 |
| R-12 | `accountingLogCalcJpn`（master 集計テーブル）を再 import 用に保管しないと再現不能 | 切替後の不可逆性 | Phase E-3 の最終 dump に必ず含める。ADR-021 §3.4 を参照 |

---

## 7. ユーザー回答（2026-05-10 確定）

| # | 論点 | 回答 | 計画への影響 |
|---|---|---|---|
| Q-1a | 銀行連携 (Banks*) 実運用 | **不使用、ただし将来実装したい** | renewal の Banks 関連は撤去せず保持。現状は Phase F の対象外、Phase G（将来）として scope 外で残す |
| Q-1b | メール取込 (LogMailJpn) 実運用 | **不使用、今後も不要** | renewal にメール取込実装があれば Phase F で撤去。LegacyImport も対象外 |
| Q-1c | 青色申告書 (BlueSheet) | **現在不使用、今後使う想定** | renewal の BlueReturn モジュールは保持。Phase C 監査対象に含める |
| Q-1d | 月次/年次バッチ | **決算締めのみ** | 期締めワークフローは renewal で必須実装（H-3 / Phase A-2 期首残高シード機構と直結） |
| Q-1e | master 過去データ規模 | **10 年超、毎年約 1,000 件 → 累計 10,000+ 仕訳** | RC-7 の性能実測のサイズ感はこれ。10 万にも届かないので集計テーブル廃止後の都度計算で問題ない見込み |
| Q-2 | renewal 既実装で運用しないモジュール | **Budget は不要** | Phase F で Budget 関連 (`src/Domain/Budget/`, `Application/Budget/`, `Http/Controller/Ui/Budget*`, `storage/templates/budgets/`, migration, tests) を削除。BEP/SS/多期間 FS の要否は別途確認（暫定保持） |
| Q-3a/b | UI 戦略 | **当面 Bootstrap 5 SSR で OK、最終的に React 化** | Phase C は既存 Smarty SSR の検証・仕上げで進行。Phase G（将来）として React 化計画を別 ADR で。それまでは API 層の安定が先 |
| Q-4 | 性能希望 | **業務 1 秒以内、ただし当面成り行き** | RC-7 で実測のみ、対策は実測 NG 時のみ Phase D で着手 |
| Q-5 | master 廃止判定 | **(a) AND (b) AND (c) 厳格 AND** | Phase E-2 の SLA を AND で確定 |
| Q-6 | ADR-007 Strangler Fig 立場 | （未明示、計画書推奨どおり） | デフォルトで **Deprecated 化** 方針で進める。`FEATURE_*` フラグは実装しない |
| Q-7 | マルチテナント運用 | **1 ユーザーで複数会社 + 個人事業を扱う** | Entity 切替 / session entity_id は本番要件。renewal の Entity モデルは既に対応している（`/ui/entity/switch` ルート存在）が、マルチエンティティ前提でテストする必要あり |

### 7.1 確定事項を踏まえたスコープ調整

- Phase F の **撤去対象を確定**: Budget、メール取込（あれば）
- Phase F で **保持確定**: BlueReturn、Banks、FixedAsset、ConsumptionTax、CashPlan
- Phase G（将来）として新設: React 化、銀行連携実装
- Phase A-3 のヒアリング項目は概ね回答済み。**残るは renewal 側の BEP / SS / 多期間 FS / NotesFS の要否確認**（暫定保持）

---

## 8. 直近のアクション（次に手を付けるもの）

1. **§3 リアリティチェックを実施**（RC-1〜RC-6 を順次） → `plan/reality-check-report.md` 作成
2. リアリティチェック結果と本計画書を **ユーザーへ提示** → §7 論点に回答もらう
3. 回答に基づき Phase A の詳細スコープを確定

---

## 9. ドキュメントと実装の同期（Phase A 内で実施）

renewal の docs/ と実装の乖離を解消する。

- `docs/PLAN.md` は Phase 6 までの計画書 → Phase 7 以降を別ファイル（例: `docs/PLAN-phase7.md`）に切り出すか追記
- `docs/adr/ADR-007-strangler-fig-migration.md`: Status を `Deprecated` または `Superseded by 並行運用方式` に。`FEATURE_*` フラグを実装しないなら明記
- `docs/adr/ADR-022-minimal-web-ui.md`: 「最小構成」と謳いつつ実装は 53 ctrl / 103 tpl まで進んでいる。実態に合わせ更新
- `docs/adr/ADR-021-legacy-data-migration.md`: 期首残高シード機構を追記、`accountingLogCalcJpn` 永久保管要件を明記
- **「10 万仕訳 < 2 秒」記述の更新**: `docs/PLAN.md:163` / `docs/adr/ADR-002:559,607,668` / `docs/adr/ADR-007:456` の性能目標を実データ規模（1,613 仕訳）に基づく現実的な値に書き換え（または「将来 10 倍に成長した場合の目標」と注記）
- 現状スナップショット `docs/STATE-2026-05.md` を残す（renewal 実装規模の最新値）

---

## 10. 補遺: 参照ドキュメント

- `plan/research-renewal.md` (renewal 構造調査の詳細)
- `plan/research-master.md` (master 機能/DB/廃止候補の詳細)
- `docs/PLAN.md` (renewal 既存ロードマップ)
- `docs/adr/ADR-002-database-schema.md` (DB 設計)
- `docs/adr/ADR-005-layered-architecture.md` (層構成)
- `docs/adr/ADR-007-strangler-fig-migration.md` (移行戦略)
- `docs/adr/ADR-021-legacy-data-migration.md` (master データ取り込み)
- `docs/adr/ADR-022-minimal-web-ui.md` (UI 方針)
