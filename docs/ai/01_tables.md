# テーブル定義一覧（会計用語訳付き）

## 凡例

- **物理名**: DB上の実際のテーブル名
- **和訳（会計用語）**: 移植時に推奨する日本語名。会計実務で一般的に使われる用語にあわせる。
- **役割/概要**: 何を保持するか、どの画面/機能で使われるか
- **キー列**: 主キー / 識別に使われる列（外部キー制約は元実装には無い）
- **JSON列の構造**: 元コードのコメントから読み取れる主要構造
- **移植時の注意**: 設計上問題のある点や検討ポイント

> 元実装の DDL は `back/tpl/templates/else/plugin/accounting/db/config.php`（accounting プラグイン）と `back/tpl/templates/else/core/base/db/config.php`（コア基盤）で定義され、`Rebuild` クラスから流し込まれる。  
> 全テーブルが `InnoDB`、命名は `<module><Entity><Lang?>` のキャメルケース。

---

## A. コア基盤テーブル（`base*`）

ユーザー認証/権限/監査ログなど、会計とは独立した基盤層。

### baseAccount — アカウント（利用者）
| 列 | 型 | 用途 |
|---|---|---|
| id | bigint UA PK | アカウント内部ID |
| stampRegister / stampUpdate | bigint | 登録/更新タイムスタンプ |
| flagLock | int | アカウントロック有無 |
| flagWebmaster | int | サイト管理者フラグ |
| strCodeName | varchar(100) | システム内表示名 |
| idLogin | text | ログインID |
| strPassword | text | ハッシュ化パスワード |
| stampUpdatePassword | bigint | パスワード更新時刻 |
| strMailPc | text | PCメールアドレス |
| flagLoginMail | int | ログイン通知 |
| flagLoginSecond | int | 二段階認証 |
| strMailMobile / idMobile / strMobileCarrier | text | 携帯メール（旧仕様） |
| numTimeZone / strLang / strHoliday | int/varchar | 表示設定 |
| numList / numAutoLogout / numAutoPopup | int | 一覧件数/自動ログアウト/自動ポップアップ |
| strAutoBoot | varchar | 自動ブートモジュール |
| idTerm / idModule | bigint | 有効期間 / モジュールパターン |
| arrSpaceStrTag | mediumtext | タグ（空白区切り） |
| jsonStampCheck | mediumtext | お知らせ既読タイムスタンプ |

### baseAccountId — アカウントIDコード（ID→表示名 索引）
表示名 (`strCodeName`) と内部ID (`id`) の対応索引（高速参照用）。

### baseAccountMemo — アカウントメモ
任意フリーフォーマット情報（JSON）

### baseTerm — 有効期間パターン
アカウントの利用期間（開始/終了スタンプ）。

### baseModule — モジュールパターン
アカウントが利用できるモジュール（一般用/管理用配列）。

### baseAccessLog — アクセスログ（監査）
全リクエストのIP/ホスト/モジュール/関数/クエリを記録。

### baseAccessUnknown — 未知アクセスIPバッファ
通知済かを判別するための一時的なIPバッファ。

### basePublish — 発行済セッション
ログイン中の認証発行履歴。

### baseLock — アカウントロック
ロックされたアカウント。

### baseApplySign — アカウント登録申請
新規登録フォームから申請されたデータ。

### baseApplyChange — アカウント変更申請
登録情報の変更申請。

### baseApplyForgot — パスワード再発行申請

### baseApiAccount — API アカウント
外部システム連携用 API キー紐付け。

### baseSession — セッション
アクティブなログインセッション。

### baseLoginSecond — 二段階認証セッション
2FA 用の一時セッション。

### baseToken — トークン
ワンタイムトークン（メール内リンク等）。

### baseLoginPassword — パスワード履歴
過去パスワード（再利用検出用）。

### baseLoginIdLogin / baseLoginMiss — ログイン試行記録
失敗回数のロックや異常検出用。

### basePreference — システム設定
サイト名/メール/IP拒否許可/メンテナンス状態 等の単一行設定テーブル。

---

## B. 会計プラグイン共通テーブル（`accounting*`、国非依存）

