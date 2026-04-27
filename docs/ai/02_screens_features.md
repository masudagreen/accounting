# 機能・画面一覧

> 抽出元: `back/tpl/vars/else/plugin/accounting/ja/js/jpn/portal.php`（206メニュー項目）と `back/tpl/vars/else/core/base/ja/js/portal.php`（コア基盤側）。  
> 各画面の実装クラスは `back/class/else/plugin/accounting/jpn/<Window名>.php` 系。

各画面の `idTarget` は JavaScript 側で window ID として使われ、**`<window名>` から `Code_Else_Plugin_Accounting_Jpn_<クラス名>` を起動** する。例: `logWindow` → `Log` クラス。

凡例：
- 移植要否のヒント：「★必須」「●任意」「▲移植不要寄り」「？要相談」
- 個人事業/法人 = 主にどの形態で使うか

---

## A. プラグイン本体（会計）画面

### 1. ダッシュボード
| ID (idTarget) | 画面名 | 実装 | 概要 | 移植 |
|---|---|---|---|---|
| `userBoard` | ダッシュボード | `Jpn_Portal::_iniJs` 経由でユーザボード | 当期採算ライン/当期収支ラインのチャート、各種お知らせ | ●任意 |

### 2. 設定 — 消費税
| ID | 画面名 | 概要 | 移植 |
|---|---|---|---|
| `flagConsumptionTaxFree` | 事業者区分 | 課税/免税 | ★必須 |
| `flagConsumptionTaxGeneralRule` | 課税方式 | 本則/簡易 | ★必須 |
| `flagConsumptionTaxDeducted` | 仕入税額控除方式 | 比例配分/個別対応 | ★必須(本則時) |
| `flagConsumptionTaxBusinessType` | 簡易課税事業区分 | 第1〜6種事業 | ●(簡易課税利用時) |
| `flagConsumptionTaxIncluding` | 経理処理方式 | 税抜/税込 | ★必須 |
| `flagConsumptionTaxCalc`（参考） | 端数処理 | 切捨/四捨五入/切上 | ★必須 |
| `flagConsumptionTaxWithoutCalc` | 消費税入力方法 | 内税/外税/別記 | ★必須 |

### 3. 設定 — 科目
| ID | 画面名 | 実装クラス | 概要 |
|---|---|---|---|
| `accountTitleWindow` | 勘定科目（PL/BS/CR） | `Jpn_AccountTitle*` | J-GAAP 対応の勘定科目ツリー編集 |
| `accountTitleCSWindow` | 勘定科目（CS） | `Jpn_AccountTitleCS*` | キャッシュフロー計算書用 |
| `subAccountTitleWindow` | 補助科目 | `Jpn_SubAccountTitle*` | 勘定科目に紐付く補助 |
| `accountTitleFSWindow` | 決算科目 | `Jpn_AccountTitleFS*` | 決算書表示用の集約科目 |
| `accountTitleFSCSWindow` | 決算項目（CS） | `Jpn_AccountTitleFSCS*` | CS の決算項目 |

### 4. 設定 — 部門
| ID | 画面名 | 概要 |
|---|---|---|
| `entityDepartmentWindow` | 部門設定 | 部門マスタの追加/編集/削除 |

### 5. 設定 — 期首残高
| ID | 画面名 | 概要 |
|---|---|---|
| `balanceWindow` | 期首残高 | 各勘定科目の期首残高入力 |

### 6. 設定 — 担当者引継ぎ
| ID | 画面名 | 概要 |
|---|---|---|
| `charge` | 担当者引継ぎ | 仕訳/固定資産などのレコードを別アカウントに引き継ぐ |

### 7. 設定 — アカウント / 繰越
| ID | 画面名 | 概要 |
|---|---|---|
| `PluginAccountingAccount` | アカウント設定 | 当該事業体での自分のアカウント設定 |
| `nextData` | 繰越処理 / 仮繰越処理 | 期末確定 → 翌期データ生成 |

