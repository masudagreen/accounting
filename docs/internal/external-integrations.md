# Phase 1 補遺: 外部連携の解析（5 銀行 / メール取込 / ファイル取込）

> 調査者: Explore サブエージェント
> 対象: `C:\Users\yusuk\StudioProjects\accounting\back\class\else\plugin\accounting\`
> 目的: 旧 Rucaro Accounting が持つ外部連携の実装を root-cause から解析し、Phase 5 の再実装に直接使える仕様書を残す。
> 前提: [encrypted-columns.md](../phase1/encrypted-columns.md), [legacy-schema.md](../phase1/legacy-schema.md)

---

## 1. 概要

旧アプリの外部連携は 3 カテゴリ・合計 3 経路。**すべて手動トリガ（UI からユーザが押下）で走る。cron / バッチスケジューラは不在**（`Routine.php` は中身が空のスタブ）。

| カテゴリ | 対象 | 頻度 | 認証情報格納 | エントリクラス |
|---|---|---|---|---|
| ネット銀行取込（Web） | 5 行（後述） | 手動 | `accountingLogBanksAccount.blobDetail` (Blowfish) | `BanksImportWeb` |
| ネット銀行取込（ファイル） | 同 5 行 | 手動 | — （CSV ファイル直接アップロード） | `BanksImportFile` |
| メール取込 — 明細 | 任意 IMAP | 手動 | `accountingLogMailJpn.strPassword` (Blowfish) | `LogImportMail` |
| メール取込 — 領収書ファイル | 任意 IMAP | 手動 | `accountingFile.strPassword` (Blowfish) | `FileImport` → `CalcFileImport` |
| ファイル取込（手動アップロード） | Yayoi / Rucaro CSV | 手動 | — | `LogImportListYayoi`, `LogImportListRucaro` |

**合計外部接続先は 2 種類のみ**:
1. `https://rucaro.org/banks.php`（旧作者の中央スクレイピングプロキシ）
2. 任意の IMAP サーバ（ユーザ指定）

---

## 2. 5 銀行共通パターン — **核心的発見**

### 2.1 実装上の事実

**5 銀行クラス（`Japannetbank.php` 他）は実際の API/スクレイピングクライアントではなく CSV パーサである**。

- いずれも `Code_Else_Plugin_Accounting_Jpn_CalcBanks` を継承するただの「CSV 列マッピング/検証ロジック」置き場。
- 主な実装メソッドは `_checkValueDetailCSV()`（ファイル取込時）と `_checkValueDetailVars()`（Web 取込時）の 2 つ。いずれも「1 行の連想配列を `stampBook / strTitle / numValueIn / numValueOut / numBalance / arrSpaceStrTag` に正規化してバリデーション」するだけ。
- HTTP を叩いているのは**共通親の外側**にいる `BanksImportWeb.php` と `BanksAccountEditor.php` だけ。接続先は**常に** `PATH_INFO_SSL . 'banks.php'`（= `https://rucaro.org/banks.php`）。

```
  ┌───────────────┐  ① POST jsonData ┌────────────────────────┐
  │ BanksImportWeb├─────────────────►│ rucaro.org/banks.php   │
  │   .php        │                   │ (中央スクレイピング proxy)│
  └───────────────┘◄─────────────────└────────────────────────┘
        │            ② JSON { arrayCSV }
        ▼
  ┌─────────────────────┐
  │ CalcBanks_*         │ ← 銀行別サブクラス（CSV→共通スキーマ）
  └─────────────────────┘
        │
        ▼
  `accountingLogBanks` (INSERT) + 任意で `accountingLog` (自動仕訳)
```

### 2.2 中央プロキシへのリクエスト仕様

`BanksImportWeb.php:656-680` と `BanksAccountEditor.php:102-126` の 2 箇所に**同一のペイロード構造**で実装されている。

| 項目 | 値 |
|---|---|
| URL | `PATH_INFO_SSL . 'banks.php'` （= `https://rucaro.org/banks.php`）|
| Method | `POST` (application/x-www-form-urlencoded) |
| Timeout | 60 秒 |
| `CURLOPT_SSL_VERIFYPEER` | **`false`**（＝証明書検証なし。脆弱） |
| `CURLOPT_FOLLOWLOCATION` | `true` |

**POST フィールド**:

