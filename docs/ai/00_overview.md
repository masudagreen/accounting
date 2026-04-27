# RUCARO Accounting アプリ全体概要

## 1. アプリの素性

- **元プロジェクト名**: RUCARO Accounting (rucaro.org)
- **ライセンス**: GPL v2
- **元の作者**: rucaro.org（OSSとして公開後、保守停止）
- **対象国/言語**: 日本（`jpn`）/ 日本語（`ja`）— `back/dat/nation/`、`back/dat/lang/` で多国・多言語前提だが実装は日本のみ
- **想定法人**: 法人 / 個人一般 / 個人不動産 / 個人農業（`accountingEntityJpn.flagCorporation` = 1〜4）
- **会計年度書式**: `numYearSheet`（既定 `2012`）— 2012年度様式の決算書をベースに実装。
- **対象基準**: 日本基準（J-GAAP）。テーブル列名・JSONキーに `Jgaap` プレフィクスが頻出。

## 2. ランタイム構成

- **PHP**: PDO + Smarty テンプレート（`back/class/else/lib/Smarty/`）
- **DB**: MySQL / MariaDB（PDO `mysql` ドライバ）。`InnoDB` 固定。接続情報は `back/dat/db/connect.cgi`（CSV）で `master / slave / log` の3チャンネル定義されているが、実体は同一DB。
- **キャッシュ**: APC (`FLAG_APC` 経由) — 将来 OPcache + apcu に置換すべき。
- **Docker**: Apache + PHP（`docker/Dockerfile`、`docker/apache/000-default.conf`、`docker-compose.yml`）。`composer install` を Docker ビルド時に実行する運用に切替済み（前セッション）。

## 3. ディレクトリ構成（最上位）

```
.
├── index.php                 # エントリポイント (Web UI)
├── api.php / output.php /
│   confirm.php               # 専用エントリ (API / 出力 / メール確認)
├── back/
│   ├── class/else/
│   │   ├── core/             # フレームワーク共通 (login/confirm/base)
│   │   ├── lib/              # 汎用ユーティリティ (Db, Smarty, Crypte 等)
│   │   ├── config/           # ランタイム設定
│   │   └── plugin/accounting # 会計プラグイン本体
│   │       ├── *.php         # 共通(国非依存)モジュール
│   │       └── jpn/          # 日本固有モジュール
│   │           ├── *.php
│   │           ├── 2012/     # 2012年度様式専用クラス
│   │           ├── api/      # 外部API向けエンドポイント
│   │           ├── calcBanks/# ネットバンク取込パーサ (5行)
│   │           ├── calcDep/  # 減価償却計算法 (定額/定率/級数法/任意 等)
│   │           ├── calcTempNext/ # 仮繰越/繰越処理
│   │           └── portal/   # 各機能の翌期繰越ロジック
│   ├── dat/
│   │   ├── db/connect.cgi    # DB接続定義
│   │   ├── lang/, nation/    # 言語/国マスタ
│   │   ├── version/          # バッチ移行スクリプト (Batch13700〜14800)
│   │   ├── log/              # 月次の運用ログ (.cgi)
│   │   └── content/list.csv  # MIMEマップ
│   └── tpl/
│       ├── templates/        # Smartyテンプレート + DBスキーマ定義
│       │   └── else/plugin/accounting/db/config.php  # 会計プラグインDDL
│       └── vars/             # テンプレート変数 (画面ツリー定義含む)
├── front/
│   └── else/                 # JS / CSS / 画像
│       ├── core/             # コア共通 JS
│       ├── lib/              # ライブラリ (Smarty等)
│       └── plugin/accounting # 会計プラグイン UI
├── docker/                   # Docker関連
└── docs/ai/                  # 本ドキュメント
```

## 4. リクエストルーティング

`index.php` で `Code_Else_Core_Base_Init` を起動し、リクエストパラメータに応じて以下に分岐する。

| クエリ | 例 | 起動クラス |
|--------|----|-----------|
| 未ログイン | — | `Code_Else_Core_Login_Login` |
| `class=Plugin&module=accounting` | プラグイン本体 | `Code_Else_Plugin_Accounting_Accounting` → `Code_Else_Plugin_Accounting_Jpn_Jpn` |
| `class=Plugin&module=accounting&ext=Log` | 仕訳帳画面 | `Code_Else_Plugin_Accounting_Jpn_Log` |
| `class=Plugin&module=accounting&ext=Log&func=DetailEdit` | 仕訳更新イベント | `Code_Else_Plugin_Accounting_Jpn_LogEditor::_iniDetailEdit` |
| `class=Base` | 管理 (アカウント等) | `Code_Else_Core_Base_Base` |
| `confirm.php` | メール確認/2段階認証 | `Code_Else_Core_Confirm_*` |

イベントは原則 `func` 値の `_ini<Func>()` メソッドで実行される。命名規則：`Detail*` は単票編集、`List*` は一覧操作、`Navi*` はナビ（フォーム/フォルダ）操作。

## 5. プラグインアーキテクチャ

会計プラグインは「コア(国非依存)層」と「`jpn` 層」に分離されている。

| 層 | 役割 | 主要クラス |
|---|------|-----------|
| コア | 事業体・アカウント・権限・証憑ファイル等の共通CRUD | `Entity`, `Account`, `Authority`, `Access`, `File`, `AccountEntity` 系 |
| jpn | 仕訳・元帳・決算・申告等、日本基準の処理 | `Log`, `Cash`, `Banks`, `FixedAssets`, `Ledger`, `TrialBalance`, `FinancialStatement*`, `Budget`, `BreakEvenPoint`, `BlueSheet`, `SummaryStatement`, `DetailedAccount*`, `NotesFS` |
| jpn/2012 | 2012年度様式の固有実装（青色申告/勘定科目内訳明細書/法人事業概況説明書） | `2012/public/*`, `2012/detailedAccount/*`, `2012/summaryStatement/*`, `2012/consumptionTax/*` |

## 6. 主要グローバル変数

`back/class/else/plugin/accounting/Init.php` 起動時にロードされ、以後グローバル参照される。

- `$varsAccount` — 現在のアカウント
- `$varsPluginAccountingAccount` — `accountingAccount` 行（idEntityCurrent / numFiscalPeriodCurrent 等）
- `$varsPluginAccountingEntity` — 全事業体マップ
- `$varsPluginAccountingPreference` — 全社設定
- `$varsPluginAccountingAuthority` — 権限パターン
- `$varsPluginAccountingAccess` — アクセス可能項目パターン
- `$varsPluginAccountingAccountsEntity` — `accountingAccountEntity` 多次元マップ
- `$varsPluginAccountingAccountsId` — アカウントIDコードマップ

## 7. ドキュメントの読み方

| ファイル | 内容 |
|----------|------|
| `00_overview.md` | （本ファイル）アプリ全体像 |
| `01_tables.md` | テーブル定義一覧（会計用語訳付き） |
| `02_screens_features.md` | 機能/画面一覧（移植要否の判断材料） |
| `03_reports.md` | 帳票一覧（出力ファイル/PDF） |
| `04_events.md` | イベント一覧（画面操作 ⇄ CRUD 対応表） |
