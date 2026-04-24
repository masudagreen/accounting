# ADR-020: 旧 UI（`back/**`）退役判断

- ステータス: **承認（2026-04-22）**
- 決定者: ユーザ（「現行システム最新化完了」方針に基づく）
- 関連: [PLAN.md](../PLAN.md) §Phase 6, ADR-001, ADR-007, ADR-009〜019

---

## 1. 文脈 (Context)

Phase 6（既存システム最新化）で旧 `back/class/else/plugin/accounting/jpn/` 配下 **223 ファイル / 約 7000 行** を新 `src/Rucaro/**`（**474 ファイル**）へ段階移植した。Wave 6-A〜6-I が完了し、全主要会計機能が新 REST API 経由で動作する状態に到達した。

本 ADR で **旧 UI（`back/**` ディレクトリ）の退役ステータスを確定**する。

---

## 2. 決定 (Decision)

### 2.1 退役方針

- 旧 `back/class/else/plugin/accounting/jpn/` は **Web 到達不能のまま温存**（Phase 1.3 で `DocumentRoot=/var/www/html/public` に切替済、`public/.htaccess` で `back/` 拒否）
- 旧 UI の PHP エントリ（`index.php` / `api.php` / `output.php` / `confirm.php`）は**リポジトリには残すが公開 DocumentRoot の外**
- 新アプリ（`src/**` + `public/api/v1/*`）のみを運用経路とする
- 旧コード削除は**行わない**（過去データ復元時の互換リファレンスとして保持）

### 2.2 旧→新 対応表

#### 会計中核機能

| 旧モジュール | 新モジュール | 実装 Wave |
|---|---|---|
| `Log.php`, `LogEditor.php` | `Rucaro\Domain\Journal\*` + `Application\Journal\*` | Wave 4.2 |
| `CalcAccountTitle.php` | `Rucaro\Application\TrialBalance\*` | Wave 4.3 |
| `TrialBalance.php`, `TrialBalanceOutput.php` | `Rucaro\Domain\TrialBalance\*` | Wave 4.3 |
| `FinancialStatement.php`, `FinancialStatementOutput.php` | `Rucaro\Domain\FinancialStatement\Port\*` | Wave 6-A |
| `CalcAccountTitleFS.php`, `AccountTitleFS.php` | `Rucaro\Domain\FinancialStatement\Port\FsSectionDefinition`, `AccountTitleFsMapping` | Wave 6-A |
| `FinancialStatementCS.php`, `FinancialStatementCSOutput.php`, `CalcAccountTitleFSCS.php` | `Rucaro\Domain\FinancialStatement\Port\Cs\*` | Wave 6-B |
| `Ledger.php`, `LedgerOutput.php` | `Rucaro\Domain\Ledger\*` | Wave 6-C |
| `FixedAssets*.php`（9 ファイル） | `Rucaro\Domain\FixedAsset\*` | Wave 6-D |
| `CashPlan.php`, `CashPlanOutput.php`, `CashAnalyze*.php` | `Rucaro\Domain\CashPlan\*` | Wave 6-E |
| `BreakEvenPoint*.php`, `CalcBreakEvenPoint.php` | `Rucaro\Domain\BreakEvenPoint\*` | Wave 6-E |
| `ConsumptionTax*.php`, `CalcConsumptionTax.php` | `Rucaro\Domain\ConsumptionTax\*` | Wave 6-F |
| `Budget*.php` | `Rucaro\Domain\Budget\*` | Wave 6-G |
| `BlueSheet.php`, `BlueSheetEditor.php`, `BlueSheetOutput.php` | `Rucaro\Domain\BlueReturn\*` | Wave 6-H-1 |
| `FinancialStatementSS.php`, `FinancialStatementSSOutput.php` | `Rucaro\Domain\StatementOfChangesInEquity\*` | Wave 6-H-2 |
| `NotesFS.php`, `NotesFSEditor.php`, `NotesFSOutput.php` | `Rucaro\Domain\FinancialStatementNotes\*` | Wave 6-H-3 |
| `FinancialStatementMulti*.php` | `Rucaro\Domain\FinancialStatement\Multi\*` | Wave 6-I |