```php
$params = [
    'cache'      => MICROTIMESTAMP,                  // キャッシュバスタ
    'jsonData'   => json_encode($arrayNew),          // 複数口座分まとめて
    'accessCode' => $varsPluginAccountingPreference['accessCode'], // 中央側のアクセスコード
    'version'    => NUM_VERSION,                     // アプリバージョン
];
```

**`jsonData` の構造**（`BanksImportWeb.php` 内コメント `636-655` より）:

```json
[
  {
    "id": 1,
    "stampCheck": 1234567890,
    "flagBank":   "japannetbank",
    "varsDetail": { "IdNumBranch":"001", "IdNumAccount":"1234567", "StrPassword":"..." }
  },
  ...（最大 10 件まで）
]
```

- 1 回の呼び出しで最大 10 口座まで。`$num == 10; break;` （`BanksImportWeb.php:633`）。
- 並び順は `stampCheck`（前回同期時刻）昇順。古い順に優先。
- `varsDetail` は**この時だけ平文**。送信直前に `blobDetail` を復号して載せる。

### 2.3 レスポンス JSON の期待形式

`BanksImportWeb.php:708-737` のコメント:

```json
{
  "flag": "success",
  "varsData": [
    {
      "id": 1,
      "flag": "success",
      "flagBank": "japannetbank",
      "strComment": "",
      "flagErrorDetail": "",
      "strErrorComment": "",
      "arraySign": [],
      "arrayCSV": [
        {
          "strYear": "2014", "strMonth": "02", "strDate": "10",
          "strHour": "08", "strMin": "32", "strSec": "45",
          "strNum": "00001",
          "strTitle": "振込 ヤフーケツサイ",
          "numValueIn": "", "numValueOut": "259",
          "flagIn": 0, "numBalance": "7001"
        }
      ]
    }
  ]
}
```

エラー時:

```json
{ "flag": "flagErrorComment", "varsData": { "strComment": "..." } }
```

### 2.4 blobDetail → accountingLog までの全体フロー

```
BanksImportWeb.run()                         ユーザが「取込実行」押下
  └─ _checkWebValue()                        権限 / accessCode チェック
  └─ _getVarsDataResponse()
       ├─ for each 口座:
       │   └─ CalcBanks.allot('checkVarsAttest') ← blobDetail 復号 + varsDetail 取得
       ├─ POST rucaro.org/banks.php          中央プロキシが実際にスクレイピング
       └─ JSON parse
  └─ CalcBanksImport.allot('checkVarsCSVBanks')  銀行別サブクラスに検証を委譲
       └─ CalcBanks_Japannetbank._iniCheck() → _setCheck() → _checkValueDetailVars()
            ├─ 期間チェック（会計年度範囲内か）
            ├─ 重複チェック（既存 accountingLogBanks と stampBook+金額で一致するか）
            └─ エラー/パス/インポート 3 配列に振り分け
  └─ CalcBanksImport.allot('runAdd')
       └─ for each 正常行:
            └─ CalcBanks.allot('add')
                 └─ INSERT accountingLogBanks（残高明細）
  └─ （任意）CalcBanksImport.allot('runAddLog')
       └─ flagAutoImport==1 なら accountingLog にも自動仕訳 INSERT
  └─ CalcBanksImport.allot('runWriteHistory')  UPDATE jsonWriteHistory
```

### 2.5 既知の設計上の問題

- **中央集権の単一障害点**: `rucaro.org/banks.php` が死ぬと全ユーザの銀行連携が停止。ドメイン自体が既に提供停止の可能性大（→ 要追加調査）。
- **TLS 証明書検証無効**: MITM 攻撃に脆弱。
- **入力検証なし**: プロキシが返す JSON をそのまま `accountingLogBanks` に INSERT。プロキシが侵害されれば即時 DB 汚染。
- **レート制限なし**: クライアント側の抑制ロジック皆無。
- **機密情報中継**: ログインパスワード・合言葉を自社サーバに中継している時点で金融機関 ToS 違反の可能性。

---

## 3. 銀行別詳細

5 銀行の**共通仕様**（`back/tpl/vars/else/plugin/accounting/ja/dat/jpn/banks/*.php` の `$vars` 配列より）:

