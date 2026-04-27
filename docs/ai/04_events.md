# イベント一覧（CRUD対応表）

> 抽出元: `back/class/else/plugin/accounting/` 配下の `_ini<Func>` メソッドと `$classDb->updateRow / getSelect / insertRow` 系の呼出パターン。  
> 元実装は **論理削除（`flagRemove=1, stampRemove=...`）** が基本。物理 DELETE は使わない。  
> CRUD は最大限の網羅を目指したが、220+ファイルを横断するため一部はクラス命名規則からの推定（**?** 印を付与）。

## 凡例

- **C**: INSERT（新規行作成）
- **R**: SELECT（行参照）
- **U**: UPDATE（行更新、論理削除も含む）
- **D**: DELETE（物理削除。元実装ではほぼ使わない）
- **間接**: 当該イベントから別クラスを呼び出し、別テーブルが触られる
- **★必須/●任意/▲移植不要寄り**: 移植判定の参考

---

## A. Log（仕訳帳）

| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 仕訳一覧表示 | `Jpn_Log::_iniJs` | | ✓ | | | accountingLog, accountingLogCalcJpn | ★ |
| 検索条件保存 | `Jpn_Log::_iniNaviSearchSave` | ✓ | | ✓ | | accountingAccountMemo | ★ |
| 検索条件削除 | `Jpn_Log::_iniNaviSearchDelete` | | | ✓ | | accountingAccountMemo (論理) | ★ |
| 検索再読込 | `Jpn_Log::_iniNaviSearchReload` | | ✓ | | | accountingLog, accountingLogCalcJpn | ★ |
| 詳細再読込 | `Jpn_Log::_iniDetailReload / _iniSearchDetail` | | ✓ | | | accountingLog, accountingLogFile | ★ |
| 仕訳新規登録 | `Jpn_LogEditor::_iniDetailAdd` | ✓ | ✓ | ✓ | | accountingLog (C), accountingLogCalcJpn (C 間接), accountingPreference (jsonStampUpdate U), accountingFSValueJpn (集計 U 間接) | ★ |
| 仕訳編集 | `Jpn_LogEditor::_iniDetailEdit` | | ✓ | ✓ | | accountingLog (U + jsonVersion), accountingLogCalcJpn (再計算 U 間接), accountingPreference (jsonStampUpdate U), accountingFSValueJpn (再集計 U 間接) | ★ |
| 仕訳論理削除（単件） | `Jpn_LogDelete::_iniDetailDelete` | | | ✓ | | accountingLog (flagRemove=1) | ★ |
| 仕訳論理削除（複数） | `Jpn_LogDelete::_iniListDelete` | | | ✓ | | accountingLog (flagRemove=1) | ★ |
| 仕訳取り戻し（申請取消） | `Jpn_LogBack::_iniDetailBack / _iniListBack` | | | ✓ | | accountingLog (flagApply, flagApplyBack U) | ★ |
| 仕訳承認 | `Jpn_LogPermit::_iniDetailPermit / _iniListPermit` | | | ✓ | | accountingLog (jsonPermitHistory, flagApply U) | ★ |
| 仕訳取込実行 | `Jpn_Log::_iniListImport` | ✓ | ✓ | | | accountingLog (C), accountingLogImportJpn (R), accountingLogImportRetryJpn (C if mismatch) | ★ |
| 仕訳一覧出力(PDF/CSV) | `Jpn_LogOutput::_iniListOutput / _iniDetailOutput / _iniListPrint` | | ✓ | | | accountingLog, accountingLogFile | ★ |

---

## B. Cash（収支管理）

| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧表示 | `Jpn_Cash::_iniJs` | | ✓ | | | accountingCash, accountingLogCash, accountingCashValue | ★ |
| 検索条件保存/削除/再読込 | `Jpn_Cash::_iniNaviSearchSave / _iniNaviSearchDelete / _iniNaviSearchReload` | ✓ | ✓ | ✓ | | accountingAccountMemo | ★ |
| 詳細再読込 | `Jpn_Cash::_iniSearchDetail / _iniDetailReload / _iniListReload` | | ✓ | | | accountingLogCash | ★ |
| 収支見積（試算） | `Jpn_CashEditor::_iniDetailEstimate` | | ✓ | | | accountingLogCash, accountingFSValueJpn | ★ |
| 収支新規登録 | `Jpn_CashEditor::_iniDetailAdd` | ✓ | ✓ | ✓ | | accountingLogCash (C), accountingCashValue (U), accountingPreference (jsonStampUpdate U) | ★ |
| 収支編集 | `Jpn_CashEditor::_iniDetailEdit` | | ✓ | ✓ | | accountingLogCash (U + jsonVersion), accountingCashValue (U) | ★ |
| ナビフォーマット保存/再読込 | `Jpn_CashEditor::_iniNaviFormatSave / _iniNaviFormatReload` | ✓ | ✓ | ✓ | | accountingAccountMemo | ★ |
| 収支削除(単件/複数) | `Jpn_CashDelete::_iniDetailDelete / _iniListDelete` | | | ✓ | | accountingLogCash (flagRemove=1) | ★ |
| 収支消込 | `Jpn_CashPay::_iniDetailPay / _iniListPay` | | | ✓ | | accountingLogCash (flagPay=1, stampPay U) | ★ |
| 元帳書出 | `Jpn_Cash::_iniDetailWrite / _iniListWrite` | ✓ | ✓ | ✓ | | accountingLog (C), accountingLogCash (jsonWriteHistory U), accountingLogCalcJpn (C 間接) | ★ |
| 出納帳出力 | `Jpn_CashOutput::_iniDetailOutput` | | ✓ | | | accountingLogCash | ★ |
| 留保ログ表示 | `Jpn_CashDefer::_iniJs` | | ✓ | | | accountingLogCashDefer | ● |
| 留保ログ確定 | `Jpn_CashDefer::_iniDetailWrite`? | ✓ | ✓ | ✓ | | accountingLogCash (C), accountingLogCashDefer (D ?) | ● |
| 資金繰り計画(画面) | `Jpn_CashPlan::_iniJs` | | ✓ | | | accountingLogCash, accountingLogCalcJpn | ● |
| 資金繰り出力 | `Jpn_CashPlanOutput::_iniDetailOutput` | | ✓ | | | (集計のみ) | ● |
| 収支分析(画面) | `Jpn_CashAnalyze::_iniJs` | | ✓ | | | accountingLogCash | ● |
| 収支分析出力 | `Jpn_CashAnalyzeOutput::_iniDetailOutput` | | ✓ | | | (集計のみ) | ● |
| 収支設定 | `Jpn_CashPreference::_iniDetailEdit` | | | ✓ | | accountingCash (jsonCash, flagPayWrite, flagAutoImport, flagPermitImport U) | ★ |

---

## C. Banks（口座管理）

| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧表示 | `Jpn_Banks::_iniJs` | | ✓ | | | accountingBanks, accountingLogBanks, accountingLogBanksAccount | ▲ |
| 検索/詳細再読込 | `Jpn_Banks::_iniNaviSearch / _iniSearchDetail / _iniDetailReload / _iniListReload` | | ✓ | | | accountingLogBanks | ▲ |
| 検索条件 保存/削除/再読込 | `Jpn_Banks::_iniNaviSearchSave / _iniNaviSearchDelete / _iniNaviSearchReload` | ✓ | ✓ | ✓ | | accountingAccountMemo | ▲ |
| 銀行明細 削除 | `Jpn_Banks::_iniDetailDelete / _iniListDelete` | | | ✓ | | accountingLogBanks (flagRemove=1) | ▲ |
| 元帳書出 | `Jpn_Banks::_iniDetailWrite / _iniListWrite` | ✓ | ✓ | ✓ | | accountingLog (C 間接 BanksWrite), accountingLogBanks (jsonWriteHistory U) | ▲ |
| 銀行明細出力 | `Jpn_Banks::_iniListOutput / _iniDetailOutput`（→ `Jpn_BanksOutput`）| | ✓ | | | accountingLogBanks | ▲ |
| 口座新規登録 | `Jpn_BanksAccountEditor::_iniDetailAdd` | ✓ | | | | accountingLogBanksAccount (C, blobDetail に接続情報) | ▲ |
| 口座編集 | `Jpn_BanksAccountEditor::_iniDetailEdit` | | | ✓ | | accountingLogBanksAccount (U) | ▲ |
| 口座一覧 | `Jpn_BanksAccount::_iniJs` / `Jpn_BanksAccountSearch::_iniJs` | | ✓ | | | accountingLogBanksAccount | ▲ |
| 口座取込(ファイル) | `Jpn_BanksImportFile::_iniJs/_iniDetailImport` (推定) | ✓ | ✓ | | | accountingLogBanks (C) | ▲ |
| 口座取込(WebAPI) | `Jpn_BanksImportWeb::_iniJs/_iniDetailImport` (推定) — `calcBanks/Japannetbank.php` 等 | ✓ | ✓ | | | accountingLogBanks (C), accountingLogBanksAccount (stampCheck U) | ▲ |
| 口座管理設定 | `Jpn_BanksPreference::_iniDetailEdit` | | | ✓ | | accountingBanks (flagAutoImport, flagLock U) | ▲ |
| 仕訳化(Write) | `Jpn_BanksWrite::_iniListWrite`（推定）| ✓ | ✓ | ✓ | | accountingLog (C), accountingLogBanks (jsonWriteHistory U), accountingLogCalcJpn (C 間接) | ▲ |

---

## D. FixedAssets（固定資産管理）

| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧表示 | `Jpn_FixedAssets::_iniJs` | | ✓ | | | accountingFixedAssetsJpn, accountingLogFixedAssetsJpn | ★ |
| 検索条件 保存/削除/再読込 | `Jpn_FixedAssets::_iniNaviSearchSave / _iniNaviSearchDelete / _iniNaviSearchReload` | ✓ | ✓ | ✓ | | accountingAccountMemo | ★ |
| 詳細再読込 | `Jpn_FixedAssets::_iniSearchDetail / _iniDetailReload / _iniListReload` | | ✓ | | | accountingLogFixedAssetsJpn | ★ |
| ナビフォーマット 保存/再読込 | `Jpn_FixedAssetsEditor::_iniNaviFormatSave / _iniNaviFormatReload` | ✓ | ✓ | ✓ | | accountingAccountMemo | ★ |
| 減価償却計算プレビュー | `Jpn_FixedAssetsEditor::_iniDetailCalc` | | ✓ | | | (`calcDep/*` で計算のみ) | ★ |
| 固定資産新規登録 | `Jpn_FixedAssetsEditor::_iniDetailAdd` | ✓ | | ✓ | | accountingLogFixedAssetsJpn (C), accountingFixedAssetsJpn (jsonDepSum U) | ★ |
| 固定資産編集 | `Jpn_FixedAssetsEditor::_iniDetailEdit` | | ✓ | ✓ | | accountingLogFixedAssetsJpn (U + jsonVersion), accountingFixedAssetsJpn (jsonDepSum U) | ★ |
| 削除 | `Jpn_FixedAssets::_iniDetailDelete / _iniListDelete` | | | ✓ | | accountingLogFixedAssetsJpn (flagRemove=1) | ★ |
| 元帳書出（償却計上） | `Jpn_FixedAssets::_iniDetailWrite / _iniListWrite` (→ `Jpn_FixedAssetsWrite`) | ✓ | ✓ | ✓ | | accountingLog (C), accountingLogFixedAssetsJpn (jsonWriteHistory U), accountingLogCalcJpn (C 間接) | ★ |
| 台帳出力 | `Jpn_FixedAssets::_iniListOutput / _iniDetailOutput / _iniListPrint / _iniDetailPrint`（→ `Jpn_FixedAssetsOutput`）| | ✓ | | | accountingLogFixedAssetsJpn | ★ |
| 固定資産設定 | `Jpn_FixedAssetsPreference::_iniDetailEdit` | | | ✓ | | accountingFixedAssetsJpn (flagDepWrite, flagFraction*, jsonAccountTitle U) | ★ |
| 固定資産科目割振 | `Jpn_FixedAssetsAccountTitleEditor::_iniDetailEdit` | | | ✓ | | accountingFixedAssetsJpn (jsonAccountTitle U) | ★ |
| 検索画面 | `Jpn_FixedAssetsSearch::_iniJs` | | ✓ | | | accountingLogFixedAssetsJpn | ★ |
| 設定画面 | `Jpn_FixedAssetsConfig::_iniJs` | | ✓ | | | accountingFixedAssetsJpn | ★ |

---

## E. File（証憑ファイル）

| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧表示 | `Plugin_Accounting_File::_iniJs` | | ✓ | | | accountingFile, accountingLogFile | ● |
| アップロード(新規) | `Plugin_Accounting_FileEditor::_iniDetailAdd` | ✓ | | | | accountingLogFile (C) | ● |
| 編集 | `Plugin_Accounting_FileEditor::_iniDetailEdit` | | | ✓ | | accountingLogFile (U + jsonVersion) | ● |
| 削除（推定: 元コードに専用 Delete クラスは無く Editor 側で flagRemove） | `Plugin_Accounting_FileEditor::_iniDetailRemove`? | | | ✓ | | accountingLogFile (flagRemove=1) | ● |
| ダウンロード/出力 | `Plugin_Accounting_FileOutput::_iniDetailOutput` | | ✓ | | | accountingLogFile | ● |
| メール取込 | `Plugin_Accounting_FileImport`（バッチ）| ✓ | ✓ | | | accountingLogFile (C), accountingFile (R 接続情報) | ● |
| 検索 | `Plugin_Accounting_FileSearch::_iniJs` | | ✓ | | | accountingLogFile | ● |
| 振分(事業体ひも付け) | `Plugin_Accounting_FileAccountEntity*` | ✓ | ✓ | ✓ | | accountingAccountEntity (R), accountingLogFile (U) | ● |
| 取込先設定 | `Plugin_Accounting_FilePreference::_iniDetailEdit` | | | ✓ | | accountingFile (jsonMail*, strHost/User/Password U) | ● |

---

## F. 集計系（読み取り中心）

### Ledger（元帳）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 元帳表示 | `Jpn_Ledger::_iniJs` | | ✓ | | | accountingLog, accountingLogCalcJpn, accountingFSValueJpn | ★ |
| 検索 | `Jpn_Ledger::_iniNaviSearch / _iniListReload` | | ✓ | | | (同上) | ★ |
| 出力(PDF/CSV) | `Jpn_LedgerOutput::_iniListOutput / _iniListPrint`（→ `Jpn_LedgerOutput`）| | ✓ | | | (同上) | ★ |

