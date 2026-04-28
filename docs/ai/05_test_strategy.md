# テスト戦略

## 1. 方針

Strangler Fig パターンで、`src/Domain/` に **純粋関数中心の新会計ドメイン層** を TDD で構築する。既存 `back/class/else/plugin/accounting/` は **仕様の参照実装** として残し、ドメインができ次第アダプタで繋ぎ替え、最終的に削除する。

### 設計原則

1. **純関数優先**: `src/Domain/` 配下のクラスは原則として副作用なし。DB / HTTP / Smarty / global を直接触らない。
2. **値オブジェクト**: 金額は `Money` (decimal文字列) や `int円` で扱い、float の浮動小数点誤差を避ける。`bcmath` または `brick/math` を採用。
3. **不変条件はテストに表明する**: 「借方=貸方」「期首残高+借方-貸方=期末残高」等の不変条件を property-based に近い形で検査する。
4. **境界条件を網羅**: 0円, マイナス, 端数, 期境界（年度跨ぎ）, 軽減税率と通常税率の混在, 課税/免税, 本則/簡易 など。
5. **既存実装との比較テスト**: ドメイン層がある程度形になったら、本番DBのスナップショット（2月までのデータ）から仕訳を読み出し、新旧で同じ集計値が出るかを比較する **Golden Master Test** を組み込む。

---

## 2. 共通基盤コンポーネント

| クラス | 責務 | テスト対象 |
|---|---|---|
| `Money` | 金額の値オブジェクト | 加減乗除, 比較, ゼロ判定, 符号反転, 端数処理 |
| `Rounding` | 端数処理ポリシー (ceil/floor/round) | `numLevel` 0/n の各分岐, 0円/負数/小数なし入力 |
| `FiscalPeriod` | 会計期 (開始月/月数/期番号) | 期境界, 不規則決算 (12ヶ月以外), 期末日計算 |
| `JournalEntry` | 仕訳 (借方明細n + 貸方明細n + メタ) | 借方=貸方バランス, 0行明細禁止, 0円明細許容/禁止 |
| `AccountTitle` | 勘定科目 (id, 名前, 区分, 残高方向) | ツリー構造, 親子整合, BS/PL/CR分類 |

---

## 3. サブドメインごとのテスト網羅表

### 3-1. Money / Rounding（土台）

| 不変条件 / ケース | テスト名（仮） |
|---|---|
| 加算は順序非依存 | `addition_is_commutative` |
| 加算は結合可能 | `addition_is_associative` |
| `add(a, -a) == 0` | `addition_with_negation_yields_zero` |
| 端数処理: 切捨 / 四捨五入 / 切上 | `rounding_floor_truncates_toward_zero` 等 |
| `numLevel=0` で整数化 | `rounding_to_integer_when_level_zero` |
| `numLevel=2` で小数2位 | `rounding_to_two_decimals` |
| 整数値はそのまま返す | `rounding_returns_integer_input_unchanged` |
| 負数: ceil/-1.5 = -1, floor/-1.5 = -2 | `rounding_handles_negative_numbers` |
| 既存 `getNumDisplay` と挙動一致 | `rounding_matches_legacy_implementation` |

### 3-2. 仕訳 (Journal Entry)

| 不変条件 / ケース | テスト |
|---|---|
| 借方合計 == 貸方合計 (バランス) | `entry_must_balance_or_throws` |
| 1借方1貸方 (シンプル仕訳) | `simple_entry_is_valid` |
| 複数借方/複数貸方 (複合仕訳) | `compound_entry_balances` |
| 各明細に勘定科目必須 | `each_line_requires_account_title` |
| 期外日付の禁止 (確定済期に投入不可) | `entry_in_locked_period_throws` |
| 申請承認ワークフロー (`flagApply` / `idAccountApply` / `arrCommaIdAccountPermit` / `flagApplyBack`) | `entry_pending_then_approved_then_recalled` |
| 論理削除 (`flagRemove=1`, `stampRemove`) | `removed_entry_excluded_from_aggregations` |
| 編集履歴 (`jsonVersion`) | `edit_appends_version_record` |
| 部門指定 | `entry_with_department_aggregates_per_department` |
| 補助科目指定 | `entry_with_sub_account_aggregates_per_sub` |
| 中間決算フラグ (`flagFiscalReport='f1'`/`'f21'`/`'f41'`等) | `interim_settlement_period_assignment` |

