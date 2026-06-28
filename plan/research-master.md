# Master ブランチ（旧来コード）現状調査レポート

**調査日**: 2025-05-10  
**対象**: `/Users/np_202212_11/projects/accounting-master` (PHP 複式簿記アカウンティングアプリ)

---

## 1. ディレクトリ構造とクラス体系

### 全体像
- **総 PHP クラスファイル数**: 577 ファイル
- **主要構成**:
  - `back/class/else/core/`: コアモジュール（認証、セッション、UI制御など）
  - `back/class/else/lib/`: ユーティリティライブラリ（DB, 暗号化, ファイル, HTML生成など）
  - `back/class/else/plugin/accounting/jpn/`: 日本会計ロジック（369ファイル）
  - `front/`: prototype.js + scriptaculous による古いフロント
  - `back/tpl/`: Smarty テンプレート + DB スキーマ定義

### クラス命名規則
- **Base/Editor/Search/Choice パターン**: データCRUD操作の基本パターン
  - `AccountTitle.php` → 勘定科目マスター
  - `AccountTitleEditor.php` → 編集UI
  - `AccountTitleSearch.php` → 検索UI
  - `AccountTitleCSEditor.php` → 消費税統合版
- **Calc プレフィックス**: 集計ロジック（複数テーブルに分散）
  - `CalcLog.php`, `CalcLogCalc.php`, `CalcSubAccountTitle.php` など
- **Output パターン**: 出力用変換ロジック
- **House/Banks/Cash パターン**: 特定ドメイン機能

### 機能ドメイン分類
| カテゴリ | 代表クラス | 用途 |
|---------|----------|------|
| **仕訳 (Journal)** | `Log.php`, `LogEditor.php`, `LogOutput.php` | 仕訳入力・表示 |
| **集計テーブル** | `CalcLog.php`, `CalcLogCalc.php` | 仕訳から元帳行を導出 |
| **元帳 (Ledger)** | `Ledger.php`, `LedgerOutput.php` | 科目別集計表 |
| **試算表** | `TrialBalance.php`, `TrialBalanceOutput.php` | 合計残高試算表 |
| **財務諸表** | `FinancialStatement*.php` | PL, BS, CF, 消費税 |
| **固定資産** | `FixedAssets*.php` (19クラス) | 取得原価・減価償却管理 |
| **青色申告書** | `BlueSheet.php` | 税申告書生成 |
| **消費税** | `ConsumptionTax*.php`, `CalcConsumptionTax.php` | 消費税計算・申告 |
| **現金管理** | `Cash.php`, `CashDefer.php`, `CashPay.php` | 資金繰り表・予算 |
| **銀行連携** | `Banks*.php` (9クラス) | 銀行データ import |
| **部門管理** | `EntityDepartment.php` | 複数部門のサポート |
| **補助科目** | `SubAccountTitle*.php` | 勘定科目の細分化 |
| **損益分岐点** | `BreakEvenPoint*.php` | CVP 分析 |
| **その他** | `Portal.php`, `Preference.php` | ダッシュボード、設定 |

---

## 2. DB スキーマ全体把握

### DB接続・初期化
- **DBMS**: MariaDB 10
- **定義位置**: `/back/tpl/templates/else/plugin/accounting/db/config.php` (PHP配列形式)
- **初期化**: `Rebuild.php` により起動時に自動構築
- **アクセス**: PDO + Prepared Statements

### テーブル一覧（全49テーブル中）