### TrialBalance（残高試算表）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 試算表表示 | `Jpn_TrialBalance::_iniJs` | | ✓ | | | accountingFSValueJpn, accountingFSJpn | ★ |
| 出力 | `Jpn_TrialBalanceOutput::_iniListOutput / _iniListPrint` | | ✓ | | | (同上) | ★ |

### ConsumptionTax（消費税集計）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 消費税集計表 | `Jpn_ConsumptionTaxSheet::_iniJs / Output` | | ✓ | | | accountingFSValueJpn (jsonConsumptionTax*), accountingLog | ★ |
| 科目別税区分表 | `Jpn_ConsumptionTaxList::_iniJs / Output` | | ✓ | | | accountingLogCalcJpn, accountingFSValueJpn | ● |
| 一般消費税申告(2012) | `Jpn_2012_consumptionTax_GeneralEditor::_iniDetailEdit` | | | ✓ | | accountingFSValueJpn (jsonConsumptionTax U) | ● |
| 設定(2012) | `Jpn_2012_consumptionTax_PreferenceEditor::_iniDetailEdit` | | | ✓ | | accountingEntityJpn (flagConsumptionTax* U) | ● |

---

## G. 決算書系

### FinancialStatement（決算 PL/BS/CR）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 決算表示 | `Jpn_FinancialStatement::_iniJs` | | ✓ | | | accountingFSJpn, accountingFSValueJpn | ★ |
| 決算出力 | `Jpn_FinancialStatementOutput::_iniListOutput` | | ✓ | | | (同上) | ★ |
| CS 表示 | `Jpn_FinancialStatementCS::_iniJs` | | ✓ | | | accountingFSJpn (jsonJgaapFSCS) | ● |
| CS 出力 | `Jpn_FinancialStatementCSOutput::_iniListOutput` | | ✓ | | | (同上) | ● |
| 株主資本 SS 表示/出力 | `Jpn_FinancialStatementSS::_iniJs / SSOutput` | | ✓ | | | accountingFSJpn (jsonJgaapFS*) | ● |
| 比較決算 表示/出力 | `Jpn_FinancialStatementMulti::_iniJs / Output` | | ✓ | | | 複数期の accountingFSValueJpn | ● |
| 比較決算CS 表示/出力 | `Jpn_FinancialStatementMultiCS::_iniJs / CSOutput` | | ✓ | | | 複数期の accountingFSJpn(CS) | ● |
| 販管費明細 | `Jpn_DetailsSellingAndAdmin*` | | ✓ | | | accountingFSValueJpn (PL の販管費) | ● |

### NotesFS（個別注記表）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 注記表示 | `Jpn_NotesFS::_iniJs` | | ✓ | | | accountingNotesFSJpn | ● |
| 注記編集 | `Jpn_NotesFSEditor::_iniDetailEdit` | ✓ | | ✓ | | accountingNotesFSJpn (Upsert: 行が無ければ C, あれば U) | ● |
| 注記出力 | `Jpn_NotesFSOutput::_iniDetailOutput` | | ✓ | | | accountingNotesFSJpn | ● |

---

## H. 分析系（読み取り中心）

| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 損益分岐点 表示 | `Jpn_BreakEvenPoint::_iniJs` | | ✓ | | | accountingBreakEvenPointJpn, accountingFSValueJpn | ● |
| 損益分岐点 設定編集 | `Jpn_BreakEvenPointAccountTitleEditor::_iniDetailEdit` | ✓ | | ✓ | | accountingBreakEvenPointJpn (Upsert) | ● |
| 損益分岐点 出力 | `Jpn_BreakEvenPointOutput::_iniListOutput` | | ✓ | | | (同上) | ● |
| 予算 表示 | `Jpn_Budget::_iniJs` | | ✓ | | | accountingBudgetJpn, accountingFSValueJpn | ● |
| 予算 編集 | `Jpn_BudgetEditor::_iniDetailEdit` | ✓ | | ✓ | | accountingBudgetJpn (Upsert) | ● |
| 予算 出力 | `Jpn_BudgetOutput::_iniListOutput` | | ✓ | | | (同上) | ● |
| 財務分析 表示/出力 | `Jpn_FinancialAnalyze::_iniJs / Output` | | ✓ | | | accountingFSValueJpn | ● |

---

## I. 入力補助・取込