### accountingPreference — 会計プラグイン設定
| 列 | 用途 |
|---|---|
| flagMaintenance, arrCommaIdAccountMaintenance | メンテナンス中フラグ/担当アカウント |
| accessCode | 外部公開用アクセスコード |
| flagIdAccountTitle | 勘定科目ID表示フラグ |
| jsonStampUpdate | 機能ごとの最終更新スタンプ |
| jsonIdAutoIncrement | ID採番の自前管理マップ |
| strVersion / jsonVersion | バージョン履歴 |

### accountingAccount — 会計アカウント設定
アカウントごとの「現在の作業事業体」「現在の会計年度」等。
- idAccount, idEntityCurrent, numFiscalPeriodCurrent
- arrCommaIdEntity（アクセス可能事業体一覧）
- flagAdmin（管理者か）

### accountingAccountId — 会計アカウントID索引

### accountingAccountMemo — 会計アカウントメモ

### accountingAccountEntity — アカウント×事業体（権限/通知メール紐付け）
- idAccount, idEntity, idAuthority, idAccess, strMailFile

### accountingEntity — 事業体（社員にとっての「会社」）
| 列 | 用途 |
|---|---|
| strTitle | 事業体名 |
| strNation | "jpn" |
| strLang | "ja" |
| strCurrency | "JPY" |
| numFiscalPeriodStart, numFiscalPeriod, numFiscalPeriodLock | 開始期/現在期/確定済期 |
| flagConfig | 初期設定完了フラグ |
| arrSpaceStrTag | タグ |

### accountingEntityJpn — 事業体（日本固有設定）
| 列 | 用途 |
|---|---|
| numFiscalPeriod | 会計期 |
| stampFiscalBeginning, numFiscalBeginningYear/Month, numFiscalTermMonth | 期首/月数 |
| flagCorporation | 1=法人 / 2=個人一般 / 3=個人不動産 / 4=個人農業 |
| numYearSheet | 様式年度 (e.g., 2012) |
| flagCR | 製造原価報告書を使用するか |
| flagSubsidiaryMoney | 補助科目で金額管理するか |
| flagConsumptionTaxFree | 課税(0)/免税(1) |
| flagConsumptionTaxGeneralRule | 本則(1)/簡易(0) 課税 |
| flagConsumptionTaxDeducted | 仕入税額控除: 個別対応(1)/比例配分(0) |
| flagConsumptionTaxIncluding | 経理: 税抜(0)/税込(1) |
| flagConsumptionTaxCalc | 端数: 切捨(1)/四捨五入(2)/切上(3) |
| flagConsumptionTaxWithoutCalc | 入力: 内税(1)/外税(2)/別記(3) |
| flagConsumptionTaxBusinessType | 簡易課税の事業区分 (1〜6種) |
| jsonFlag | 拡張フラグ |

### accountingAuthority — 権限パターン
画面・データ単位の「自分(My)/全件(All)」× CRUDO のフラグマトリクス（select/insert/delete/update/output）。

### accountingAccess — アクセス可能項目パターン
画面ID単位で表示制御を行う JSON 設定。

### accountingEntityDepartment — 部門マスタ
| 列 | 用途 |
|---|---|
| idDepartment | 部門ID |
| idEntity | 事業体 |
| numFiscalPeriod | 期 |
| strTitle | 部門名 |

### accountingEntityDepartmentFSValueJpn — 部門別 FS値
部門単位の財務諸表JSON（`jsonJgaapAccountTitle*`、`jsonJgaapFS*`）。

### accountingFSJpn — 決算書設定（事業体×期）
PL/BS/CR（製造原価）/CS（株主資本変動計算書）の科目ツリーJSON。

### accountingFSValueJpn — 決算書集計値（事業体×期）
| 列 | 用途 |
|---|---|
| jsonJgaapAccountTitle{PL,BS,CR} | 勘定科目別の `sumPrev/sumDebit/sumCredit/sumNext` と税区分集計 |
| jsonJgaapFS{PL,BS,CR,CS} | 決算項目別の集計 |
| jsonConsumptionTax | 消費税合算（軽減税率込/抜きの別） |
| jsonConsumptionTaxDetail | 消費税明細（軽減税率/それ以外） |

### accountingFSIdJpn — 決算書ID採番管理（事業体）
科目ID/決算項目IDの最大値を保持。