| 銀行 | `flagBank` | `strFileType` | ログイン URL | 認証情報（`varsDetail`） |
|---|---|---|---|---|
| ジャパンネット銀行 | `japannetbank` | `csv` | `https://login.japannetbank.co.jp/login_L.html` | 店番号3桁 / 口座番号7桁 / ログインパスワード |
| ゆうちょ銀行 | `japanpostbank` | （なし＝CSV DL 非対応） | `https://direct.jp-bank.japanpost.jp/tp1web/U010101SCK.do` | お客さま番号4+4+5桁 / ログインパスワード / 質問1-3 + 合言葉1-3 |
| じぶん銀行 | `jibunbank` | `csv` | `http://www.jibunbank.co.jp/` | お客さま番号10桁 / ログインパスワード（6-16文字） |
| 住信SBIネット銀行 | `sumisinnetbank` | `csv` | `https://www.netbk.co.jp/wpl/NBGate` | ユーザネーム / WEB ログインパスワード |
| スルガ銀行 | `surugabank` | `csv` | `https://ib.surugabank.co.jp/im/IBGate` | ユーザネーム / ログインパスワード |

### 3.1 ジャパンネット銀行（`japannetbank`）

- **API**: 公式 API 非対応。旧実装は rucaro.org プロキシ経由スクレイピングと推定。
- **認証**: ID/PW のみ（当時はワンタイムトークン未対応）。
- **CSV 列**: `操作日(年/月/日)`, `操作時刻(時/分/秒)`, `取引順番号`, `摘要`, `お支払金額`, `お預り金額`, `残高` — 日時を年月日時分秒に分解した粒度。
- **blobDetail**: `{"IdNumBranch":"001","IdNumAccount":"1234567","StrPassword":"..."}`。
- **レート制限**: クライアント側なし。
- **例外ハンドリング**: `_checkValueDetailError()` で残高/金額/日付の形式・範囲のみ検査。HTTP エラーは `var_dump($output); exit;`（！）で即終了（`BanksImportWeb.php:681-682`）。
- **2024 時点の状況**: ジャパンネット銀行は**2021 年に PayPay 銀行へ社名変更**。旧スクレイパはほぼ確実に動作不能 → 要再実装。

### 3.2 ゆうちょ銀行（`japanpostbank`）

- **特殊**: `flagCsv => 0, strFileType => ''` — **CSV ダウンロード非対応。Web スクレイピング専用**。
- **認証**: お客さま番号（4+4+5 桁）/ パスワード / **合言葉 3 種（質問選択＋回答）**。
- **`flagSignBtn => 1`**: 他行と異なり「質問挿入」ボタンがあり、質問選択肢を動的に増やせる UI。スクレイピング側が質問文を返す必要あり。
- **blobDetail**: `{"IdNumAccount1":"1234","IdNumAccount2":"5678","IdNumAccount3":"90123","StrPassword":"...","StrSignQuestion1":"...","StrSignAnswer1":"...",...}`
- **レート制限**: クライアント側なし。
- **2024 時点**: ゆうちょダイレクトは**トークン認証/生体認証必須化**で、ID/PW+合言葉単独ログイン不可 → 旧実装は動作不能。

### 3.3 じぶん銀行（`jibunbank`）

- **特殊（コード上）**: `_iniUpdateArrayCsv()` で `array_shift($arrayCSV); array_shift($arrayCSV);` を 2 回実行 — 先頭 2 行スキップ（ダウンロード CSV にヘッダが 2 行あるためと推測。要追加調査）。
- **認証**: お客さま番号 10 桁 / ログインパスワード（6〜16 文字）。
- **CSV 列**: `年月日`, `入金`, `出金`, `お取引内容`, `残高`。
- **2024 時点**: じぶん銀行は**2022 年に au じぶん銀行へリブランド**。ID/PW のみログインは段階的廃止。

### 3.4 住信SBIネット銀行（`sumisinnetbank`）

- **認証**: ユーザネーム / WEB ログインパスワード。
- **CSV 列**: `日付`, `内容`, `出金金額(円)`, `入金金額(円)`, `残高(円)`, `メモ`。
- **2024 時点**: SMS 認証必須化済み → ID/PW のみでは不可。

### 3.5 スルガ銀行（`surugabank`）

