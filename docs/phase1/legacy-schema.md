# Phase 1.1.a レガシースキーマ調査レポート

> Rucaro Accounting (日本語会計 Web アプリ / MySQL + PHP) の `back/dat/version/BatchXXXXX.php` を根拠にした、レガシー DB 現状調査レポート。
>
> 本書は `back/dat/version/*.php` (マイグレーション DDL) と `back/dat/version/*/templates/config.php` (テーブル定義配列) を一次情報として作成している。`back/dat/db/connect.cgi` で接続情報、`back/class/else/lib/Db.php` で DSN 文字コードを確認した。

---

## 1. 概要

| 項目 | 値 | 根拠 |
|------|----|------|
| 最新スキーマバージョン | **`14800`** | 最後に実行されるバッチは `Batch14800.php` (`numVersionThis => 14800`) |
| マイグレーションバッチ総数 | 9 (13700, 14100, 14110, 14200, 14300, 14301, 14310, 14311, 14800) | `back/dat/version/BatchXXXXX.php` の列挙 |
| 合計テーブル数 | **59** (base: 20, accounting: 39) | `'table' =>` のユニーク値を Batch13700/14200/14300 の config.php から集計 |
| DB エンジン | `InnoDB` (`type=InnoDB`) | すべての config.php `'db' => 'type=InnoDB'`。MySQL 5.5 以降を検出すると `str_replace('type', 'engine', ...)` で `ENGINE=InnoDB` に置換 (`Batch13700.php:99-101`, `Batch14200.php:185-187`, `Batch14300.php:153-155`, および `Db.php:checkVersion55()`) |
| テーブル文字コード / collation | **DDL で明示的に指定なし**。DB サーバのデフォルト (MySQL の場合 `latin1` or `utf8`) が採用される | どの config.php にも `CHARACTER SET` 句が無い |
| PDO 接続文字コード | **utf8** (PHP >= 5.3.6 のとき `charset=utf8` を DSN に追加) | `back/class/else/lib/Db.php:68, 99, 344` |
| DSN (master/slave/log) | `host=db, dbname=rucaro, username=rucaro, password=rucaro, driver=mysql` 三系統 | `back/dat/db/connect.cgi` |
| タイムゾーン | Asia/Tokyo (`PLUGIN_ACCOUNTING_NUM_TIME_ZONE = 9`) | `Batch14200.php:76-77`, `Batch14311.php:58-59`。アプリ側で `classTime->setTimeZone` 実行 |
| 日付系カラム | `bigint` (UNIX エポック秒) を採用。MySQL `DATETIME`/`TIMESTAMP` は未使用 | すべての `stampRegister`/`stampUpdate`/`stampBook` カラムが `bigint not null` |
| 文字コード運用実態 | 日本語データは UTF-8 を前提に PHP / PDO 経由で read/write。MySQL 側 collation は未固定 (リスク) | `Db.php` の `charset=utf8` だけが根拠。テーブル DDL 側には裏付けなし |

### 設計上のトップラインまとめ

- **完全な ORM は無い**。独自 DB ラッパー `Code_Else_Lib_Db` (`back/class/else/lib/Db.php`) が `getSelect` / `insertRow` / `updateRow` を提供し、SQL は「組み立て + PDO prepare」で発行。
- **外部キー制約は一切定義していない**。参照整合性はアプリ層のみ。
- **インデックスはほぼ PK のみ**。`create table` の index 配列には PK 以外の明示 INDEX が無い (全件 grep 検索で 0 件)。
- **JSON カラム多用**。仕訳・FS・消費税・権限履歴まですべて `longtext` に JSON を格納し、`jsonVersion` として監査ログ履歴も格納。
- **論理削除 (`flagRemove` + `stampRemove`)** は仕訳／ファイル／銀行ログ／固定資産ログ等で使用。
- **soft delete + JSON + PK のみ**という構成で、数千行を超えると重くなりやすい。

---

## 2. マイグレーションバッチ履歴

| Batch | バージョン | 主な変更点 | サブディレクトリ内容 |
|-------|-----------|-----------|------------------|
| Batch13700 | 13700 | `baseLoginSecond` テーブル作成 (drop→create)、`basePreference` / `baseAccount` に `flagLoginSecond` カラム追加 (2段階ログイン機能) | `Batch13700/templates/config.php` … base 系 19 テーブルの DDL 定義 |
| Batch14100 | 14100 | ファイル削除のみ。`back/dat/fla/pending.fla, cake.fla`、`front/else/lib/flash/pending.swf, cake.swf` と親ディレクトリ `fla/`, `flash/` を削除 (Flash 廃止) | なし |
| Batch14110 | 14110 | `basePreference` に `flagReject` カラム追加 (海外/接続拒否機能の初出) | なし |
| Batch14200 | 14200 | `accountingBlueSheetJpn` テーブル作成 (drop→create)、`accountingFixedAssetsJpn` に `numRatioOperateDepSum` カラム追加、`accountingFSJpn.jsonJgaapFSPL` の値変換 (`selfConsumption` の `flagDebit`=0)、勘定科目合計再計算 | `Batch14200/class/Accounting.php`、`Batch14200/class/jpn/{Jpn,CalcAccountTitle,CalcAccountTitleFS,CalcAccountTitleFSCS}.php`、`Batch14200/class/jpn/2012/public/*.php`、`Batch14200/class/jpn/2012/public/calcTempNext/*.php`、`Batch14200/templates/config.php` (accounting 系 39 テーブルの DDL 定義) |
| Batch14300 | 14300 | `baseAccessUnknown` テーブル作成 (drop→create)、`basePreference` に `flagAccessUnknownMail` カラム追加、`.htaccess` を `normal.cgi`/`foreign.cgi` に置換 (reject 設定反映) | `Batch14300/templates/config.php` (base 系 20 テーブル定義版)、`Batch14300/templates/normal.cgi`、`Batch14300/templates/foreign.cgi` |
| Batch14301 | 14301 | `.htaccess` の再置換のみ (14300 の雛形を差し替え)。スキーマ変更なし | `Batch14301/templates/normal.cgi`、`Batch14301/templates/foreign.cgi` |
| Batch14310 | 14310 | Smarty テンプレートエンジンのアーカイブ展開 (`Batch14310/Smarty.zip` を `back/class/else/lib/Smarty/` にデプロイ、キャッシュ/APC 再初期化)。**スキーマ変更なし** | `Batch14310/Smarty.zip` (バンドル済 Smarty 最新版) |
| Batch14311 | 14311 | FS 値 (`accountingFSValueJpn` / `accountingEntityDepartmentFSValueJpn`) の `jsonJgaapAccountTitleBS.profitBroughtForward` を前期から再計算して埋め直す。DDL 変更は**なし** (データ正規化のみ) | `Batch14311/class/Accounting.php`、`Batch14311/class/jpn/{Jpn,Portal,CalcAccountTitle,CalcConsumptionTax,CalcAccountTitleFS,CalcAccountTitleFSCS}.php`、`Batch14311/class/jpn/calcTempNext/{AccountTitle,AccountTitleCS,AccountTitleFS,AccountTitleFSCS,EntityDepartment,Log,SubAccountTitle}.php`、`Batch14311/class/jpn/portal/NextData.php`、`Batch14311/class/jpn/2012/public/*`、`Batch14311/vars/{FSItem,department,tax}.php` |
| Batch14800 | 14800 | `accountingLogCalcJpn` に `flagRateConsumptionTaxReduced` 追加、`accountingFSValueJpn` に `jsonConsumptionTaxDetail` 追加、既存 `jsonConsumptionTax` を `jsonConsumptionTaxDetail.varsOther` へコピー (軽減税率対応 / 2019-10-01) | なし (コードはすべて `Batch14800.php` 単一ファイルに内包) |

### 備考

- 13700 以前のバッチファイルはリポジトリに残っていない。13700 が実質的な「初期スキーマ (base 系 19 テーブル) のドロップ+再作成」を担う。14300 が `baseAccessUnknown` を追加して base 系が 20 に。
- `Batch14100`, `Batch14110`, `Batch14301`, `Batch14310` はスキーマ変更なしのバージョン番号消化バッチ。
- config.php に載っている `drop table if exists ... → create table ...` は **新規インストール時のみ** 実行される。既存運用中は `alter table ... add column` のみ適用。
- 14200 のテーブル定義 config.php だけは過去の全 accounting 系テーブル (39 個) を保持。新規インストール時にまとめて再作成する仕組み。

---

## 3. 最終スキーマ テーブル一覧

凡例:
- カラム数が多いテーブルは `<details>` で折りたたむ
- **PK**: `id` 列 (`bigint unsigned auto_increment, primary key(id)` or `int unsigned auto_increment, primary key(id)`)
- **インデックス**: config.php には PRIMARY KEY 以外のインデックス定義は**存在しない** (全件 grep 検索で 0 件)
- **外部キー**: 定義ゼロ (アプリ層で管理)
- **エンジン**: 全テーブル InnoDB (MySQL 5.5+ では `ENGINE=InnoDB`, 5.5 未満では `TYPE=InnoDB`)
- **文字コード**: DDL 明示なし、サーバデフォルト依存

