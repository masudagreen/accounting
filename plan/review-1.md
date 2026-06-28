# 移行計画レビュー #1

レビュー対象: `plan/MIGRATION_PLAN.md` (2026-05-10)
レビュアー: 批判的レビュー subagent
作業ディレクトリ: `/Users/np_202212_11/projects/accounting` (renewal)
master worktree: `/Users/np_202212_11/projects/accounting-master`

---

## 総評

「continue（条件付き）」という結論は概ね妥当だが、**計画書は renewal の到達度を過小評価している**（特に Web UI）。一方で **Strangler Fig の中核装置である `FEATURE_*` フラグは実コードに存在せず ADR-007 と完全乖離**、ADR-021 の正本データセットは **journal 1,613 件しかない** のに性能目標は「10 万仕訳 < 2 秒」のまま、`ZeroOpeningBalanceRepository` がデフォルト配線で **複数年データ取り込み時に必ず壊れる**、という致命的な穴がある。リアリティチェックの範囲もこれらをカバーしていない。「partial-reuse / discard 検討」は §0 で 2 行触れた程度で実質皆無。論点提示は良いが、**事実誤認に基づく Phase C 設計（仕訳 UI から作る）は早急に修正すべき**。

---

## 1. 結論「continue (条件付き)」の妥当性

### 同意（ただし理由は計画書と少し違う）

- continue が妥当である根拠 (5) 「やり直しコスト」は強い説得力。ただし計画書 §0 が掲げる規模（src 579/test 845）以上に **業務 UI まで広範囲に揃っている**ため、捨てるコストは更に大きい (§2-d 検証参照)。
- discard が選ばれない理由は妥当。partial-reuse は「層間が密」を理由に却下されているが、**ADR-005/006 で意図的に Ports & Adapters 化されている**ため実は粒度の細かい流用は容易なはず。「実質的に再設計と同コスト」は粗い。
- 代替案として「**partial-discard**」（renewal の使わない領域だけ捨てる、≒ 計画書 Phase F の前倒し）を §2.1 段階で検討するべき。Budget / BEP / BlueReturn / 多期間 FS は 800+ 行の実装が乗っており、運用で使わないなら計画 Phase A-3 の確定**前**に着手保留扱いにできる。

### 不十分な検討

- **「continue」の対義として「**継続するが、UI 開発はもうやらない**」という選択肢が提示されていない**。実際には計画書が想定する「Phase C: コア業務 UI のモダン化（〜1〜2 ヶ月）」は **大半が既に実装済み**（§2-d 参照）であり、計画書の Phase C は工数見積も含めてゼロベースで再考が必要。

---

## 2. 事実検証結果（a〜g）

### (a) `vendor/` 不在 / `composer install` 必要 → ✓ 一致

```
$ ls vendor
ls: vendor: No such file or directory
$ ls composer.json composer.lock
composer.json composer.lock
```

- 出典: 作業ディレクトリ root
- 計画書 §3 RC-1 は `docker compose up` の動作を見るが、**`composer install` を別ステップとして明示していない**のは詰めが甘い。RC に「RC-0: `composer install` 完走 + `vendor/` 生成」を追加すべき。`composer.json:35-37` に `"classmap": ["back/class/else/"]` が残っているため master クラスを autoload してしまい、PHPStan/Psalm が予想外のエラーを吐く可能性も。

### (b) `scripts/migrate/` の SQL は本当に実装されているか → ✓ 一致

- 全 19 個（`0000`〜`0018` + seed 数本、計 43 ファイル含む `down`）の SQL を確認 (`scripts/migrate/`)。
- 中身の例:
  - `scripts/migrate/0003_create_journal_tables.sql:1-106`: `journal_entries` / `journal_entry_lines`、ULID + DECIMAL(18,4) + TIMESTAMP(6) + FK + CHECK 制約の本格実装。
  - `scripts/migrate/0011_fixed_assets.sql:1-103`: `fixed_asset_categories` / `fixed_assets` / `fixed_asset_depreciation_schedules` の DDL。
  - `scripts/migrate/0010_opening_balances.sql:11-15`: 期首残高テーブルは存在するが、コメントで「**`ZeroOpeningBalanceRepository` is wired by default**」と明記。期締めワークフローは未実装（→ §3 致命指摘 H-3）。
- 計画書 §1.1 の「migration 18 個」は事実。

