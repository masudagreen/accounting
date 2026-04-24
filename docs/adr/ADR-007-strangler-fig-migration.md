# ADR-007: Strangler Fig 移行計画

- ステータス: 提案中 (2026-04-21)
- 決定者: (ユーザ承認待ち)
- 関連: [PLAN.md](../PLAN.md), [ADR-001](./ADR-001-directory-layout.md), ADR-005 (Layered Architecture), ADR-006 (Ports & Adapters)

> 注: ADR-005 / ADR-006 は本 ADR と同じ Phase 4.1 で起草予定の同胞 ADR。本文中では両者の結論（HTTP→Application→Domain→Infrastructure の 4 層と、外界 I/O の Port 化）を前提に記述している。

---

## 1. 文脈 (Context)

### 1.1 規模感

旧アプリ `back/class/else/plugin/accounting/` は `docs/internal/class-table-matrix.md` §0 によれば以下の規模を持つ:

| 指標 | 値 |
|------|----|
| 総 PHP ファイル数 | 432 |
| ユニーククラス数 | 431 (Core 認証 36 + Login 5 + Confirm 8 + Lib ~15 + Accounting 共通 39 + Accounting Jpn 149 + Accounting Jpn 2012 旧版 80+ + その他) |
| DB テーブルを直接触るクラス | 223 (51.7%) |
| DDL 定義済みテーブル数 | 59 |
| コードから参照される DDL 定義テーブル数 | 54/59 (91.5%) |
| DB 抽象化 | 独自ラッパー 1 個 (`Code_Else_Lib_Db`)、ORM なし |
| DB アクセスパス | `getSelect` / `insertRow` / `updateRow` / `deleteRow` の 4 メソッドのみ |

ADR-001 で採った「旧 `back/` は一切触らず温存、新 `src/Rucaro\\` を隣に建てる」方針を、個別ドメインの退役計画にまで具体化する必要がある。

### 1.2 ビッグバン書き換えが非現実的である理由

- **数値整合性の担保が困難**: `accountingLog` (仕訳) → `accountingLogCalcJpn` (元帳キャッシュ) → `accountingFSValueJpn` (FS 集計値) の 3 段射影を同時移行すると、旧アプリとの数値一致を golden test で検証する際に差分の原因特定が不可能になる。
- **被参照度の偏り**: class-table-matrix.md §4 の依存グラフによれば、`accountingLog` は 25 クラスから参照されており、Journal を動かすには実質「仕訳クラスタ 8 クラス」を同時に持ってくる必要がある。逆に言えば、クラスタ単位で切れば退役は小さく刻める。
- **PHP 5 互換のレガシーイディオム**: `Code_Else_` クラス命名、`$varsAccount` / `$varsApiAccounts` などのグローバル、`Init.php` の長大な populate は、名前空間 / DI / PSR-4 のある新世界と 1 行ずつ書き換えるコストが天文学的。
- **旧 Smarty テンプレートの数**: `back/tpl/` 配下に数百ファイル。Blade / Twig への機械変換は不可能で、段階置換の余地が必要。

### 1.3 ユーザ決定事項（PLAN.md §0 より）

| 項目 | 確定内容 |
|------|----------|
| 旧アプリ | **温存**。読取専用で残す |
| 旧 DB | **温存**。新 DB は新規構築、必要時のみ旧 DB から手動/半自動インポート |
| 再暗号化バッチ | **不要**（旧 Blowfish CBC データは必要時に個別復号） |
| 法令対応 | **不要**（ローカル使用） |
| 期限 | **なし**。じっくり取り組む |
| PHP バージョン | 8.3 固定、新アプリのみ |

よって本計画は「ビジネス制約のない個人運用環境で、数値整合性と段階的検証に最大の重みを置いた退役計画」として設計する。

### 1.4 認証層の既知問題

`docs/internal/auth-flow.md` §12 によれば、旧認証には以下の CRITICAL / HIGH 級の問題が存在する:

- **V1 (CRITICAL)**: 失敗時パスワード平文で `baseLoginMiss.strPassword` に蓄積
- **V2 (CRITICAL)**: `baseAccount.strPassword` が unsalted SHA-256（salt / stretching / pepper なし）
- **V3 (HIGH)**: Cookie `id` に HttpOnly 無し / SameSite 未設定
- **V4 (HIGH)**: CSRF 検証が master DB 書込時のみ
- **V6 (HIGH)**: セッション IP バインド厳格一致（モバイル環境で予告なく切れる）