#### 基盤機能

| 旧 | 新 |
|---|---|
| `Code_Else_Lib_Db` | `Rucaro\Infrastructure\Database\ConnectionFactory` |
| `Code_Else_Lib_Crypte`（Blowfish） | `Rucaro\Infrastructure\Crypto\AesGcmCipher` + `LegacyBlowfishDecryptor`（互換復号） |
| `Code_Else_Core_Login_Login`（セッション） | `Rucaro\Http\Controller\Auth\LoginController` + Bearer token |
| `Code_Else_Core_Base_Attest` | `Rucaro\Http\Middleware\AuthenticateBearer` |
| 旧 `accountingLog*` 等 59 テーブル | 新 18 テーブル（ADR-002 命名規則） |
| Smarty 3（旧同梱） | Smarty 5.7（Composer 経由） |

### 2.3 退役未了の旧機能

以下は Phase 6 スコープ外として**移植していない**:

| 旧モジュール | 状態 | 理由 |
|---|---|---|
| `Access*`, `Authority*` 系（RBAC の UI） | 新 API の Bearer 認証で代替、UI は未移植 | Phase 3 の最小認証で当面十分 |
| `calcBanks/`（5 銀行 CSV 取込） | 未移植 | CSV パーサなので必要時に Infrastructure Adapter として追加 |
| `CalcLogImportMail.php`（IMAP メール取込） | 未移植 | Phase 5 で Dockerfile に `ext-imap` 未搭載、必要時に追加 |
| `LogImport*.php`（汎用ファイル取込） | 未移植 | 必要時に Import Adapter として |
| `Portal.php`, `Preference.php` 系 UI | 未移植 | 新 UI は REST API + 将来のフロントエンド、旧 UI は参照のみ |
| 旧 Smarty テンプレート `back/tpl/templates/**/*.tpl` | 未移植（参考資料として保持） | 新 UI は OpenAPI スキーマ経由のフロントエンドを将来構築 |

---

## 3. 実稼働検証状況

### 3.1 テスト

```
Unit:    729 tests / 1642 assertions green
PHPStan: level 6 No errors（baseline 未変更）
```

### 3.2 実機生成 PDF（2026-04-22 時点、`C:\Users\yusuk\OneDrive\デスクトップ\rucaro-out\`）

17 ファイル・合計約 720 KB、すべて日本語 IPAex Gothic 埋め込み:
- `pl.pdf` / `bs.pdf` / `cs.pdf` / `all.pdf` — 単期 BS/PL/CS
- `multi-period-bs.pdf` / `-pl.pdf` / `-all.pdf` — 複数期比較
- `ledger.pdf` — 総勘定元帳
- `fixed-assets.pdf` — 固定資産台帳
- `cash-plan.pdf` — 資金繰り表
- `break-even-point.pdf` — 損益分岐点
- `budget.pdf` / `budget-variance.pdf` — 予算書・予実対比
- `consumption-tax-report.pdf` — 消費税申告書イメージ
- `blue-return.pdf` — 青色申告決算書 4 ページ
- `statement-of-changes-in-equity.pdf` — 株主資本等変動計算書
- `fs-notes.pdf` — 注記表

### 3.3 マイグレーション

Phase 6 で追加された DB テーブル:

| Migration | テーブル |
|---|---|
| 0008 | `account_title_fs_mappings`, `fs_section_definitions` |
| 0009 | `account_title_cs_mappings`, `fs_cs_section_definitions` |
| 0010 | `opening_balances` |
| 0011 | `fixed_assets`, `fixed_asset_categories`, `fixed_asset_depreciation_schedules` |
| 0012 | `cash_plans`, `cash_plan_entries` |
| 0013 | `account_title_cvp_classifications` |
| 0014 | `consumption_tax_rates`, `consumption_tax_categories`, `account_title_consumption_tax_defaults`, `consumption_tax_invoice_registrations`, `consumption_tax_periods` |
| 0015 | `budgets`, `budget_line_items` |
| 0016 | `blue_return_forms`（+ `entities.is_corporate` カラム） |
| 0017 | `ss_manual_adjustments` |
| 0018 | `fs_note_templates`, `fs_notes` |