### (c) `tests/Unit/Golden/` の Golden test 実装 → 部分的 △

- `tests/Unit/Golden/FinancialStatement/FinancialStatementGoldenTest.php:1-277` のみ存在（1 ファイル）。`testPlScenarios` データプロバイダ式テスト + `testContraAssetIsSubtractedAtScenarioTen`。
- **これは旧 master と数値突合する Golden ではなく、新ロジック内部の入出力一致 (シナリオ表) を見るだけ**。計画書 §3 RC-6 が要求する「import したデータで TrialBalance 計算 → master 試算表と一致」を担う Golden は **存在しない**。
- 計画書 B-2 で「Golden test の機械化」を Phase B 入ってから設計するのは順序として遅い。**RC-6 を成立させるには新規にコードが必要**であることを RC 段階で明示すべき。

### (d) Phase 7-1 Web UI は本当にログイン+ダッシュボードしか無いか → ✗ **重大な誤認**

renewal 計画書 §1.2 ・本計画書 §1.2 表「(1) モダン UI: △ 基盤のみ。ログイン/ダッシュボードのみ実装」は**完全に誤り**。

- `src/Http/Controller/Ui/` 配下に **53 ファイル** ある。出典:
  - Journal (一覧/編集/作成/削除/詳細): `src/Http/Controller/Ui/Journal/Journal{List,Edit,New,Delete,Show}Controller.php`
  - Report (PL/BS/CS/Multi-FS/Ledger/SS/BEP/BlueReturn/Notes): `src/Http/Controller/Ui/Report/{Pl,Bs,Cs,MultiPeriodFs,Ledger,Ss,Bep,BlueReturn,NotesList}ViewController.php`
  - Master (科目/補助科目/会計期/会社): `src/Http/Controller/Ui/Master/*.php`
  - FixedAsset (一覧/作成/編集/詳細/除却/減価): 6 ファイル
  - Budget / CashPlan / ConsumptionTax / SsAdjustment 各種
- テンプレート 50 個 (`storage/templates/ui/` 配下)。
  - `journals/{list,form,show,delete-confirm}.html.tpl`、`{pl,bs,cs}/view.html.tpl`、`fs_multi/view.html.tpl`、`fixed_assets/{list,form,show}.html.tpl`、`budgets/*.tpl`、`consumption_tax/*.tpl`、`masters/**` 等が揃っている。
- ルート定義: `src/Http/WebKernel.php:86-160` 以降に `/ui/journals`, `/ui/ledger`, `/ui/pl`, `/ui/bs`, `/ui/cs`, `/ui/fs/multi`, `/ui/bep`, `/ui/blue-return`, `/ui/ss`, `/ui/notes`, `/ui/masters/{account-titles,sub-account-titles,entities,fiscal-terms}/...`, `/ui/fixed-assets/...`, `/ui/budgets/...`, `/ui/cash-plans/...`, `/ui/consumption-tax/...` が登録済み。
- → **Phase C「コア業務 UI のモダン化」の前提が崩れている**。Phase C は「未実装の何かを作る」ではなく「**既存実装の動作確認 + 不足の埋め合わせ + デザイン仕上げ**」が正しいスコープ。1〜2 ヶ月見積は過剰の可能性が高い一方、E2E テストはほぼゼロから書く必要があり、その工数は別建てで残る。

### (e) `src/Infrastructure/Import/LegacyImport/` Importer 実装 → ✓ 実装あり、ただし設計が要注意

- 14 ファイル実装。`LegacyJournalImporter.php` は 242 行、`LegacyToV2Command.php` は 212 行。Symfony Console の `legacy:import` コマンドを `--source-db` / `--target-db` / `--stage=...` / `--dry-run|--apply` / `--placeholder-password` で起動可能。
- **要注意**: `LegacyJournalImporter` は **`accountingLogCalcJpn` (旧 集計テーブル) を金額の正本として読む** (`src/Infrastructure/Import/LegacyImport/LegacyJournalImporter.php:18-30`)。理由は `accountingLog.arrComma*` がコードしか持たないため (ADR-021 §3.4)。
- 含意: master を retire するときに **`accountingLogCalcJpn` も同時に持っていないと再 import できない**。集計テーブル廃止というユーザーゴールの「移行段階での扱い」が ADR-021 §2.2 で「**source として使うが import しない**」と明示されているのは一貫しているが、**計画書はこの依存関係を一切触れていない**。
- ADR-021 §2.1 表で本データセットは **journal_entries 1,613 / lines ~3,700 / fixed_assets 0** と明示。後述の性能議論に直結。