Strangler Fig の初期 Wave でこれらを引きずると、新 API 側でも問題が残存する。よって **認証は旧と新で完全分離**し、新 API (`/api/v1/*`) は独自のトークン発行 (ADR-003/Phase 3 の opaque token + Argon2id 方針) を使い、旧認証との相互運用は行わない方針とする。

---

## 2. 決定サマリ (Decision Summary)

1. **DB は独立スキーマ**で新規構築する
   - 新 DB: MariaDB 10.11、utf8mb4 既定、snake_case、ULID/UUIDv7（ADR-002 準拠）
   - 旧 DB: 別ファイル / 別スキーマとして温存。通常は起動すらしない
   - 必要時のみ旧 DB を **read-only でマウント** し、ワンショットインポータで新 DB に取り込む

2. **新機能はすべて `src/**` に実装**
   - PSR-4 名前空間 `Rucaro\\` のみ（ADR-001 §6）
   - 旧クラスを新コードから直接 `new` することを禁止
   - どうしても旧クラスを呼ぶ場合は `Rucaro\Infrastructure\Legacy\*` アダプタ経由に限定

3. **旧機能は退役順序表 (§3) に従って段階移行**
   - 5 つの Wave に分けて実施
   - Wave 間は **エントリ条件 / エグジット条件**（§4）で gate

4. **各移行モジュールに feature flag を 1 つ設ける**
   - `.env` に `FEATURE_JOURNAL_V2`, `FEATURE_TRIAL_BALANCE_V2`, `FEATURE_LEDGER_V2`, ...
   - デフォルト `false`。Wave が GA になったら `true` に切替
   - ローカル使用のため値変更のみで済み、デプロイパイプラインは不要

5. **新が master、旧は参照用**
   - 新 API (`/api/v1/*`) が master
   - 旧 UI (`/index.php?class=...`) は撤退予定（フロント刷新は本計画スコープ外）
   - 新旧並走期間は Wave 単位で短く区切る

---

## 3. 退役順序 (Decommission Waves)

class-table-matrix.md §4「Phase 4 移行優先度 Top 10」とクラスタ定義 §4 補足を踏まえ、以下 5 波に分ける。

### 3.1 Wave 1 — Journal + TrialBalance（Phase 4、工数 ~88h の内 ~60h）

**対象クラス（旧）**:

- 仕訳クラスタ: `Jpn_Log`, `Jpn_LogEditor`, `Jpn_LogBack`, `Jpn_LogDelete`, `Jpn_LogPermit`
- 試算表: `Jpn_TrialBalance`, `Jpn_TrialBalanceOutput`
- 集計エンジン: `Jpn_CalcAccountTitle`, `Jpn_CalcSubAccountTitle`（読取側のみ）

**対象テーブル（旧）**:

- `accountingLog`（仕訳本体、25 クラス被参照）
- `accountingSubAccountTitleValueJpn`（試算表の read source）
- `accountingLogCalcJpn`（元帳キャッシュ、読取のみ Wave 1 ではキャッシュ再構築を扱わない）

**対象テーブル（新）**:

- `journal_entries`（ADR-002 §4 の初期スキーマ）
- `journal_lines`
- `trial_balance_snapshot`（月次スナップショット、本 ADR §9 で migration 追加）

**理由**:

- **会計の中核**: 他の全てがこの上に乗る
- **独立性が高い**: 入出力が限られる（仕訳登録フォーム + 試算表画面）
- **不変条件が明確**: 借方合計 = 貸方合計、scale(2) の小数一致
- **Golden test が書きやすい**: 旧 DB の仕訳一覧を CSV に dump → 新 DB に投入 → 試算表比較、の 3 ステップで検証できる

**feature flags**:

- `FEATURE_JOURNAL_V2=true`（新 API `/api/v1/journals` を有効化）
- `FEATURE_TRIAL_BALANCE_V2=true`（新 API `/api/v1/trialBalance` を有効化）

### 3.2 Wave 2 — Ledger + FinancialStatement 3 種（Phase 4 末〜Phase 5 初期、工数 ~40h）

**対象クラス（旧）**:

- `Jpn_Ledger`
- `Jpn_FinancialStatement`, `Jpn_FinancialStatementCS`, `Jpn_FinancialStatementMulti`, `Jpn_FinancialStatementOutput`
- FS/集計クラスタの書込側: `Jpn_AccountTitle`, `Jpn_AccountTitleEditor`, `Jpn_SubAccountTitle`, `Jpn_SubAccountTitleEditor`
- `Jpn_CalcAccountTitle`（書込側、FS 集計再計算）

**対象テーブル（新側で構築）**:

- `financial_statement_templates`（BS/PL/CS のツリー定義）
- `financial_statement_values`（期別値）
- `account_titles`, `sub_account_titles`
- `department_fs_values`