### LogImport（仕訳取込フィルタ）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| フィルタ一覧 | `Jpn_LogImport::_iniJs / _LogImportSearch::_iniJs` | | ✓ | | | accountingLogImportJpn | ● |
| フィルタ新規/編集 | `Jpn_LogImportEditor::_iniDetailAdd / _iniDetailEdit` | ✓ | | ✓ | | accountingLogImportJpn | ● |
| フィルタ品目設定 | `Jpn_LogImportItemPreferenceEditor::_iniDetailEdit` | | | ✓ | | accountingLogImportJpn | ● |
| リトライキュー表示 | `Jpn_LogImportRetry::_iniJs` | | ✓ | | | accountingLogImportRetryJpn | ● |
| リトライ実行 | `Jpn_LogImportRetry::_iniDetailRetry`? | ✓ | ✓ | ✓ | | accountingLog (C if matched), accountingLogImportRetryJpn (D 行削除) | ● |
| 取込元一覧(YAYOI/Rucaro) | `Jpn_LogImportListYayoi / Rucaro` | | ✓ | | | (CSV/外部から R) | ● |
| 取込メール設定 | `Jpn_LogImportMail::_iniDetailEdit` (or batch) | ✓ | ✓ | ✓ | | accountingLogMailJpn | ● |

### LogHouse（家事按分）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧表示 | `Jpn_LogHouse::_iniJs / _LogHouseSearch::_iniJs` | | ✓ | | | accountingLogHouseJpn | ● |
| 新規/編集 | `Jpn_LogHouseEditor::_iniDetailAdd / _iniDetailEdit` | ✓ | | ✓ | | accountingLogHouseJpn | ● |
| 確定（仕訳化） | `Jpn_LogHouseWrite::_iniListWrite` | ✓ | ✓ | ✓ | | accountingLog (C), accountingLogHouseJpn (jsonWriteHistory U) | ● |

---

## J. マスタ管理

### AccountTitle（勘定科目）
科目ツリーは `accountingFSJpn.jsonJgaapAccountTitle{PL,BS,CR}` の JSON 内に保持される。実体テーブルは無い。
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 勘定科目表示 | `Jpn_AccountTitle::_iniJs` | | ✓ | | | accountingFSJpn | ★ |
| 勘定科目編集 | `Jpn_AccountTitleEditor::_iniDetailEdit` | | | ✓ | | accountingFSJpn (jsonJgaapAccountTitle* U), accountingFSIdJpn (採番 U) | ★ |
| 勘定科目(CS)編集 | `Jpn_AccountTitleCSEditor::_iniDetailEdit` | | | ✓ | | accountingFSJpn (jsonJgaapAccountTitleCR/CS U) | ● |
| 決算科目編集 | `Jpn_AccountTitleFSEditor::_iniDetailEdit` | | | ✓ | | accountingFSJpn (jsonJgaapFS* U) | ★ |
| 決算項目(CS)編集 | `Jpn_AccountTitleFSCSEditor::_iniDetailEdit` | | | ✓ | | accountingFSJpn (jsonJgaapFSCS U) | ● |

### SubAccountTitle（補助科目）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 補助科目一覧 | `Jpn_SubAccountTitle::_iniJs / SubAccountTitleSearch::_iniJs` | | ✓ | | | accountingSubAccountTitleJpn | ★ |
| 補助科目編集 | `Jpn_SubAccountTitleEditor::_iniDetailAdd / Edit` | ✓ | | ✓ | | accountingSubAccountTitleJpn, accountingSubAccountTitleValueJpn | ★ |

### Balance（期首残高）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 期首残高表示 | `Jpn_Balance::_iniJs` | | ✓ | | | accountingFSValueJpn (sumPrev), accountingSubAccountTitleValueJpn | ★ |
| 期首残高編集 | `Jpn_BalanceEditor::_iniDetailEdit` | | | ✓ | | accountingFSValueJpn (sumPrev U) | ★ |
| 補助の期首残高編集 | `Jpn_BalanceSubEditor::_iniDetailEdit` | | | ✓ | | accountingSubAccountTitleValueJpn (sumPrev U) | ★ |

### EntityDepartment（部門）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 部門一覧 | `Jpn_EntityDepartment::_iniJs / Search::_iniJs` | | ✓ | | | accountingEntityDepartment | ● |
| 部門編集 | `Jpn_EntityDepartmentEditor::_iniDetailAdd / Edit` | ✓ | | ✓ | | accountingEntityDepartment, accountingEntityDepartmentFSValueJpn | ● |

---

## K. 申告系（▲全般作り直し前提）

### SummaryStatement（法人事業概況説明書）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 表示 | `Jpn_SummaryStatement::_iniJs` | | ✓ | | | accountingSummaryStatementJpn | ▲ |
| 編集 | `Jpn_2012_summaryStatement_PublicEditor::_iniDetailEdit` | ✓ | | ✓ | | accountingSummaryStatementJpn | ▲ |
| 科目編集 | `Jpn_2012_summaryStatement_PublicAccountTitleEditor::_iniDetailEdit` | | | ✓ | | accountingSummaryStatementJpn (jsonJgaapAccountTitle*) | ▲ |
| 出力 | `Jpn_SummaryStatement::Output系` | | ✓ | | | (同上) | ▲ |