### (f) Phase 1〜6 の各 Phase に該当するコードが実在するか → ✓ 概ね実在（spot check）

- Phase 1 基盤: `composer.json` (PSR-4), `phpunit.xml.dist`, `.github/workflows/ci.yml`, `docker-compose.yml` 確認済。
- Phase 2 現代化: `src/Infrastructure/Crypto/AesGcmCipher.php:23-50` AES-256-GCM + HKDF-SHA256 実装。`src/Infrastructure/Crypto/LegacyBlowfishDecryptor.php` も存在。
- Phase 3 REST API: `public/api/v1/index.php`, `src/Http/ApiKernel.php` 存在、Integration test (`tests/Integration/Api/V1/AuthLoginTest.php` 等) あり (RUCARO_TEST_DB_* 未設定で skip)。
- Phase 4 ドメイン: `src/Domain/Journal/{Journal,JournalLine,JournalStatus}.php`, `src/Domain/Journal/Service/{JournalBalancer,JournalReverser}.php` 確認。`tests/Unit/Domain/Journal/Service/JournalBalancerTest.php` 存在。
- Phase 5 承認: `src/Domain/Approval/`、`docs/adr/ADR-008-approval-workflow.md` 存在。
- Phase 6 移植: `src/Domain/{ConsumptionTax,FixedAsset,CashPlan,BreakEvenPoint,Budget,BlueReturn,StatementOfChangesInEquity,FinancialStatementNotes}/` を確認。`src/Domain/FixedAsset/Rate/DecliningBalanceRateTable.php` あり（償却方式 9 種は数えていないが基盤あり）。
- → 「Phase 1-6 完走」自己申告は **コード裏付けあり**。ただし「動作する」検証は別問題（§4 致命指摘 R-1 のとおり）。

### (g) `docs/PLAN.md` が現実に合っているか → 部分的 △

- `docs/PLAN.md:1-7` に「Phase 6 完了: 2026-04-22」「全 6 フェーズ完走」と書かれ、`Phase 7` は計画上は触れられているが、Phase 7-1 は ADR-022 に分離されている（`docs/adr/ADR-022-minimal-web-ui.md`）。
- ただし `docs/PLAN.md` は **Phase 6 までの計画書** であり、現在進行中の Web UI 拡張 (上述 d) を反映していない。**implementation > documentation のドリフトが起きている**。
- ADR-007 §6 (`docs/adr/ADR-007-strangler-fig-migration.md:80,124-303`) は `FEATURE_JOURNAL_V2` 等 14 個のフラグを謳うが、`grep -rnE "FEATURE_" src/` は **0 件**。`.env.example` にも該当 env なし。**Strangler Fig の中核装置がドキュメント上にしか存在しない** (→ §3 致命指摘 H-1)。

---

## 3. 重大な指摘 (must-fix)

### [HIGH] H-1: Strangler Fig の `FEATURE_*` フラグは実装されていない

- 計画書 §2.1 (3) は「Feature flag で旧/新切替可能 (FEATURE_JOURNAL_V2 等 14 個)」と謳い、これを continue 判定の根拠にしている。
- 実態: `grep -rnE "FEATURE_(JOURNAL|TRIAL|LEDGER|FS|FIXED|BUDGET|BEP|CONSUMPTION|ENTITY|BANK|MAIL)_V2" src/` 0 件、`.env.example` にも未定義。ADR-007 はドキュメント上の構想のみ。
- 影響: **「並行運用しながら Wave ごとに切り替える」という移行戦略の前提が崩れる**。Phase E「並行運用→切替」は feature flag を前提にしているが、実コードは旧/新を切り替える機構を持っていない。
- 提案:
  - 計画書 §2 で「**Strangler Fig は ADR 上の設計であり、実装は未着手**」と明記する。
  - Phase A に「A-5: Feature flag 実装の要否確定」を追加。renewal が **新 DB / 新エンドポイント / 新 UI 完結**で動くなら master は完全に隣接環境で稼働するだけで、feature flag は不要かもしれない。
  - 不要なら ADR-007 を deprecate（renewal は実は **Strangler Fig ではなく Greenfield + データ片方向取り込み** だった、と整理する）。