### accountingSubAccountTitleJpn — 補助科目マスタ
| 列 | 用途 |
|---|---|
| idSubAccountTitle | 補助科目ID |
| idAccountTitle | 紐付く勘定科目ID |
| strTitle | 補助科目名 |

### accountingSubAccountTitleValueJpn — 補助科目集計値
科目別×補助×期間の `sumPrev/Debit/Credit/Next` JSON。

### accountingCash — 収支管理設定
| 列 | 用途 |
|---|---|
| jsonCash | 収支対象勘定科目 |
| flagPayWrite | 自動消込書出フラグ |
| flagAutoImport | 自動取込フラグ |
| flagPermitImport | 取込許可 |

### accountingCashValue — 収支期間別集計値
期×収支対象×（入金/出金/勘定科目別/補助別）のJSON。

### accountingLogCash — 収支ログ（収支管理画面の伝票）
- jsonVersion: 編集履歴
- arrCommaId* / arrComma*: 借方/貸方の科目/部門/補助/消費税情報
- jsonChargeHistory / jsonPermitHistory / jsonWriteHistory: 担当履歴 / 申請承認履歴 / 元帳書出履歴
- flagPay / stampPay: 消込済フラグと消込日時
- flagRemove / stampRemove: 論理削除

### accountingLogCashDefer — 留保ログ
取込時に補助等の決定が出来ず保留中の収支ログ。

### accountingLog — 仕訳ログ（仕訳帳）
- 構造は `accountingLogCash` とほぼ同等
- flagFiscalReport（'f1' 期末 / 'f2'..'f43' 中間決算 / 'f0' 任意）
- flagApply / idAccountApply / flagApplyBack: 申請ワークフロー
- arrCommaIdLogFile: 紐付く証憑ファイルID
- flagRemove / stampRemove: 論理削除

### accountingLogMailJpn — 仕訳取込メール設定
IMAP接続先・パスワード（暗号化されたBLOB）等。

### accountingLogImportJpn — 仕訳取込フィルタ（インポートルール）
取込元レコードを仕訳に変換するためのマッピングルール（借方/貸方候補、消費税、文字一致条件）。
- flagAttest: eq / start / end / like
- numColStampBook / numColNumValue / numColStrTitle: CSV列マッピング

### accountingLogImportRetryJpn — フィルタ不一致リトライ
- flagType: mail / item / post

### accountingLogCalcJpn — 仕訳計算結果（元帳/集計用）
仕訳1件を借方・貸方それぞれ1行に展開した「サイド別」レコード。残高試算表/元帳/補助元帳のソースになる。
- flagDebit / idAccountTitle / idDepartment / idSubAccountTitle
- numValue / numValueConsumptionTax / numRateConsumptionTax
- numBalance / numBalanceSubAccount / numBalanceDepartment
- flagRateConsumptionTaxReduced（軽減税率）

### accountingFile — 証憑ファイル取込設定
メール取込やファイル種別。

### accountingLogFile — 証憑ファイル
PDF/画像など。jsonVersion で編集履歴。

### accountingFixedAssetsJpn — 固定資産設定（事業体×期）
- flagDepWrite: 期末計上 / flagLossWrite: 損失計上
- flagFractionDep* / numRatioOperateDepSum: 端数処理 / 事業供用割合
- jsonAccountTitle: 科目別の償却フラグ
- jsonDepSum: 期別償却限度額のサマリ

### accountingLogFixedAssetsJpn — 固定資産台帳
1資産1行。
- flagDepMethod: 償却方法（定額/定率/級数法/任意/平均/一括）
- numUsefulLife / numVolume / flagDepUnit / flagFraction
- numValue（取得価額） / numValueCompression（圧縮記帳） / numValueNet（圧縮後）
- numSurvivalRate / numSurvivalRateLimit（残存率）
- numValueRemainingBook / numValueAccumulated（簿価/減価累計）
- numValueDepCalcBase / numValueDepLimit（償却基礎/限度額）
- numValueDep / numValueDepCalc / numValueDepUp / numValueDepExtra / numValueDepSpecial
- numValueAssured / numValueDepSpecialShortPrev/Current/Next
- numValueDepCurrentOver / numValueDepNextOver（償却過不足）
- numValueAccumulatedClosing / numValueNetClosing（期末累計/簿価）
- numRatioOperate / numValueDepOperate（事業供用割合反映後）
- 配賦（販管費/製造原価/非営業費/農業/累計）への割振率: numRatioSellingAdminCost 等
- アカウントタイトル仮引当: lossOnDisposalOfFixedAssets / accumulatedDepreciation 等
- jsonChargeHistory / jsonWriteHistory / jsonVersion / flagRemove