- **特殊（コード上）**: `_iniUpdateArrayCsv()` で `array_pop($arr['arrayCSV'])` — 末尾 1 行削除（CSV 末尾のフッタ行対策と推測）。
- **認証**: ユーザネーム / ログインパスワード。
- **CSV 列**: `日付`, `お支払い`, `お預り`, `摘要`, `取引区分`, `残高`, `メモ`。
- **特徴**: `strTitle` = `摘要 + ' ' + 取引区分` と連結してタイトル化（`Surugabank.php:186, 296`）。
- **2024 時点**: トークン認証移行済み。

### 3.6 5 行すべての共通例外ハンドリング

- API 呼び出し失敗時: `BanksImportWeb.php:688-706` で `varsResponse == null` なら `'dataError'`、`varsResponse['flag'] != 'success'` なら `strComment` を画面に表示。
- 個別行の検証エラー: `_checkValueDetailError()` が `strMissBank / strLockBank / strMissNumValue / strMissNumBalance / strMissStrTitle / strFormat / strTime / strNumMin / strFormatNumValue / strFormatNumBalance / strNumMax / strNumBalanceMax` のいずれかを返し、`arrError` 配列に積まれて画面表示。
- **リトライなし、Circuit Breaker なし、指数バックオフなし**。

---

## 4. メール取込（IMAP 明細取込）

**ソース**: `CalcLogImportMail.php`（明細 CSV）, `CalcFileImport.php`（領収書ファイル）— どちらも**ほぼ同じ IMAP ロジック**。

### 4.1 接続仕様

`CalcLogImportMail.php:58-141`:

```php
$strPassword = $classCrypte->setDecrypt($varsPreference['strPassword']);  // Blowfish 復号
$strSecure = '/imap/notls' | '/imap/tls/novalidate-cert' | '/imap/ssl/novalidate-cert';
$strServer = "{$strHost:$numPort$strSecure}INBOX";
$mbox = @imap_open($strServer, $strUser, $strPassword);
```

| 項目 | 値 |
|---|---|
| プロトコル | IMAP（`imap_open`）— PHP ext/imap 必須 |
| TLS | `none` / `starttls`（**証明書検証なし**）/ `ssl`（証明書検証なし） |
| ボックス | `INBOX` のみ（固定） |
| 検索 | `imap_search($mbox, 'UNSEEN')` — 未読のみ |
| 認証情報テーブル | `accountingLogMailJpn`（国別サフィックス） |
| 列 | `strHost / strUser / strPassword(enc) / numPort / flagSecure / jsonMail / jsonMailHost` |

### 4.2 送信元フィルタ

以下の **OR** で通過:
1. `From` のホスト部が `jsonMailHost` 連想配列に存在（ドメイン単位許可）
2. `From` 全体が `jsonMail` 連想配列に存在（メアド単位許可）
3. `From` が任意アカウントの `strMailFile` と一致（`CalcFileImport` のみ — 領収書アップロード専用メアド）

非該当メールは **読み捨て**（`imap_setflag_full(..., '\Seen')` で既読化し処理スキップ）。

### 4.3 件名＝アカウント紐付け

`Subject` を MIME デコード → `strCodeName` として取得。
`$varsAccountName[$strCodeName]` が該当すればそのアカウントに紐付け、なければアカウント ID=1（管理者）扱い。

### 4.4 添付ファイル処理

- Content-Disposition が `attachment` のパートのみ処理。
- サイズ制限: `NUM_MAX_UPLOAD_SIZE`。
- `CalcLogImportMail`: **`.csv` のみ受理**（他形式は無視）。Base64 デコード → `PATH_BACK_DAT_TEMP` に `sha256` ファイル名で保存 → `nkf -wLu --overwrite` で文字コード正規化（**外部コマンド依存 — PATH に nkf が必要**）。
- `CalcFileImport`: `jsonFileType` で指定された拡張子を受理（csv / pdf / png / jpg / jpeg / gif / bmp / ... 設定次第）。画像は `getimagesize()` で幅高取得。保存先は `PATH_BACK_DAT_FILE . accounting/$idEntity/$numFiscalPeriod-YYYYMM/`。

### 4.5 PDF/画像のパース

**しない**。添付ファイルはファイル登録（`accountingLogImportJpn` の添付参照エントリ追加）のみ。OCR や PDF→テキスト抽出は未実装。ユーザが後で手動で仕訳を付ける想定。

### 4.6 現状の稼働可否