### 3.1 base 系 (認証・システム共通) — 20 テーブル

| # | テーブル | 役割 | PK |
|---|---------|------|-----|
| 1 | basePreference | システム全体のプリファレンス (メンテナンス / IP 制限 / メール設定 / バージョン情報) | id bigint unsigned |
| 2 | baseAccount | ユーザーアカウント (ログイン情報 / パスワード暗号 / 言語 / タイムゾーン / 所属 Term・Module) | id bigint unsigned |
| 3 | baseAccountId | アカウント id と codeName の参照テーブル | **PK 宣言なし** |
| 4 | baseAccountMemo | アカウントに紐づくフリーフォーム JSON メモ | id bigint unsigned |
| 5 | baseTerm | 期間 (期) | id bigint unsigned |
| 6 | baseModule | インストール済モジュール (accounting など) | id bigint unsigned |
| 7 | baseAccessLog | ユーザー操作アクセスログ (ip / host / device / module / ext / func / query) | id bigint unsigned |
| 8 | baseAccessUnknown | 不明 IP アクセスログ (14300 で追加) | id bigint unsigned |
| 9 | basePublish | publish セッション (管理公開一時発行) | id bigint unsigned |
| 10 | baseLock | アカウントロック記録 | id bigint unsigned |
| 11 | baseApplySign | サインアップ申請 | id bigint unsigned |
| 12 | baseApplyChange | アカウント変更申請 | id bigint unsigned |
| 13 | baseApplyForgot | パスワード忘れ申請 | id bigint unsigned |
| 14 | baseApiAccount | 外部 API アクセスアカウント | id bigint unsigned |
| 15 | baseSession | ログインセッション (ip / idCookie / idAccount) | **なし** |
| 16 | baseLoginSecond | 2段階認証セッション (13700 で追加) | **なし** |
| 17 | baseToken | トークン (プッシュ用) | **なし** |
| 18 | baseLoginPassword | 過去パスワード履歴 (暗号化列 `strPassword` 有) | **なし** |
| 19 | baseLoginIdLogin | 過去ログイン ID 履歴 | **なし** |
| 20 | baseLoginMiss | ログイン失敗ログ (ip / idLogin / strPassword / strError) | **なし** |

<details>
<summary>#1 basePreference (カラム詳細)</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | bigint unsigned auto_increment | - | - | PRIMARY KEY |
| stampRegister | bigint | NOT NULL | - | 登録 UNIX epoch |
| stampUpdate | bigint | NOT NULL | - | 更新 UNIX epoch |
| jsonStampUpdate | mediumtext | YES | - | カラム単位 update 時刻の JSON |
| flagMaintenance | int(1) unsigned | YES | 0 | メンテナンスモード |
| arrCommaIdAccountMaintenance | mediumtext | YES | - | メンテモード中アクセス可 id 群 (comma) |
| numTimeZone | int | YES | - | 既定タイムゾーン |
| strTopUrl | text | YES | - | トップ URL |
| numAutoLock | int unsigned | YES | 3 | 自動ロック回数 |
| numPasswordLimit | varchar(7) | YES | 0 | パスワード有効期限日数 |
| numPassword | int | YES | 4 | パスワード最小文字数 |
| arrCommaLockAccount | mediumtext | YES | - | ロック中 id 群 |
| flagLoginMail | int(1) unsigned | YES | 0 | ログイン時メール通知 |
| flagAccessUnknownMail | int(1) unsigned | YES | 0 | 不明アクセスメール通知 (14300 追加) |
| flagLoginSecond | int(1) unsigned | YES | 0 | 2段階認証有効 (13700 追加) |
| flagVersionUpdate | int(1) unsigned | YES | 0 | 自動バージョンアップ有効 |
| strSiteName | text | NOT NULL | - | サイト名 |
| strSiteUrl | text | YES | - | サイト URL |
| strSiteMailPc | text | NOT NULL | - | 管理者メール |
| numAutoMustLogout | int unsigned | YES | 0 | 強制ログアウト秒 |
| flagForgot | int(1) unsigned | YES | 0 | パス忘れフォーム有効 |
| flagSign | int(1) unsigned | YES | 0 | サインアップフォーム有効 |
| jsonIpAccessAccept | mediumtext | YES | - | IP 許可リスト JSON |
| jsonIpSubnetAccessAccept | mediumtext | YES | - | IP サブネット許可リスト |
| flagReject | int(1) unsigned | YES | 1 | 海外アクセス拒否モード (14110/14300) |
| jsonIpAccessReject | mediumtext | YES | - | IP 拒否リスト |
| jsonIpSubnetAccessReject | mediumtext | YES | - | IP サブネット拒否リスト |
| jsonIpSignReject | mediumtext | YES | - | サインアップ拒否 IP |
| jsonIpSubnetSignReject | mediumtext | YES | - | サインアップ拒否 IP サブネット |
| jsonMailSignReject | mediumtext | YES | - | サインアップ拒否メールアドレス |
| jsonMailHostSignReject | mediumtext | YES | - | サインアップ拒否メールホスト |
| jsonModule | mediumtext | YES | - | インストール済モジュール JSON |
| strVersion | varchar(11) | YES | - | 現行バージョン文字列 |
| jsonVersion | mediumtext | YES | - | 適用済バッチ履歴 JSON |

</details>

<details>
<summary>#2 baseAccount (カラム詳細)</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | bigint unsigned auto_increment | - | - | PRIMARY KEY |
| stampRegister | bigint | NOT NULL | - | |
| stampUpdate | bigint | NOT NULL | - | |
| flagLock | int(1) unsigned | YES | 0 | ロック中 |
| flagWebmaster | int(1) unsigned | YES | 0 | webmaster 権限 |
| strCodeName | varchar(100) | NOT NULL | - | 表示名 |
| idLogin | text | NOT NULL | - | ログイン ID (生) |
| **strPassword** | text | NOT NULL | - | **暗号化パスワード** |
| stampUpdatePassword | bigint | YES | - | パス更新時刻 |
| strMailPc | text | NOT NULL | - | PC メールアドレス |
| flagLoginMail | int(1) unsigned | YES | 0 | |
| flagLoginSecond | int(1) unsigned | YES | 0 | 2段階認証有効 |
| strMailMobile | text | YES | - | |
| idMobile | text | YES | - | |
| strMobileCarrier | varchar(100) | YES | - | |
| numTimeZone | int | YES | - | |
| strLang | varchar(2) | YES | - | 言語 (ja/en 等) |
| strHoliday | varchar(2) | YES | - | 祝日パターン |
| numList | int unsigned | YES | 25 | 一覧件数 |
| numAutoLogout | int unsigned | YES | 0 | |
| numAutoPopup | int unsigned | YES | 0 | |
| strAutoBoot | varchar(100) | YES | "base" | デフォルト起動画面 |
| idTerm | bigint unsigned | YES | - | 所属 Term |
| idModule | bigint unsigned | YES | - | 所属 Module |
| arrSpaceStrTag | mediumtext | YES | - | タグ (空白区切り) |
| jsonStampCheck | mediumtext | YES | - | ニュース既読タイムスタンプ |
| flagDefault | int | YES | 0 | |

</details>

<details>
<summary>#3 baseAccountId</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | bigint unsigned | YES | - | **PK 宣言なし** (auto_increment も無い) |
| strCodeName | varchar(100) | NOT NULL | - | |

注: `id` + `strCodeName` の履歴参照テーブル。**明示 INDEX も PRIMARY KEY もない** — パフォーマンスリスク。
</details>

<details>
<summary>#4 baseAccountMemo</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idAccount | bigint unsigned | NOT NULL | - |
| flagColumn | varchar(50) | NOT NULL | - |
| jsonData | mediumtext | YES | - |

</details>

<details>
<summary>#5 baseTerm</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| strTitle | text | NOT NULL | - |
| stampStart | bigint | NOT NULL | - |
| stampEnd | bigint | YES | - |
| arrSpaceStrTag | mediumtext | YES | - |
| flagDefault | int | YES | 0 |

</details>

<details>
<summary>#6 baseModule</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| strTitle | text | NOT NULL | - |
| arrCommaIdModuleUser | mediumtext | YES | - |
| arrCommaIdModuleAdmin | mediumtext | YES | - |
| arrSpaceStrTag | mediumtext | YES | - |
| flagDefault | int | YES | 0 |

</details>

<details>
<summary>#7 baseAccessLog</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| ip | varchar(15) | NOT NULL | - (IPv4 前提) |
| strHost | text | YES | - |
| idAccount | bigint unsigned | YES | - |
| strDbType | text | YES | - |
| strDevice | text | YES | - |
| idModule | text | YES | - |
| strChild | text | YES | - |
| strExt | text | YES | - |
| strFunc | text | YES | - |
| jsonQuery | mediumtext | YES | - |