### 8. 入力（取引データ）
| ID | 画面名 | 実装 | 概要 | 移植 |
|---|---|---|---|---|
| `logWindow` | **仕訳帳** | `Jpn_Log` / `Jpn_LogEditor` / `Jpn_LogDelete` 等 | 仕訳の入力・編集・削除・申請承認・取込フィルタ・取込メール・家事按分 | ★必須 |
| `fileWindow` | **証憑ファイル** | `Plugin_Accounting_File*` | PDF/画像のアップロード、メール取込、仕訳との紐付け | ★必須 |
| `cashWindow` | **収支管理** | `Jpn_Cash*` / `Jpn_CashEditor` / `Jpn_CashDefer` / `Jpn_CashPay` | 出納帳。消込（Pay）と元帳書出（Write）あり | ★必須 |
| `fixedAssetsWindow` | **固定資産管理** | `Jpn_FixedAssets*` | 固定資産台帳。減価償却計算（定額/定率/級数法/任意/平均/一括） | ★必須(資産があれば) |
| `banksWindow`（参考） | 口座管理 | `Jpn_Banks*` / `Jpn_BanksAccount*` | ネットバンク自動取込（ジャパンネット銀行/ゆうちょ/じぶん銀行/住信SBI/スルガ） | ▲移植不要寄り（外部API契約断絶のため） |

### 9. 集計
| ID | 画面名 | 実装 | 概要 | 移植 |
|---|---|---|---|---|
| `ledgerWindow` | **元帳** | `Jpn_Ledger*` | 総勘定元帳。科目別の借方/貸方/残高 | ★必須 |
| `trialBalanceWindow` | **残高試算表** | `Jpn_TrialBalance*` | 期間絞込の試算表 | ★必須 |
| `consumptionTaxSheetWindow` | **消費税集計表** | `Jpn_ConsumptionTaxSheet` | 税率別/取引種別の消費税集計 | ★必須(課税事業者) |
| `consumptionTaxListWindow` | **科目別税区分表** | `Jpn_ConsumptionTaxList` | 科目別の税区分一覧 | ●任意 |

### 10. 分析
| ID | 画面名 | 実装 | 概要 | 移植 |
|---|---|---|---|---|
| `financialAnalyzeWindow` | 財務分析 | `Jpn_FinancialAnalyze*` | 財務指標（流動比率等） | ●任意 |
| `breakEvenPointWindow` | 損益分岐点分析 | `Jpn_BreakEvenPoint*` | 売上/変動費/固定費のマッピング → BEP計算 | ●任意 |
| `cashAnalyzeWindow` | 収支分析 | `Jpn_CashAnalyze*` | キャッシュ分析 | ●任意 |
| `cashPlanWindow` | 資金繰り分析 | `Jpn_CashPlan*` | 資金繰り表 | ●任意 |
| `budgetWindow` | 予算実績比較表 | `Jpn_Budget*` | 予算入力＆実績差分 | ●任意 |
| `financialStatementMultiWindow` | 比較決算 | `Jpn_FinancialStatementMulti*` | 期間比較 | ●任意 |
| `financialStatementMultiCSWindow` | 比較決算(CS) | `Jpn_FinancialStatementMultiCS*` | CS の期間比較 | ●任意 |

### 11. 報告（決算書）
| ID | 画面名 | 実装 | 概要 | 移植 |
|---|---|---|---|---|
| `financialStatementWindow` | **決算（PL/BS）** | `Jpn_FinancialStatement*` | 損益計算書/貸借対照表 | ★必須 |
| `financialStatementCSWindow` | 決算（CS） | `Jpn_FinancialStatementCS*` | キャッシュフロー計算書 | ●(法人で必要なら) |
| `financialStatementSSWindow` | **株主資本等変動計算書** | `Jpn_FinancialStatementSS*` | 法人決算用 | ●(法人) |
| `detailsSellingAndAdminWindow` | 販売費及び一般管理費の明細 | `Jpn_DetailsSellingAndAdmin*` | 販管費の科目別明細 | ●任意 |
| `notesFSWindow` | **個別注記表** | `Jpn_NotesFS*` | フリー記述の注記 | ●(法人) |

### 12. 申告 — 法人事業概況説明書
| ID | 画面名 | 実装 | 概要 | 移植 |
|---|---|---|---|---|
| `summaryStatementPublicWindow` | 法人事業概況説明書 | `Jpn_2012_summaryStatement_*` | 17項目の法人税別表 | ▲(2012様式固定。最新様式に作り替え推奨) |

### 13. 申告 — 勘定科目内訳明細書（17種類）

`Jpn_2012_detailedAccount_*` の各 Editor / Output クラスで実装。**会計規則の正本確認が最重要**な領域（税務署提出書類）。