**現 Docker イメージでは動作不能**。`docker/Dockerfile:28-35` を見る限り、`docker-php-ext-install` で `imap` 拡張をインストールしていない。`pdo_mysql / mysqli / gd / intl / zip / bcmath / opcache` のみ。  
→ IMAP 取込を有効化するには Dockerfile に `apt-get install libc-client-dev libkrb5-dev && docker-php-ext-configure imap --with-kerberos --with-imap-ssl && docker-php-ext-install imap` 追加が必要。もしくは Phase 5 で別実装に置き換え。

### 4.7 既知の問題

- `novalidate-cert` で TLS 検証無効化。
- パスワード復号後にメモリ常駐。`imap_close()` まで握る。
- `` `nkf -wLu --overwrite $strUrl` ``（バッククォート = shell exec）— パスに未サニタイズ値を渡す可能性（`$strUrl` は `sha256()` ベースなので現状は安全だが脆い実装）。

---

## 5. ファイル取込

### 5.1 手動 CSV アップロード（銀行用）

- エントリ: `BanksImportFile.php`
- `_checkValueFile()`（`BanksImportFile.php:521-566`）で `$_FILES` からマルチアップロードを受理。
- 拡張子チェック: `$varsBanks['strFileType']` と一致必須（銀行ごとに固定。`csv` のみ）。
- サイズ上限: `NUM_MAX_UPLOAD_SIZE`。
- `move_uploaded_file()` → `nkf -wLu --overwrite` で文字コード正規化 → `CalcBanks_*` に渡してパース。
- **FTP 取込は未実装**（`strHost / strUser / strPassword` は IMAP 用のみ）。

### 5.2 手動 CSV アップロード（仕訳用）

- エントリ: `LogImportListRucaro`（Rucaro 形式）, `LogImportListYayoi`（弥生会計形式）
- 基底クラス `LogImportList` が共通ロジックを持ち、サブクラスで形式ごとの差分を吸収。

| 派生クラス | 入力 CSV 形式 | 特有処理 |
|---|---|---|
| `LogImportListRucaro` | Rucaro ネイティブ CSV（`stampBook / strTitle / numValue / ...`） | `_iniDetailAdd()` で `_setDetailAdd()` 親呼び出しのみ（差分なし） |
| `LogImportListYayoi` | 弥生会計書出 CSV（勘定科目日本語名付き） | `_updateVarsRule()` で `yayoiConvert.php`（勘定科目マッピング表）を読込。`_getVarsTaxConvert()` で消費税区分を弥生←→Rucaro 変換。税区分 `flagConsumptionTaxFree / Including / GeneralRule` を考慮。|

`LogImportListYayoi._childSelf['pathVarsYayoiConvert']` = `back/tpl/vars/else/plugin/accounting/ja/dat/jpn/yayoiConvert.php` — ここに弥生勘定科目 ↔ Rucaro 勘定科目のマッピング表が存在（本解析では未展開。再実装時に読むこと）。

### 5.3 OFX / 全銀協フォーマット / QIF

**未対応**。検索しても `ofx / qif / xml（全銀）` の取込ロジックなし（`FilePreference.php` の `jsonFileType` は「拡張子許可リスト」であって**パース実装を伴わない**）。

### 5.4 受理される拡張子（全体）

| 経路 | 拡張子 |
|---|---|
| 銀行 Web 取込 | （ファイル不使用。中央プロキシから JSON） |
| 銀行 File 取込 | 銀行ごとに `strFileType` 固定（5 行中 4 行が `csv`、ゆうちょは未対応） |
| メール添付（明細） | `.csv` のみハードコード |
| メール添付（領収書） | `jsonFileType` で設定（典型: csv / pdf / png / jpg / jpeg / gif） |
| 手動仕訳アップロード | Rucaro 形式 CSV / 弥生形式 CSV |

---

## 6. 再実装設計（Phase 5 向け）

### 6.1 設計原則

1. **中央スクレイピングプロキシは捨てる**。`rucaro.org/banks.php` は失効前提。
2. **公式 API 優先**。API がない銀行は「CSV ファイル取込のみ」でサービス開始。
3. **Adapter Pattern + DI**。銀行ごとにドライバを差し替え可能に。
4. **資格情報は Secret Manager に移す**（DB 暗号化はフォールバック）。
5. **IMAP は別プロセス化**（ポーリングジョブ分離 / 将来 OAuth2 IMAP 対応）。