</details>

<details>
<summary>#8 baseAccessUnknown</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| ip | varchar(15) | NOT NULL | - (IPv4 only) |

</details>

<details>
<summary>#9 basePublish / #10 baseLock (構造共通)</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| session (basePublish のみ) | varchar(100) | YES | - |
| idAccount | bigint unsigned | YES | - |

</details>

<details>
<summary>#11-13 baseApplySign / baseApplyChange / baseApplyForgot</summary>

**baseApplySign / baseApplyChange**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| idAccount (baseApplyChange のみ) | bigint unsigned | YES | - |
| session | varchar(100) | NOT NULL | - |
| ip | varchar(15) | NOT NULL | - |
| strCodeName | text | YES | - |
| idLogin | text | YES | - |
| strMailPc | text | YES | - |
| flagAttest | int | YES | 0 |

**baseApplyForgot**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| idAccount | bigint unsigned | NOT NULL | - |
| session | varchar(100) | NOT NULL | - |
| ip | varchar(15) | NOT NULL | - |

</details>

<details>
<summary>#14 baseApiAccount</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| ip | varchar(15) | NOT NULL | - |
| strSiteUrl | text | YES | - |
| idAccount | bigint unsigned | NOT NULL | - |
| arrSpaceStrTag | mediumtext | YES | - |

</details>

<details>
<summary>#15 baseSession / #16 baseLoginSecond (いずれも PK なし)</summary>

**baseSession**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| stampRegister | bigint | NOT NULL | - |
| ip | varchar(15) | NOT NULL | - |
| idCookie | varchar(100) | NOT NULL | - |
| idMobile | text | YES | - |
| idAccount | bigint unsigned | NOT NULL | - |
| flagAPI | int | YES | 0 |

**baseLoginSecond**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| stampRegister | bigint | NOT NULL | - |
| ip | varchar(15) | NOT NULL | - |
| session | varchar(100) | NOT NULL | - |
| idAccount | bigint unsigned | NOT NULL | - |

注: **どちらも PRIMARY KEY が定義されていない**。件数が増えると致命的に遅くなる可能性。

</details>

<details>
<summary>#17-20 baseToken / baseLoginPassword / baseLoginIdLogin / baseLoginMiss (いずれも PK なし)</summary>

**baseToken**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| stampRegister | bigint | NOT NULL | - |
| token | varchar(100) | NOT NULL | - |
| idAccount | bigint unsigned | YES | - |

**baseLoginPassword**:

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| stampRegister | bigint | NOT NULL | - | |
| idAccount | bigint unsigned | YES | - | |
| **strPassword** | text | NOT NULL | - | **暗号化** |

**baseLoginIdLogin**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| stampRegister | bigint | NOT NULL | - |
| idLogin | text | NOT NULL | - |

**baseLoginMiss**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| stampRegister | bigint | NOT NULL | - |
| ip | varchar(15) | NOT NULL | - |
| idLogin | text | NOT NULL | - |
| strPassword | text | NOT NULL | - (プレーンテキスト) |
| strError | text | NOT NULL | - |

注: **4 テーブルすべて PRIMARY KEY なし**。`baseLoginMiss` は失敗時のプレーンテキストパスワードを蓄積していく (**セキュリティリスク**)。

</details>

---

### 3.2 accounting コア / 権限 — 7 テーブル (全体 #21-27)

| # | テーブル | 役割 |
|---|---------|------|
| 21 | accountingPreference | accounting モジュールのプリファレンス (バージョン / id 自動採番) |
| 22 | accountingAccount | accounting ログインアカウントの現在カレント (idEntityCurrent / numFiscalPeriodCurrent) |
| 23 | accountingAccountEntity | アカウント × 会社 (Entity) の権限割り当て |
| 24 | accountingAccountMemo | accounting 用メモ |
| 25 | accountingAccountId | ID-codeName 参照 (**PK 宣言なし**) |
| 26 | accountingAuthority | 権限定義 (my/all × select/insert/delete/update/output) |
| 27 | accountingAccess | アクセス制御 (部門やクライアント単位) |

<details>
<summary>#21 accountingPreference</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| jsonStampUpdate | longtext | YES | - |
| flagMaintenance | int(1) unsigned | YES | 0 |
| arrCommaIdAccountMaintenance | longtext | YES | - |
| strVersion | varchar(11) | YES | - |
| flagIdAccountTitle | int(1) unsigned | YES | 0 |
| accessCode | varchar(100) | YES | - |
| jsonVersion | longtext | YES | - |
| jsonIdAutoIncrement | longtext | YES | - (id 自動採番テーブル用 JSON) |

</details>

<details>
<summary>#22-25 accountingAccount / Entity / Memo / Id</summary>

**accountingAccount**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idAccount | int unsigned | YES | - |
| flagAdmin | int unsigned | YES | 1 |
| idEntityCurrent | int unsigned | YES | 1 |
| numFiscalPeriodCurrent | int unsigned | YES | 1 |
| arrCommaIdEntity | longtext | YES | - |

**accountingAccountEntity**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| idAccount | int unsigned | YES | 1 |
| idEntity | int unsigned | YES | 1 |
| idAuthority | int unsigned | YES | 1 |
| idAccess | int unsigned | YES | 1 |
| strMailFile | text | YES | - |

**accountingAccountMemo**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idAccount | int unsigned | YES | - |
| idEntity | int unsigned | YES | 0 |
| flagColumn | varchar(50) | NOT NULL | - |
| jsonData | longtext | YES | - |

**accountingAccountId**:

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | int unsigned | YES | - | **PK 宣言なし** |
| strCodeName | varchar(100) | NOT NULL | - | |

</details>

<details>
<summary>#26 accountingAuthority</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| strTitle | varchar(100) | YES | - |
| flagMySelect | int unsigned | YES | 1 |
| flagMyInsert | int unsigned | YES | 1 |
| flagMyDelete | int unsigned | YES | 1 |
| flagMyUpdate | int unsigned | YES | 1 |
| flagMyOutput | int unsigned | YES | 1 |
| flagAllSelect | int unsigned | YES | 1 |
| flagAllInsert | int unsigned | YES | 1 |
| flagAllDelete | int unsigned | YES | 1 |
| flagAllUpdate | int unsigned | YES | 1 |
| flagAllOutput | int unsigned | YES | 1 |
| arrSpaceStrTag | mediumtext | YES | - |
| flagDefault | int | YES | 0 |

</details>

<details>
<summary>#27 accountingAccess</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idAccess | int unsigned | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| strTitle | varchar(100) | YES | - |
| jsonData | longtext | YES | - |
| arrSpaceStrTag | mediumtext | YES | - |
| flagDefault | int | YES | 0 |

</details>

---

### 3.3 会社 (Entity) / 部門 — 3 テーブル (#28-30)

| # | テーブル | 役割 |
|---|---------|------|
| 28 | accountingEntity | 会社マスタ (strNation / strLang / strCurrency / numFiscalPeriod) |
| 29 | accountingEntityJpn | 会社の JPN 固有属性 (会計年度 / 法人個人区分 / 消費税設定) |
| 30 | accountingEntityDepartment | 部門 (会社×会計期間×部門) |

<details>
<summary>#28 accountingEntity</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | int unsigned auto_increment | - | - | PK |
| stampRegister | bigint | NOT NULL | - | |
| stampUpdate | bigint | NOT NULL | - | |
| strTitle | varchar(100) | YES | - | |
| strNation | varchar(3) | YES | "jpn" | |
| strLang | varchar(3) | YES | "ja" | |
| strCurrency | varchar(3) | YES | "JPY" | |
| numFiscalPeriodStart | int unsigned | YES | 1 | |
| numFiscalPeriod | int unsigned | YES | 1 | 現在の期 |
| numFiscalPeriodLock | int unsigned | YES | 0 | ロック済の期 |
| flagConfig | int unsigned | YES | 1 | 初期設定済フラグ |
| arrSpaceStrTag | mediumtext | YES | - | |

</details>

<details>
<summary>#29 accountingEntityJpn</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | int unsigned auto_increment | - | - | PK |
| idEntity | int unsigned | NOT NULL | - | |
| numFiscalPeriod | int unsigned | YES | - | |
| stampFiscalBeginning | bigint | YES | - | |
| numFiscalBeginningYear | int unsigned | YES | - | |
| numFiscalBeginningMonth | int unsigned | YES | - | |
| numFiscalTermMonth | int unsigned | YES | 12 | |
| flagCorporation | int unsigned | YES | 1 | 1:法人 2:個人一般 3:個人不動産 4:個人農業 |
| numYearSheet | int unsigned | YES | 2012 | 申告書テンプレ年度 |
| flagCR | int unsigned | YES | - | |
| flagSubsidiaryMoney | int unsigned | YES | 0 | |
| flagConsumptionTaxFree | int unsigned | YES | 1 | |
| flagConsumptionTaxGeneralRule | int unsigned | YES | 1 | |
| flagConsumptionTaxDeducted | int unsigned | YES | 1 | 1:each 0:proration |
| flagConsumptionTaxIncluding | int unsigned | YES | 1 | 税込み／税抜き |
| flagConsumptionTaxCalc | int unsigned | YES | 1 | 1:floor 2:round 3:ceil |
| flagConsumptionTaxWithoutCalc | int unsigned | YES | 1 | 1:in 2:out 3:another |
| flagConsumptionTaxBusinessType | int unsigned | YES | 1 | |
| jsonFlag | longtext | YES | - | |