### [HIGH] H-2: 性能目標「10 万仕訳 < 2 秒」のスケール感がユーザー実データと乖離

- 出典: `docs/PLAN.md:163`, `docs/adr/ADR-007-strangler-fig-migration.md:456`, `docs/adr/ADR-002-database-schema.md:559,607,668`。すべてに 10 万仕訳閾値が登場。
- 一方、ADR-021 §2.1 (`docs/adr/ADR-021-legacy-data-migration.md:30`) では **本データセット = 1,613 仕訳 / ~3,700 行**。**100 倍超のギャップ**。
- 計画書 §6 R-3 は「実装コスト超過」のみリスク化、§7 Q-4 で「Performance はどこまで許容?」とユーザーに質問しているが、**実データ規模で「都度集計が遅すぎる」リスクはほぼゼロ**であることに気付いていない。
- 提案:
  - 計画書 §3 RC-7 に「**実データ全件で試算表応答時間を実測**（数 ms 〜 数十 ms 想定、実測値を記録）」を追加。
  - 性能対策（trial_balance_snapshot キャッシュ、materialized view 検討）はおそらく **不要**。Phase D-1 を「実測 → 90% の確率で snapshot 廃止」に書き換える。
  - 注: 多年運用で 5〜10 年累積した場合は別途見積もるが、それでも 10 万件には届きにくい。

### [HIGH] H-3: `ZeroOpeningBalanceRepository` がデフォルト配線、複数年 import で破綻

- `src/Support/Container/ContainerBootstrap.php:415` で `OpeningBalanceRepositoryInterface` の実装は `ZeroOpeningBalanceRepository` がデフォルト。
- `src/Infrastructure/Ledger/PdoOpeningBalanceRepository.php` は実装済みだが配線されていない。
- `scripts/migrate/0010_opening_balances.sql:11-15` のコメント: 「**Until a close-fiscal-term workflow is introduced (later Wave) this table stays empty**」。
- 期首残高ゼロでは、**複数会計期にまたがる import 時に総勘定元帳の繰越残高が壊れる**（前期繰越が常に 0）。試算表は当期発生額だけを見るので破綻は目立たないが、Ledger / BS の累計表示が狂う。
- ユーザーが master で 10 年運用してきた場合、renewal に取り込んだ瞬間に「master では 1,000 万円ある現金残高が renewal では当期入出金しか反映されない」現象が起きる。
- 提案:
  - 計画書 §3 RC-8 に「**複数会計期データを import → 期首残高一致確認**」を追加。
  - Phase A-2 のスコープに「**期締め (close-fiscal-term) または初期期首残高シードのワークフロー設計**」を必須として明記。
  - 簡易策: ADR-021 importer に `--seed-opening-balances` オプションを追加し、master の最古期 BS から初期期首残高を逆算する。

### [HIGH] H-4: Web UI 状態の事実誤認に基づく Phase C 計画

- §2-d で示したとおり Phase C C-1〜C-6 は **大半が既に実装済み**。
- 計画書 §4 Phase C は「1〜2 ヶ月」を見積もり「Phase C-1 が完成しないと renewal を日常運用できない」と言っているが、`/ui/journals/new` 等は既存。
- 真にやるべきは:
  - (i) **動作確認** (RC-4 を /ui/dashboard 止まりではなく `/ui/journals/new` 〜 仕訳投稿 〜 `/ui/trial-balance` まで通す)
  - (ii) **既存 UI の不足機能の埋め合わせ** (例: `/ui/trial-balance` ルートが本当にあるか? WebKernel.php 確認では `/ui/ledger` `/ui/pl` `/ui/bs` `/ui/cs` 系はあるが **`/ui/trial-balance` というパスは Web UI には未登録** の可能性 — 別途確認要)
  - (iii) **E2E テストをゼロから書く** (Playwright)
  - (iv) **デザイン (Bootstrap デフォルトのまま) のブラッシュアップ** — ユーザーの「モダン UI」期待値次第
- 提案:
  - Phase C を「**A-4 でギャップ表を作ってから着手**」に並べ替え。
  - C を「C-existing-audit (既存 UI の網羅監査) → C-fill-gaps (穴埋め) → C-e2e (テスト) → C-polish (UI 仕上げ)」に分割。