### accountingBanks — 口座管理設定（事業体×期）
- flagAutoImport / flagLock

### accountingLogBanks — 銀行明細ログ
1取引1行。
- idLogBanks / idLogAccount / stampBook
- numValueIn / numValueOut / numBalance
- flagCaution（要注意マーク）

### accountingLogBanksAccount — 銀行口座マスタ
- flagBank: 銀行プラグイン種別（japannetbank / japanpostbank / jibunbank / sumisinnetbank / surugabank）
- blobDetail: 接続情報JSON（id/pass等の暗号化）

### accountingBudgetJpn — 予算（PL / 製造原価）
flagFiscalPeriod: f1/f2x/f4x/msum/月別 + idDepartment + flagFS で粒度を切替え。jsonData が金額。

### accountingBreakEvenPointJpn — 損益分岐点設定
科目を「売上 / 変動費 / 固定費」のどれに分類するかのマップ。

### accountingSummaryStatementJpn — 法人事業概況説明書
2012様式の17項目（jsonData[id] = ...）。

### accountingNotesFSJpn — 個別注記表
strComment フリー記述。

### accountingDetailedAccountJpn — 勘定科目内訳明細書
2012様式の17種類の内訳書（DepositsからMiscellaneousIncome）を flagDetail で分けて1テーブルに格納。

### accountingLogHouseJpn — 家事按分ログ
個人事業主用の按分仕訳マスタ。numRatio が按分率。

### accountingBlueSheetJpn — 青色申告書（決算書様式）
2014年度様式以降の年度別 BLOB（フォーム入力結果）。

---

## C. 移植時に再考すべき設計上の論点

| 項目 | 内容 | 推奨方針 |
|------|------|----------|
| JSON列の多用 | 仕訳の借方/貸方明細・履歴など、本来正規化すべきデータが longtext JSON に格納されている | 監査要件次第。少なくとも **仕訳明細・履歴は別テーブルへ正規化** すべき。インデックス・参照整合性が効かない |
| `arrComma*` 列 | カンマ区切り文字列を where like で検索する設計 | 正規化（多対多テーブル）に置換 |
| 採番の自前管理 | `accountingPreference.jsonIdAutoIncrement` / `accountingFSIdJpn` で自前採番 | DB の AUTO_INCREMENT に統一 |
| 暗号化 | パスワード/銀行接続情報は `blob` / `tinyblob` に独自暗号化 | 環境変数 + KMS or libsodium に統一 |
| 多通貨/多国 | `strCurrency` / `strNation` カラムは存在するが実装は jpn 固定 | 当面 jpn のみ前提でカット可 |
| 部門別FS | `accountingEntityDepartmentFSValueJpn` は実装上ほぼ未使用の可能性 | 利用実績を確認してから移植要否判断 |
| 2012年度固定 | 内訳書/概況説明書は `numYearSheet=2012` に固定 | 直近の様式（最新の e-Tax / 別表）に合わせるなら全面再作成。**会計規則の裏取りが必要** |
| `flagAttest` の取込フィルタ | `eq/start/end/like` だけでは AI による分類のほうが精度高 | 移植時に LLM 分類器に置き換える選択肢 |
| 監査ログ | `baseAccessLog` に全リクエストを記録 | 容量爆発しがち。S3 or 外部監査基盤推奨 |

## D. テーブル数のサマリ

| カテゴリ | テーブル数 |
|----------|----------:|
| コア基盤 (`base*`) | 18 |
| 会計共通 (`accounting*`) | 約14 |
| 会計日本固有 (`accounting*Jpn`) | 約20 |
| **合計** | **約52** |

> 詳細な物理列定義は `back/tpl/templates/else/{core/base,plugin/accounting}/db/config.php` を正とする。