</details>

<details>
<summary>#30 accountingEntityDepartment</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idDepartment | int unsigned | YES | 0 |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| strTitle | varchar(100) | YES | - |
| arrSpaceStrTag | mediumtext | YES | - |

</details>

---

### 3.4 勘定科目 (Chart of Accounts) / 財務諸表 — 6 テーブル (#31-36)

| # | テーブル | 役割 |
|---|---------|------|
| 31 | accountingFSJpn | 財務諸表 (勘定科目ツリー + FS 定義) を JSON 格納 |
| 32 | accountingFSValueJpn | FS 値 (BS/PL/CR/CS の期ごと集計値) JSON |
| 33 | accountingFSIdJpn | FS 内で使う id 自動採番用 |
| 34 | accountingSubAccountTitleJpn | 補助科目マスタ |
| 35 | accountingSubAccountTitleValueJpn | 補助科目の期ごと値 |
| 36 | accountingEntityDepartmentFSValueJpn | 部門別 FS 値 |

<details>
<summary>#31 accountingFSJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | - |
| jsonJgaapAccountTitlePL | longtext | YES | - |
| jsonJgaapAccountTitleBS | longtext | YES | - |
| jsonJgaapAccountTitleCR | longtext | YES | - |
| jsonJgaapFSPL | longtext | YES | - |
| jsonJgaapFSBS | longtext | YES | - |
| jsonJgaapFSCR | longtext | YES | - |
| jsonJgaapFSCS | longtext | YES | - |

</details>

<details>
<summary>#32 accountingFSValueJpn (最重要)</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | bigint unsigned auto_increment | - | - | PK |
| stampRegister | bigint | NOT NULL | - | |
| stampUpdate | bigint | NOT NULL | - | |
| idEntity | int unsigned | NOT NULL | - | |
| numFiscalPeriod | int unsigned | YES | - | |
| jsonJgaapAccountTitlePL | longtext | YES | - | 勘定科目別 PL 値。`{idAccountTitle:{f1:{sumPrev,sumDebit,sumCredit,sumNext,varsTax,varsAdjust}}}` 構造 |
| jsonJgaapAccountTitleBS | longtext | YES | - | 同 BS 値 |
| jsonJgaapAccountTitleCR | longtext | YES | - | 同 CR 値 |
| jsonJgaapFSPL | longtext | YES | - | FS 階層集計 PL |
| jsonJgaapFSBS | longtext | YES | - | FS 階層集計 BS |
| jsonJgaapFSCR | longtext | YES | - | FS 階層集計 CR |
| jsonJgaapFSCS | longtext | YES | - | FS 階層集計 CS (C/F) |
| jsonConsumptionTax | longtext | YES | - | 消費税集計 JSON |
| jsonConsumptionTaxDetail | longtext | YES | - | 軽減税率対応詳細 (14800 追加) |

注: 1 期分の FS 全データがこの 1 行に JSON として埋め込まれる。単一テーブル単行ドキュメント型。

</details>

<details>
<summary>#33 accountingFSIdJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| jsonJgaapAccountTitlePL | longtext | YES | - |
| jsonJgaapAccountTitleBS | longtext | YES | - |
| jsonJgaapAccountTitleCR | longtext | YES | - |
| jsonJgaapFSPL | longtext | YES | - |
| jsonJgaapFSBS | longtext | YES | - |
| jsonJgaapFSCR | longtext | YES | - |
| jsonJgaapFSCS | longtext | YES | - |

</details>

<details>
<summary>#34 accountingSubAccountTitleJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idSubAccountTitle | int unsigned | YES | 0 |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| idAccountTitle | varchar(100) | YES | - |
| strTitle | text | YES | - |
| arrSpaceStrTag | mediumtext | YES | - |

</details>

<details>
<summary>#35 accountingSubAccountTitleValueJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idSubAccountTitle | int unsigned | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| jsonData | longtext | YES | - |

</details>

<details>
<summary>#36 accountingEntityDepartmentFSValueJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idDepartment | int unsigned | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| jsonJgaapAccountTitlePL | longtext | YES | - |
| jsonJgaapAccountTitleBS | longtext | YES | - |
| jsonJgaapAccountTitleCR | longtext | YES | - |
| jsonJgaapFSPL | longtext | YES | - |
| jsonJgaapFSBS | longtext | YES | - |
| jsonJgaapFSCR | longtext | YES | - |

</details>

---

### 3.5 仕訳 (Transactions) / ログ — 9 テーブル (#37-45)

会計システムの中核。すべての仕訳・現金伝票・銀行取込・月締めバッチのログはここに入る。

| # | テーブル | 役割 |
|---|---------|------|
| 37 | accountingCash | 現金出納帳の設定 |
| 38 | accountingCashValue | 現金出納帳の集計値 (期・期値単位) |
| 39 | accountingLogCash | 現金出納帳仕訳ログ |
| 40 | accountingLogCashDefer | 現金出納帳の未払 (defer) 仕訳 |
| 41 | accountingLog | **仕訳本体 (会計システムの中心テーブル)** |
| 42 | accountingLogMailJpn | メール取り込み仕訳の設定 (IMAP パス) |
| 43 | accountingLogImportJpn | CSV 取込用テンプレ／マッピング |
| 44 | accountingLogImportRetryJpn | 取込失敗のリトライキュー |
| 45 | accountingLogCalcJpn | 仕訳を勘定科目別にフラット化したキャッシュ (元帳用) |

<details>
<summary>#37 accountingCash / #38 accountingCashValue</summary>

**accountingCash**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| jsonCash | text | YES | - |
| flagPayWrite | int unsigned | YES | 0 |
| flagAutoImport | int unsigned | YES | 1 |
| flagPermitImport | int unsigned | YES | 1 |

**accountingCashValue**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister | bigint | NOT NULL | - |
| stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| numFiscalPeriodValue | int unsigned | YES | 1 |
| flagPay | int | YES | 0 |
| jsonData | longtext | YES | - |

</details>

<details>
<summary>#39 accountingLogCash (約 40 カラム)</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate / stampBook | bigint | NOT NULL | - |
| idLogCash | bigint unsigned | YES | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| idAccount | int unsigned | NOT NULL | - |
| strTitle | varchar(100) | YES | - |
| arrSpaceStrTag | mediumtext | YES | - |
| flagIn | int | YES | - |
| flagPay | int | YES | 0 |
| stampPay | bigint | YES | 0 |
| arrCommaIdLogFile | longtext | YES | - |
| jsonVersion | longtext | YES | - |
| flagApply | int unsigned | YES | - |
| idAccountApply | int unsigned | YES | - |
| arrCommaIdAccountPermit | text | YES | - |
| numValue | decimal(19,0) unsigned | YES | - |
| arrCommaIdDepartmentDebit/Credit | longtext | YES | - |
| arrCommaIdAccountTitleDebit/Credit | longtext | YES | - |
| arrCommaIdSubAccountTitleDebit/Credit | longtext | YES | - |
| arrCommaRateConsumptionTaxDebit/Credit | text | YES | - |
| arrCommaConsumptionTaxDebit/Credit | longtext | YES | - |
| arrCommaConsumptionTaxWithoutCalcDebit/Credit | longtext | YES | - |
| arrCommaTaxPaymentDebit/Credit | text | YES | - |
| arrCommaTaxReceiptDebit/Credit | text | YES | - |
| jsonChargeHistory | longtext | YES | - |
| jsonPermitHistory | longtext | YES | - |
| jsonWriteHistory | longtext | YES | - |
| flagRemove | int unsigned | YES | 0 |
| stampRemove | bigint | YES | 0 |

</details>

<details>
<summary>#40 accountingLogCashDefer (類似構造)</summary>

accountingLogCash とほぼ同じ。追加: `stampArrive`, `flagType`, `numRow`, `flagFiscalReport` (f1/f2/f41/f42/f43/0), `arrCommaIdDepartmentVersion`/`arrCommaIdAccountTitleVersion`/`arrCommaIdSubAccountTitleVersion`。削除: `flagPay/stampPay/flagRemove/stampRemove/arrCommaIdLogFile`。

</details>

<details>
<summary>#41 accountingLog (最重要・40+ カラム)</summary>