### [HIGH] H-5: master の暗号化列 (Banks/Mail/BlueSheet) を「対象外」とした影響評価がない

- ADR-021 §2.2 (`docs/adr/ADR-021-legacy-data-migration.md:44-46`) で「Mail/bank ingestion pipelines」「Encrypted blobs」が import 対象外と明記。
- これは renewal が master を完全置換できないことを意味する。ユーザーがネット銀行連携 (`accountingLogBanksAccount.blobDetail`) や IMAP メール取込 (`accountingLogMailJpn.strPassword`) を使っているなら、renewal に切替えた瞬間その機能は消える。
- 計画書 §4 Phase A-3「使われている master 機能のリスト確定」「想定不要: 銀行連携」と書いているが、「不要」は推測。**ヒアリング項目として §7 Q-1 に明示が必要**。
- 提案:
  - §7 Q-1 を「Q-1a: 銀行連携 (Banks*)、Q-1b: メール取込 (LogMailJpn)、Q-1c: 青色申告書 (BlueSheet) を実運用しているか?」に分解。
  - 使っているなら **Phase B-6 として「暗号化列の master → renewal 移行」を別 ADR で設計** が必須。LegacyBlowfishDecryptor は実装済みだが、master の暗号化キー (Crypte の master secret) は ADR-003 §不明 で「不明」とされている。**キー入手可否の確認が最優先**。

---

## 4. 重要な指摘 (should-fix)

### [MED] M-1: リアリティチェックに「 `composer install`、PHPStan、Psalm、CS-Fixer の green 確認」が抜けている

- 計画書 §3 RC-1〜RC-6 には DB と Web UI の動作確認のみ。CI で守られているはずの静的解析が **手元でも green になるか** の確認が無い。
- vendor/ 不在環境で開始するため、`composer install` が失敗する可能性 (PHP 拡張要件 ext-mbstring 等) もある。
- 提案: RC-0 を「`composer install --no-progress` 完走」、RC-3.1 を「`composer phpstan` / `composer psalm` / `composer cs-check` 全 green」に分離追記。

### [MED] M-2: テスト「845 ケース」の **DB 必要分が skip されているだけ** であることが明示されていない

- `tests/Integration/**/*.php` は `RUCARO_TEST_DB_USER` 未設定で markTestSkipped (`tests/Integration/Application/Approval/PdoApprovalTokenRepositoryTest.php:42` ほか多数)。
- 計画書 §1.1 表「テスト 186 ファイル / 845 メソッド」は事実だが、**「local で何件 skip されるか」は別**。CI では service container で MariaDB を立てている (`.github/workflows/ci.yml`) はずだが、ローカルで same を再現するには env 設定が必要。
- 提案: B-1「PHPUnit を CI と同等で local 実行できるようにする」の中身として、**`scripts/test/setup-local-db.sh` のような起動スクリプト** または `RUCARO_TEST_DB_*` を docker-compose env 経由でセットする手順を明記。

### [MED] M-3: master を「日常業務で稼働中」のまま renewal を開発する場合の **データ同期方向** の意思決定が弱い

- 計画書 §5.3 で「双方向同期は破綻しがち」「**見るだけ**、入力は master 側継続」と書いているが、**いつまでこの片方向状態を続けるかの基準** が無い。
- Phase E-1 で「両方使う」、E-2 で「renewal が primary」とフェーズ移行するが、**移行判定基準** が「無事故」程度。
- 提案:
  - 切替判定基準 SLA の数値化:
    - (a) 直近 90 日 master 登録の 100% が renewal でも数値一致して Golden test を pass
    - (b) renewal の主要画面 (仕訳 CRUD、TB、PL、BS、消費税) を 1 会計期通して操作試験完了
    - (c) E2E テスト Critical Path 全 green
  - これらを §7 Q-5 の選択肢として埋め込む。

### [MED] M-4: バックアップ/ロールバック戦略が無い

- 計画書全体に「mysqldump」「snapshot」「rollback」の文字が無い。
- master を read-only にするタイミング、renewal DB の自動バックアップ運用、import 失敗時のロールバック (`legacy_id_mapping` を truncate するだけで十分か?) が未定義。
- 提案:
  - §6 R-9 に「データ消失リスク」を追加。
  - Phase A-6 として「日次 mysqldump + 7 日保持 + S3 等オフサイト退避（ローカル運用なら外付け SSD）」を明示。
  - 切替時には master の最終 dump を **永久保持** することを §5 に追記。