### 3-3. 勘定科目 / 補助科目 / 決算項目

| ケース | テスト |
|---|---|
| 標準勘定科目セットの読込 (`JgaapAccountTitle{BS,PL,CR}.php`) | `loads_default_chart_of_accounts` |
| 科目ツリーへの新規追加 (採番) | `inserting_account_assigns_next_id` |
| 親子整合 (子の合計 = 親) | `parent_total_equals_children_sum` |
| BS の借方/貸方残高方向 | `bs_normal_balance_direction` |
| PL の収益/費用区分 | `pl_revenue_expense_classification` |
| 決算項目 (FS) と勘定科目のマッピング | `fs_item_aggregates_mapped_account_titles` |
| 補助科目: 科目との紐付け | `sub_account_inherits_parent_account` |

### 3-4. 消費税

| ケース | テスト |
|---|---|
| 課税事業者 (10%) で 1000円 → 税抜910(切捨)/910(四捨五入)/910(切上※整数のため変化なし) | `tax_inclusive_input_excludes_tax_correctly` |
| 税抜入力 (外税) で 1000円 → 税込1100円 | `tax_exclusive_input_adds_tax` |
| 別記入力 | `tax_separate_input_keeps_amounts_independent` |
| 軽減税率 (8%) と通常税率 (10%) の混在 | `mixed_reduced_and_standard_rate` |
| 端数処理ポリシー反映 | `rounding_policy_applied_to_tax` |
| 免税事業者 → 税額計算スキップ | `tax_free_entity_skips_tax_calc` |
| 簡易課税: 第1種(90%)〜第6種(40%) のみなし仕入率 | `simplified_tax_rate_per_business_type` |
| 本則課税 + 個別対応: 課税売上対応/非課税売上対応/共通対応 | `general_rule_specific_match_classification` |
| 本則課税 + 比例配分: 課税売上割合 | `general_rule_proration_uses_taxable_sales_ratio` |
| 軽減税率8%は8%入力と区別される (`'8_reduced'` フラグ) | `reduced_rate_eight_distinguished_from_standard_eight` |
| 旧税率 (5%, 8%) の混在期 | `legacy_rates_supported` |

### 3-5. 元帳 / 試算表

| ケース | テスト |
|---|---|
| 期初〜期末の累計残高 | `ledger_cumulative_balance` |
| 期間絞込 (任意月範囲) | `ledger_filtered_by_date_range` |
| 部門絞込 | `ledger_filtered_by_department` |
| 補助科目絞込 | `ledger_filtered_by_sub_account` |
| 試算表: 借方合計 == 貸方合計 | `trial_balance_invariant_holds` |
| 試算表: 期首残高 + 当期発生 = 期末残高 (科目別) | `trial_balance_period_movement` |

### 3-6. 期首残高 / 繰越

| ケース | テスト |
|---|---|
| 初期セットアップ時の期首残高入力 | `initial_opening_balance_input` |
| BS科目: 当期末残高 → 翌期期首残高 | `bs_carry_forward_to_next_period` |
| PL科目: 当期末残高 → 翌期は0クリア | `pl_resets_to_zero_next_period` |
| 仮繰越 (`tempPrev`) → 本繰越 (`done`) | `temp_then_final_close` |
| 当期確定後の編集禁止 | `closed_period_is_immutable` |
| 確定取消 | `unlock_closed_period` |

### 3-7. 減価償却