### 6.2 推奨パッケージ構成

```
src/
├── Domain/
│   ├── BankTransaction/
│   │   ├── Entity/BankTransaction.php
│   │   ├── Entity/BankAccount.php
│   │   └── Repository/BankTransactionRepositoryInterface.php
│   └── Import/
│       ├── Entity/ImportJob.php
│       └── Entity/ImportSource.php
│
├── Application/
│   ├── UseCase/ImportBankTransactions/     ← 銀行明細の取込ユースケース
│   ├── UseCase/ImportFromMail/             ← IMAP 取込ユースケース
│   └── UseCase/ImportFileUpload/           ← 手動ファイルアップロード
│
└── Infrastructure/
    ├── BankConnector/
    │   ├── BankConnectorInterface.php       ← fetchTransactions(range, credentials)
    │   ├── BankConnectorRegistry.php        ← flagBank→driver 解決
    │   │
    │   ├── PayPayBank/                      ← 旧 Japannetbank
    │   │   ├── PayPayBankCsvParser.php      ← 現時点では確実に動く
    │   │   └── PayPayBankApiClient.php      ← 公式 API（将来: OAuth2）
    │   ├── JapanPost/
    │   │   └── JapanPostCsvParser.php       ← スクレイピング撤廃
    │   ├── AuJibun/                         ← 旧 Jibunbank
    │   │   └── AuJibunCsvParser.php
    │   ├── SbiSumishin/                     ← 旧 Sumisinnetbank
    │   │   └── SbiSumishinCsvParser.php
    │   └── Suruga/
    │       └── SurugaCsvParser.php
    │
    ├── Import/
    │   ├── MailImport/
    │   │   ├── ImapMailImporter.php         ← ext/imap 利用
    │   │   ├── SymfonyMailerImapAdapter.php ← 推奨: php-imap/php-imap
    │   │   ├── MailAttachmentPersister.php
    │   │   └── SenderFilter.php             ← jsonMail / jsonMailHost 相当
    │   │
    │   └── File/
    │       ├── CsvFileImporter.php
    │       ├── YayoiCsvParser.php           ← 旧 LogImportListYayoi を移植
    │       ├── RucaroCsvParser.php          ← 旧 LogImportListRucaro を移植
    │       ├── OfxParser.php                ← 新規（openfinance/ofx-parser など）
    │       └── ZenginCsvParser.php          ← 新規（全銀協標準）
    │
    └── Secrets/
        ├── SecretManagerInterface.php
        ├── EnvSecretManager.php              ← dev: env var
        └── AwsSecretsManagerAdapter.php      ← prod
```

### 6.3 BankConnectorInterface の最小契約

```php
interface BankConnectorInterface
{
    public function getBankCode(): string;

    /**
     * @return iterable<BankTransaction>
     */
    public function fetchTransactions(
        BankAccount $account,
        DateRange $range,
        BankCredentials $credentials
    ): iterable;

    /** @return list<CredentialField> credentials スキーマ */
    public function getCredentialSchema(): array;

    public function supportsLiveFetch(): bool;   // API/スクレイピング対応か
    public function supportsCsvImport(): bool;   // ファイル取込対応か
}
```

### 6.4 銀行別の現状評価と推奨実装

| 銀行（現名） | 旧 flagBank | 現状 | Phase 5 推奨 |
|---|---|---|---|
| **PayPay 銀行** | japannetbank | 公式 API なし。SMS / アプリ認証必須化。スクレイピング極めて困難。 | CSV 取込のみ先行実装。公式 API が出たら追加。 |
| **ゆうちょ銀行** | japanpostbank | ゆうちょダイレクト API は法人向け API Gateway 経由のみ提供。個人向け API なし。 | 個人: CSV 取込のみ。法人: `ゆうちょ Web API`（要審査）対応を将来追加。 |
| **au じぶん銀行** | jibunbank | 公式 API なし。スクレイピングは SMS 認証でほぼ不可。 | CSV 取込のみ。 |
| **住信 SBI ネット銀行** | sumisinnetbank | **公式 API あり（住信 SBI API）**。法人口座は API 経由で取引履歴取得可。認証は OAuth2 / API Key。 | Phase 5.1 で API 実装。資格情報は OAuth リフレッシュトークン。 |
| **スルガ銀行** | surugabank | API 公開なし。FinTech 連携は Moneytree 経由のみ。 | CSV 取込のみ。将来的に Moneytree/Money Forward Cloud API で間接連携。 |