| 内訳書 | idTarget | クラス基底名 | 移植 |
|---|---|---|---|
| 預貯金等の内訳書 | `detailedAccountDepositsWindow` | `Deposits` | ▲(様式が古い) |
| 受取手形の内訳書 | `detailedAccountNotesReceivableWindow` | `NotesReceivable` | ▲ |
| 売掛金(未収入金)の内訳書 | `detailedAccountAccountsReceivableWindow` | `AccountsReceivable` | ▲ |
| 仮払金(前渡金)の内訳書 | `detailedAccountSuspensePaymentWindow` | `SuspensePayment` | ▲ |
| 貸付金及び受取利息の内訳書 | `detailedAccountLoansReceivableWindow` | `LoansReceivable` | ▲ |
| 棚卸資産(商品...)の内訳書 | `detailedAccountInventriesWindow` | `Inventries`*※スペル誤り (Inventories が正) | ▲ |
| 有価証券の内訳書 | `detailedAccountSecuritiesWindow` | `Securities` | ▲ |
| 固定資産(土地...)の内訳書 | `detailedAccountFixedAssetsWindow` | `FixedAssets` | ▲ |
| 支払手形の内訳書 | `detailedAccountNotesPayableWindow` | `NotesPayable` | ▲ |
| 買掛金(未払金・未払費用)の内訳書 | `detailedAccountAccountsPayableWindow` | `AccountsPayable` | ▲ |
| 未払配当金の内訳書 | `detailedAccountDividendsPayableWindow` | `DividendsPayable` | ▲ |
| 未払役員賞与の内訳書 | `detailedAccountAccruedBonusToDirectorsWindow` | `AccruedBonusToDirectors` | ▲ |
| 仮受金(前受金・預り金)の内訳書 | `detailedAccountSuspenseReceiptWindow` | `SuspenseReceipt` | ▲ |
| 源泉所得税預り金の内訳 | `detailedAccountIncomeTaxWithholdingWindow` | `IncomeTaxWithholding` | ▲ |
| 借入金及び支払利子の内訳書 | `detailedAccountLoansPayableWindow` | `LoansPayable` | ▲ |
| 土地の売上高等の内訳書 | `detailedAccountLandWindow` | `Land` | ▲ |
| 売上高等の事業所別の内訳書 | `detailedAccountSalesWindow` | `Sales` | ▲ |
| 役員報酬手当等及び人件費の内訳書 | `detailedAccountEmployeeWindow` | `Employee` | ▲ |
| 地代家賃の内訳書 | `detailedAccountRentsWindow` | `Rents` | ▲ |
| 権利金等の期中支払の内訳 | `detailedAccountKeyMoneyWindow` | `KeyMoney` | ▲ |
| 工業所有権等の使用料の内訳書 | `detailedAccountIndustrialPropertyWindow` | `IndustrialProperty` | ▲ |
| 雑損失等の内訳書 | `detailedAccountBadMiscellaneousExpensesWindow` | `BadMiscellaneousExpenses` | ▲ |
| 雑益等の内訳書 | `detailedAccountMiscellaneousIncomeWindow` | `MiscellaneousIncome` | ▲ |

> 注: メニュー上は17項目（一部統合表記）だが、クラス実装は23種類用意されている。最新の e-Tax 様式と差分があるため**移植時は会計規則を必ず参照**。

### 14. 仕訳帳の追加機能
| 機能 | 画面 | 概要 |
|---|---|---|
| インポートフィルタ | (logWindow 内) | CSV/メール内のテキストを仕訳に自動変換するルール (`accountingLogImportJpn`) |
| フィルタリトライ | (logWindow 内) | フィルタ不一致のリトライキュー (`accountingLogImportRetryJpn`) |
| 取込メール設定 | (logWindow 内) | IMAP 接続定義 (`accountingLogMailJpn`) |
| 家事按分 | (logWindow 内) | 個人事業主用の按分ルール (`accountingLogHouseJpn`) |

---

## B. 管理者向け（事業体・権限・アカウント）画面