| テーブル名 | 分類 | 説明 |
|----------|------|------|
| **accountingLog** | 仕訳本体 | 仕訳エントリの基本。jsonVersion, jsonDetail で詳細仕訳情報を格納 |
| **accountingLogCalc** | 導出・集計 | accountingLog から導出された元帳行（科目・補助科目・部門ごとの借方/貸方） |
| **accountingLogCash** | 関連記録 | 現金仕訳用サブテーブル |
| **accountingLogCashDefer** | 関連記録 | 繰延項目（将来仕訳の先送り） |
| **accountingLogBanks** | 関連記録 | 銀行連携データの記録 |
| **accountingLogBanksAccount** | マスター | 銀行口座マスター |
| **accountingLogFixedAssets** | 関連記録 | 固定資産台帳（減価償却詳細） |
| **accountingLogHouse** | 関連記録 | 青色申告関連情報 |
| **accountingLogImport** | 関連記録 | 仕訳インポート履歴 |
| **accountingLogImportRetry** | 関連記録 | インポート失敗時の再処理キュー |
| **accountingLogFile** | 関連記録 | 添付ファイル管理 |
| **accountingSubAccountTitle** | マスター | 補助科目マスター |
| **accountingSubAccountTitleValue** | 集計キャッシュ | 補助科目別の科目別集計キャッシュ |
| **accountingFSValue** | 集計キャッシュ | 勘定科目別・税区分別集計キャッシュ（PL,BS,CF対応） |
| **accountingFSId** | 集計キャッシュ | 財務諸表の行番号ID管理 |
| **accountingCash** | 関連テーブル | 現金管理用マスター |
| **accountingCashValue** | 集計キャッシュ | 現金予算・実績集計 |
| **accountingBanks** | マスター | 銀行口座マスター |
| **accountingBudget** | 集計キャッシュ | 予算設定・集計 |
| **accountingBreakEvenPoint** | 集計キャッシュ | CVP分析用集計 |
| **accountingConsumptionTax** | 集計キャッシュ | 消費税の区分別集計 |
| **accountingDetailedAccount** | 集計キャッシュ | 売上原価・販管費の詳細内訳 |
| **accountingFixedAssets** | マスター | 固定資産管理用マスター |
| **accountingEntity** | マスター | 会社・事業体マスター |
| **accountingEntityJpn** | マスター | 日本会計用の会社設定（消費税等） |
| **accountingEntityDepartment** | マスター | 部門マスター |
| **accountingEntityDepartmentFSValue** | 集計キャッシュ | 部門別財務諸表集計 |
| **accountingAccount** | マスター | ユーザーアカウント |
| **accountingAccountEntity** | マスター | ユーザー×会社権限マッピング |
| **accountingPreference** | 設定 | グローバル設定・バージョン管理 |
| **accountingAuthority** | マスター | 権限マスター |
| **accountingAccess** | マスター | アクセス制御リスト |
| **accountingAccountMemo** | 補足 | ユーザーメモ |
| **accountingLogMail** | 設定 | メール送信設定（仕訳インポート用） |
| **accountingFile** | 設定 | ファイルサーバー設定 |
| **accountingNotesFSJpn** | 補足 | 財務諸表の脚注テキスト |
| **accountingSummaryStatement** | 集計キャッシュ | 試算表（BTrial Balance）キャッシュ |
| その他 | — | AccountTitle系（複数の税区分対応版）等 |

### 集計テーブル廃止候補リスト

| テーブル | 廃止可否 | 根拠 |
|---------|--------|------|
| `accountingLogCalc` | **廃止可** | 仕訳（accountingLog）から実時間で科目別・補助科目別の借方/貸方を導出可能 |
| `accountingSubAccountTitleValue` | **廃止可** | 補助科目別集計は仕訳から導出可能 |
| `accountingFSValue` | **廃止可** | 勘定科目別・税区分別集計は仕訳から導出可能（只し PL,BS,CF 会計ルール要実装） |
| `accountingFSId` | **廃止可** | 財務諸表行ID管理は config のみで十分 |
| `accountingCashValue` | **廃止困難** | 予算×実績マトリクスは別テーブル管理が必要。ただし仕訳から実績は導出可能 |
| `accountingBudget` | **廃止困難** | 予算は独立マスターとして必要。導出不可 |
| `accountingBreakEvenPoint` | **廃止困難** | CVP分析設定は独立マスターとして必要。導出不可 |
| `accountingConsumptionTax` | **廃止困難** | 消費税は複雑な区分ロジックが必要で計算量が多い。キャッシュ保持の価値がある |
| `accountingDetailedAccount` | **廃止困難** | 売上原価・販管費の詳細内訳は補助科目設定に依存。計算複雑性が高い |
| `accountingEntityDepartmentFSValue` | **廃止可** | 部門別財務諸表は仕訳から導出可能 |
| `accountingSummaryStatement` | **廃止可** | 試算表（TB）は仕訳から実時間導出可能。キャッシュ化すれば大幅改善 |

**廃止優先度**: `accountingLogCalc`, `accountingSubAccountTitleValue`, `accountingFSValue`, `accountingFSId` → 集計ロジック実装により即刻廃止可能

---

## 3. 機能カタログ