| カラム | 型 | 備考 |
|-------|----|------|
| id | bigint unsigned auto_increment | PK |
| stampRegister / stampUpdate / stampArrive / stampBook | bigint | 複数の時刻 |
| idLog | bigint unsigned | 同一仕訳の親 |
| idEntity | int unsigned NOT NULL | |
| numFiscalPeriod | int unsigned default 1 | |
| idAccount | int unsigned NOT NULL | |
| flagFiscalReport | varchar(3) | f1/f2/f41/f42/f43/0 |
| strTitle | varchar(100) | |
| arrSpaceStrTag | mediumtext | |
| flagApply | int unsigned | |
| idAccountApply | int unsigned | |
| flagApplyBack | int unsigned | |
| arrCommaIdAccountPermit | text | |
| arrCommaIdLogFile | longtext | 添付ファイル id |
| jsonVersion | longtext | 全フィールドの変更履歴 JSON |
| numValue | decimal(19,0) unsigned | 仕訳金額 |
| arrCommaIdDepartment{Debit,Credit} | longtext | |
| arrCommaIdAccountTitle{Debit,Credit} | longtext | |
| arrCommaIdSubAccountTitle{Debit,Credit} | longtext | |
| arrCommaRateConsumptionTax{Debit,Credit} | text | |
| arrCommaConsumptionTax{Debit,Credit} | longtext | |
| arrCommaConsumptionTaxWithoutCalc{Debit,Credit} | longtext | |
| arrCommaTaxPayment{Debit,Credit} | text | |
| arrCommaTaxReceipt{Debit,Credit} | text | |
| arrCommaIdDepartmentVersion | longtext | |
| arrCommaIdAccountTitleVersion | longtext | |
| arrCommaIdSubAccountTitleVersion | longtext | |
| jsonChargeHistory | longtext | |
| jsonPermitHistory | longtext | |
| flagRemove | int unsigned default 0 | soft delete |
| stampRemove | bigint default 0 | |

仕訳ヘッダ + 借方明細配列 + 貸方明細配列 + 承認履歴 + 版管理をひとつの行に詰める設計。**検索用の索引は PK のみ**で、`idEntity` / `numFiscalPeriod` / `stampBook` 等のアクセスキーには物理 INDEX 無し。

</details>

<details>
<summary>#42 accountingLogMailJpn</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | int unsigned auto_increment | - | - | PK |
| stampRegister / stampUpdate | bigint | NOT NULL | - | |
| idEntity | int unsigned | NOT NULL | - | |
| numFiscalPeriod | int unsigned | YES | - | |
| jsonMail | longtext | YES | - | 許可メール |
| jsonMailHost | longtext | YES | - | 許可ホスト |
| strHost | text | YES | - | IMAP |
| strUser | text | YES | - | IMAP |
| **strPassword** | **tinyblob** | YES | - | **暗号化** |
| numPort | varchar(5) | YES | 993 | |
| flagSecure | varchar(5) | YES | "ssl" | |
| strMail | text | YES | - | |

</details>

<details>
<summary>#43 accountingLogImportJpn</summary>

`accountingLog` の debit/credit 系カラムを全て持ちつつ、追加で `flagAttest`, `numColStampBook`, `numColNumValue`, `numColStrTitle` を持つ CSV 取込マッピング。PK: id bigint auto_increment。

</details>

<details>
<summary>#44 accountingLogImportRetryJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idAccount / idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| idLogRetry | bigint unsigned | YES | - |
| flagType | text | YES | - (mail/item/post) |
| jsonData | longtext | YES | - |
| arrSpaceStrTag | mediumtext | YES | - |

</details>

<details>
<summary>#45 accountingLogCalcJpn (元帳用フラットテーブル)</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | bigint unsigned auto_increment | - | - | PK |
| stampRegister | bigint | NOT NULL | - | |
| stampBook | bigint | NOT NULL | - | |
| idLog | bigint unsigned | YES | - | |
| idEntity | int unsigned | NOT NULL | - | |
| numFiscalPeriod | int unsigned | YES | 1 | |
| idAccount | int unsigned | NOT NULL | - | |
| strTitle | varchar(100) | YES | - | |
| flagFiscalReport | varchar(3) | YES | - | |
| flagDebit | int unsigned | YES | - | |
| idAccountTitle | varchar(100) | YES | - | |
| idDepartment | int unsigned | YES | - | |
| idSubAccountTitle | int unsigned | YES | - | |
| idAccountTitleContra | varchar(100) | YES | - | 相手科目 |
| idDepartmentContra | int unsigned | YES | - | |
| idSubAccountTitleContra | int unsigned | YES | - | |
| numValue | decimal(19,0) unsigned | YES | - | |
| numValueConsumptionTax | decimal(19,0) | YES | 0 | |
| numRateConsumptionTax | int unsigned | YES | - | |
| flagRateConsumptionTaxReduced | int unsigned | YES | 0 | 軽減税率 (14800 追加) |
| flagConsumptionTax | text | YES | - | |
| flagConsumptionTaxWithoutCalc | int unsigned | YES | - | |
| numBalance | decimal(19,0) | YES | 0 | |
| numBalanceSubAccount | decimal(19,0) | YES | 0 | |
| numBalanceDepartment | decimal(19,0) | YES | 0 | |
| numBalanceDepartmentSubAccount | decimal(19,0) | YES | 0 | |

accountingLog 1 行あたり複数行が生成される (仕訳明細 × 勘定科目)。行数が最も爆発的に増える想定テーブル。**PK 以外のインデックスがない** ので `where idEntity=? and numFiscalPeriod=? and idAccountTitle=?` などは常にフルスキャン。

</details>

---

### 3.6 ファイル / 固定資産 / 銀行 — 7 テーブル (#46-52)

| # | テーブル | 役割 |
|---|---------|------|
| 46 | accountingFile | ファイル取込 (メール/IMAP) 設定 |
| 47 | accountingLogFile | アップロードファイルメタ (仕訳添付) |
| 48 | accountingFixedAssetsJpn | 固定資産マスタの期ごと設定 |
| 49 | accountingLogFixedAssetsJpn | 固定資産台帳明細 (期ごと) |
| 50 | accountingBanks | 銀行取込設定 |
| 51 | accountingLogBanks | 銀行取込行 (通帳明細) |
| 52 | accountingLogBanksAccount | 銀行口座 (暗号化された詳細 JSON `blobDetail`) |

<details>
<summary>#46 accountingFile</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | int unsigned auto_increment | - | - | PK |
| stampRegister / stampUpdate | bigint | NOT NULL | - | |
| idEntity | int unsigned | NOT NULL | - | |
| numFiscalPeriod | int unsigned | YES | - | |
| jsonFileType | longtext | YES | - | どのファイル種別を取り込むか |
| jsonMail | longtext | YES | - | 許可メール |
| jsonMailHost | longtext | YES | - | 許可ホスト |
| strHost | text | YES | - | IMAP |
| strUser | text | YES | - | IMAP |
| **strPassword** | **tinyblob** | YES | - | **暗号化** |
| numPort | varchar(5) | YES | 993 | |
| flagSecure | varchar(5) | YES | "ssl" | |
| strMail | text | YES | - | |

</details>

<details>
<summary>#47 accountingLogFile</summary>

| カラム | 型 | 備考 |
|-------|----|------|
| id | bigint unsigned auto_increment | PK |
| stampRegister / stampUpdate / stampArrive | bigint | |
| idLogFile | bigint unsigned | |
| idAccount / idEntity | int unsigned NOT NULL | |
| numFiscalPeriod | int unsigned default 1 | |
| strTitle | varchar(100) | |
| numByte | bigint unsigned NOT NULL | |
| numWidth / numHeight | int unsigned | 画像 |
| strUrl | text | ファイルパス |
| strFileType | varchar(10) NOT NULL | pdf/jpg 等 |
| arrSpaceStrTag | mediumtext | |
| jsonVersion | longtext | |
| jsonChargeHistory | longtext | |
| idAccountUpload | int unsigned NOT NULL | |
| flagRemove | int unsigned default 0 | |
| stampRemove | bigint default 0 | |

</details>

<details>
<summary>#48 accountingFixedAssetsJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | int unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | - |
| flagDepWrite | varchar(3) | YES | "f1" |
| flagLossWrite | int | YES | 0 |
| flagFractionDepWrite | varchar(5) | YES | "ceil" |
| flagFractionDep | varchar(5) | YES | "ceil" |
| flagFractionDepSurvivalRate | varchar(5) | YES | "floor" |
| flagFractionDepSurvivalRateLimit | varchar(5) | YES | "floor" |
| flagFractionRatioOperate | varchar(5) | YES | "ceil" |
| jsonAccountTitle | longtext | YES | - |
| jsonDepSum | longtext | YES | - |
| numRatioOperateDepSum | decimal(5,2) | YES | "100.00" (14200 追加) |

</details>

<details>
<summary>#49 accountingLogFixedAssetsJpn (50+ カラム)</summary>

