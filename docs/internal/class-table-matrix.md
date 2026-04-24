# クラス ↔ テーブル対応表 (Rucaro Accounting Legacy)

> `back/class/else/**/*.php` (PHP ファイル 432 個 / ユニーククラス 431 個) と `back/dat/version/Batch14200/templates/config.php` + `Batch14300/templates/config.php` (テーブル定義 59 個) を突合した、Phase 4 移行設計用の対応マトリクス。
>
> **スコープ**: 中核 30 クラスは詳細セクション、残りは機械集計、テーブル逆引きは全 59 テーブル (被参照 54 + ゼロ 5)。
>
> 一次情報:
> - `back/class/else/lib/Db.php` (独自 DB ラッパー `Code_Else_Lib_Db`, メソッド: `getSelect` / `insertRow` / `updateRow` / `deleteRow`)
> - `'strTable' => '...'` 呼び出し (アプリ全域で 958 箇所, 223 クラス)
> - `'strTable' => 'accountingXxx' . $strNation` は `$strNation = 'Jpn'` が束縛されて `accountingXxxJpn` テーブルを参照 (`back/class/else/plugin/accounting/Accounting.php:140-153`)

---

## 0. トップライン

| 指標 | 値 | 根拠 |
|------|----|------|
| 総 PHP ファイル数 | 432 | `find back/class/else -name "*.php"` (Smarty 等ライブラリ除外) |
| ユニーククラス数 | **431** | 1 クラス (`Jpn_CalcBanks_Japannetbank`) が 2 ファイルで同名宣言のため 1 つ減 |
| DB テーブルを直接触るクラス | **223** (51.7%) | `'strTable' =>` 出現クラス |
| DB を触らないクラス | 208 (48.3%) | Search.php / Choice.php / Output.php / 抽象化層など |
| DDL 定義済みテーブル数 | **59** | `legacy-schema.md` |
| コードから参照される DDL 定義テーブル数 | **54** / 59 (91.5%) | 参照ゼロ: `baseApplyForgot`, `baseLoginMiss`, `baseLoginSecond`, `baseSession`, `baseToken` の 5 テーブル (すべて login/session 系。`Code_Else_Core_Login_Login` / `Code_Else_Core_Base_Attest` などが `arrSearch` 経由の動的 strTable 組立で間接参照している) |
| 集計上の注意 | - | 本書は `'accountingEntity'` のリテラル参照と `'accountingEntity' . $strNation` (実行時は `accountingEntityJpn`) を区別せず後者に寄せてある。`Entity.php` / `EntityEditor.php` の移行調査時は両テーブルを当たること |
| 参照はあるが DDL に存在しない名前 | 4 | `accounting` (prefix 途中), `accountingAdminMemo`, `accountingConsumptionTax`, `accountingCorporateTax` (旧世代のコードに残骸) |
| DB 抽象化 | 独自ラッパー 1 個 (`Code_Else_Lib_Db`) | ORM なし。SQL は内部で組み立て + PDO prepare |

### 読み方