**理由**:

- Wave 1 で仕訳が新側に出揃ったので、その上に集計を重ねる自然な順序
- FS テンプレは JSON (`accountingFSJpn.jsonFS`) から正規化するため設計工数が大きい。Wave 2 を独立に建てる価値あり
- BS / PL / CS / Multi の 4 画面を同時に扱うことでテンプレ変換ロジックを 1 回で検証できる

**feature flags**:

- `FEATURE_LEDGER_V2=true`
- `FEATURE_FS_V2=true`（BS/PL/CS/Multi を一括切替）

### 3.3 Wave 3 — Receipt 新規追加 + Journal 連携、FixedAssets 移行（Phase 5、工数 ~75h）

**対象**:

- **新規**: 領収書 AI パイプライン（`Receipt`, `ApprovalToken`, `ReceiptActionLog`）
- **移行**: 固定資産クラスタ `Jpn_FixedAssets`, `Jpn_FixedAssetsEditor`, `Jpn_FixedAssetsOutput`, `Jpn_CalcDep` + `calcDep/{Average,Declining,One,Straight,Sum,Voluntary}`, `Jpn_CalcFixedAssets`
- **接続**: 領収書承認 → 仕訳登録への連携（Wave 1 の Journal UseCase を呼び出す）

**理由**:

- 領収書フローは旧アプリに無い新機能のため、旧コードに足を引っ張られず新側で設計できる
- 固定資産は減価償却計算（戦略パターン 6 実装）が純粋計算で、テストが書きやすい
- Wave 1 の Journal UseCase を呼び出すクライアントとして Wave 3 を通すことで、Wave 1 の設計の正しさを逆説的に検証できる

**feature flags**:

- `FEATURE_RECEIPT_PIPELINE=true`
- `FEATURE_FIXED_ASSETS_V2=true`

### 3.4 Wave 4 — Budget / BlueSheet / BreakEvenPoint / ConsumptionTax / Entity 管理（将来、時期未定）

**対象**:

- `Jpn_Budget`（予算）
- `Jpn_BlueSheet`（青色申告決算書）
- `Jpn_BlueSheet_2012_Public`（2012 年旧版、温存のまま放置する可能性あり）
- `Jpn_BreakEvenPoint`（損益分岐点分析）
- `Jpn_ConsumptionTax`（消費税申告書）
- `Jpn_NotesFS`（注記表）
- `Entity`, `EntityEditor`（会社マスタ、Wave 4 より前は旧側で管理）
- 権限系: `Authority`, `Access`

**理由**:

- いずれも頻度の低い周辺機能。Wave 1〜3 で動作が安定してから着手したほうがリスクが低い
- `Entity` は移行が早すぎると全子テーブルのカスケード削除ロジックを新旧両対応にする必要が出て複雑化する。会計中核が新側に移ったタイミングで一括置換したほうが簡潔

**feature flags**: 機能単位に各 1 つ

### 3.5 Wave 5 — 銀行連携・メール取込（将来、Adapter 差し替えで済む部分）

**対象**:

- 銀行クラスタ: `Jpn_Banks`, `Jpn_BanksAccount`, `Jpn_BanksEditor`, `Jpn_BanksPreference`, `Jpn_BanksOutput`, `Jpn_CalcBanks_Japannetbank`, `Jpn_CalcBanks_Rakutenbank`, その他
- メール取込: `Code_Else_Plugin_Accounting_MailImport`（該当があれば）
- 現金クラスタ: `Jpn_Cash`, `Jpn_CashEditor`, `Jpn_CashDefer`, `Jpn_CashDelete`, `Jpn_CashPay`, `Jpn_CashPreference`, `Jpn_CashPlan`, `Jpn_CalcCash*`

**理由**:

- 外部連携は ADR-006 の Ports & Adapters 設計が効く領域。adapter 差し替えだけで済む
- 銀行 API は各行独自仕様で、1 行ごとの adapter 実装が長期作業になる。Wave 5 として独立
- 現金出納帳は仕訳と二重登録整合性が必要。Wave 1 の仕訳 UseCase が枯れてから着手するのが安全

---

## 4. 各 Wave のエントリ / エグジット条件

### 4.1 エントリ条件（Wave 開始前に満たすこと）

各 Wave は以下を全て満たしてから開始する。

1. **前 Wave の golden test が通る**
   - 旧 DB の該当データを dump → 新 DB に投入 → 画面表示（または API レスポンス）の数値が旧と一致
   - ただし Wave 1 については旧アプリ稼働が前提なので後述の §9 で再規定