| カラム | 型 | 備考 |
|-------|----|------|
| id | int unsigned auto_increment | PK |
| stampRegister / stampUpdate | bigint | |
| idFixedAssets / idAccount / idEntity | int unsigned NOT NULL | |
| numFiscalPeriod | int unsigned default 1 | |
| strTitle | varchar(100) | |
| idAccountTitle | varchar(100) | |
| flagDepMethod | varchar(100) | |
| numUsefulLife | int | 耐用年数 |
| numVolume | decimal(7,2) unsigned | |
| flagDepUnit | varchar(100) | |
| idDepartment | int unsigned | |
| flagTaxFixed / flagTaxFixedType | varchar(100) | |
| flagDepUp / flagDepDown | varchar(100) | |
| stampBuy / stampStart / stampEnd / stampDrop | bigint | |
| numValue | decimal(19,0) unsigned | 取得価額 |
| numValueCompression | decimal(19,0) unsigned | 圧縮額 |
| numValueNet | decimal(19,0) unsigned | |
| numSurvivalRate | decimal(3,0) unsigned | |
| numSurvivalRateLimit | decimal(3,0) unsigned | |
| numValueRemainingBook | decimal(19,0) unsigned | 残存簿価 |
| numValueAccumulated | decimal(19,0) unsigned | 累計 |
| numValueNetOpening | decimal(19,0) unsigned | 期首簿価 |
| numValueDepCalcBase | decimal(19,0) unsigned | 償却計算基礎 |
| numValueDepPrevOver | decimal(19,0) unsigned | 前期超過 |
| arrCommaDepMonth | varchar(28) | 当期償却月 CSV |
| numRateDep | decimal(6,5) unsigned | 償却率 |
| flagDepRateType | int unsigned default 1 | normal:1 update:0 |
| numValueAssured | decimal(19,0) unsigned | 保証額 |
| numValueDepCalc / Up / Extra / Special | decimal(19,0) unsigned | 償却計算後内訳 |
| numValueDepSpecialShortPrev / ShortCurrent / ShortCurrentCut / ShortNext | decimal(19,0) unsigned | 特別償却 不足額 |
| numValueDepLimit | decimal(19,0) unsigned | 償却限度額 |
| numValueDep | decimal(19,0) unsigned | 当期償却費 |
| numValueAccumulatedClosing | decimal(19,0) unsigned | |
| numValueNetClosing | decimal(19,0) unsigned | |
| numRatioOperate | decimal(5,2) default "100.00" | 事業供用割合 |
| numValueDepOperate | decimal(19,0) unsigned | |
| numValueDepCurrentOver | decimal(19,0) | 当期償却超過額 |
| numValueDepNextOver | decimal(19,0) unsigned | 翌期繰越 |
| lossOnDisposalOfFixedAssets | varchar(100) | |
| accumulatedDepreciation | varchar(100) | |
| sellingAdminCost / productsCost / nonOperatingExpenses / agricultureCost | varchar(100) | |
| numRatioSellingAdminCost / ProductsCost / NonOperatingExpenses / AgricultureCost | decimal(5,2) | |
| flagFraction | varchar(100) | |
| strMemo | text | |
| arrSpaceStrTag | mediumtext | |
| jsonChargeHistory / jsonWriteHistory / jsonVersion | longtext | |
| flagRemove | int unsigned default 0 | |
| stampRemove | bigint default 0 | |

</details>

<details>
<summary>#50 accountingBanks / #51 accountingLogBanks</summary>

**accountingBanks**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| flagAutoImport | int unsigned | YES | 1 |
| flagLock | int | YES | 0 |

**accountingLogBanks**:

| カラム | 型 | 備考 |
|-------|----|------|
| id | bigint unsigned auto_increment | PK |
| stampRegister / stampUpdate | bigint NOT NULL | |
| idEntity | int unsigned NOT NULL | |
| numFiscalPeriod | int unsigned default 1 | |
| idLogBanks | int unsigned NOT NULL | |
| idLogAccount | int unsigned NOT NULL | |
| idAccount | int unsigned NOT NULL | |
| stampBook | bigint NOT NULL | |
| strTitle | varchar(100) | |
| flagIn | int | |
| numValueIn / numValueOut | decimal(19,0) unsigned | |
| numBalance | decimal(19,0) | |
| arrSpaceStrTag | mediumtext | |
| jsonChargeHistory / jsonWriteHistory / jsonVersion | longtext | |
| flagCaution | int default 0 | |
| flagRemove | int unsigned default 0 | |
| stampRemove | bigint default 0 | |

</details>

<details>
<summary>#52 accountingLogBanksAccount</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | bigint unsigned auto_increment | - | - | PK |
| stampRegister / stampUpdate | bigint | NOT NULL | - | |
| idEntity | int unsigned | NOT NULL | - | |
| numFiscalPeriod | int unsigned | YES | - | |
| idLogAccount | int unsigned | YES | - | |
| strTitle | varchar(100) | YES | - | |
| flagBank | text | YES | - | 銀行サービス識別子 |
| **blobDetail** | **blob** | YES | - | **暗号化された口座詳細 (ID / パス / 秘匿 JSON)** |
| stampCheck | bigint | YES | - | |
| flagLockReason | text | YES | - | |
| flagLock | int | YES | 0 | |
| arrSpaceStrTag | text | YES | - | |

</details>

---

### 3.7 予算 / 帳票 / 家事按分 / 青色申告 — 6 テーブル (#53-58)

| # | テーブル | 役割 |
|---|---------|------|
| 53 | accountingBudgetJpn | 予算 (期・部門・BS/PL/CR・月単位) |
| 54 | accountingBreakEvenPointJpn | 損益分岐点分析 (勘定科目タイプ分類) |
| 55 | accountingSummaryStatementJpn | 要約精算表 |
| 56 | accountingNotesFSJpn | FS 注記 |
| 57 | accountingDetailedAccountJpn | 勘定明細帳 |
| 58 | accountingLogHouseJpn | 家事按分仕訳 (個人事業主向け) |
| 59 | accountingBlueSheetJpn | 青色申告決算書 (画像バイナリ / 14200 で再作成) |

<details>
<summary>#53 accountingBudgetJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| flagFiscalPeriod | varchar(5) | YES | - (f1/f21/f22/f2sum/f41/f42/f43/f44/f4sum/msum/1..12) |
| idDepartment | int unsigned | YES | - |
| flagFS | varchar(2) | YES | - (PL/CR) |
| jsonData | longtext | YES | - |

</details>

<details>
<summary>#54 accountingBreakEvenPointJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| idDepartment | int unsigned | YES | - |
| jsonJgaapAccountTitlePL | longtext | YES | - |
| jsonJgaapAccountTitleCR | longtext | YES | - |

</details>

<details>
<summary>#55 accountingSummaryStatementJpn</summary>

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| flagReport | varchar(100) | YES | - |
| flagDetail | varchar(100) | YES | - |
| jsonJgaapAccountTitleBS | longtext | YES | - |
| jsonJgaapAccountTitlePL | longtext | YES | - |
| jsonJgaapAccountTitleCR | longtext | YES | - |
| jsonData | longtext | YES | - |

</details>

<details>
<summary>#56 accountingNotesFSJpn / #57 accountingDetailedAccountJpn</summary>

**accountingNotesFSJpn**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| strComment | text | YES | - |

**accountingDetailedAccountJpn**:

| カラム | 型 | NULL | Default |
|-------|----|------|---------|
| id | bigint unsigned auto_increment | - | - (PK) |
| stampRegister / stampUpdate | bigint | NOT NULL | - |
| idEntity | int unsigned | NOT NULL | - |
| numFiscalPeriod | int unsigned | YES | 1 |
| flagReport | varchar(100) | YES | - |
| flagDetail | varchar(100) | YES | - |
| numPage | int unsigned | YES | 1 |
| jsonData | longtext | YES | - |

</details>

<details>
<summary>#58 accountingLogHouseJpn</summary>

家事按分 (`numRatio` 0..100 で経費を按分) 用仕訳テンプレート。カラム構成は `accountingLog` の debit/credit 系そっくりだが、ヘッダに `numRatio decimal(5,2) default "100.00"` が加わる。PK: id bigint auto_increment。

</details>

<details>
<summary>#59 accountingBlueSheetJpn</summary>

| カラム | 型 | NULL | Default | 備考 |
|-------|----|------|---------|------|
| id | bigint unsigned auto_increment | - | - | PK |
| stampRegister / stampUpdate | bigint | NOT NULL | - | |
| idEntity | int unsigned | NOT NULL | - | |
| numFiscalPeriod | int unsigned | YES | 1 | |
| numYearSheet | int unsigned | NOT NULL | - | 2014 等 |
| **blobData** | **blob** | YES | - | **暗号化または青色申告データバイナリ** (用途 Phase 1.1.b で確認) |

</details>

### テーブル総数 集計

- base 系: 20 (Batch13700 で 19、14300 で `baseAccessUnknown` 追加して 20)
- accounting 系: 39 (Batch14200 の `'table' =>` 宣言 39 個)
- **合計 59 テーブル**