| ケース | テスト |
|---|---|
| 定額法: 取得価額 × 償却率 × (使用月/12) | `straight_line_basic` |
| 定率法: 残存簿価 × 償却率, 償却保証額への切替 | `declining_balance_with_assured_amount` |
| 級数法 | `sum_of_years_method` |
| 任意償却 | `voluntary_method` |
| 一括償却 (3年均等) | `lump_sum_3year_method` |
| 平均償却 | `average_method` |
| 2007/04/01 を境にした償却ルール変更 | `boundary_20070401_changes_method` |
| 残存簿価1円処理 (旧資産) | `legacy_asset_residual_one_yen` |
| 事業供用割合の反映 | `business_use_ratio_applied` |
| 圧縮記帳 | `compression_accounting` |
| 減価償却の販管費/製造原価への配賦 | `depreciation_split_by_cost_category` |
| 期中取得 (使用開始月の按分) | `mid_period_acquisition_prorates_months` |
| 期中除却 / 売却 | `disposal_in_period` |
| 償却過不足 (前期繰越) | `prev_over_under_carried_forward` |

### 3-8. 収支管理 (Cash)

| ケース | テスト |
|---|---|
| 入金/出金の集計 | `cash_in_out_totals` |
| 消込: `flagPay=1` 反映 | `cash_payment_marks_settled` |
| 留保ログ → 確定で仕訳化 | `deferred_cash_to_journal` |
| 期間絞込 | `cash_period_filter` |

### 3-9. 決算書 (PL / BS / CR / CS / SS)

| ケース | テスト |
|---|---|
| PL: 売上総利益 / 営業利益 / 経常利益 / 当期純利益 の計算 | `pl_layered_profits_calculated` |
| BS: 資産 == 負債 + 純資産 | `bs_balance_invariant` |
| CR: 製造原価の3区分 (材料費/労務費/経費) → 当期製品製造原価 | `cr_three_categories_to_cost_of_production` |
| CS: 営業/投資/財務活動 (間接法) | `cfs_indirect_method` |
| SS (株主資本等変動計算書): 期首 → 当期変動 → 期末の遷移 | `sse_period_transition` |
| 個別注記表: フリー記述の保存/取得 | `notes_free_text_persisted` |
| 比較決算: n期分の並列出力 | `comparative_statements_n_periods` |

### 3-10. 部門別

| ケース | テスト |
|---|---|
| 部門別 PL/BS の集計 | `department_specific_aggregates` |
| 共通部門 (按分対象) の配賦 | `common_department_allocation` |

### 3-11. 損益分岐点 / 予算

| ケース | テスト |
|---|---|
| BEP: 売上 = 変動費 + 固定費 + 0 | `break_even_definition` |
| BEP: 限界利益率 = (売上 - 変動費)/売上 | `marginal_profit_ratio` |
| 安全余裕率 = (実績売上 - BEP売上) / 実績売上 | `safety_margin_ratio` |
| 予算実績比較: 差額 = 実績 - 予算 | `budget_vs_actual_diff` |

### 3-12. 家事按分（個人事業主）

| ケース | テスト |
|---|---|
| 按分率 numRatio% を仕訳に適用 | `house_proration_applied` |
| 按分後仕訳の自動生成 | `house_journal_auto_generated` |

---

## 4. 統合テスト（後段で導入）

| 種類 | 内容 |
|---|---|
| **Golden Master** | 本番DBのスナップショット (2月までのデータ) を読み込み、新ドメインで集計した PL/BS/試算表が、現行UI で出る数字と一致することを比較する |
| **DB往復テスト** | 新ドメインの永続化（リポジトリ層 = `src/Infrastructure/`）を Testcontainers なしで MariaDB Docker 上に作る |
| **マイグレーションテスト** | スキーマを変更する場合は、現行 → 新スキーマへのデータ移行スクリプトを用意し、移行前後で集計値が一致することを確認 |

---

## 5. テスト基盤

### ツール選定（既に composer の `vendor/` にあるもの）