2. **前 Wave の feature flag を `true` にした状態で 1 週間以上**、ローカル環境で日常利用が破綻なく続いていること
3. **前 Wave の `docs/` が更新済み**
   - `INTERNAL_ARCHITECTURE.md` の該当節
   - OpenAPI（`docs/api/openapi.yaml`）のエンドポイント追記
   - 必要なら新規 ADR 起草
4. **前 Wave のテストカバレッジが 80% 以上**（`common/testing.md` 準拠）
5. **CI が全て green**（PHPStan level 6、Psalm level 3、PHPUnit、PHP-CS-Fixer）

### 4.2 エグジット条件（Wave 完了と認める条件）

各 Wave は以下を全て満たしたら「完了」とする。

1. **旧コードへのアクセスが 0**
   - Monolog で旧経路（`Rucaro\Infrastructure\Legacy\*` アダプタ呼出、および旧 `index.php?class=...` でのヒット）をログし、1 週間で該当 Wave の機能について 0 件であること
2. **DB 参照が新側のみ**
   - 該当 Wave の対象テーブルについて、旧 DB の接続が行われていないことを PDO レイヤの接続ログで確認
3. **Golden test が最新データで合格**
   - 旧 DB の該当データの最新 snapshot を dump し、新 DB と再比較して数値一致
4. **旧クラスが新コードに参照されていない**
   - `grep -r "Rucaro\\\\Infrastructure\\\\Legacy" src/` で該当 Wave 関連 use がゼロ（Wave 5 の Adapter 使用は例外）
5. **Feature flag が `true` 固定で運用されている**
   - `.env` 以外で切り替え可能にしないこと（設定ミスで旧に戻るリスク排除）

Wave をエグジットしても **旧コード・旧 DB は削除しない**。凍結コードとして `back/` に残し、将来の監査 / 参照用途に保持する。

---

## 5. 旧・新の同期戦略

### 5.1 マスタと同期方向

| 期間 | Master | 同期方向 |
|------|--------|----------|
| Wave 1 開始前 | 旧 DB | 同期なし（新 DB は空） |
| Wave N 実施中 | 旧 DB（対象機能について） | 旧 → 新の一方向、ワンショット |
| Wave N GA 後 | 新 DB（対象機能について） | 同期なし（旧 DB は参照禁止） |
| 全 Wave 完了後 | 新 DB | 旧 DB は凍結アーカイブ |

### 5.2 ワンショットインポータの原則

- **必要になったら設計、今は空**。推測で先行実装しない（YAGNI）
- 実装時は `bin/cowork import:legacy <wave> [--dry-run]` の形で Symfony Console コマンドとして提供
- 冪等性: 同じ入力で何度流しても同じ結果（ULID / UUID をキーに UPSERT）
- ログ: `storage/logs/import-legacy-<wave>-<timestamp>.log` に件数と失敗行を記録
- 差分検証: インポート後、行数と合計値（金額列）が旧と一致するかを自動チェック
- ロールバック: 新 DB の migration を 1 つ戻すか、該当テーブルを TRUNCATE する（本番データがない個人運用前提）

### 5.3 データ差分が疑われた場合の対応

1. Wave を一時停止（feature flag を `false` に戻す）
2. 旧 DB と新 DB の同一期間・同一条件での SELECT 結果を CSV dump
3. `diff` で差分行を特定、Monolog に記録
4. 差分原因を特定して修正（データ / 実装 / migration のどれか）
5. 差分が解消してから再開

---

## 6. Feature Flag 運用

### 6.1 定義方法

`.env` に以下の形式で定義する:

```
# Wave 1
FEATURE_JOURNAL_V2=false
FEATURE_TRIAL_BALANCE_V2=false

# Wave 2
FEATURE_LEDGER_V2=false
FEATURE_FS_V2=false

# Wave 3
FEATURE_RECEIPT_PIPELINE=false
FEATURE_FIXED_ASSETS_V2=false

# Wave 4
FEATURE_BUDGET_V2=false
FEATURE_BLUE_SHEET_V2=false
FEATURE_BREAK_EVEN_POINT_V2=false
FEATURE_CONSUMPTION_TAX_V2=false
FEATURE_ENTITY_V2=false

# Wave 5
FEATURE_BANK_V2=false
FEATURE_MAIL_IMPORT_V2=false
FEATURE_CASH_V2=false
```

### 6.2 読取方法

`src/Support/FeatureFlags.php`（新設）に集約する:

```php
namespace Rucaro\Support;

final class FeatureFlags
{
    public function __construct(private readonly array $flags) {}

    public function isEnabled(string $key): bool { /* ... */ }
}
```