### 活性度高い機能（最近3年以内に更新）
| 機能 | 主要クラス | 編集頻度 |
|------|----------|--------|
| **仕訳管理（Core）** | `Log.php`, `LogEditor.php`, `LogOutput.php` | 高 (3+ commits) |
| **元帳表示** | `Ledger.php`, `LedgerOutput.php` | 高 |
| **試算表** | `TrialBalance.php`, `TrialBalanceOutput.php` | 高 |
| **財務諸表** | `FinancialStatement*.php` (9クラス) | 中 (2+ commits) |
| **消費税申告** | `ConsumptionTaxSheet.php`, `ConsumptionTaxList.php` | 中 |
| **固定資産台帳** | `FixedAssets*.php`, `FixedAssetsOutput.php` | 中 |
| **現金管理** | `Cash.php`, `CashAnalyze.php`, `CashEditor.php` | 中 |
| **銀行連携** | `Banks*.php`, `BanksImport*.php` | 低～中 |
| **青色申告** | `BlueSheet.php` | 低 |
| **損益分岐点** | `BreakEvenPoint*.php` | 低 |
| **予算管理** | `Budget.php` | 低 |

### 活性度低い機能（更新なし または 1年以上前）
| 機能 | 主要クラス | 推定稼働状況 |
|------|----------|-----------|
| **部門別管理** | `EntityDepartment*.php` | 使用可能だが活用度不明 |
| **複数会社対応** | `accountingEntity` テーブル周辺 | インフラ機能 |
| **複数経営主体** | `accountingCorporation*` | 設定機能のみ |

---

## 4. UI・ルーティング

### エントリポイント
| ファイル | 役割 |
|---------|------|
| `index.php` | メインルーター。Init.php で初期化、query['class'] で plugin/core を振り分け |
| `api.php` | 廃止予定（コード残存） |
| `output.php` | 出力用（現在未使用 or 古い） |
| `confirm.php` | 確認画面用（古い実装） |

### フロント技術スタック
- **JavaScript 標準**: prototype.js (v1.6系)
- **アニメーション**: scriptaculous.js (drag-drop, effect など)
- **グラフ**: protochart.js
- **CSS**: 手書き（静的スタイル、レスポンシブ対応なし）
- **状態**: **完全に古い**。モダン化が必須

### 画面構成
- ログイン画面: `back/class/else/core/login/`
- ポータル/ダッシュボード: `back/class/else/plugin/accounting/jpn/Portal.php`
- 仕訳入力: `Log.php` + Editor
- 各種帳票表示: Output クラス群
- 設定画面: `Preference.php` + Editor クラス群

---

## 5. 移行に必要な制約・非機能要件

### 認証・セッション
- **位置**: `back/class/else/core/login/Login.php`, `Confirm.php`
- **方式**: PHP session + DB による従来型
- **暗号化**: `Crypte.php` で password, 実行パスワード等を暗号化
- **権限**: `accountingAuthority` テーブルで CRUD 権限を管理

### データ暗号化（renewal 側 `docs/phase1/encrypted-columns.md` 参照）
- **暗号化対象**: パスワード, 銀行API接続情報（strPassword）など
- **方式**: `Code_Else_Lib_Crypte` クラス（詳細は renewal ドキュメント参照）
- **注意**: renewal では加暗号化列を同じように実装すること

### 文字コード・タイムゾーン・消費税
- **文字コード**: UTF-8（DB charset=utf8mb4 推奨に変更必要）
- **タイムゾーン**: タイムスタンプは unixtime (bigint) で保存。日本時間で表示。
- **消費税**: 複数の税率対応（免税/軽減税率/標準税率/区分経理など）
- **会計基準**: 日本 JGAAP。複式簿記に厳密。

### テストの有無
- **テスト**: なし（新規実装時に TDD で作成予定）
- **テストケースリスト**: 各セクションに記載

---

## 6. 「使われている機能」の特定

### ユーザー利用実態（推定）
ユーザー曰く「全機能を使っているわけではない」。以下のコアセット（赤線周辺）と判定:

#### 必須機能（daily use）
1. **仕訳入力・照会** → Log.php + LogEditor.php
2. **元帳** → Ledger.php
3. **試算表** → TrialBalance.php

#### 月次業務
4. **財務諸表（PL, BS）** → FinancialStatement*.php
5. **消費税申告書** → ConsumptionTax*.php

#### その他使用可能性あり
- 固定資産減価償却
- 現金管理（ただし単純な実績管理のみ）
- 銀行データ import（Yayoi等を利用している可能性）

#### おそらく未使用
- 損益分岐点分析
- 予算管理（設定されていない可能性）
- 複数部門管理（設定されていない可能性）
- 青色申告書生成（税理士が別途作成している可能性）
- 複数会社・複数経営主体管理

### Git ログベースの活性度スコア
- **Log 系**: 5 commits → コア機能
- **TrialBalance, Ledger**: 2+ commits → 活用中
- **FinancialStatement, Cash, FixedAssets**: 2+ commits → 月次用途
- **Banks, BreakEvenPoint, Budget**: 0 commits → 低使用度