- `'strTable' => 'accountingLog'` は **そのまま** `accountingLog` テーブル (39 個中の `accountingLog` #41) を指す。
- `'strTable' => 'accountingFS' . $strNation` は実行時に `accountingFSJpn` (#31) になる。以下の全集計でこれを正規化済。
- `accountingEntity` (#28) と `accountingEntityJpn` (#29) は別テーブル。コードで `'accountingEntity'` がリテラル使用されている場合は #28、`. $strNation` が付く場合は #29。本書の集計では `$strNation` 有無で区別できない場所は `accountingEntityJpn` 側にカウントした。実際の移行調査時は両方を当たるべし。

---

## 1. 中核 30 クラスの詳細

> DB 操作は **`$classDb->getSelect(...)` / `insertRow(...)` / `updateRow(...)` / `deleteRow(...)`** の 4 種のみ。`arrSearch` を Search 系クラスに渡す読み出しは実体として `getSelect` に落ちる。

### 優先度 S (Phase 4 最優先)

#### Code_Else_Plugin_Accounting_Jpn_Log

- ファイル: `back/class/else/plugin/accounting/jpn/Log.php` (2445 行)
- 役割: 仕訳入力・検索画面のコントローラ (会計システムの中心画面)
- 触るテーブル: `accountingLog` [S/U], `accountingLogFile` [S], `accountingAccountMemo` [U]
- 主要メソッド: `run()`, `getDBAuthority()`, `checkValueSearch()`

#### Code_Else_Plugin_Accounting_Jpn_LogEditor

- ファイル: `back/class/else/plugin/accounting/jpn/LogEditor.php` (1284 行)
- 役割: 仕訳エディタ (追加・編集・削除の書込ロジック)
- 触るテーブル: `accountingLog` [S/U], `accountingAccountMemo` [U]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_CalcAccountTitle

- ファイル: `back/class/else/plugin/accounting/jpn/CalcAccountTitle.php` (1967 行)
- 役割: 勘定科目合計の再集計 (部門別 FS / 会社 FS を更新)
- 触るテーブル: `accountingFSJpn` [S], `accountingFSValueJpn` [I/U], `accountingEntityDepartment` [S], `accountingEntityDepartmentFSValueJpn` [I/U]
- 主要メソッド: `run()`, `allot()`

#### Code_Else_Plugin_Accounting_Jpn_TrialBalance

- ファイル: `back/class/else/plugin/accounting/jpn/TrialBalance.php` (1065 行)
- 役割: 試算表画面 (期間別集計出力の画面側)
- 触るテーブル: `accountingSubAccountTitleValueJpn` [S]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_TrialBalanceOutput

- ファイル: `back/class/else/plugin/accounting/jpn/TrialBalanceOutput.php` (953 行)
- 役割: 試算表の CSV/PDF エクスポート (DB 直接参照なし、`TrialBalance` の結果を整形)
- 触るテーブル: (直接参照なし / 画面層から受け取った配列を整形)
- 主要メソッド: `run()`

### 優先度 A

#### Code_Else_Plugin_Accounting_Jpn_Ledger

- ファイル: `back/class/else/plugin/accounting/jpn/Ledger.php`
- 役割: 元帳 (勘定科目別明細) 表示
- 触るテーブル: `accountingLogCalcJpn` [S] (仕訳フラット化キャッシュ)
- 主要メソッド: `run()`, `getDBAuthority()`

#### Code_Else_Plugin_Accounting_Jpn_FinancialStatement

- ファイル: `back/class/else/plugin/accounting/jpn/FinancialStatement.php`
- 役割: 財務諸表 (BS/PL) の単期表示 (DB 直接参照なし、親 `Jpn.php` がロード済の FS JSON を使う)
- 触るテーブル: (直接参照なし / `accountingFSValueJpn` を親から受領)
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_FinancialStatementCS

- ファイル: `back/class/else/plugin/accounting/jpn/FinancialStatementCS.php`
- 役割: キャッシュフロー計算書 (CS) 表示
- 触るテーブル: (直接参照なし)
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_FinancialStatementMulti

- ファイル: `back/class/else/plugin/accounting/jpn/FinancialStatementMulti.php`
- 役割: 複数期間の財務諸表比較表示
- 触るテーブル: (直接参照なし)
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_FinancialStatementOutput

- ファイル: `back/class/else/plugin/accounting/jpn/FinancialStatementOutput.php`
- 役割: 財務諸表 CSV/PDF 出力
- 触るテーブル: (直接参照なし)
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_AccountTitle

- ファイル: `back/class/else/plugin/accounting/jpn/AccountTitle.php`
- 役割: 勘定科目マスタ画面 (ツリー表示・追加・削除)
- 触るテーブル: `accountingFSJpn` [S/U], `accountingFSIdJpn` [S/U], `accountingSubAccountTitleJpn` [S/D], `accountingSubAccountTitleValueJpn` [D]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_AccountTitleEditor

- ファイル: `back/class/else/plugin/accounting/jpn/AccountTitleEditor.php`
- 役割: 勘定科目の編集・FS 配置変更
- 触るテーブル: `accountingFSJpn` [S/U], `accountingFSIdJpn` [S], `accountingSubAccountTitleJpn` [U]
- 主要メソッド: `run()`, `_setValueTemplate()`

#### Code_Else_Plugin_Accounting_Jpn_SubAccountTitle

- ファイル: `back/class/else/plugin/accounting/jpn/SubAccountTitle.php`
- 役割: 補助科目マスタ画面
- 触るテーブル: `accountingSubAccountTitleJpn` [S/D], `accountingSubAccountTitleValueJpn` [D], `accountingLog` [S], `accountingAccountMemo` [U]
- 主要メソッド: `run()`, `getDBAuthority()`

#### Code_Else_Plugin_Accounting_Entity

- ファイル: `back/class/else/plugin/accounting/Entity.php`
- 役割: 会社マスタ画面 (Entity = 法人 / 個人) 作成・削除
- 触るテーブル: `accountingEntity` [S/D], `accountingAccount` [S/U], `accountingAccountMemo` [U], (削除時に連動: `accountingEntityJpn` / FS / 固定資産 / 仕訳 / ...)
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_EntityEditor

- ファイル: `back/class/else/plugin/accounting/EntityEditor.php`
- 役割: 会社の登録・更新・期変更のコントローラ (初期 FS テンプレをここでコピー)
- 触るテーブル: `accountingEntity` [I/S/U], `accountingEntityJpn` [S] (読出時は `. ucwords($strNation)`), `accountingFSJpn` [S], `accountingFixedAssetsJpn` [S]
- 主要メソッド: `run()`

### 優先度 B

#### Code_Else_Plugin_Accounting_Jpn_FixedAssets

- ファイル: `back/class/else/plugin/accounting/jpn/FixedAssets.php`
- 役割: 固定資産台帳 (明細 + 減価償却計算) 画面
- 触るテーブル: `accountingFixedAssetsJpn` [S/U], `accountingLogFixedAssetsJpn` [S/U/D], `accountingAccountMemo` [U]
- 主要メソッド: `run()`, `getDBAuthority()`, `checkValueSearch()`

#### Code_Else_Plugin_Accounting_Jpn_CalcDep (+ `calcDep/*`)

- ファイル: `back/class/else/plugin/accounting/jpn/CalcDep.php` と `calcDep/{Average,Declining,One,Straight,Sum,Voluntary}.php`
- 役割: 減価償却算定 (定額 / 定率 / 平均 / 一括 / 任意 / 合計の 6 アルゴリズム)
- 触るテーブル: `Sum.php` のみ `accountingLogFixedAssetsJpn` [S], 他は pure 計算
- 主要メソッド: `run()`, `allot()` (`CalcDep`), 各アルゴリズム固有メソッド (戦略パターン)

#### Code_Else_Plugin_Accounting_Jpn_Budget

- ファイル: `back/class/else/plugin/accounting/jpn/Budget.php`
- 役割: 予算画面 (月別 BS/PL 予算登録)
- 触るテーブル: `accountingBudgetJpn` [S]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_Cash

- ファイル: `back/class/else/plugin/accounting/jpn/Cash.php`
- 役割: 現金出納帳 (簡易入力) 画面
- 触るテーブル: `accountingCash` [S], `accountingLogCash` [S], `accountingLogFile` [S], `accountingAccountMemo` [U]
- 主要メソッド: `run()`, `getDBAuthority()`, `checkValueSearch()`

#### Code_Else_Plugin_Accounting_Jpn_CashPlan

- ファイル: `back/class/else/plugin/accounting/jpn/CashPlan.php`
- 役割: 資金繰計画
- 触るテーブル: `accountingCashValue` [S], `accountingCash` [S]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_BlueSheet

- ファイル: `back/class/else/plugin/accounting/jpn/BlueSheet.php`
- 役割: 青色申告決算書 (個人事業主向け) 表示 (直接 DB 参照なし、親クラスが `accountingBlueSheetJpn` をロード)
- 触るテーブル: (直接参照なし)
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_NotesFS

- ファイル: `back/class/else/plugin/accounting/jpn/NotesFS.php`
- 役割: 注記表
- 触るテーブル: `accountingNotesFSJpn` [S]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_BreakEvenPoint

- ファイル: `back/class/else/plugin/accounting/jpn/BreakEvenPoint.php`
- 役割: 損益分岐点分析
- 触るテーブル: `accountingBreakEvenPointJpn` [S]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Jpn_ConsumptionTax

- ファイル: `back/class/else/plugin/accounting/jpn/ConsumptionTax.php`
- 役割: 消費税申告書 (一般課税・簡易課税)
- 触るテーブル: `accountingConsumptionTax` [S] (注: DDL 未定義の名前。実体は `accountingFSValueJpn.jsonConsumptionTaxDetail` に格納。このクラス内の`'accountingConsumptionTax'` は過渡期の残骸/将来予約)
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Authority

- ファイル: `back/class/else/plugin/accounting/Authority.php`
- 役割: 権限定義 (my/all × select/insert/update/delete/output) 管理画面
- 触るテーブル: `accountingAuthority` [S/D], `accountingAccountEntity` [U], `accountingAccountMemo` [U]
- 主要メソッド: `run()`

#### Code_Else_Plugin_Accounting_Access

- ファイル: `back/class/else/plugin/accounting/Access.php`
- 役割: アクセス制御 (部門単位・取引先単位) 管理画面
- 触るテーブル: `accountingAccess` [S/D], `accountingEntityJpn` [S], `accountingAccountEntity` [U], `accountingAccountMemo` [U]
- 主要メソッド: `run()`, `getDBAuthority()`

### 優先度 C (共通基盤)

#### Code_Else_Core_Base_Init

- ファイル: `back/class/else/core/base/Init.php`
- 役割: アプリ起動時のプリファレンス / モジュール / 期ロード。`$varsPreference`, `$varsModule`, `$varsTerm` のグローバル初期化
- 触るテーブル: `baseModule` [S], `baseTerm` [S] (+ 継承先で `basePreference` / `baseAccount` も読む)
- 主要メソッド: `run()`, `updateVarsPreference()`, `updateVars()`, `updateVar()`, `updateVarsAll()`, `updateVarsAccount()`

#### Code_Else_Core_Base_Attest

- ファイル: `back/class/else/core/base/Attest.php`
- 役割: セッション認証確認 (ログイン状態を毎リクエストで検証)
- 触るテーブル: (直接参照なし) — `arrSearch` 経由で `baseSession` / `baseLoginSecond` / `baseAccount` を読む
- 主要メソッド: `run()`

#### Code_Else_Core_Base_Access

- ファイル: `back/class/else/core/base/Access.php`
- 役割: URL ルーティング後のモジュール / 機能呼び出しディスパッチ
- 触るテーブル: (直接参照なし)
- 主要メソッド: `run()`, `_setRoutine()`

#### Code_Else_Core_Login_Login

- ファイル: `back/class/else/core/login/Login.php`
- 役割: ログインフォーム処理 (ID/PW 検証 → `baseSession` 発行)
- 触るテーブル: (直接参照なし) — `arrSearch` と `insertRow` を継承クラス・子クラス経由で呼び、`baseAccount` / `baseSession` / `baseLoginMiss` / `baseLoginPassword` を操作
- 主要メソッド: `run()`, `loop()`

#### Code_Else_Lib_Db

- ファイル: `back/class/else/lib/Db.php` (1106 行超)
- 役割: 独自 DB ラッパー。`master` / `slave` / `log` の 3 系統 PDO 接続を管理し、4 系統 CRUD API を提供
- 触るテーブル: (すべて。ラッパー自体は任意のテーブル名を受理)
- 主要メソッド: `getSelect()`, `insertRow()`, `updateRow()`, `deleteRow()`, `getDbh()`, `setDbhMaster()`, `getFlagMaster()`, `getDbhLog()`, `getColumnValue()`, `getColumnArrValue()`, `getTableList()`, `getTableColumn()`, `getDbSize()`, `checkVersion55()`

---

## 2. 全クラス機械集計

> PHP ファイル 432 個から抽出したユニーククラス 431 個を網羅。DB を触る 223 クラスと触らない 208 クラスに分けて列挙する。不一致の 1 個はレガシーコード側のバグ (`back/class/else/plugin/accounting/jpn/calcBanks/Japanpostbank.php` が隣接 `Japannetbank.php` と同じクラス名 `Code_Else_Plugin_Accounting_Jpn_CalcBanks_Japannetbank` を宣言している)。

> `back/class/else/**/*.php` からクラス名と `'strTable' => '...'` を抽出して生成。プレフィクス `Code_Else_Plugin_Accounting_` / `Code_Else_Core_` / `Code_Else_` は省略。Jpn サフィックス付きテーブルは正規化済。

### 2.1 DB を直接触る 223 クラス (アルファベット順)

| クラス (prefix 省略) | 触るテーブル |
|------|------|
| Access | accountingAccess, accountingAccountEntity, accountingAccountMemo, accountingEntity |
| AccessChoice | accountingAccess |
| AccessEditor | accountingAccess |
| Account | accountingAccount, accountingAccountMemo |
| AccountChoice | accountingAccount |
| AccountEditor | accountingAccount |
| AccountEntity | accountingAccount, accountingAccountEntity, accountingAccountMemo |
| AccountEntityAuthority | accountingAccountEntity, accountingAccountMemo |
| AccountEntityAuthorityEditor | accountingAccount, accountingAccountEntity |
| AccountStatus | accountingAccount, accountingAccountEntity, accountingAccountId, accountingAccountMemo, accountingEntity, accountingLog, accountingLogFile |
| Accounting | accountingEntityDepartment, accountingPreference |
| Authority | accountingAccountEntity, accountingAccountMemo, accountingAuthority |
| AuthorityChoice | accountingAuthority |
| AuthorityEditor | accountingAuthority |
| Base_Account | baseAccount, baseAccountId, baseAccountMemo |
| Base_AccountAllChoice | baseAccount |
| Base_AccountChoice | baseAccount |
| Base_AccountEditor | baseAccount, baseAccountId, baseLoginIdLogin, baseLoginPassword, basePublish |
| Base_ApiAccount | baseAccountMemo, baseApiAccount |
| Base_ApiAccountEditor | baseApiAccount |
| Base_ApplyChange | baseAccount, baseAccountId, baseApplyChange, baseLoginIdLogin |
| Base_ApplySign | baseAccount, baseAccountId, baseAccountMemo, baseApplySign, baseLoginIdLogin, basePublish |
| Base_Base | baseAccountMemo |
| Base_Init | baseModule, baseTerm |
| Base_Lock | baseAccount, baseLock, baseLoginIdLogin, basePublish |
| Base_Log | baseAccessLog, baseAccountMemo |
| Base_LogOutput | baseAccessLog |
| Base_Module | baseAccount, baseAccountMemo, baseModule |
| Base_ModuleAbstract | baseAccount, basePreference |
| Base_ModuleChoice | baseModule |
| Base_ModuleEditor | baseAccount, baseModule |
| Base_Portal | baseAccessUnknown, baseAccount, baseApplyChange, baseLoginPassword, basePreference |
| Base_Term | baseAccount, baseAccountMemo, baseTerm |
| Base_TermChoice | baseTerm |
| Base_TermEditor | baseTerm |
| CalcFileBoard | accountingLogFile |
| CalcFileImport | accountingFile |
| Confirm_Change | baseApplyChange |
| Entity | accounting, accountingAccount, accountingAccountMemo, accountingEntity |
| EntityChoice | accountingEntity |
| EntityChoiceWithoutConfig | accountingEntity |
| EntityEditor | accountingEntity, accountingFSJpn, accountingFixedAssetsJpn |
| File | accountingAccountMemo, accountingFile, accountingLogFile |
| FileAccountEntity | accountingAccountEntity, accountingAccountMemo |
| FileAccountEntityEditor | accountingAccount, accountingAccountEntity |
| FileChoice | accountingLogFile |
| FileEditor | accountingLogFile |
| FileImport | accountingLogFile |
| FileOutput | accountingLogFile |
| FilePreference | accountingAccount, accountingAccountEntity, accountingFile |
| Init | accountingAuthority, accountingEntity, accountingPreference |
| Jpn_2012_ConsumptionTax_GeneralEditor | accountingConsumptionTax |
| Jpn_2012_ConsumptionTax_PreferenceEditor | accountingConsumptionTax |
| Jpn_2012_DetailedAccount_AccountsPayableEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_AccountsPayablePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_AccountsReceivableEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_AccountsReceivablePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_AccruedBonusToDirectorsEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_AccruedBonusToDirectorsPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_BadMiscellaneousExpensesEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_BadMiscellaneousExpensesPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_DepositsEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_DepositsPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_DividendsPayableEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_DividendsPayablePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_EmployeeEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_EmployeePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_FixedAssetsEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_FixedAssetsPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_IncomeTaxWithholdingEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_IncomeTaxWithholdingPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_IndustrialPropertyEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_IndustrialPropertyPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_InventriesEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_InventriesPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_KeyMoneyEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_KeyMoneyPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_LandEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_LandPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_LoansPayableEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_LoansPayablePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_LoansReceivableEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_LoansReceivablePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_MiscellaneousIncomeEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_MiscellaneousIncomePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_NotesPayableEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_NotesPayablePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_NotesReceivableEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_NotesReceivablePreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_RentsEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_RentsPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SalesEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SalesPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SecuritiesEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SecuritiesPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SuspensePaymentEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SuspensePaymentPreference | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SuspenseReceiptEditor | accountingDetailedAccountJpn |
| Jpn_2012_DetailedAccount_SuspenseReceiptPreference | accountingDetailedAccountJpn |
| Jpn_2012_SummaryStatement_PublicAccountTitleEditor | accountingSummaryStatementJpn |
| Jpn_2012_SummaryStatement_PublicEditor | accountingSummaryStatementJpn |
| Jpn_AccountTitle | accountingFSJpn, accountingFSIdJpn, accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn |
| Jpn_AccountTitleCSEditor | accountingFSJpn |
| Jpn_AccountTitleEditor | accountingFSJpn, accountingFSIdJpn, accountingSubAccountTitleJpn |
| Jpn_AccountTitleFS | accountingFSJpn, accountingFSIdJpn |
| Jpn_AccountTitleFSCS | accountingFSJpn, accountingFSIdJpn |
| Jpn_AccountTitleFSCSEditor | accountingFSJpn, accountingFSIdJpn |
| Jpn_AccountTitleFSEditor | accountingFSJpn, accountingFSIdJpn |
| Jpn_AccountTitleFSEditor_2012_Public | accountingFSJpn |
| Jpn_Balance | accountingSubAccountTitleValueJpn |
| Jpn_Banks | accountingAccountMemo, accountingBanks, accountingLogBanks, accountingLogBanksAccount |
| Jpn_BanksAccount | accountingAccountMemo, accountingLogBanks, accountingLogBanksAccount |
| Jpn_BanksAccountEditor | accountingLogBanksAccount |
| Jpn_BanksEditor | accountingLogBanks |
| Jpn_BanksOutput | accountingLogBanks |
| Jpn_BanksPreference | accountingBanks |
| Jpn_BlueSheetEditor_2012_Public | accountingBlueSheetJpn |
| Jpn_BlueSheet_2012_Public | accountingBlueSheetJpn, accountingLogFixedAssetsJpn |
| Jpn_BreakEvenPoint | accountingBreakEvenPointJpn |
| Jpn_BreakEvenPointAccountTitle | accountingBreakEvenPointJpn |
| Jpn_BreakEvenPointAccountTitleEditor | accountingBreakEvenPointJpn |
| Jpn_Budget | accountingBudgetJpn |
| Jpn_BudgetEditor | accountingBudgetJpn |
| Jpn_CalcAccountTitle | accountingEntityDepartment, accountingEntityDepartmentFSValueJpn, accountingFSJpn, accountingFSValueJpn |
| Jpn_CalcAccountTitleFSCS | accountingFSValueJpn |
| Jpn_CalcBanks | accountingBanks, accountingLogBanks, accountingLogBanksAccount |
| Jpn_CalcBanksBoard | accountingLogBanks, accountingLogBanksAccount |
| Jpn_CalcBanksImport | accountingLogBanks |
| Jpn_CalcBreakEvenPoint | accountingBreakEvenPointJpn |
| Jpn_CalcCash | accountingCash, accountingCashValue, accountingLogCash |
| Jpn_CalcCashBoard | accountingCashValue, accountingLogCash, accountingLogCashDefer |
| Jpn_CalcCashDefer | accountingLogCashDefer |
| Jpn_CalcCashPay | accountingCash, accountingLogCash, accountingLogCashDefer |
| Jpn_CalcConsumptionTax | accountingFSValueJpn |
| Jpn_CalcDep_Sum | accountingLogFixedAssetsJpn |
| Jpn_CalcDetailsSync | accountingSubAccountTitleValueJpn |
| Jpn_CalcEntityDepartmentImport | accountingEntityDepartment, accountingEntityDepartmentFSValueJpn |
| Jpn_CalcFixedAssets | accountingFixedAssetsJpn |
| Jpn_CalcFixedAssetsBoard | accountingLogFixedAssetsJpn |
| Jpn_CalcLog | accountingLog, accountingLogFile |
| Jpn_CalcLogBoard | accountingLog, accountingLogHouseJpn, accountingLogImportJpn, accountingLogImportRetryJpn |
| Jpn_CalcLogCalc | accountingFSJpn, accountingLogCalcJpn |
| Jpn_CalcLogConsumptionTax | accountingEntity, accountingLog, accountingLogCash, accountingLogCashDefer, accountingLogHouseJpn, accountingLogImportJpn |
| Jpn_CalcLogHouse | accountingLogHouseJpn |
| Jpn_CalcLogImport | accountingLog, accountingLogImportJpn, accountingLogImportRetryJpn |
| Jpn_CalcLogImportMail | accountingLogMailJpn |
| Jpn_CalcLogModify | accountingLog |
| Jpn_CalcSubAccountTitle | accountingEntityDepartment, accountingFSJpn, accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn |
| Jpn_CalcSubAccountTitleImport | accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn |
| Jpn_CalcTempNext_AccountTitle | accountingFSJpn, accountingFSIdJpn, accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn |
| Jpn_CalcTempNext_AccountTitleCS | accountingFSJpn |
| Jpn_CalcTempNext_AccountTitleFS | accountingFSJpn, accountingFSIdJpn |
| Jpn_CalcTempNext_AccountTitleFSCS | accountingFSJpn, accountingFSIdJpn |
| Jpn_CalcTempNext_EntityDepartment | accountingEntityDepartment, accountingEntityDepartmentFSValueJpn, accountingLog |
| Jpn_CalcTempNext_SubAccountTitle | accountingLog, accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn |
| Jpn_Cash | accountingAccountMemo, accountingCash, accountingLogCash, accountingLogFile |
| Jpn_CashAnalyze | accountingCash, accountingCashValue |
| Jpn_CashDefer | accountingCash, accountingLogCash, accountingLogCashDefer |
| Jpn_CashDelete | accountingLogCash |
| Jpn_CashEditor | accountingAccountMemo, accountingLogCash |
| Jpn_CashPay | accountingLogCash |
| Jpn_CashPlan | accountingCash, accountingCashValue |
| Jpn_CashPreference | accountingCash, accountingLogCash |
| Jpn_ConsumptionTax | accountingConsumptionTax |
| Jpn_ConsumptionTaxList | accountingLog |
| Jpn_DetailedAccount | accountingDetailedAccountJpn, accountingSubAccountTitleValueJpn |
| Jpn_EntityDepartment | accountingAccountMemo, accountingEntityDepartment, accountingEntityDepartmentFSValueJpn, accountingLog |
| Jpn_EntityDepartmentEditor | accountingEntityDepartment, accountingEntityDepartmentFSValueJpn |
| Jpn_ExportLog | accountingLog |
| Jpn_FinancialStatementSS | accountingLogCalcJpn |
| Jpn_FixedAssets | accountingAccountMemo, accountingFixedAssetsJpn, accountingLogFixedAssetsJpn |
| Jpn_FixedAssetsAccountTitleEditor | accountingFixedAssetsJpn |
| Jpn_FixedAssetsConfig | accountingEntity, accountingFixedAssetsJpn |
| Jpn_FixedAssetsEditor | accountingAccountMemo, accountingLogFixedAssetsJpn |
| Jpn_FixedAssetsOutput | accountingLogFixedAssetsJpn |
| Jpn_FixedAssetsOutput_2012_Public | accountingLogFixedAssetsJpn |
| Jpn_FixedAssetsPreference | accountingFixedAssetsJpn |
| Jpn_FixedAssetsWrite | accountingLogFixedAssetsJpn |
| Jpn_ImportLog | accountingLog |
| Jpn_Jpn | accountingEntity, accountingEntityDepartmentFSValueJpn, accountingFSJpn, accountingFSValueJpn, accountingFixedAssetsJpn, accountingLog, accountingSubAccountTitleJpn |
| Jpn_Ledger | accountingLogCalcJpn |
| Jpn_Log | accountingAccountMemo, accountingLog, accountingLogFile |
| Jpn_LogBack | accountingLog |
| Jpn_LogDelete | accountingLog |
| Jpn_LogEditor | accountingAccountMemo, accountingLog |
| Jpn_LogHouse | accountingAccountMemo, accountingLog, accountingLogHouseJpn |
| Jpn_LogHouseEditor | accountingAccountMemo, accountingLogHouseJpn |
| Jpn_LogHouseWrite | accountingLogHouseJpn |
| Jpn_LogImport | accountingAccountMemo, accountingLogImportJpn |
| Jpn_LogImportEditor | accountingAccountMemo, accountingLogImportJpn |
| Jpn_LogImportItemPreference | accountingAccountMemo, accountingAdminMemo, accountingLogImportJpn |
| Jpn_LogImportItemPreferenceEditor | accountingLogImportJpn |
| Jpn_LogImportList | accountingLog |
| Jpn_LogImportRetry | accountingLogImportRetryJpn |
| Jpn_LogOutput | accountingLog |
| Jpn_LogPermit | accountingLog |
| Jpn_LogPreference | accountingLogMailJpn |
| Jpn_NotesFS | accountingNotesFSJpn |
| Jpn_NotesFSEditor | accountingNotesFSJpn |
| Jpn_OrderLog | accountingLog, accountingLogFile |
| Jpn_Portal | accountingAccount, accountingEntity, accountingFSJpn, accountingFixedAssetsJpn, accountingLogCash, accountingLogFixedAssetsJpn, accountingPreference |
| Jpn_Portal_NextBanks | accountingBanks, accountingLogBanksAccount |
| Jpn_Portal_NextBreakEvenPoint | accountingBreakEvenPointJpn |
| Jpn_Portal_NextBudget | accountingBudgetJpn |
| Jpn_Portal_NextCash | accountingCash, accountingCashValue, accountingLogCash, accountingLogCashDefer |
| Jpn_Portal_NextCorporateTax | accountingCorporateTax |
| Jpn_Portal_NextData | accountingEntity, accountingEntityDepartment, accountingFSJpn, accountingFSValueJpn, accountingLog, accountingSubAccountTitleJpn |
| Jpn_Portal_NextDetailedAccount | accountingDetailedAccountJpn |
| Jpn_Portal_NextFile | accountingFile, accountingLogFile |
| Jpn_Portal_NextFixedAssets | accountingFixedAssetsJpn, accountingLogFixedAssetsJpn |
| Jpn_Portal_NextLogHouse | accountingLogHouseJpn |
| Jpn_Portal_NextLogImport | accountingLogImportJpn |
| Jpn_Portal_NextLogImportRetry | accountingLogImportRetryJpn |
| Jpn_Portal_NextLogMail | accountingLogMailJpn |
| Jpn_Portal_NextNotesFS | accountingNotesFSJpn |
| Jpn_Portal_NextSummaryStatement | accountingSummaryStatementJpn |
| Jpn_Preference | accountingAccount, accountingEntity, accountingFSJpn, accountingFixedAssetsJpn, accountingLogFixedAssetsJpn, accountingPreference |
| Jpn_PreferenceNextData | accountingEntity, accountingEntityDepartment, accountingFSJpn, accountingFSValueJpn, accountingLog, accountingSubAccountTitleJpn |
| Jpn_SubAccountTitle | accountingAccountMemo, accountingLog, accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn |
| Jpn_SubAccountTitleEditor | accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn |
| Jpn_SummaryStatement | accountingSummaryStatementJpn |
| Jpn_TrialBalance | accountingSubAccountTitleValueJpn |
| Rebuild | accountingEntity, accountingPreference |

### 2.2 DB を直接触らない 208 クラス

これらは `arrSearch` 経由で Search 系クラスに委譲するか、Choice/Output/Popup/Routine/Rebuild 等の純粋 UI/出力/初期化層。Phase 4 では DB アダプタから切り離してコントローラ層へ吸収できる。

分類の傾向:
- `*Search` / `*Choice` / `*Popup`: リスト絞込 UI。内部で Search クラスを呼ぶのみ
- `*Output`: CSV / PDF エクスポート専用。親クラスから配列を受領
- `Code_Else_Lib_*`: ユーティリティ (DB ラッパー `Lib_Db` を除く)
- `Code_Else_Core_Login_*` / `Code_Else_Core_Confirm_*`: 認証フローの状態遷移クラス
- `Jpn_2012_DetailedAccount_*Output` と `Jpn_*_2012_Public`: 決算書・公開版の表示専用 (データは親経由)
- `Code_Else_Plugin_Accounting_*API*`, `Jpn_API`: 外部 REST API エンドポイント (DB は内部で Search に委譲)

<details>
<summary>全 208 クラス一覧 (prefix 省略, アルファベット順)</summary>

```text
API API_Jpn_Entity API_Jpn_EntityDepartment API_Jpn_FinancialStatement API_Jpn_FinancialStatementCS
API_Jpn_LogImport API_Jpn_TrialBalance AccessSearch AccountEntityAuthorityChoice
AccountEntityAuthoritySearch AccountEntityChoicePermit AccountSearch AuthoritySearch
Base_API Base_API_Session Base_Access Base_AccountSearch Base_ApiAccountSearch Base_Attest
Base_LogSearch Base_Logout Base_ModuleSearch Base_Output Base_Popup Base_Rebuild Base_Root
Base_Routine Base_TermSearch Config Confirm_Confirm Confirm_Forgot Confirm_Login Confirm_Portal
Confirm_Publish Confirm_Rebuild Confirm_Sign EntitySearch FileAccountEntitySearch FileSearch
Jpn_2012_ConsumptionTax_General Jpn_2012_ConsumptionTax_Preference Jpn_2012_DetailedAccount_04Output
Jpn_2012_DetailedAccount_09Output Jpn_2012_DetailedAccount_10Output Jpn_2012_DetailedAccount_15Output
Jpn_2012_DetailedAccount_16Output Jpn_2012_DetailedAccount_AccountsPayable
Jpn_2012_DetailedAccount_AccountsPayableOutput Jpn_2012_DetailedAccount_AccountsReceivable
Jpn_2012_DetailedAccount_AccountsReceivableOutput Jpn_2012_DetailedAccount_AccruedBonusToDirectors
Jpn_2012_DetailedAccount_AccruedBonusToDirectorsOutput Jpn_2012_DetailedAccount_BadMiscellaneousExpenses
Jpn_2012_DetailedAccount_BadMiscellaneousExpensesOutput Jpn_2012_DetailedAccount_Deposits
Jpn_2012_DetailedAccount_DepositsOutput Jpn_2012_DetailedAccount_DividendsPayable
Jpn_2012_DetailedAccount_DividendsPayableOutput Jpn_2012_DetailedAccount_Employee
Jpn_2012_DetailedAccount_EmployeeOutput Jpn_2012_DetailedAccount_FixedAssets
Jpn_2012_DetailedAccount_FixedAssetsOutput Jpn_2012_DetailedAccount_IncomeTaxWithholding
Jpn_2012_DetailedAccount_IncomeTaxWithholdingOutput Jpn_2012_DetailedAccount_IndustrialProperty
Jpn_2012_DetailedAccount_IndustrialPropertyOutput Jpn_2012_DetailedAccount_Inventries
Jpn_2012_DetailedAccount_InventriesOutput Jpn_2012_DetailedAccount_KeyMoney
Jpn_2012_DetailedAccount_KeyMoneyOutput Jpn_2012_DetailedAccount_Land
Jpn_2012_DetailedAccount_LandOutput Jpn_2012_DetailedAccount_LoansPayable
Jpn_2012_DetailedAccount_LoansPayableOutput Jpn_2012_DetailedAccount_LoansReceivable
Jpn_2012_DetailedAccount_LoansReceivableOutput Jpn_2012_DetailedAccount_MiscellaneousIncome
Jpn_2012_DetailedAccount_MiscellaneousIncomeOutput Jpn_2012_DetailedAccount_NotesPayable
Jpn_2012_DetailedAccount_NotesPayableOutput Jpn_2012_DetailedAccount_NotesReceivable
Jpn_2012_DetailedAccount_NotesReceivableOutput Jpn_2012_DetailedAccount_Rents
Jpn_2012_DetailedAccount_RentsOutput Jpn_2012_DetailedAccount_Sales Jpn_2012_DetailedAccount_SalesOutput
Jpn_2012_DetailedAccount_Securities Jpn_2012_DetailedAccount_SecuritiesOutput
Jpn_2012_DetailedAccount_SuspensePayment Jpn_2012_DetailedAccount_SuspensePaymentOutput
Jpn_2012_DetailedAccount_SuspenseReceipt Jpn_2012_DetailedAccount_SuspenseReceiptOutput
Jpn_2012_SummaryStatement_Public Jpn_2012_SummaryStatement_PublicAccountTitle Jpn_API
Jpn_AccountTitleCS Jpn_AccountTitleEditor_2012_Public Jpn_AccountTitleFS_2012_Public
Jpn_AccountTitle_2012_Public Jpn_BalanceEditor Jpn_BalanceEditor_2012_Public Jpn_BalanceSubEditor
Jpn_BalanceSubEditor_2012_Public Jpn_Balance_2012_Public Jpn_BanksAccountSearch Jpn_BanksImportFile
Jpn_BanksImportWeb Jpn_BanksSearch Jpn_BanksWrite Jpn_BlueSheet Jpn_BlueSheetOutput
Jpn_BlueSheetOutput_2012_Public Jpn_BreakEvenPointAccountTitleEditor_2012_Public
Jpn_BreakEvenPointAccountTitle_2012_Public Jpn_BreakEvenPointOutput
Jpn_BreakEvenPointOutput_2012_Public Jpn_BreakEvenPoint_2012_Public Jpn_BudgetOutput
Jpn_CalcAccountTitleFS Jpn_CalcAccountTitleFSCS_2012_Public Jpn_CalcAccountTitleFS_2012_Public
Jpn_CalcAccountTitle_2012_Public Jpn_CalcBanks_Japannetbank Jpn_CalcBanks_Jibunbank
Jpn_CalcBanks_Sumisinnetbank Jpn_CalcBanks_Surugabank Jpn_CalcBreakEvenPoint_2012_Public
Jpn_CalcDep Jpn_CalcDep_Average Jpn_CalcDep_Declining Jpn_CalcDep_One Jpn_CalcDep_Straight
Jpn_CalcDep_Voluntary Jpn_CalcDictionary Jpn_CalcTempNext_AccountTitleFSCS_2012_Public
Jpn_CalcTempNext_AccountTitleFS_2012_Public Jpn_CalcTempNext_AccountTitle_2012_Public
Jpn_CalcTempNext_Log Jpn_CalcTempNext_Log_2012_Public Jpn_CalcTempNext_SubAccountTitle_2012_Public
Jpn_CashAnalyzeOutput Jpn_CashOutput Jpn_CashPlanOutput Jpn_CashSearch Jpn_Cash_2012_Public
Jpn_ConsumptionTaxSheet Jpn_DetailsSellingAndAdmin Jpn_DetailsSellingAndAdminOutput
Jpn_EntityDepartmentSearch Jpn_FinancialAnalyze Jpn_FinancialAnalyzeOutput
Jpn_FinancialStatement Jpn_FinancialStatementCS Jpn_FinancialStatementCSOutput
Jpn_FinancialStatementMulti Jpn_FinancialStatementMultiCS Jpn_FinancialStatementMultiCSOutput
Jpn_FinancialStatementMultiOutput Jpn_FinancialStatementMulti_2012_Public
Jpn_FinancialStatementOutput Jpn_FinancialStatementOutput_2012_Public
Jpn_FinancialStatementSSOutput Jpn_FinancialStatement_2012_Public Jpn_FixedAssetsAccountTitle
Jpn_FixedAssetsAccountTitleEditor_2012_Public Jpn_FixedAssetsAccountTitle_2012_Public
Jpn_FixedAssetsEditor_2012_Public Jpn_FixedAssetsPreference_2012_Public Jpn_FixedAssetsSearch
Jpn_FixedAssetsSearch_2012_Public Jpn_FixedAssetsWrite_2012_Public Jpn_FixedAssets_2012_Public
Jpn_LedgerOutput Jpn_LogEditor_2012_Public Jpn_LogHouseSearch Jpn_LogImportItem Jpn_LogImportListRucaro
Jpn_LogImportListYayoi Jpn_LogImportMail Jpn_LogImportSearch Jpn_LogImport_2012_Public
Jpn_LogPreference_2012_Public Jpn_LogSearch Jpn_LogSearch_2012_Public Jpn_Log_2012_Public
Jpn_NotesFSOutput Jpn_Portal_NextData_2012_Public Jpn_SubAccountTitleSearch Jpn_TrialBalanceOutput
Lib_Check Lib_Crypte Lib_Db Lib_Display Lib_Escape Lib_File Lib_Html Lib_Mail Lib_Media Lib_Rebuild
Lib_Request Lib_Time Login_Forgot Login_Login Login_Portal Login_Rebuild Login_Sign Login_Start Routine
```

</details>

---

## 3. テーブル逆引き

> DDL 定義 59 テーブルのうち 54 が参照され (§3.1)、5 が参照ゼロ (§3.2)。加えて DDL 未定義の 4 つの名前がコードに出現する (§3.3)。§3.1 のユニーク行数は 57 だが、その内訳は「DDL 定義 53 + `accountingEntityJpn` 行が #28/#29 両方を包含 = 54」と「DDL 未定義 4」。

本表の件数は `'strTable' => '...'` の出現クラス数 (リテラル参照) を示す。個別のクラスで読み専用 (`getSelect`) か書き込み含み (`insertRow` / `updateRow` / `deleteRow`) かは §1 の中核クラス詳細を参照。中核 30 クラス以外の Writers/Readers 区別は原ソースを直接当たる必要あり。

### 3.1 被参照数 Top → Bottom

| テーブル | 被参照クラス数 | 主要クラス (上位 3, prefix 省略) |
|---------|--------------|-----------------------------|
| accountingDetailedAccountJpn | 48 | Jpn_2012_DetailedAccount_AccountsPayableEditor, Jpn_2012_DetailedAccount_AccountsPayablePreference, Jpn_2012_DetailedAccount_AccountsReceivableEditor |
| accountingLog | 25 | AccountStatus, Jpn_CalcLog, Jpn_CalcLogBoard |
| accountingAccountMemo | 24 | Access, Account, AccountEntity |
| accountingFSJpn | 21 | EntityEditor, Jpn_AccountTitle, Jpn_AccountTitleCSEditor |
| accountingEntityJpn | 15 | Access, AccountStatus, Entity |
| baseAccount | 12 | Base_Account, Base_AccountAllChoice, Base_AccountChoice |
| accountingLogFile | 12 | AccountStatus, CalcFileBoard, File |
| accountingLogCash | 12 | Jpn_CalcCash, Jpn_CalcCashBoard, Jpn_CalcCashPay |
| accountingSubAccountTitleValueJpn | 11 | Jpn_AccountTitle, Jpn_Balance, Jpn_CalcDetailsSync |
| accountingSubAccountTitleJpn | 11 | Jpn_AccountTitle, Jpn_AccountTitleEditor, Jpn_CalcSubAccountTitle |
| accountingLogFixedAssetsJpn | 11 | Jpn_BlueSheet_2012_Public, Jpn_CalcDep_Sum, Jpn_CalcFixedAssetsBoard |
| accountingAccount | 11 | Account, AccountChoice, AccountEditor |
| accountingFixedAssetsJpn | 10 | EntityEditor, Jpn_CalcFixedAssets, Jpn_FixedAssets |
| accountingFSIdJpn | 9 | Jpn_AccountTitle, Jpn_AccountTitleEditor, Jpn_AccountTitleFS |
| accountingEntityDepartment | 9 | Accounting, Jpn_CalcAccountTitle, Jpn_CalcEntityDepartmentImport |
| accountingAccountEntity | 9 | Access, AccountEntity, AccountEntityAuthority |
| accountingLogImportJpn | 8 | Jpn_CalcLogBoard, Jpn_CalcLogConsumptionTax, Jpn_CalcLogImport |
| accountingCash | 8 | Jpn_CalcCash, Jpn_CalcCashPay, Jpn_Cash |
| baseAccountMemo | 7 | Base_Account, Base_ApiAccount, Base_ApplySign |
| accountingLogHouseJpn | 7 | Jpn_CalcLogBoard, Jpn_CalcLogConsumptionTax, Jpn_CalcLogHouse |
| accountingLogBanks | 7 | Jpn_Banks, Jpn_BanksAccount, Jpn_BanksEditor |
| accountingLogCashDefer | 6 | Jpn_CalcCashBoard, Jpn_CalcCashDefer, Jpn_CalcCashPay |
| accountingLogBanksAccount | 6 | Jpn_Banks, Jpn_BanksAccount, Jpn_BanksAccountEditor |
| accountingFSValueJpn | 6 | Jpn_CalcAccountTitle, Jpn_CalcAccountTitleFSCS, Jpn_CalcConsumptionTax |
| accountingEntityDepartmentFSValueJpn | 6 | Jpn_CalcAccountTitle, Jpn_CalcEntityDepartmentImport, Jpn_CalcTempNext_EntityDepartment |
| accountingPreference | 5 | Accounting, Init, Jpn_Portal |
| accountingCashValue | 5 | Jpn_CalcCash, Jpn_CalcCashBoard, Jpn_CashAnalyze |
| accountingBreakEvenPointJpn | 5 | Jpn_BreakEvenPoint, Jpn_BreakEvenPointAccountTitle, Jpn_BreakEvenPointAccountTitleEditor |
| baseTerm | 4 | Base_Init, Base_Term, Base_TermChoice |
| baseModule | 4 | Base_Init, Base_Module, Base_ModuleChoice |
| baseLoginIdLogin | 4 | Base_AccountEditor, Base_ApplyChange, Base_ApplySign |
| baseAccountId | 4 | Base_Account, Base_AccountEditor, Base_ApplyChange |
| accountingSummaryStatementJpn | 4 | Jpn_2012_SummaryStatement_PublicAccountTitleEditor, Jpn_2012_SummaryStatement_PublicEditor, Jpn_Portal_NextSummaryStatement |
| accountingLogImportRetryJpn | 4 | Jpn_CalcLogBoard, Jpn_CalcLogImport, Jpn_LogImportRetry |
| accountingFile | 4 | CalcFileImport, File, FilePreference |
| accountingBanks | 4 | Jpn_Banks, Jpn_BanksPreference, Jpn_CalcBanks |
| accountingAuthority | 4 | Authority, AuthorityChoice, AuthorityEditor |
| basePublish | 3 | Base_AccountEditor, Base_ApplySign, Base_Lock |
| baseApplyChange | 3 | Base_ApplyChange, Base_Portal, Confirm_Change |
| accountingNotesFSJpn | 3 | Jpn_NotesFS, Jpn_NotesFSEditor, Jpn_Portal_NextNotesFS |
| accountingLogMailJpn | 3 | Jpn_CalcLogImportMail, Jpn_LogPreference, Jpn_Portal_NextLogMail |
| accountingLogCalcJpn | 3 | Jpn_CalcLogCalc, Jpn_FinancialStatementSS, Jpn_Ledger |
| accountingConsumptionTax | 3 | Jpn_2012_ConsumptionTax_GeneralEditor, Jpn_2012_ConsumptionTax_PreferenceEditor, Jpn_ConsumptionTax |
| accountingBudgetJpn | 3 | Jpn_Budget, Jpn_BudgetEditor, Jpn_Portal_NextBudget |
| accountingAccess | 3 | Access, AccessChoice, AccessEditor |
| basePreference | 2 | Base_ModuleAbstract, Base_Portal |
| baseLoginPassword | 2 | Base_AccountEditor, Base_Portal |
| baseApiAccount | 2 | Base_ApiAccount, Base_ApiAccountEditor |
| baseAccessLog | 2 | Base_Log, Base_LogOutput |
| accountingBlueSheetJpn | 2 | Jpn_BlueSheetEditor_2012_Public, Jpn_BlueSheet_2012_Public |
| baseLock | 1 | Base_Lock |
| baseApplySign | 1 | Base_ApplySign |
| baseAccessUnknown | 1 | Base_Portal |
| accounting | 1 | Entity |
| accountingCorporateTax | 1 | Jpn_Portal_NextCorporateTax |
| accountingAdminMemo | 1 | Jpn_LogImportItemPreference |
| accountingAccountId | 1 | AccountStatus |

### 3.2 参照ゼロのテーブル (5 件)

直接 `'strTable' =>` のリテラル参照はないが、以下のいずれかの経路で触られている可能性:

- `arrSearch` に文字列変数経由で渡される
- `back/dat/version/BatchXXXXX.php` の DDL / データ移行コードのみが触る
- `Code_Else_Core_Login_*` / `Code_Else_Core_Base_Attest` が `$arr['strTable']` を動的ビルドする

| テーブル | 想定参照元 |
|---------|----------|
| baseSession | `Code_Else_Core_Login_Login`, `Code_Else_Core_Base_Attest` (セッション発行/検証) |
| baseLoginSecond | 同上 (2段階認証) |
| baseLoginMiss | `Code_Else_Core_Login_Login` (失敗回数ロック判定) |
| baseApplyForgot | `Code_Else_Core_Login_Forgot`, `Code_Else_Core_Confirm_Forgot` (変数経由アクセス) |
| baseToken | プッシュ通知機能 (`back/dat/` 配下のスクリプトから直接 SQL 発行の可能性) |

### 3.3 DDL 未定義だがコードに出現する 4 名前

| 名前 | 出現クラス | 備考 |
|-----|----------|-----|
| `accounting` | `Code_Else_Plugin_Accounting_Entity` (1件) | 途中切り抜きの疑い or モジュール名フォールバック |
| `accountingConsumptionTax` | `Jpn_ConsumptionTax`, `Jpn_2012_ConsumptionTax_GeneralEditor`, `Jpn_2012_ConsumptionTax_PreferenceEditor` | 実体は `accountingFSValueJpn.jsonConsumptionTaxDetail`。旧世代コードの残骸 |
| `accountingCorporateTax` | `Jpn_Portal_NextCorporateTax` | 法人税関連。将来機能予約 (未実装) |
| `accountingAdminMemo` | `Jpn_LogImportItemPreference` | 運用者メモ枠。`accountingAccountMemo` の誤記の可能性 |

---

## 4. Phase 4 移行優先度 Top 10

スコア算出 = (触るテーブル数) x 1 + (他クラスからの依存度 = 同一テーブルを触る他クラスの総数) x 0.5。仕訳・FS・試算表・元帳を触る中核が上位、かつテーブル共有の観点で連動移行が不可避なクラスを抽出。

| # | クラス | 触るテーブル数 | 依存度 | スコア | 根拠 |
|---|-------|---------|------|------|-----|
| 1 | Jpn_CalcAccountTitle | 4 | 高 (FS/FSValue/EntityDepartment/DeptFSValue すべて中核集計) | 52 | 仕訳 → FS 集計の心臓部。全数値整合性の要 |
| 2 | Jpn_Log | 3 | 最高 (`accountingLog` は 25 クラス被参照) | 35 | 仕訳本体テーブルの入出力画面。ここを移行しないと何も動かない |
| 3 | Jpn_LogEditor | 2 | 最高 (Log と AccountMemo) | 26 | 仕訳書込の排他ロジック。Log と同時移行 |
| 4 | Entity | 4 | 高 (Entity/Account/AccountMemo + カスケード削除で全子テーブル) | 23 | 会社作成/削除で全テーブルに跨るトランザクション |
| 5 | Jpn_SubAccountTitle | 4 | 高 (SubAccountTitle + Value / Log / Memo) | 22 | 補助科目の CRUD と仕訳整合性 |
| 6 | Jpn_AccountTitleEditor | 3 | 高 (FS / FSId / SubAccountTitle) | 21 | 勘定科目編集で FS ツリーを書き換え |
| 7 | Jpn_FixedAssets | 3 | 中 (FixedAssets / LogFixedAssets / Memo) | 18 | 固定資産台帳。減価償却計算連携 |
| 8 | Jpn_Cash | 4 | 中 (Cash / LogCash / LogFile / Memo) | 18 | 現金出納帳。仕訳との二重登録整合性 |
| 9 | Jpn_AccountTitle | 4 | 中 (FS / FSId / SubAccountTitle / SubAccountTitleValue) | 17 | 勘定科目マスタ表示とマスタメンテ |
| 10 | EntityEditor | 4 | 中 (Entity / EntityJpn / FS / FixedAssets) | 17 | 会社登録の初期テンプレ投入。新規作成フロー全体 |

### 補足: 同時移行すべきクラスタ

以下は「同じテーブルを共有するため、単独移行すると整合性が崩れる」クラスタ:

- **仕訳クラスタ**: `Jpn_Log`, `Jpn_LogEditor`, `Jpn_LogBack`, `Jpn_LogDelete`, `Jpn_LogPermit`, `Jpn_OrderLog`, `Jpn_ExportLog`, `Jpn_ImportLog` (→ `accountingLog`)
- **FS/集計クラスタ**: `Jpn_CalcAccountTitle`, `Jpn_AccountTitle`, `Jpn_AccountTitleEditor`, `Jpn_AccountTitleFS*`, `Jpn_CalcTempNext_*`, `Jpn_Portal_NextData`, `Jpn_Preference*` (→ `accountingFSJpn` / `accountingFSValueJpn`)
- **補助科目クラスタ**: `Jpn_SubAccountTitle`, `Jpn_SubAccountTitleEditor`, `Jpn_CalcSubAccountTitle`, `Jpn_Balance`, `Jpn_TrialBalance`, `Jpn_DetailedAccount` (→ `accountingSubAccountTitleJpn` / `accountingSubAccountTitleValueJpn`)
- **固定資産クラスタ**: `Jpn_FixedAssets`, `Jpn_FixedAssetsEditor`, `Jpn_FixedAssetsOutput`, `Jpn_CalcDep*`, `Jpn_CalcFixedAssets`, `Jpn_BlueSheet_2012_Public` (→ `accountingLogFixedAssetsJpn`)
- **現金クラスタ**: `Jpn_Cash`, `Jpn_CashEditor`, `Jpn_CashDefer`, `Jpn_CashDelete`, `Jpn_CashPay`, `Jpn_CashPreference`, `Jpn_CashPlan`, `Jpn_CalcCash*` (→ `accountingCash` / `accountingLogCash`)
- **銀行クラスタ**: `Jpn_Banks`, `Jpn_BanksAccount`, `Jpn_BanksEditor`, `Jpn_BanksPreference`, `Jpn_BanksOutput`, `Jpn_CalcBanks*` (→ `accountingBanks` / `accountingLogBanks`)
- **認証クラスタ**: `Base_Account`, `Base_AccountEditor`, `Base_ApplyChange`, `Base_ApplySign`, `Base_Lock`, `Base_Module`, `Base_Term`, `Base_ModuleAbstract`, `Base_Portal`, `Base_ModuleEditor`, `Core_Login_Login`, `Core_Base_Attest` (→ `baseAccount` / `baseModule` / `baseTerm` + 参照ゼロの login 系)

---

## 5. 調査メモ (Phase 4 設計者へ)

- **DB アクセスパス**: アプリ全体で 4 メソッド (`getSelect` / `insertRow` / `updateRow` / `deleteRow`) しか使わないので、Laravel / Rails に載せ替える際は Eloquent/ActiveRecord に 1-to-1 マッピング可能。
- **`$strNation` 戦略**: 現状 Jpn のみだが DDL ではマルチネーションを仮定。Phase 4 で外すなら `Jpn` サフィックスをテーブル名から除去し、`accountingEntity` (nation 非依存列) と `accountingEntityJpn` (日本固有列) を素直な親子 1:1 に書き直すのが自然。
- **JSON カラムの扱い**: `accountingFSJpn.jsonFS`, `accountingFSValueJpn.jsonJgaapFSPL`, `accountingLog.jsonLog` など多数が `longtext` に JSON 格納。Phase 4 で正規化するか MySQL 5.7+ の JSON 型に乗せるかの方針決定が必要。
- **権限モデル**: `accountingAccountEntity.jsonAuthority` と `accountingAuthority`, `accountingAccess` の三層構造 (ユーザ×会社の権限 / 権限テンプレ / 個別アクセス制御) は RBAC に落とせるが、既存 JSON の互換維持で複雑化する。
- **論理削除**: `flagRemove` + `stampRemove` パターンが `accountingLog`, `accountingLogFile`, `accountingLogBanks`, `accountingLogFixedAssetsJpn` 等で使われている。Laravel の SoftDeletes (`deleted_at`) へ置換可。
- **ログ系テーブル肥大化**: `accountingLog` (仕訳本体) と `accountingLogCalcJpn` (元帳キャッシュ) は主に数十万行スケール。PK 以外のインデックスがゼロなので移行と同時に `(idEntity, numFiscalPeriod, idAccountTitle, stampBook)` 複合 index 等を張るべき。

---

## Appendix A: ファイル配置

| 分類 | 場所 | クラス数 |
|-----|-----|--------|
| Core 認証/共通 | `back/class/else/core/base/` | 36 |
| Core ログイン | `back/class/else/core/login/` | 5 |
| Core Confirm | `back/class/else/core/confirm/` | 8 |
| Lib | `back/class/else/lib/` | ~15 (Smarty 除外) |
| Accounting Plugin (共通) | `back/class/else/plugin/accounting/` | 39 |
| Accounting Plugin (JPN) | `back/class/else/plugin/accounting/jpn/` | 149 |
| Accounting Plugin (JPN 2012 旧版) | `back/class/else/plugin/accounting/jpn/2012/` | 80+ |
| Accounting Plugin (JPN calcTempNext / portal / calcDep 等) | `back/class/else/plugin/accounting/jpn/{calcTempNext,portal,calcDep}/` | 20 |
| Config | `back/class/else/config/` | 1 |

## Appendix B: 原始データ

- クラス→テーブル抽出結果 (pipe 区切り): 中間成果物として `/tmp/classtable/all.txt` (432 行)
- テーブル→クラス逆引き: `/tmp/classtable/tablemap.txt` (57 行)
- 抽出スクリプト (参考):

```bash
cd back/class/else && find . -name '*.php' | while read f; do
  cls=$(grep -oE 'class\s+Code_Else_[A-Za-z0-9_]+' "$f" | head -1 | awk '{print $2}')
  tables=$(grep -oE "'strTable'\s*=>\s*'[a-zA-Z]+'" "$f" | grep -oE "'[a-zA-Z]+'$" | tr -d "'" | sort -u | tr '\n' ',' | sed 's/,$//')
  [ -n "$cls" ] && echo "$cls|$tables"
done
```