`config/app.php` から DI コンテナに注入し、`Rucaro\Http\Api\V1\*Controller` が起動時に判定。無効なら 404 または 503 を返す。

### 6.3 切替タイミング

- デフォルト: `false`
- Wave が GA になった時点（§4.2 のエグジット条件を全て満たした時点）で `true` に切替
- 本計画はローカル使用のため `.env` の値を変えるだけ。デプロイパイプラインは不要
- 切替はコミットとして残す（`chore: enable FEATURE_JOURNAL_V2 after Wave 1 GA`）

### 6.4 並走期間中のルーティング

- **新 API** (`/api/v1/*`): feature flag が `true` のエンドポイントのみ有効化。`false` なら 503 Service Unavailable
- **旧 UI** (`/index.php?class=...`): 常時有効（Wave 終了後も削除しない）
- **クライアント側の切替**: フロントエンド刷新はスコープ外のため、本計画では新 API を直接 curl / Postman / CLI (`bin/cowork`) で叩く運用を想定

---

## 7. 旧新並走時の Master 判定

### 7.1 API 層

| リソース | Master | 旧経路 | 新経路 |
|---------|--------|--------|--------|
| `/api/v1/auth/*` | 新（Phase 3） | `/index.php?class=Core&type=Login` | `/api/v1/auth/login` |
| `/api/v1/journals` | 新（Wave 1 以降） | `/index.php?class=Plugin&module=Accounting&type=Jpn_Log` | `/api/v1/journals` |
| `/api/v1/trialBalance` | 新（Wave 1 以降） | `/index.php?class=Plugin&module=Accounting&type=Jpn_TrialBalance` | `/api/v1/trialBalance` |
| `/api/v1/financialStatements` | 新（Wave 2 以降） | `/index.php?class=Plugin&module=Accounting&type=Jpn_FinancialStatement` | `/api/v1/financialStatements` |
| `/api/v1/receipts` | 新（Wave 3、新規） | なし | `/api/v1/receipts` |
| `/api/v1/fixedAssets` | 新（Wave 3 以降） | `/index.php?class=Plugin&module=Accounting&type=Jpn_FixedAssets` | `/api/v1/fixedAssets` |

旧 API (`/api.php`) はログイン時に旧 Cookie `id` を発行する経路のみが残存。新 API の opaque token とは相互運用しない。

### 7.2 DB 層

Wave N の GA までは該当機能について旧 DB 読取可、GA 後は新 DB のみ。`PdoJournalRepository` などの新リポジトリ実装は、GA 後にコンストラクタで `newDb` のみを受けるように固定する（読取 fallback を書かない）。

### 7.3 二重書込の禁止

**旧 DB と新 DB に同じデータを二重書込することは一切しない**。二重書込は:

- 同期処理のバグ（どちらかが欠損しても気付きにくい）
- トランザクション境界の混乱
- 監査ログの断絶

を招く。必要なら「旧 → 新 のワンショットインポート」のみ、「新 → 旧」は実装しない。

---

## 8. 撤退判断 (Abort Criteria)

### 8.1 撤退トリガ

以下のいずれかに該当したら該当 Wave を rollback する。

1. **数値ズレ**: 新実装の集計値が旧と 0.1% 以上乖離し、2 営業日以内に原因特定できない
2. **性能劣化**: 新実装の応答時間が旧の 3 倍以上になり、プロファイリング結果から 1 週間以内に改善目処が立たない
3. **データ破損**: 新 DB の migration / 実装ミスで該当 Wave のテーブルに不整合が発生
4. **認証障害**: 新 API の opaque token 発行が連続失敗し、ユーザがログインできない状態が 1 時間以上
5. **依存 Wave の失敗**: 後続 Wave の前提条件（例: Wave 1 の Journal UseCase）が破綻し、Wave の独立性が崩れた

### 8.2 撤退方法（Rollback Procedure）

1. **feature flag を `false` に戻す**
   - `.env` の該当 flag を false に変更
   - Apache 再起動（opcache invalidation 含む）
   - 旧経路のみが有効になることを確認
2. **新コードは残すが使わない**
   - `src/` の該当クラスは削除しない（次回リトライで再利用）
   - ユニットテストは残す（リグレッション防止）
3. **新 DB の該当テーブルは残すが使わない**
   - migration は rollback しない（破壊的操作を避ける）
   - 次回リトライ時に既存テーブルを活用
4. **旧 DB への書込を再開**
   - Wave 実施前の状態に戻る
5. **事後分析 (postmortem)**
   - `docs/phase4/postmortem-wave-<N>-<date>.md` に原因・対応・再発防止を記録
   - 次回リトライのエントリ条件に「postmortem で指摘された課題が解消」を追加