### DetailedAccount（勘定科目内訳明細書、17種類）
全 23 種類で同一パターン（`*Editor`、`*Output`、`*Preference` の3クラス構成）。
| 共通イベント | クラス基底名 | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧表示 | `Jpn_2012_detailedAccount_<種類>::_iniJs` | | ✓ | | | accountingDetailedAccountJpn (`flagDetail=<種類>` で絞込) | ▲ |
| 計算プレビュー | `<種類>Editor::_iniDetailCalc` | | ✓ | | | accountingFSValueJpn, accountingLog | ▲ |
| 編集 | `<種類>Editor::_iniDetailEdit` | ✓ | | ✓ | | accountingDetailedAccountJpn (Upsert by flagDetail) | ▲ |
| 出力(PDF) | `<種類>Output::_iniDetailOutput / _iniDetailPrint` | | ✓ | | | accountingDetailedAccountJpn | ▲ |
| 設定 | `<種類>Preference::_iniDetailEdit` | | | ✓ | | accountingDetailedAccountJpn (flagDetail=`<種類>Pref`?) | ▲ |
| 番号別出力 | `04Output / 09Output / 10Output / 15Output / 16Output` | | ✓ | | | accountingDetailedAccountJpn (複数 flagDetail を統合出力) | ▲ |

### BlueSheet（青色申告書）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 表示 | `Jpn_BlueSheet::_iniJs / _2012_public_BlueSheet::_iniJs` | | ✓ | | | accountingBlueSheetJpn | ▲ |
| 編集 | `Jpn_2012_public_BlueSheetEditor::_iniDetailEdit` | ✓ | | ✓ | | accountingBlueSheetJpn (blobData Upsert by numYearSheet) | ▲ |
| 出力 | `Jpn_BlueSheetOutput::_iniDetailOutput` | | ✓ | | | accountingBlueSheetJpn | ▲ |

---

## L. プラグイン管理（事業体・権限・アカウント）

### Entity（事業体）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧 | `Plugin_Accounting_Entity::_iniJs / EntitySearch::_iniJs` | | ✓ | | | accountingEntity, accountingEntityJpn | ★ |
| 新規/編集 | `Plugin_Accounting_EntityEditor::_iniDetailAdd / Edit` | ✓ | | ✓ | | accountingEntity (C/U), accountingEntityJpn (C/U), accountingFSJpn (C 初期化), accountingFSValueJpn (C 初期化) | ★ |
| 設定確定 | `Plugin_Accounting_EntityEditor::_iniDetailEnd`? (jpn/Portal::_updateDbEntityConfigEnd) | | | ✓ | | accountingEntity (flagConfig=0 U) | ★ |
| 選択(現在事業体切替) | `Jpn_Portal::_updateDbIdEntityCurrent` | | | ✓ | | accountingAccount (idEntityCurrent, numFiscalPeriodCurrent U) | ★ |
| 削除 | (推定: 専用 Delete クラス無し) | | | ✓ | | (実装上は flagRemove での運用？要確認) | ★ |

### Authority（権限パターン）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧 | `Plugin_Accounting_Authority::_iniJs / AuthoritySearch::_iniJs` | | ✓ | | | accountingAuthority | ● |
| 新規/編集 | `Plugin_Accounting_AuthorityEditor::_iniDetailAdd / Edit` | ✓ | | ✓ | | accountingAuthority | ● |

### Access（アクセス可能項目パターン）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧 | `Plugin_Accounting_Access::_iniJs / AccessSearch::_iniJs` | | ✓ | | | accountingAccess | ● |
| 新規/編集 | `Plugin_Accounting_AccessEditor::_iniDetailAdd / Edit` | ✓ | | ✓ | | accountingAccess | ● |

### AccountEntity（アカウント×事業体）
| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 一覧 | `Plugin_Accounting_Account::_iniJs / AccountSearch::_iniJs` | | ✓ | | | accountingAccount, accountingAccountEntity | ★ |
| アクセス構成編集 | `Plugin_Accounting_AccountEntityAuthorityEditor::_iniDetailEdit` | ✓ | | ✓ | | accountingAccountEntity (C/U) | ★ |
| 担当引継 | `Jpn_Portal::_updateDbCharge / _updateDbChargeLogCash / _updateDbChargeLogFixedAssets` | | ✓ | ✓ | | accountingLog (jsonChargeHistory U), accountingLogCash (同 U), accountingLogFixedAssetsJpn (同 U) | ★ |

---

## M. 繰越処理 (Next*)

`back/class/else/plugin/accounting/jpn/portal/Next*.php` 配下の各 Next クラスが、機能ごとに翌期データを生成。`Jpn_Portal::_iniDetailEdit / _updateDbNextData` から一括起動される。