### [MED] M-5: `composer.json` の `classmap: ["back/class/else/"]` で master クラスが renewal に autoload される

- `composer.json:35-37` で master 側のレガシーコード一式を classmap として登録している。
- これは Phase 1.3 で「旧アプリ整合性のため温存」(`docs/PLAN.md:88-90`) として残されているが、**新アプリの PHPStan/Psalm 解析範囲にも入る**ので、誤って `Code_Else_Plugin_Accounting_Jpn_*` を `use` する可能性がある。
- ADR-005 の「Domain は他層に依存しない」を grep ベースで検証 (`docs/adr/ADR-007*` 参照) しているとのことだが、**legacy への混入は別ルール**で検出する必要あり。
- 提案: `phpstan.neon` の `excludePaths` に `back/class/` を追加することを Phase A-7 として推奨。**または** 本格的に renewal を primary にしたタイミングで `classmap` を外す Phase E-3 を追加。

### [MED] M-6: 「Smarty SSR を継続する」決定の再検証が欠落

- 計画書 §7 Q-3 で「フロントは Smarty SSR 継続? それとも将来 React/Vue 等に置換?」と問うているが、**ユーザーが「モダン UI」と言ったときの期待値の確認が不足**。
- ADR-022 は SSR + Bootstrap 5 で確定、実装も既に 50 templates 投入済。今ここで React に乗り換えるとは Web UI を **再度** 作り直すことになる (§2-d の既存実装が無駄になる)。
- 提案:
  - Q-3 を「Q-3a: Bootstrap 5 のままで OK か? Q-3b: 既存 Smarty テンプレートを破棄して SPA 化するなら工数 N か月の追加投資が必要、それを許容するか?」に分割。
  - **デフォルトは SSR 継続**（既存資産活用）として明記し、ユーザーが NO と言わない限り Phase C-polish の中で Bootstrap テーマ調整に留める。

---

## 5. 検討推奨 (nice-to-have)

### [LOW] L-1: ADR の deprecate / 更新ポリシーが無い

- 22 本の ADR があるが、ADR-007 の Strangler Fig が実装に反映されていない (H-1) ように、ADR と実装の乖離が放置されている。
- 提案: Phase A-4 のギャップ表に「**ADR と実装の差分**」セクションを追加。乖離している ADR は (a) 実装に合わせて更新 (b) Status を Superseded に変更 (c) 削除 のいずれかを明記。

### [LOW] L-2: PDF 生成の master ↔ renewal 比較戦略が無い

- `src/Infrastructure/Ledger/DompdfLedgerGenerator.php` 等 dompdf ベースの PDF 生成が renewal にある。Domain Port は 7 個 (`Budget*PdfGeneratorInterface`, `BlueReturn*`, `BreakEvenPoint*`, `StatementOfChangesInEquity*`, `FsNotes*`, `CashPlan*`)。
- 計画書 B-2 Golden test は数値の比較のみで、**PDF の視覚比較やレイアウト一致確認**は触れられていない。
- 提案: Phase B-7 として「**master と renewal の PDF を pixel diff (ImageMagick `compare`) または PDF text extract で比較**」を追加。完全一致は不要だが、項目漏れ・数値漏れの検出は重要。

### [LOW] L-3: 暗号化キーローテーション・運用が ADR-003 にあるが renewal 計画書に含まれていない

- `APP_ENCRYPTION_KEY` (`.env.example:30`) のローテーション手順、versioned cipher (`src/Infrastructure/Crypto/VersionedCipher.php`) の運用手順が計画書スコープ外。
- 提案: ローカル運用なので緊急性は低いが、Phase F-2 として「鍵管理ドキュメント整備」を追加。

### [LOW] L-4: 月次・年次バッチ処理の有無の整理

- master 側に cron 系バッチがあるか不明 (`bin/cowork`, `bin/smoke-*` は renewal に存在)。
- 月次決算の締め処理 (期首残高シード、消費税申告期間切替、固定資産減価償却の月次計上) を **手動か自動か** の整理が無い。
- 提案: Phase A-3 のヒアリングに「Q-1d: 月次/年次の手動バッチ作業はあるか?」を追加。

---