---

## 9. Wave 1（Phase 4）の詳細タスク

本 ADR 採択直後に着手する Wave 1 のタスクを具体化する。

### 9.1 ドメイン再設計（ADR-005 / ADR-006 準拠）

- `Rucaro\Domain\Journal\Journal` 集約ルート
  - 不変条件: 借方合計 = 貸方合計
  - 値オブジェクト: `Amount`, `AccountTitleId`, `JournalDate`, `TaxRate`
- `Rucaro\Application\Journal\*UseCase`
  - `CreateJournalUseCase`, `UpdateJournalUseCase`, `DeleteJournalUseCase`, `SearchJournalUseCase`
- `Rucaro\Application\Journal\Ports\JournalRepositoryInterface`
- `Rucaro\Infrastructure\Database\Repository\PdoJournalRepository`
- `Rucaro\Http\Api\V1\JournalController`
- `Rucaro\Domain\TrialBalance\TrialBalanceSnapshot`（read model）
- `Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase`

### 9.2 Migration: `trial_balance_snapshot` テーブル追加

`scripts/migrate/0005_trial_balance_snapshot.sql`（仮）:

- 列: `id` (UUIDv7), `entity_id`, `fiscal_period`, `account_title_id`, `sub_account_title_id nullable`, `debit_total`, `credit_total`, `snapshot_date`, `created_at`
- 索引: `(entity_id, fiscal_period, snapshot_date)`, `(account_title_id, snapshot_date)`
- 文字コード: utf8mb4, ENGINE=InnoDB
- PK: `id`
- 外部キー: `entity_id` → `entities.id`, `account_title_id` → `account_titles.id`

実 DDL は ADR-002 の命名規則に揃えて別途確定する。

### 9.3 旧 DB からの仕訳インポータ（必要時のみ実装）

`bin/cowork import:legacy journal --from <旧 DB 接続情報> [--fiscal-period YYYY-MM-MM] [--dry-run]`:

- 旧 `accountingLog` から CSV dump
- `journal_entries` + `journal_lines` に UPSERT
- 旧 ID → 新 ID のマッピングを `legacy_id_map` テーブルに記録（将来の再インポート用）
- ログ: `storage/logs/import-legacy-journal-<timestamp>.log`

### 9.4 Golden Test

**Wave 1 では部分的に skip**する:

- 理由: 本計画の対象環境では旧アプリがローカル稼働しておらず、旧 DB の live snapshot が取得できない
- 代替: 既に取得済みの旧 DB dump（`legacy-schema.md` で定義されている 59 テーブル）の静的スナップショットを fixture として使い、新 DB に投入した結果が静的に期待値と一致することのみを検証
- 本格的な旧アプリ稼働 → golden 比較は **Wave 2 で取り組む**（Wave 2 は Wave 1 の上に乗るため、Wave 1 の数値が正しいことを前提にできる）

### 9.5 Wave 1 テストスイート

| 種別 | 対象 | ツール |
|------|------|--------|
| Unit | `Journal`, `Amount`, `JournalDate`, UseCase, Query | PHPUnit |
| Property-based | 借方合計 = 貸方合計の不変条件 | PHPUnit + eris（Phase 4 で導入検討） |
| Integration | `PdoJournalRepository`（MariaDB コンテナ） | PHPUnit + testcontainers or 専用 compose |
| E2E | `POST /api/v1/journals` → `GET /api/v1/trialBalance` 往復 | Playwright |
| Performance | 10 万仕訳で試算表計算 < 2 秒 | PHPBench（Phase 4 で導入検討） |

カバレッジ目標: 80% 以上。

---

## 10. 結果 (Consequences)

### 10.1 Pros

- **リスク逓減型の移行**: Wave 単位で区切り、各 Wave でエントリ / エグジット条件を明確化することで、障害が起きても影響範囲を局所化できる
- **旧アプリ温存**: 検証期間中は旧アプリで日常業務を継続可能。バックアップとしての価値も長期的に残る
- **DB 独立構築**: スキーマ設計の自由度が最大。レガシー制約（`Jpn` サフィックス、JSON longtext、index 欠損、論理削除パターン）を持ち込まずに済む
- **Feature flag による即座のロールバック**: `.env` 一行の変更で旧経路に戻せる
- **Golden test 戦略**: 数値整合性を機械的に検証でき、主観評価を排除できる
- **認証問題の断絶**: 旧認証の CRITICAL な脆弱性（unsalted SHA-256、平文パスワード蓄積）を新 API に持ち込まない