### 15. 管理者項目（プラグイン側）
| ID | 画面名 | 実装 | 概要 |
|---|---|---|---|
| `entityWindow` | 事業体 | `Plugin_Accounting_Entity*` | 会計対象会社/事業体の登録/編集 |
| `accountWindow` | アクセス可能事業体（≠ コアの accountWindow） | `Plugin_Accounting_AccountEntity*` | アカウントが利用できる事業体の紐付け |
| `authorityWindow` | アクセス権限パターン | `Plugin_Accounting_Authority*` | CRUDOフラグの権限テンプレ |
| `idEntityCurrent` | 作業事業体選択 | (`Portal::_updateVarsIdEntityCurrent`) | 操作対象の事業体/会計年度を切替 |
| `accountEntityAuthorityWindow` | アクセス構成 | `Plugin_Accounting_AccountEntityAuthority*` | アカウント×事業体×権限×アクセスパターンの組合せ |
| `accessWindow` | アクセス可能項目パターン | `Plugin_Accounting_Access*` | 画面項目単位の表示制御 |

### 16. 管理者項目（コア基盤側）
（`back/tpl/vars/else/core/base/ja/js/portal.php` のメニュー）

| ID | 画面名 | 概要 |
|---|---|---|
| `flagMaintenance` | 運用ステータス | 稼働中 / メンテナンス中 |
| `strSiteName` | システム運営者 | サイト名・送信用メール・連絡先URL |
| `arrCommaIdAccountMaintenance` | メンテナンス要員 | メンテ中アクセス可アカウント |
| `flagLoginMail` / `flagAccessUnknownMail` | ログイン/アクセス通知 | メール通知設定 |
| `rebuild` | プラグイン再構築 | DB/CSS/JS 再生成 |
| `logWindow`（コア） | システムログ | `baseAccessLog` 検索 |
| `flagSign` / `flagForgot` | フォーム表示 | 登録/パスワード再発行 |
| `version` | バージョン更新 | 自動アップデート |
| `apiAccountWindow` | API | 外部システム連携用APIキー |
| `jsonIp*` / `jsonMail*` / `flagReject` | 拒否設定 / アクセス制限 | IP/メールホワイト・ブラックリスト |
| `numAutoMustLogout` | 自動ログアウト | 強制ログアウト時間 |

### 17. ユーザ項目（コア基盤側）
| ID | 画面名 | 概要 |
|---|---|---|
| `accountWindow` | アカウント | アカウントの追加/編集/削除 |
| `termWindow` | 有効期間パターン | アカウント有効期間 |
| `moduleWindow` | モジュールパターン | 利用可能モジュール |
| `numPasswordLimit` | パスワード設定 | 有効期間/最小文字数/試行回数 |
| `flagLoginSecond` | 二段階認証 | 強制有無 |
| `lockWindow` | アカウントロック | ロックされたアカウント |
| `applySignWindow` | アカウント登録申請 | 新規登録の承認 |
| `applyChangeWindow` | アカウント変更申請 | 変更の承認 |
| `strCodeName` (変更申請) | アカウント変更申請 | 自分の情報の変更申請 |
| `strPassword` | ログインパスワード変更 | パスワード再設定 |
| `flagLoginMailAccount` / `flagLoginSecondAccount` | ログイン通知 / 二段階認証 (アカウント設定) | 個人別 |
| `numTimeZone` / `strLang` / `strHoliday` / `numList` / `strAutoBoot` / `numAutoLogout` / `numAutoPopup` | デスクトップ設定 | UI 個別設定 |

---

## C. 移植要否のサマリ

| カテゴリ | 必須 | 任意 | 不要寄り |
|---|---|---|---|
| 入力 | 仕訳帳・収支管理・固定資産・証憑ファイル | 取込メール / 取込フィルタ / 家事按分 | 口座管理（5行のネットバンク取込） |
| 集計 | 元帳・残高試算表・消費税集計表 | 科目別税区分表 | — |
| 分析 | — | 全部（任意） | — |
| 決算 | PL/BS（決算）・株主資本（法人時） | CS / 販管費明細 / 個別注記表 | — |
| 申告 | — | — | 17種類の内訳書・法人事業概況・青色申告書（**様式が古いので作り直し前提**） |
| 設定 | 消費税・科目・部門・期首残高・繰越 | 担当引継 | — |
| 管理 | 事業体・アカウント・権限 | API / 拒否設定 / 二段階認証 | バージョン自動更新 |

> 個別の判断はユーザ確認のうえ、`04_events.md` のCRUD表とあわせて行ってください。