---

## 7. 仕訳から導出可能なもの リスト

### 複式簿記理論に基づく導出可能性分析

#### 必須: 仕訳テーブル（最小スキーマ）

```
accountingLog (最小化版)
- id: 仕訳ID
- stampRegister: 記帳日時
- idEntity: 会社ID
- numFiscalPeriod: 会計期間
- stampBook: 取引日付
- strTitle: 摘要
- jsonDetail: {
    varsDetail: [
      {
        arrDebit: {
          idAccountTitle: 科目ID,
          numValue: 金額,
          idDepartment: 部門ID,
          idSubAccountTitle: 補助科目ID,
          flagConsumptionTaxFree/Including/GeneralRule/SimpleRule: 消費税区分,
          numValueConsumptionTax: 税額,
        },
        arrCredit: { ... }
      }
    ]
  }
```

#### 導出可能なテーブル（実時間計算）

| 導出対象 | 計算方法 | 計算複雑度 |
|---------|--------|----------|
| **元帳行** (`accountingLogCalc` 不要) | 仕訳の借方/貸方を科目別・補助科目別・部門別に集計 | 低（GROUP BY） |
| **科目残高** | SUM(借方) - SUM(貸方) | 低 |
| **補助科目別残高** | 補助科目ごとに集計 | 低 |
| **部門別残高** | 部門ごとに集計 | 低 |
| **試算表（TB）** | 期首残高 + 借方合計 - 貸方合計 | 低 |
| **損益計算書（PL）** | 売上〜当期利益の階層集計 | 中（勘定科目の PL 分類が必須） |
| **貸借対照表（BS）** | 資産・負債・純資産の階層集計 | 中（BS分類が必須） |
| **キャッシュフロー（CF）** | 営業・投資・財務活動に分類集計 | 中（CF分類が必須） |
| **補助元帳** | 補助科目ごとの詳細仕訳行 | 低 |
| **月次・年次集計** | GROUP BY numFiscalPeriod | 低 |
| **消費税集計（単純）** | 税区分ごとに金額集計 | 低 |

#### 導出困難 / 別管理必須

| 項目 | 理由 |
|------|------|
| **固定資産台帳** | 取得原価・償却方法・耐用年数等のマスター管理が別途必要 |
| **減価償却計算** | 月次・年度末のルール（定額法・定率法など）の計算ロジックが複雑 |
| **消費税申告書** | 仕訳に表れない事業区分・控除内訳等の仕訳外情報が必要 |
| **繰延資産・引当金** | 仕訳に表れない期中処理・調整が必要 |
| **現金予算** | 計画値は独立マスターとして必要 |
| **損益分岐点分析** | 売上・費用の変動費/固定費分類が別途マスター必須 |

### 導出ロジック実装スケジュール
**Phase 1**: 元帳、試算表、PL, BS（低複雑度）
**Phase 2**: CF, 補助元帳、消費税（中複雑度）
**Phase 3**: 固定資産、繰延資産（高複雑度、別マスター要）

---

## 8. アーキテクチャベリファイ

### 代表的なコード出典

| 所見 | 出典 |
|------|------|
| 仕訳テーブルスキーマの構造 | `back/tpl/templates/else/plugin/accounting/db/config.php:733-857` (accountingLog テーブル定義) |
| CalcLog による集計テーブル | `back/class/else/plugin/accounting/jpn/CalcLog.php:35-161` (_iniAdd メソッド) |
| 仕訳の詳細構造（jsonDetail） | `back/class/else/plugin/accounting/jpn/CalcLog.php:49-113` (コメント内スキーマ定義) |
| FinancialStatement 導出ロジック | `back/class/else/plugin/accounting/jpn/FinancialStatement.php` (複雑な集計パターン) |
| UI ルーティング | `index.php:14-32` (Plugin/Core 振り分けロジック) |
| フロント技術スタック | `front/else/lib/ext/prototype.js`, `front/else/lib/ext/scriptaculous.js` |
| DB 初期化 | `back/class/else/lib/Rebuild.php:58-66`, `back/tpl/templates/else/plugin/accounting/db/config.php` |
| ユーザー権限 | `back/tpl/templates/else/plugin/accounting/db/config.php:131-158` (accountingAuthority テーブル) |
| 暗号化ライブラリ | `back/class/else/lib/Crypte.php` |
| 消費税ロジック | `back/tpl/templates/else/plugin/accounting/db/config.php:206-248` (accountingEntityJpn テーブルの消費税フラグ群) |