## 6. 計画書への具体的な追記/修正提案

### §0 エグゼクティブサマリ

```diff
- 棄却した代替案:
- - **discard（破棄してゼロから作り直し）**: 既存投資（特に DB 設計、ドメインモデリング、移植済み 10 モジュール）を失うコストが大きい
- - **partial-reuse（一部だけ流用）**: renewal はレイヤー間が密に設計されているため、部分流用は実質的に再設計と同コスト
+ 棄却した代替案:
+ - **discard**: src 579 + UI Controller 53 + テンプレート 50 + テスト 845 + ADR 22 本を捨てるコストが大きい
+ - **partial-reuse (master を本体に新ロジックだけ移植)**: master は手続き的継承、テストゼロ、UI 旧式で
+   再生不能。partial-reuse の選択肢としてはあり得ない
+ - **partial-discard (renewal の使わない領域だけ捨てる)**: Budget / BEP / BlueReturn 等の運用しないモジュー
+   ル削除は Phase F として残す。これは continue の中の枝
```

### §1.2 ゴール整合性

```diff
- | (1) モダン UI | prototype.js（保守不能） | △ 基盤のみ。ログイン/ダッシュボードのみ実装。仕訳/帳票画面は未実装 |
+ | (1) モダン UI | prototype.js（保守不能） | ○ Web UI Controller 53 個、テンプレート 50 個実装済 (journals/ledger/pl/bs/cs/masters/fixed-assets/budgets/cash-plans/consumption-tax/blue-return)。動作検証と E2E テストは未実施 |
```

### §2.1 continue の根拠 (3)

```diff
- 3. **段階移行戦略が明示済み**（ADR-007 strangler fig + ADR-021 legacy import）
-    - Feature flag で旧/新切替可能（FEATURE_JOURNAL_V2 等 14 個）
+ 3. **段階移行戦略**（ADR-021 legacy import が中心）
+    - Strangler Fig (ADR-007) は **ADR 上の設計のみで実装は未着手**。`grep -rnE "FEATURE_" src/` 0 件
+    - 現在の renewal は実質「**新 DB / 新エンドポイント / 新 UI で完結**」する Greenfield
+    - master との切替は片方向 import + 並行運用 (§5) で行う
```

### §3 リアリティチェック表に追加行

```diff
+ | RC-0 | `composer install` 完走、`vendor/` 生成 | autoloader OK | ext-* 不足を補い再実行 |
+ | RC-3.1 | `composer phpstan` / `composer psalm` / `composer cs-check` 全 green | 報告緑 | baseline 出して issue 化 |
+ | RC-7 | **実データ全件で試算表応答時間を実測** | < 100 ms 程度 (1,613 仕訳) | 想定外に遅ければ §6 R-3 を発動 |
+ | RC-8 | **複数会計期データ import → 期首残高一致確認** | master の BS と renewal の BS で開始残高が一致 | ZeroOpeningBalanceRepository を PdoOpeningBalanceRepository に切替 + 期首残高シード |
+ | RC-9 | `/ui/journals/new` で仕訳投稿 → `/ui/journals` 一覧に反映、`/ui/ledger` 反映 | 一巡操作 OK | UI バグを issue 化 |
```

### §4 Phase C を全面改訂

```diff
- ### Phase C: コア業務 UI のモダン化（〜1〜2 ヶ月）
-
- **目的**: 「使っている機能」を renewal Web UI で完結させる
-
- 優先順位（A-3 で確定したコア機能順）:
- - C-1. 仕訳入力・編集・検索画面（`/ui/journals`）
-   - ボトルネック。完全に動かないと renewal を日常運用できない
- ...
+ ### Phase C: 既存 Web UI の検証と仕上げ（〜2〜4 週間）
+
+ **目的**: **既に実装済みの** Web UI が業務利用に耐えるかを検証し、不足を埋める
+
+ - C-1. **既存 UI の網羅監査** (A-4 のギャップ表ベース)
+   - WebKernel ルート定義 vs Controller / Template の対応マトリクス
+   - 不足ルート (例: `/ui/trial-balance` 等) の特定
+ - C-2. **手動操作試験** (RC-9 を縦方向に拡張)
+ - C-3. **E2E テスト** (Playwright) - Critical Path のみ最初に
+ - C-4. **不足機能の実装** (C-1 で見つかった穴)
+ - C-5. **UI ブラッシュアップ** (Bootstrap テーマ調整、Q-3a 確定後)
```