| イベント | クラス::メソッド | C | R | U | D | 主要テーブル | 移植 |
|---|---|---|---|---|---|---|---|
| 繰越メインエントリ | `Jpn_Portal::_updateDbNextData` | | ✓ | ✓ | | accountingAccount (numFiscalPeriodCurrent U), accountingEntity (numFiscalPeriod U), accountingPreference (jsonStampUpdate U) | ★ |
| 仕訳の繰越 | `portal/NextData / calcTempNext/Log / AccountTitle / AccountTitleCS / AccountTitleFS / AccountTitleFSCS / SubAccountTitle / EntityDepartment` | ✓ | ✓ | | | accountingFSJpn (C 翌期), accountingFSValueJpn (C 翌期), accountingSubAccountTitleJpn (C), accountingSubAccountTitleValueJpn (C), accountingEntityDepartment (C) | ★ |
| 銀行・証憑・収支・固定資産・予算・損益分岐点・家事按分・取込・取込リトライ・取込メール の繰越 | `portal/NextBanks / NextFile / NextCash / NextFixedAssets / NextBudget / NextBreakEvenPoint / NextLogHouse / NextLogImport / NextLogImportRetry / NextLogMail` | ✓ | ✓ | | | 各種 accounting*Jpn（翌期行を C） | ★ |
| 内訳明細・概況説明書・個別注記・法人税の繰越 | `portal/NextDetailedAccount / NextSummaryStatement / NextNotesFS / NextCorporateTax` | ✓ | ✓ | | | 同上 | ▲ (再作成予定なら不要) |

---

## N. CRUDサマリ（テーブル別）

各テーブルが「どの機能でどの操作を受けるか」を逆引き。

| テーブル | 主に C する機能 | 主に R する機能 | 主に U する機能 | D（論理削除）する機能 |
|---|---|---|---|---|
| accountingLog | LogEditor, CashWrite, FixedAssetsWrite, BanksWrite, LogHouseWrite, LogImport, NextData (翌期初期) | Log一覧, Ledger, TrialBalance, FS各種, ConsumptionTax | LogEditor, LogBack, LogPermit | LogDelete |
| accountingLogCalcJpn | LogEditor (再計算), Cash/Banks/FixedAssetsWrite | Ledger, TrialBalance, ConsumptionTaxList | (再計算で都度 C/U) | (Logの flagRemove で論理削除扱い) |
| accountingLogCash | CashEditor | Cash一覧, Ledger | CashEditor, CashPay, Cash(Write履歴) | CashDelete |
| accountingLogCashDefer | (取込でC) | CashDefer | (確定時 D 相当) | — |
| accountingLogBanks | BanksImport*, BanksWrite | Banks一覧 | Banks(Write履歴) | Banks Delete系 |
| accountingLogBanksAccount | BanksAccountEditor | Banks系 | BanksAccountEditor (stampCheck等) | — |
| accountingBanks | (Entity初期化時にC) | Banks一覧 | BanksPreference | — |
| accountingLogFixedAssetsJpn | FixedAssetsEditor | FixedAssets一覧, FixedAssetsOutput | FixedAssetsEditor, FixedAssetsWrite | FixedAssetsDelete? (実体は FixedAssets::_iniListDelete) |
| accountingFixedAssetsJpn | (Entity初期化) | FixedAssets画面 | FixedAssetsPreference, FixedAssetsAccountTitleEditor | — |
| accountingFile | (Entity初期化) | File 画面 | FilePreference | — |
| accountingLogFile | FileEditor, FileImport (mail) | File一覧, Log | FileEditor (jsonVersion) | FileEditor (flagRemove) |
| accountingEntity / accountingEntityJpn | EntityEditor (新規) | 全画面 (現在事業体ロード) | EntityEditor, Portal切替, NextData | — |
| accountingAccount | (Initで都度upsert), AccountEntityAuthorityEditor | 全画面 | Portal idEntityCurrent切替, NextData | — |
| accountingAccountEntity | AccountEntityAuthorityEditor | 全画面 | 同上 | — |
| accountingAccountMemo | (検索条件保存/フォーマット保存) | (検索条件再読込/フォーマット再読込) | 同上 | (検索条件削除) |
| accountingAuthority | AuthorityEditor | 全画面 (権限判定) | AuthorityEditor | — |
| accountingAccess | AccessEditor | 全画面 (項目表示制御) | AccessEditor | — |
| accountingPreference | (rebuild時 C) | 全画面 | 各イベント末尾の jsonStampUpdate U | — |
| accountingFSJpn | EntityEditor (初期化), NextData | TrialBalance, FS, Ledger, AccountTitle | AccountTitle*Editor, AccountTitleFS*Editor | — |
| accountingFSValueJpn | EntityEditor (初期化), NextData, BalanceEditor | TrialBalance, FS, ConsumptionTax | LogEditor等で集計再計算, BalanceEditor | — |
| accountingFSIdJpn | EntityEditor (初期化) | AccountTitleEditor | AccountTitleEditor (採番) | — |
| accountingSubAccountTitleJpn | SubAccountTitleEditor, NextData | SubAccountTitle, Ledger, FS | SubAccountTitleEditor | — |
| accountingSubAccountTitleValueJpn | SubAccountTitleEditor, NextData | Ledger, Balance | LogEditor等で再計算, BalanceSubEditor | — |
| accountingEntityDepartment | EntityDepartmentEditor, NextData | 全画面 | EntityDepartmentEditor | — |
| accountingEntityDepartmentFSValueJpn | (Logの集計時 upsert) | 部門別FS表示 | 同上 | — |
| accountingBudgetJpn | BudgetEditor, NextData | Budget | BudgetEditor | — |
| accountingBreakEvenPointJpn | BreakEvenPointAccountTitleEditor, NextData | BreakEvenPoint | BreakEvenPointAccountTitleEditor | — |
| accountingNotesFSJpn | NotesFSEditor (初回), NextData | NotesFS | NotesFSEditor | — |
| accountingDetailedAccountJpn | 各 detailedAccount\*Editor (初回), NextData | 各 detailedAccount\*画面 | 各 \*Editor | — |
| accountingSummaryStatementJpn | summaryStatement_PublicEditor, NextData | SummaryStatement | summaryStatement_PublicEditor | — |
| accountingBlueSheetJpn | 2012_public_BlueSheetEditor (初回) | BlueSheet | 同 (年度別 blob を上書き) | — |
| accountingLogImportJpn | LogImportEditor | LogImport, Log取込時 | LogImportEditor, LogImportItemPreferenceEditor | — |
| accountingLogImportRetryJpn | (取込で不一致時 C) | LogImportRetry | (リトライ成功時 D 相当) | — |
| accountingLogMailJpn | (LogImportMail設定 C) | (取込バッチ R) | LogImportMail設定編集 | — |
| accountingLogHouseJpn | LogHouseEditor, NextData | LogHouse | LogHouseEditor, LogHouseWrite | LogHouse Delete系 |
| accountingCash | (Entity初期化) | Cash画面 | CashPreference | — |
| accountingCashValue | CashEditor (初回, 期別) | Cash画面 | CashEditor | — |