Phase 1 の 11 テーブルと合わせて計 **約 30 テーブル**が新スキーマで稼働中。

---

## 4. 退役承認条件

以下を満たせば旧 UI を**正式退役**とする:

- [x] 全主要会計機能が新 REST API で動作（Wave 6-A〜6-I 実装完了）
- [x] デスクトップ PDF で視覚検査済（17 ファイル、日本語レンダリング OK）
- [x] Unit 729 tests green
- [x] PHPStan level 6 No errors
- [x] 旧 `back/**` は Web 到達不能（Phase 1.3 以降）
- [x] ADR-020（本書）によって退役方針が明文化

**→ 退役条件すべて充足、旧 UI は正式退役状態**

---

## 5. 今後の運用

### 5.1 旧 `back/**` の扱い
- **削除しない**（過去データ参照・緊急時リファレンスとして保持）
- **修正しない**（温存方針、バグがあっても新アプリ側で対応）
- Composer の `classmap` に残しているので、旧クラスを新コードから呼び出すことは技術的には可能（が、そうすべきでない）

### 5.2 将来のメンテナンス方針

- 新機能追加は常に `src/**` 側
- DB スキーマ変更は `scripts/migrate/NNNN_*.sql` で追加
- ADR を起票してから設計変更を実施
- 旧 `back/dat/version/Batch*.php` は歴史的遺物として保持するが、実行しない（`DocumentRoot=public/` で到達不能）

### 5.3 退役未了モジュールへの対応

§2.3 の未移植モジュール（銀行取込・メール取込・RBAC UI・Portal 等）が必要になった時点で個別 Wave を設定し、ADR を追加起票して段階移植する。

---

## 6. 代替案と却下理由

| 代替案 | 却下理由 |
|---|---|
| 旧 `back/**` 全削除 | 過去データ参照・緊急時リファレンスとして有用、ディスク容量も軽微 |
| 旧 UI を新 DocumentRoot に復活 | 移植で品質・セキュリティ・保守性すべて向上済、退行はリスク |
| 全モジュール完全移植（RBAC UI・Portal 含む）を Phase 6 で完結 | スコープ巨大化、ユーザ要望（現行最新化の完了を優先）に反する |
| Strangler Fig を中止して旧 UI を延命 | Phase 1〜5 の投資を無駄にする、PHP 8.3 / MariaDB 10 運用を活かせない |

---

## 7. 結果 (Consequences)

### Pros
- 旧 PHP 5 時代設計から完全脱却、PHP 8.3 + MariaDB 10 + PSR-4 + Hexagonal で新規機能開発が可能
- Bearer token 認証 + OpenAPI 3.1 で外部連携（Claude 等）の入口準備完了
- 全主要機能が PDF で視覚検証済、実用レベルに到達
- 729 tests / PHPStan level 6 で回帰耐性確保

### Cons
- 旧 UI でしか行えない操作（RBAC UI / Portal / 銀行取込 / メール取込）は当面 REST API + CLI + 将来の新フロントエンドで代替が必要
- 新 API 用のフロントエンド（Web UI）は本計画スコープ外、別途構築が必要
- 旧 `back/**` の存在がリポジトリサイズに影響（許容範囲）

---

## 8. Phase 6 完了宣言

本 ADR をもって **Phase 6「既存システム最新化」完了** を宣言する。

以降の新機能追加・拡張は別 Phase / Wave として ADR を新規起票して進める。

---

## 9. 関連リンク

- [PLAN.md](../PLAN.md)
- [INTERNAL_ARCHITECTURE.md](../INTERNAL_ARCHITECTURE.md)
- ADR-001 〜 ADR-019
- `docs/phase1/class-table-matrix.md` — 旧クラス ↔ SQL 対応表