### §6 リスク追加

```diff
+ | R-9 | データ消失（import 失敗、SQL 誤実行、ディスク故障） | 致命的 | Phase A-6 で日次バックアップ + 切替時 dump 永久保持 |
+ | R-10 | master の暗号化列 (Banks/Mail/BlueSheet) を使う運用が判明 | renewal が完全置換できない | A-3 ヒアリングで早期検出、必要なら別 ADR で master Blowfish キー入手と再暗号化を設計 |
+ | R-11 | 複数会計期 import で期首残高がゼロになり数値破綻 | BS / Ledger の累計表示崩壊 | RC-8 で検出、ZeroOpeningBalanceRepository を切替 |
```

### §7 ユーザー確認事項を分解

```diff
- | Q-1 | master の機能のうち実運用しているものはどれか？ | ヒアリング or 業務フローの書き出し |
+ | Q-1a | 銀行連携 (Banks*, Yayoi 等の取込) を実運用しているか? | はい / いいえ |
+ | Q-1b | メール経由の仕訳取込 (LogMailJpn) を実運用しているか? | はい / いいえ |
+ | Q-1c | 青色申告書 (BlueSheet) を生成しているか? | はい / いいえ (税理士任せ?) |
+ | Q-1d | 月次/年次の手動バッチ作業は何があるか? | 列挙 |
+ | Q-1e | 過去何会計期分のデータが master に蓄積されているか? | 期数 + 仕訳件数概算 |
+ | Q-3a | Bootstrap 5 デザインのままで「モダン UI」要件は満たすか? | はい / いいえ |
+ | Q-3b | NO の場合、SPA (React/Vue) 化に追加 N か月投資する意思があるか? | はい / いいえ |
+ | Q-5 | master 廃止判定基準: (a) 90 日 Golden 一致 (b) 1 会計期 renewal 単独運用無事故 (c) E2E green | (a)+(b)+(c) を AND? いずれか? |
```

### 追加: §10 ドキュメント整合性タスク (新設)

```diff
+ ## 10. ドキュメントと実装の同期 (Phase A 内で実施)
+ - docs/PLAN.md は Phase 6 までのもの。Phase 7 以降を別ファイルに切る or 追記
+ - ADR-007 (Strangler Fig) は実装と乖離 → Status を `Deprecated` か `Superseded by ADR-NNN` に
+ - renewal の現状を反映した `docs/STATE-2026-05.md` 等のスナップショットを残す
```

---

## 7. 次イテレーションで確認したいこと（計画作成者へ）

1. **Phase 7-1「最小構成 Web UI」と現実 (UI Controller 53 / Template 50) の乖離はなぜ起きた?** research-renewal.md / MIGRATION_PLAN.md ともに ADR-022 を引用して「ログイン+ダッシュボード」と書いているが、実装は遥かに進んでいる。**ADR-022 が古いのか、実装が ADR を逸脱したのか、どちらか?**

2. **`FEATURE_*` フラグ未実装の認識はあったか?** ADR-007 の Wave x feature flag セットを continue 判定の根拠に挙げているが、grep でゼロ件。 **ADR-007 を deprecate する** か **これから実装する** か、立場を確定してほしい。

3. **`ZeroOpeningBalanceRepository` がデフォルト配線である件は認識していたか?** opening_balances テーブル設計は完了しているのに、データを入れる仕組みも、配線する仕組みもない。**期首残高シード機構は誰が作る予定か?**

4. **`accountingLogCalcJpn` を金額正本として使う依存性について**: master を retire する際に `accountingLogCalcJpn` も保管しないと再 import 不能。これは「master 廃止」のクリティカルパスに含まれるか?

5. **本データ (1,613 仕訳) と性能目標 (10 万仕訳 < 2 秒) の 100 倍ギャップに対する立場は?** Phase D「集計テーブル廃止と性能チューニング」のスコープを大幅縮小できる可能性が高い。

6. **`composer.json` の classmap に `back/class/else/` が残っている件**: 意図的か? renewal 側の解析対象に master クラスが混入している。

7. **PDF Golden test (master と renewal の PDF 比較) は B-2 のスコープに含まれるか?** 含めるなら別建ての方が良い。

---

レビュー終わり。