---

## O. 移植時の留意

1. **`*Delete` クラスは Log と Cash 以外には専用ファイルが存在しない**。それ以外の機能は通常 `*Editor` の中で `flagRemove=1` を更新する／そもそも論理削除を提供していないことが多い。設計を統一しなおすことを推奨。
2. **イベント終端で必ず `accountingPreference.jsonStampUpdate` が更新される**。これはダッシュボードの「お知らせ」と新着判定に使われる。移植時は WebSocket / SSE への置換が望ましい。
3. **`jsonChargeHistory / jsonPermitHistory / jsonWriteHistory / jsonVersion`** は longtext JSON で履歴を蓄積する設計。監査要件次第で別テーブル化する判断ポイント。
4. **採番** は `accountingPreference.jsonIdAutoIncrement` と `accountingFSIdJpn` の2系統で自前管理されている。移植時は AUTO_INCREMENT または UUID v7 等に統一推奨。
5. **再集計の連鎖** は重い: 仕訳1件編集で `accountingLog → accountingLogCalcJpn (展開) → accountingFSValueJpn (PL/BS/CR集計) → accountingSubAccountTitleValueJpn (補助集計) → accountingEntityDepartmentFSValueJpn (部門集計) → accountingCashValue (収支集計)` まで連鎖更新がある。性能観点ではイベントソーシング/ビュー化を検討する余地。

---

## P. 移植優先度の総合判定

| 機能群 | テーブル中心 | 推奨判定 |
|---|---|---|
| 仕訳・元帳・試算表・PL/BS・期首残高・科目設定・部門・消費税基本 | accountingLog, accountingLogCalcJpn, accountingFSJpn, accountingFSValueJpn, accountingSubAccountTitleJpn(+Value) | **★ 必須** |
| 収支管理・固定資産・証憑ファイル | accountingLogCash, accountingCash(+Value), accountingFixedAssetsJpn(+Log), accountingLogFile | **★ 必須**（実利用前提） |
| 取込・家事按分・分析・予算・損益分岐点 | accountingLogImportJpn, accountingLogHouseJpn, accountingBudgetJpn, accountingBreakEvenPointJpn | **● 任意**（実利用に応じて） |
| 銀行口座取込 | accountingLogBanks(+Account), accountingBanks | **▲ 移植不要寄り**（外部 API 廃止 / 利用銀行限定） |
| 個別注記表・株主資本・CS | accountingNotesFSJpn, accountingFSJpn(jsonJgaapFSCS) | **●**（法人で使うなら） |
| 内訳明細・概況説明書・青色申告書（2012様式） | accountingDetailedAccountJpn, accountingSummaryStatementJpn, accountingBlueSheetJpn | **▲ 作り直し前提** |
| 権限・アクセス・APIアカウント | accountingAuthority, accountingAccess, baseApiAccount | **●**（運用形態次第） |
| 繰越処理 | (上記すべての翌期生成) | **★ 必須**（年度跨ぎが必須業務のため） |