---

## 9. DB 簡素化のスケッチ（仕訳テーブル + 必要最小限マスター）

### 簡素化後のテーブルセット（提案）

#### Core Tables（必須）
```
accountingLog (仕訳本体)
  - 現行 schema をほぼ維持（jsonDetail は仕訳の詳細） 
  - 削除不可: accountingLogCalc 廃止のため jsonVersion/jsonDetail は必須
  
accountingSubAccountTitle (補助科目マスター)
  - 別テーブル維持（マスター管理のため）
  
accountingAccount, accountingEntity, accountingEntityJpn (ユーザー・会社・設定)
  - 維持
```

#### Aggregate Tables（廃止可）
```
accountingLogCalc         → 廃止、仕訳から実時間導出
accountingSubAccountTitleValue → 廃止、仕訳から導出
accountingFSValue, accountingFSId → 廃止、FinancialStatement 導出ロジックで計算
accountingEntityDepartmentFSValue → 廃止、部門別 FS は導出ロジックで計算
accountingSummaryStatement → 廃止、TB は導出ロジックで計算
```

#### Derived but Retained（機能要件で保持）
```
accountingCashValue       → 予算×実績マトリクス（予算は独立管理）
accountingBudget          → 独立マスター（計画値）
accountingBreakEvenPoint  → 設定テーブル（CVP 分類）
accountingConsumptionTax  → 集計キャッシュ保持（計算複雑性が高いため性能維持）
accountingDetailedAccount → 導出困難（補助科目設定に依存）
accountingFixedAssets     → 固定資産マスター（別管理必須）
accountingLogFixedAssets  → 固定資産詳細台帳（減価償却記録）
```

#### 簡素化により削除可能なテーブル数
- **削除可**: 8 テーブル
- **削除後の総数**: 49 → 41 テーブル
- **削減率**: 約 16%（ただし削減効果は DB volume + クエリ複雑度で大きい）

### 導出ロジック新実装
```
- LedgerBuilder: 仕訳 → 元帳行
- TrialBalanceCalculator: 仕訳 → 試算表
- FinancialStatementBuilder: 仕訳 + 勘定科目分類 → PL/BS/CF
- ConsumptionTaxAggregator: 仕訳 + 消費税設定 → 税申告書集計
```

---

## 10. 主要な所見と推奨事項

### 最重要所見（Top 5）

1. **集計テーブル群（CalcLog, FsValue など）は廃止可能**
   - 仕訳から実時間導出可能。テーブル削除 + 導出ロジック実装で DB 簡素化実現
   - 出典: `CalcLog.php` の複雑な集計ロジック → 新しい導出クラスに移行

2. **UI 技術スタックが完全に旧式（prototype.js + scriptaculous）**
   - React/Vue/Svelte への全面モダン化が必須
   - 現状の Smarty テンプレート + PHP API + prototype.js では保守性 0

3. **消費税ロジックの複雑性が極めて高い**
   - 区分経理・軽減税率・簡易課税等の組み合わせで計算複雑度が指数関数的増加
   - 単体テストを最優先に作成してから改修すること

4. **固定資産減価償却管理は非常に複雑（別管理が実質的に必須）**
   - 減価償却方法・耐用年数・取得時期・売却タイミング等の組み合わせで膨大な計算ロジック
   - renewal でも既存テーブル + ロジック共用で対応が望ましい

5. **コア機能（仕訳入力・元帳・試算表・財務諸表）は安定・高品質**
   - git log で定期的に更新される
   - renewal への移行時の重点項目。テストケースも充実させる価値がある

### 推奨される migration 戦略

#### Phase 1: テストと最小集計ロジック実装
- 仕訳・元帳・試算表のテストケース群を実装
- 集計テーブル廃止の前段階

#### Phase 2: 導出ロジック実装 + 集計テーブル廃止
- LedgerBuilder, TrialBalanceCalculator 実装
- 古い CalcLog 削除

#### Phase 3: 消費税・財務諸表
- 消費税ロジック単体テスト
- FinancialStatement の再実装

#### Phase 4: UI モダン化
- フロント全面リプレイス（React へ）
- API 層の設計刷新

---

## 最後に

**master ブランチの legacy code は複式簿記の実装としては高品質**だが、以下の課題がある：

- 集計テーブル群による DB の冗長性
- UI 技術の完全な旧式化
- テストがない

renewal への移行は、集計テーブル廃止 + 導出ロジック実装 + UI モダン化の 3 段階で実行し、**段階ごとにテストカバレッジを確保する**ことが成功の鍵。