**結論**: Phase 5 初期リリースでは**全行 CSV ファイル取込のみ**を実装し、API 連携は「住信 SBI 先行、他行は後追い」の段階リリースが現実解。旧 Web 取込相当の体験を提供するなら、**Money Forward クラウド会計 / freee / Moneytree のアグリゲータ API を買う**のが最短（自前スクレイピング保守は詰む）。

### 6.5 メール取込の再設計

- ext/imap は非推奨化傾向 → `php-imap/php-imap`（Webklex）を採用。
- TLS 検証を**デフォルト有効**（`novalidate-cert` はユーザが明示的に opt-in したときのみ）。
- 件名→アカウント紐付けは残すが、`Subject` 完全一致ではなく `[Accounting:alice]` のようなタグ規約に変更推奨。
- 添付パースを「ハンドラチェーン」に: CSV→仕訳インポート、PDF→OCR（将来）、画像→領収書として保存（現行同等）。
- `nkf` 外部コマンド依存を `mb_convert_encoding` へ置換。
- **ポーリングをキューワーカ化**: `php artisan queue:work` 相当のデーモンで 5 分ごとに `fetchUnseen()` 実行。

### 6.6 ファイル取込の再設計

- 全ファイル受理を **Pipeline Pattern**に:
  `UploadedFile → MagicByteDetector → Parser (Csv|Ofx|Zengin|Yayoi) → Normalizer → Validator → ImportJob`
- Yayoi 変換表はコードから JSON / YAML 設定ファイルに外出し（`config/yayoi-mapping.yaml`）。
- OFX / QIF は新規サポート（`openfinance/ofx-parser`, `schneidtech/qif-parser` 等）。
- 全銀協標準フォーマット（入出金明細 CSV）も追加（日本の上場企業で主流）。

### 6.7 セキュリティ・信頼性の最低ライン

- TLS 検証を**常に有効**。
- HTTP タイムアウトとリトライ（指数バックオフ）を必須。
- 取込ジョブを**冪等**に設計（`accountingLogBanks` への UNIQUE 制約相当: `stampBook + idLogAccount + numValueIn + numValueOut + numBalance`）。
- 資格情報ローテーション機構（`credentials_rotated_at` 列追加、期限切れ検知）。
- 取込イベントを `import_job_events` テーブルに全記録（監査ログ）。

---

## 7. 要追加調査事項

1. `rucaro.org/banks.php` の**中央プロキシ実装**（Rucaro 本家側にソースがあるはず）— スクレイピング詳細と `arraySign` / `flagErrorDetail` の意味。
2. `back/tpl/vars/else/plugin/accounting/ja/dat/jpn/yayoiConvert.php` の勘定科目マッピング表全貌。
3. じぶん銀行 CSV の**先頭 2 行スキップ**の理由（フッタ行でなくヘッダ行 2 行構成なのか、空行なのか）。
4. `flagAutoImport` が ON/OFF で何が変わるか詳細（`accountingLog` 自動仕訳の条件・ルール）。
5. `accountingLogImportJpn` テーブル側のリトライロジック（`LogImportRetry.php` は本解析範囲外）。
6. 公式 API 対応銀行の追加調査: **ソニー銀行 / 楽天銀行 / 東京スター銀行 / GMO あおぞらネット銀行**（特に GMO あおぞらは法人向け銀行 API を積極提供）。

---

## 8. サマリ

- **5 銀行連携は実質「中央スクレイピングプロキシへのフォワード + CSV 正規化」**。銀行個別の API 実装は旧コードベース内には存在しない。
- **メール取込は IMAP ベース**、CSV / 画像 / PDF の添付を保存。PDF OCR は未実装。
- **ファイル取込は Rucaro / 弥生 CSV の 2 形式のみ対応**。OFX / QIF / 全銀協は未対応。
- **バッチ/cron なし**、すべて UI 手動トリガ。`Routine.php` は空スタブ。
- **現 Dockerfile では IMAP 拡張未搭載** — 旧メール取込は即時には動かない。
- **Phase 5 推奨**: 中央プロキシは廃止、CSV 取込を全行で提供、公式 API 対応は住信 SBI 先行、PDF OCR とアグリゲータ API 連携は後続リリース。