- **PHPUnit 12** (vendor 配下に PHPUnit ディレクトリあり)
- **Mockery** (使うのは Repository / Clock 等のインターフェース部分のみ)
- **PHPStan** (level 8 を目標)
- **PHP-CS-Fixer** または **friendsofphp/php-cs-fixer**
- **Rector**: PHP 8 機能への自動アップグレード補助
- **brick/math**: BigDecimal で金額計算

### ディレクトリ構成案

```
src/
├── Domain/
│   ├── Money/
│   │   ├── Money.php
│   │   └── Rounding.php
│   ├── FiscalPeriod/
│   ├── Journal/
│   │   ├── JournalEntry.php
│   │   └── JournalLine.php
│   ├── AccountTitle/
│   ├── ConsumptionTax/
│   ├── Ledger/
│   ├── Depreciation/
│   ├── Cash/
│   ├── FinancialStatement/
│   └── ...
├── Application/        # ユースケース (新ドメインを使う orchestration 層)
└── Infrastructure/
    ├── Persistence/    # MariaDB アクセス
    └── Legacy/         # 旧global/$classDb 系のラッパー (移行期のみ)

tests/
├── Unit/Domain/        # 純関数テスト
├── Integration/        # DB を使う統合テスト
└── Golden/             # 既存実装との集計値比較
```

### コマンド（予定）

```bash
composer install
composer test              # phpunit 全実行
composer test:unit         # ユニットのみ
composer test:integration  # 統合のみ (要 docker compose up db)
composer test:golden       # 既存DBスナップショットとの比較
composer phpstan           # 静的解析
composer cs-fix            # コード整形
```

---

## 6. マイグレーション戦略（スキーマ変更が必要になったら）

1. 新スキーマは `migrations/` に SQL ファイルで管理（`Phinx` または手書き SQL）
2. 旧スキーマからのデータ移行スクリプトを `migrations/data/` に
3. 各マイグレーションは **必ず可逆 (up/down)** を用意
4. Golden Master テストで「移行前と移行後で集計値が同じ」を保証
5. 本番反映前に: 本番スナップショット → ステージング DB → マイグレーション → 検証 の手順

---

## 7. 進め方（Roadmap）

| Sprint | 期間目安 | 内容 |
|---|---|---|
| 0 | 完了 | アプリ調査・ドキュメント化 (`docs/ai/00`〜`04`) |
| 1 | 半日 | scaffolding (composer require-dev, phpunit.xml, autoload, GitHub Actions) |
| 2 | 1〜2日 | Money / Rounding / FiscalPeriod のテスト＆実装 |
| 3 | 2〜3日 | JournalEntry / AccountTitle / Ledger の基礎 |
| 4 | 3〜4日 | 消費税 (本則/簡易/軽減税率) |
| 5 | 1日 | 試算表 / 期首残高 / 繰越 |
| 6 | 4〜5日 | 減価償却 (6種類 × 2007/04/01境界) |
| 7 | 2〜3日 | 決算書 (PL/BS/CR/CS/SS) |
| 8 | 1〜2日 | Cash / Banks (スタブ) / FixedAssets 統合 |
| 9 | 2〜3日 | Golden Master テスト (本番スナップショット比較) |
| 10 | 後段 | アダプタで既存UIから新ドメインを呼び出し → 旧 `Calc*` 削除 |
| 11 | 後段 | 帳票レイヤ (国税庁最新様式に合わせた決算書/内訳書/概況説明書) |

---

## 8. 留意点

- **PHP8/MariaDB10 アップグレード時の null/0 取扱の修正は完全ではない可能性**（`master` ブランチの履歴より）。新ドメイン層は `?int` `?Money` 等で null を明示的に扱う。
- **本番データ (2月まで) を Golden Master の入力に使うため、テスト時に PII / 機密が混入しないよう** スナップショットは別 DB に分離し、テストコードや CI には載せない。
- **税法改正への追従**: 軽減税率 (2019/10/01)、インボイス (2023/10/01)、適格請求書発行事業者番号など、ドメイン層は税法バージョンを入力として持たせる設計を意識する。