`'table' =>` マッチは Batch14200/templates/config.php で 39 行、Batch13700/templates/config.php で 19 行、Batch14300/templates/config.php で 20 行。新規インストール時には Batch13700 が 19 テーブル、Batch14200 が 39 テーブル、Batch14300 が 1 テーブル (baseAccessUnknown 追加分) を作る。

> 過去の「53 テーブル」という数値は不正確。正しくは **59**。

---

## 4. 命名規則辞書

レガシー Rucaro では DB カラム・PHP 変数双方でハンガリアン風の接頭辞を採用している。

| 接頭辞 | 意味 | 主な型 | 例 |
|--------|------|-------|----|
| `id` | 主キー / 外部参照 (整数) | int / bigint unsigned | `id`, `idAccount`, `idEntity`, `idLog`, `idFixedAssets` |
| `num` | 数量・カウント・金額 | int / decimal | `numFiscalPeriod`, `numValue`, `numPort`, `numRatio`, `numRateDep` |
| `str` | 文字列 (単体) | text / varchar | `strTitle`, `strPassword`, `strMailPc`, `strNation` |
| `arr` | 配列をシリアライズした文字列。接尾語で区切り文字を示す: `arrComma` (CSV), `arrSpace` (SSV) | mediumtext / longtext | `arrCommaIdAccountPermit`, `arrSpaceStrTag`, `arrCommaIdDepartmentDebit` |
| `flag` | フラグ (int unsigned / int(1) / varchar で enum 的表現) | int / int(1) / varchar(N) | `flagLock`, `flagRemove`, `flagDebit`, `flagFiscalReport` (f1/f2/f41 等) |
| `json` | JSON シリアライズ文字列 | mediumtext / longtext | `jsonData`, `jsonVersion`, `jsonConsumptionTax`, `jsonJgaapFSPL` |
| `blob` | バイナリ or 暗号化バイナリ | blob / tinyblob | `blobDetail`, `blobData` (`strPassword` も暗号化時は `tinyblob`) |
| `stamp` | UNIX epoch 秒 | bigint | `stampRegister`, `stampUpdate`, `stampBook`, `stampBuy`, `stampEnd` |
| `vars` | PHP 内部変数接頭辞 (DB カラム命名には使わない) | - | (DB スキーマ外) |

### 補足

- `Jpn` 接尾辞 … 国別 (nation) 属性。実行時に `strNation` を大文字頭で連結して `accountingConsumptionTax` + `Jpn` = `accountingConsumptionTaxJpn` という形で動的にテーブル名が組み立てられる (`back/class/else/plugin/accounting/jpn/*.php`)。
- `Log` 接頭辞 … イベント／トランザクションログ型。仕訳の本体は `accountingLog`、キャッシュ化は `accountingLogCalcJpn`。
- `flag` の値は int のみならず `"f1"`, `"ceil"`, `"ssl"` など文字列 enum としても使われる。
- `base*` は共通基盤、`accounting*` は会計モジュール名前空間。今後新モジュールは `<module>*` で並ぶ想定。

---

## 5. 暗号化カラム一覧

Phase 1.1.b `docs/phase1/encrypted-columns.md` と連動。本スキーマで暗号化されている / される可能性のあるカラムは以下 6 列。

| # | テーブル | カラム | 型 | 用途 |
|---|---------|-------|----|------|
| 1 | `baseAccount` | `strPassword` | text NOT NULL | ログインパスワード (不可逆ハッシュ + salt 疑惑。Phase 1.1.b で要確認) |
| 2 | `baseLoginPassword` | `strPassword` | text NOT NULL | 過去パスワード履歴 (同じ暗号化方式で保存) |
| 3 | `accountingLogMailJpn` | `strPassword` | tinyblob | IMAP パスワード (可逆暗号想定。バイナリ) |
| 4 | `accountingFile` | `strPassword` | tinyblob | IMAP/メール取込パスワード (可逆暗号想定) |
| 5 | `accountingLogBanksAccount` | `blobDetail` | blob | 銀行口座接続情報 (ID / パス / サイト指紋等)。可逆暗号想定 |
| 6 | `accountingBlueSheetJpn` | `blobData` | blob | 青色申告決算書データ (PDF / 画像 / 構造化 JSON の可能性)。用途はコードレビューで要確認 |

### 注意

- `baseLoginMiss.strPassword` も **プレーンテキスト** でログに残る可能性があり、セキュリティ上の問題 (リスト型攻撃時にパスワード候補が蓄積)。Phase 1.1.b の調査対象に追加推奨。
- `tinyblob` は 255 バイト上限で IMAP パスワードには小さい。長いパスワードだと切り詰められるリスク。

---

## 6. 未使用疑惑テーブル & コードで参照されるが DDL 未定義のテーブル

### 6.1 DDL で作成されるが `back/class/else/` から strTable として参照されないテーブル

`back/class/else/**/*.php` に対し `"strTable" => "..."` を grep した結果のユニーク値 56 個と config.php で定義された 59 テーブルを突合した。**直接文字列一致で参照が検出できない** テーブルは以下:

| テーブル | 備考 |
|---------|------|
| `baseApplyForgot` | 参照検出 0 件。フォーム系なので実際は別経路でアクセスされている可能性 |
| `accountingEntityJpn` | `strTable => 'accountingEntity' . $strNation` 形で動的生成されるので、直接文字列では 0 件。実質は使われている |
| `accountingFSIdJpn` | 同上。動的に `'accountingFSId' . $strNation` |
| `accountingFSJpn` / `accountingFSValueJpn` 等 `*Jpn` の一部 | 同様に動的連結で生成される |

本当に使われていないテーブルは今回の grep では決めきれない。Phase 1 の次のステップで「全 59 テーブル × 直接+動的参照」の完全マトリクスを作ることを推奨。

### 6.2 コードで参照されるが DDL 未定義のテーブル (★ 重要 ★)

`back/class/else/**/*.php` を grep すると **マイグレーションに含まれていない** テーブル名が見つかる:

| テーブル | 参照ファイル例 | 状態 |
|---------|--------------|------|
| `accountingAdminMemo` | `back/class/else/plugin/accounting/*.php` 内の `strTable` 集計で出現 | **config.php に DDL 定義なし** |
| `accountingConsumptionTax` + $strNation → `accountingConsumptionTaxJpn` | `plugin/accounting/jpn/ConsumptionTax.php:143`, `plugin/accounting/jpn/2012/consumptionTax/{PreferenceEditor,GeneralEditor}.php` 他 | **DDL 未定義** |
| `accountingCorporateTax` + $strNation → `accountingCorporateTaxJpn` | `plugin/accounting/jpn/portal/NextCorporateTax.php:88, 149` | **DDL 未定義** |

推測:
- これらは 13700 より前のバッチで作成された遺物 (履歴ファイルが消されている)
- または、別セットアップパス (管理画面の「初期化」操作、プラグイン初回起動時の動的 `CREATE TABLE`) で作られる
- または、当該機能は未実装 / 別途手動作成が必要

**Phase 1.2 以降、実機で `SHOW TABLES` して現状確認が必須**。

### 6.3 削除された機能の痕跡

- `Batch14100` が削除した Flash (`pending.swf`, `cake.swf`) は元々 DB テーブルを持っていない。
- インストール直後のみに存在する一時テーブルがある可能性。

---

## 7. 設計上のリスク / 癖

### 7.1 スキーマ設計

1. **外部キー (FK) がゼロ**。`idAccount`, `idEntity`, `idLog`, `idAccountTitle` 等の参照整合性はアプリケーション任せ。孤児レコードが溜まりやすい。
2. **PRIMARY KEY すらないテーブルが 8 つ**: `baseSession`, `baseLoginSecond`, `baseToken`, `baseLoginPassword`, `baseLoginIdLogin`, `baseLoginMiss`, `baseAccountId`, `accountingAccountId`。行数が増えると線形スキャンでしか検索できない。
3. **PK 以外のインデックスが事実上ゼロ**。`accountingLog(idEntity, numFiscalPeriod, stampBook)` など実運用のクエリパスに対する索引が配置されていない。全件 grep で `create index` / `add index` がマイグレーションに 0 件。
4. **文字コード・collation が DDL で固定されていない**。サーバ設定依存で、MySQL → MariaDB / バージョン差異で文字化けや unique 判定の揺れが起こり得る。
5. **日時は `bigint` epoch**。タイムゾーン情報を保持しない。アプリ側 `classTime` で TZ=9 固定。タイムゾーン跨ぎに脆弱。

### 7.2 データモデリング

6. **JSON カラムへの過度な集約**。仕訳 1 行が debit/credit 明細 + 承認履歴 + 版管理 + 消費税情報を 10+ 個の `longtext` JSON に分けて格納 (`accountingLog`, `accountingLogCash`, `accountingLogFixedAssetsJpn`)。検索・集計・部分更新が困難。
7. **`arrCommaX` / `arrSpaceX` は CSV シリアライズ**。SQL 側で `LIKE '%,id,%'` 検索になりがちで遅い。正規化テーブルが必要。
8. **論理削除 (`flagRemove` + `stampRemove`)** がある仕訳系テーブルはすべて物理削除ではない。累積すると肥大化。