### 10.2 Cons / トレードオフ

- **全機能の新旧二重保持期間が長期化する**: 本計画は期限なしのため、Wave 5 まで完了するまで数年かかる可能性あり。その間、ドキュメント / メンタルモデルが二重化する
- **新旧 DB 同期の運用負荷**: Wave 1 開始から全 Wave 完了までの期間、旧 DB からのワンショットインポートを随時行う必要があり、インポータの保守が継続的に発生する
- **feature flag の数が増える**: 全 Wave で 14+ flag を .env に持つ。設定ミスのリスクあり。 → Wave 完了後は削除する運用を §12 のチェックリストに含める
- **ロールバック時の投資損失感**: Wave 1 で 60h 投入して rollback した場合、心理的コストが高い。対策として「rollback は恥ではなく、学習成果を postmortem に残す文化」を敷く必要がある
- **旧アプリの凍結コードが残り続ける**: `back/` 以下 432 ファイルは全 Wave 完了後も削除しない。レポジトリサイズの増大と、ブランチ保護での誤触防止運用が必要

---

## 11. 代替案と却下理由

### 11.1 ビッグバン書き換え（全機能を一気に新側に移行）

- **利点**: 新旧並走期間が最短、feature flag や同期戦略が不要
- **却下理由**:
  1. 368 クラス / 59 テーブル / 数百 Smarty テンプレを同時移行する工数が見積もり不能
  2. 数値整合性を golden test で検証する際、差分の原因特定が不可能（どの計算が崩れたか切り分けられない）
  3. 個人運用・期限なしのプロジェクトで、リスクの大きい一括切替を選ぶ合理性がない

### 11.2 旧アプリを PHP 5 のまま維持

- **利点**: 移行工数ゼロ
- **却下理由**:
  1. PHP 5 は 2019 年にサポート終了済み（セキュリティパッチなし）
  2. PHP 7.4 も 2022 年に EOL、現環境は既に PHP 8.3 前提
  3. 本プロジェクトの動機（PHP 8.3 + MariaDB 10 への近代化、領収書 AI パイプライン追加）が達成できない
  4. composer エコシステム（Monolog, Guzzle, Claude Client）が使えない

### 11.3 Symfony / Laravel に全面移行

- **利点**: フレームワークの恩恵（Eloquent, Blade, ルーティング, DI, テスト）
- **却下理由**:
  1. ADR-001 §11.1 / §11.2 で詳細に却下済み（Eloquent と DDD の不整合、フレームワーク追従の継続負担）
  2. 本計画のスコープ（個人運用、期限なし）でフルフレームワーク採用は過剰
  3. `nikic/fast-route` + `guzzlehttp/psr7` の組合せで必要機能は賄える

### 11.4 マイクロサービス化（機能単位で別プロセスに切り出す）

- **利点**: 各 Wave を独立デプロイ可能
- **却下理由**:
  1. ローカル使用前提のため、独立デプロイの恩恵がない
  2. プロセス間通信・サービス発見・分散トランザクションのオーバーヘッドが純増
  3. モノリス内の境界（Domain 層）で十分分離可能

### 11.5 旧 DB を新 DB に merge して master にする

- **利点**: データ移行が不要
- **却下理由**:
  1. 旧スキーマの問題（index 欠損、longtext JSON、ENGINE 統一なし、外部キーゼロ、`Jpn` サフィックス）を持ち込むことになる
  2. ADR-002 で定義する新スキーマ方針（utf8mb4 既定、snake_case、ULID）と互換性がない
  3. 再暗号化バッチ不要（PLAN.md §0 確定）との整合で、旧 Blowfish CBC の列と新 AES-256-GCM の列を同居させる必要があり、複雑度が跳ね上がる

---

## 12. 実装チェックリスト（Wave 1 キックオフ時）

Wave 1 着手前に以下を全て満たすこと。