### 7.3 税計算・会計ロジック

9. **消費税情報の散在**: 設定は `accountingEntityJpn` に 8 個の `flag*`。計算結果は `accountingLogCalcJpn.flagConsumptionTax`、合計値は `accountingFSValueJpn.jsonConsumptionTax` + `jsonConsumptionTaxDetail` (14800 後)、仕訳内は `arrCommaConsumptionTax(Debit|Credit)` と `arrCommaRateConsumptionTax(Debit|Credit)` に分散。複数バージョンの税率を抱えるため複雑化。
10. **承認ワークフロー (apply/permit/charge)** が `flagApply` + `idAccountApply` + `arrCommaIdAccountPermit` + `jsonPermitHistory` の 4 カラムで 1 セット。全仕訳系テーブルに同じカラム群が複製されている (DRY 違反)。

### 7.4 運用 / セキュリティ

11. **`baseLoginMiss.strPassword` が生文字列で蓄積**。ブルートフォース試行値がそのまま保存される (プライバシー/セキュリティ懸念)。
12. **`accountingBlueSheetJpn.blobData` が `blob` (64KB 上限)**。青色申告決算書データが大きくなると溢れる。現代なら `longblob` が必要。
13. **`varchar(15)` のまま IP 列 (`ip`) に保存**。IPv6 (最長 39 文字) に対応できない。
14. **Smarty テンプレートをマイグレーションバッチで zip 解凍する仕組み** (`Batch14310`)。現代なら CI/CD / Composer で扱うべきで、レガシー的。
15. **destructive migration**: `drop table if exists ... → create table` パターンなので、既存運用環境で「新規インストール」パスを踏むと全データ消失のリスク。

---

## 8. オープンクエスチョン

Phase 1.2 以降で確認する必要がある項目:

1. **本番の実際の行数**: `accountingLog`, `accountingLogCalcJpn`, `accountingLogFile`, `accountingLogBanks` の実レコード数は？ 移行コストに直結。
2. **本番での実際のインデックス追加**: migration は PK のみだが、DBA が手動で `CREATE INDEX` を追加している可能性あり。`SHOW INDEXES FROM <table>` で要確認。
3. **暗号化方式**: `strPassword` / `blobDetail` / `blobData` の暗号化アルゴリズムと鍵管理。Phase 1.1.b と相互参照。
4. **未定義テーブルの運用実態**: `accountingAdminMemo`, `accountingConsumptionTaxJpn`, `accountingCorporateTaxJpn` は本番にあるか？ あるなら DDL はどこで定義されたか？
5. **13700 以前のスキーマ進化**: 13700 は `numVersionThis` が 13700 を意味し、それ以前のバッチファイルが別ディレクトリに存在した可能性。履歴が消されているか要確認。
6. **collation 揺れ**: 本番 MySQL のデフォルト collation は `utf8_general_ci` / `utf8mb4_unicode_ci` / `latin1_swedish_ci` のいずれか。日本語データで検索 / Unique 判定が正しく動いているか。
7. **絵文字対応**: 現状 `utf8` (= 3 バイト) 想定。`utf8mb4` への移行は未対応。モバイル / SNS 連動時に注意。
8. **文字コード DDL の後付け**: マイグレーションで `ALTER TABLE ... CONVERT TO CHARACTER SET utf8mb4` が実行された形跡はあるか？
9. **`accountingFSValueJpn` のサイズ**: 1 行に FS 全体を詰めるため、大企業運用では `longtext` (4GB 上限) に迫る可能性。実測要。
10. **3 系統 DSN (master/slave/log)**: `connect.cgi` で定義されているが、現状はすべて同一ホスト (`db`) の同一 DB (`rucaro`)。将来の read replica 分離予定か、単に未使用か？

---

## 9. 新スキーマ設計への示唆 (ADR-002 入力)

Phase 1.2 の ADR-002「新スキーマ方針」で決めるべき項目:

### 9.1 保存すべき特性

- **エポック時刻による stamp**: 揺れない。ただしカラム型は `BIGINT` → `TIMESTAMPTZ` (PostgreSQL) / `DATETIME(6)` (MySQL8) に差し替え検討。
- **論理削除**: 会計データは消さない。ただし `flagRemove + stampRemove` の 2 列構成を継続するか、audit log テーブル化するか選択。
- **複数期サポート**: `idEntity + numFiscalPeriod` の複合キーは全テーブルに貫徹。新設計でも会計期を一級市民として扱う。
- **接頭辞命名**: `idX` / `numX` / `strX` / `flagX` / `jsonX` / `stampX` はチームの既習知識。そのまま残す価値あり。

### 9.2 変更すべき点

1. **JSON blob の正規化**: `accountingLog` の `arrCommaId...Debit/Credit` 群と `jsonVersion` は関連テーブル (仕訳明細 `accountingLogItem`, 承認ログ `accountingLogApproval`) に分解すべき。
2. **`arrCommaX` / `arrSpaceX` の正規化**: CSV シリアライズをやめて関連テーブルへ。または `ARRAY<T>` (PG) / `JSON` (MySQL8) へ型変更。
3. **外部キー制約**: すべての `idX` は FK 化。孤児レコードゼロを保証。
4. **インデックス設計**: 最低限 `accountingLog(idEntity, numFiscalPeriod, stampBook)`, `accountingLog(idEntity, numFiscalPeriod, flagRemove, stampBook)`, `accountingLogCalcJpn(idEntity, numFiscalPeriod, idAccountTitle)`, `baseSession(idCookie)`, `baseSession(idAccount)` を追加。
5. **承認ワークフロー統一**: 全テーブルに散らばる `flagApply / idAccountApply / arrCommaIdAccountPermit / jsonPermitHistory / jsonChargeHistory` を共通の `workflow_step` テーブル + ポリモーフィック参照へ。
6. **税務ロジックの一元化**: `accountingTax` マスタ (rate / flagReduced / stampStart / stampEnd) を立てて、仕訳側は id のみ持つ。
7. **IPv6 対応**: `ip varchar(15)` → `inet` (PG) / `varchar(45)` (MySQL) へ。
8. **文字コード**: `utf8mb4` / `CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci` (MySQL 8) / `UTF8` (PG) を DDL レベルで固定。
9. **PRIMARY KEY 必須化**: セッション系・ログイン系の 8 テーブルは `id` 列 or 複合 PK を追加。
10. **blob サイズ**: `accountingBlueSheetJpn.blobData` は `longblob` へ、または別ストレージ (S3/R2/MinIO) へ外出し。
11. **パスワード再ハッシュ**: Argon2id / bcrypt `password_hash()` への移行。`baseLoginMiss.strPassword` のログ停止 (失敗カウントのみ残す)。
12. **migration ツール**: 独自 `BatchXXXXX.php` 方式を Liquibase / Flyway / Phinx / Alembic へ移行。

### 9.3 新規検討項目

- **多国対応**: `*Jpn` 接尾辞のテーブル分割をやめ、型を共通化 + `nation` 列を導入。
- **Audit Log 統合**: テーブル単位 `jsonVersion` を共通 `audit_event(entity_id, entity_type, diff, stamp, actor)` へ。
- **JSON 利用**: JSON を残すなら `JSONB` (PG) / `JSON` + functional index (MySQL8) にして検索性能を担保。
- **multi-tenant 化**: 現状は 1 Deployment = 1 会社集合だが、SaaS 化するなら `tenant_id` を全テーブルに持たせて RLS / Row-Level Security を設計。

### 9.4 互換性方針の選択肢

- **Big Bang 移行**: 全 59 テーブルを新設計 (PostgreSQL, 正規化済) に写経移行。ダウンタイム大。
- **段階移行**: `baseSession`, `baseAccessLog` などログイン周辺だけ先に切り出し、会計コアは維持。
- **Read Replica Bridge**: レガシー DB を read-only で残し、書き込みは新スキーマへ。Dual-write 期間が必要。
- **CDC (Debezium / Airbyte)**: レガシー MySQL の binlog を新 DB に流し続け、検証後に切り替え。

---

## 10. Summary

Rucaro Accounting は **MySQL InnoDB / 59 テーブル (base 20 + accounting 39) / PK 以外の索引ゼロ / FK ゼロ / JSON 多用 / エポック bigint 時刻 / 命名規則ハンガリアン** という独特なレガシー設計。9 個の PHP マイグレーションバッチで 13700 → 14800 まで進化し、最新は軽減税率対応 (14800, 2019-10-01)。外部キー不在・論理削除・JSON blob 中心が **最大のリスク** で、Phase 1.2 の新スキーマ ADR では FK / インデックス / 正規化 / utf8mb4 固定 / PK 必須化を中核方針に据えるべき。