- [ ] ADR-005（Layered Architecture）と ADR-006（Ports & Adapters）が起草され、ユーザ承認済み
- [ ] ADR-002 の新 DB スキーマに `journal_entries`, `journal_lines`, `trial_balance_snapshot` の DDL が確定済み
- [ ] `.env.example` に Wave 1 の feature flag 2 つ（`FEATURE_JOURNAL_V2`, `FEATURE_TRIAL_BALANCE_V2`）を追加
- [ ] `src/Support/FeatureFlags.php` の実装と Unit test が完了
- [ ] `scripts/migrate/0005_trial_balance_snapshot.sql`（仮番号）の migration が作成済みで `bin/migrate` で適用可能
- [ ] `src/Domain/Journal/`, `src/Application/Journal/`, `src/Infrastructure/Database/Repository/`, `src/Http/Api/V1/` の空ディレクトリが準備済み
- [ ] `tests/Unit/Domain/Journal/JournalTest.php` のサンプルが RED → GREEN で動作（TDD 立ち上げ確認）
- [ ] `tests/Integration/Journal/PdoJournalRepositoryIntegrationTest.php` の MariaDB 接続テストが green
- [ ] `docs/api/openapi.yaml` に `/api/v1/journals` と `/api/v1/trialBalance` のエンドポイント定義が追加済み
- [ ] Monolog にレガシー経路監視用のチャンネル（`legacy-access`）が設定され、`Rucaro\Infrastructure\Legacy\*` 呼出がログに残る
- [ ] `bin/cowork import:legacy journal --dry-run` の骨組みが存在（実装は必要時まで空でよい）
- [ ] PHPStan / Psalm / PHP-CS-Fixer / PHPUnit が全て green
- [ ] ユーザが本 ADR および ADR-005 / ADR-006 を承認し、Wave 1 開始を決定

上記チェックリストを満たしたら Wave 1 着手を宣言し、`docs/phase4/wave1-kickoff.md` に着手日・想定完了時期・担当エージェント割当を記録する。

---

## 付録 A: Wave 退役順序の依存グラフ

```
                    Wave 1: Journal + TrialBalance
                            │
                            ▼
                    Wave 2: Ledger + FS (BS/PL/CS/Multi)
                            │
                ┌───────────┴───────────┐
                ▼                       ▼
      Wave 3: Receipt + FixedAssets   (Wave 2 で枯れた
      (Journal UseCase を利用)          FS 集計を活用)
                │
                ▼
      Wave 4: Budget / BlueSheet / BreakEvenPoint /
              ConsumptionTax / Entity 管理 UI
                │
                ▼
      Wave 5: 銀行連携 / メール取込 / 現金出納帳
              (Adapter 差し替え主体)
```

- Wave 1 は全ての基礎。ここを失敗させない
- Wave 2 と Wave 3 は Wave 1 の上に並列で乗せ得るが、工数上は逐次実施
- Wave 4 / Wave 5 は本計画では「将来」として時期を明示しない

---

## 付録 B: Feature Flag 一覧と対応クラス

| Flag | Wave | 旧クラス（抜粋） | 新エンドポイント |
|------|------|------------------|------------------|
| `FEATURE_JOURNAL_V2` | 1 | Jpn_Log, Jpn_LogEditor, Jpn_LogBack, Jpn_LogDelete, Jpn_LogPermit | `/api/v1/journals` |
| `FEATURE_TRIAL_BALANCE_V2` | 1 | Jpn_TrialBalance, Jpn_TrialBalanceOutput | `/api/v1/trialBalance` |
| `FEATURE_LEDGER_V2` | 2 | Jpn_Ledger | `/api/v1/ledger` |
| `FEATURE_FS_V2` | 2 | Jpn_FinancialStatement, FS_CS, FS_Multi, FS_Output | `/api/v1/financialStatements` |
| `FEATURE_RECEIPT_PIPELINE` | 3 | (新規) | `/api/v1/receipts`, `/api/v1/approvals/{token}` |
| `FEATURE_FIXED_ASSETS_V2` | 3 | Jpn_FixedAssets, calcDep/* | `/api/v1/fixedAssets` |
| `FEATURE_BUDGET_V2` | 4 | Jpn_Budget | `/api/v1/budget` |
| `FEATURE_BLUE_SHEET_V2` | 4 | Jpn_BlueSheet | `/api/v1/blueSheet` |
| `FEATURE_BREAK_EVEN_POINT_V2` | 4 | Jpn_BreakEvenPoint | `/api/v1/breakEvenPoint` |
| `FEATURE_CONSUMPTION_TAX_V2` | 4 | Jpn_ConsumptionTax | `/api/v1/consumptionTax` |
| `FEATURE_ENTITY_V2` | 4 | Entity, EntityEditor | `/api/v1/entities` |
| `FEATURE_BANK_V2` | 5 | Jpn_Banks, Jpn_CalcBanks_* | `/api/v1/banks` |
| `FEATURE_MAIL_IMPORT_V2` | 5 | MailImport 系 | `/api/v1/mailImports` |
| `FEATURE_CASH_V2` | 5 | Jpn_Cash, Jpn_CashEditor, Jpn_CalcCash_* | `/api/v1/cash` |

Wave 完了後は該当 flag を削除し、コードから条件分岐を取り除くこと（`refactor-cleaner` エージェントで Wave GA 後 1 ヶ月を目安に実施）。
