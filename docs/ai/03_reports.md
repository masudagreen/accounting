# 帳票一覧

> 帳票出力は原則 `*Output.php` クラスが担当する（`_iniDetailOutput / _iniListOutput / _iniDetailPrint / _iniListPrint`）。  
> 出力形式は **PDF（dompdf）/ CSV / 印刷用 HTML**。dompdf は composer 経由（前セッションで追加済）。

## A. 業務帳票

| 帳票名 (会計用語) | クラス基底 | メニュー | 形式 | 移植 |
|---|---|---|---|---|
| 元帳（総勘定元帳） | `Jpn_LedgerOutput` | 集計 / 元帳 | PDF/CSV | ★必須 |
| 残高試算表 | `Jpn_TrialBalanceOutput` | 集計 / 残高試算表 | PDF/CSV | ★必須 |
| 仕訳帳出力 | `Jpn_LogOutput` | 仕訳帳の一覧/詳細出力 | PDF/CSV | ★必須 |
| 出納帳出力 | `Jpn_CashOutput` | 収支管理の出力 | PDF/CSV | ★必須 |
| 銀行明細出力 | `Jpn_BanksOutput` | 口座管理の出力 | CSV | ▲ |
| 固定資産台帳 | `Jpn_FixedAssetsOutput` | 固定資産管理 | PDF/CSV | ★必須 |
| 証憑ファイル一覧 | `Plugin_Accounting_FileOutput` | 証憑ファイル | PDF/CSV | ●任意 |
| 消費税集計表 | `Jpn_ConsumptionTaxSheet` (Output相当) | 集計 / 消費税集計表 | PDF | ★必須 (課税事業者) |
| 科目別税区分表 | `Jpn_ConsumptionTaxList` | 集計 / 科目別税区分表 | PDF | ●任意 |

## B. 決算書

| 帳票 | クラス | メニュー | 移植 |
|---|---|---|---|
| 損益計算書(PL) / 貸借対照表(BS) | `Jpn_FinancialStatementOutput` | 報告 / 決算 | ★必須 |
| 製造原価報告書(CR) | `Jpn_FinancialStatementOutput` (CR分) | 報告 / 決算 | ●(法人で `flagCR=1` 時) |
| キャッシュフロー計算書(CS) | `Jpn_FinancialStatementCSOutput` | 報告 / 決算(CS) | ●任意 |
| 株主資本等変動計算書(SS) | `Jpn_FinancialStatementSSOutput` | 報告 | ●(法人) |
| 比較決算（多期比較） | `Jpn_FinancialStatementMultiOutput` | 分析 / 比較決算 | ●任意 |
| 比較決算(CS) | `Jpn_FinancialStatementMultiCSOutput` | 分析 / 比較決算(CS) | ●任意 |
| 販売費及び一般管理費の明細 | `Jpn_DetailsSellingAndAdminOutput` | 報告 | ●任意 |
| 個別注記表 | `Jpn_NotesFSOutput` | 報告 / 個別注記表 | ●(法人) |
| 損益分岐点分析 | `Jpn_BreakEvenPointOutput` | 分析 / 損益分岐点分析 | ●任意 |
| 予算実績比較表 | `Jpn_BudgetOutput` | 分析 / 予算実績比較表 | ●任意 |
| 財務分析 | `Jpn_FinancialAnalyzeOutput` | 分析 / 財務分析 | ●任意 |
| 収支分析 | `Jpn_CashAnalyzeOutput` | 分析 / 収支分析 | ●任意 |
| 資金繰り分析 | `Jpn_CashPlanOutput` | 分析 / 資金繰り分析 | ●任意 |

## C. 申告関連帳票

### 法人事業概況説明書（17項目）
- クラス: `Jpn_2012_summaryStatement_Public` 系
- メニュー: 申告 / 法人事業概況説明書
- 様式: 2012年度版固定
- 移植: ▲ **最新様式（e-Tax）への作り直し前提**

### 勘定科目内訳明細書（23クラス・17種類）

`Jpn_2012_detailedAccount_*Output` で出力。各帳票には `04Output.php` `09Output.php` `10Output.php` `15Output.php` `16Output.php` のような番号付き Output もあり、これは **法令上の様式番号** に対応する内訳書（複数種類を1ファイルで出力するパターン）。

| 出力番号 | 内訳書 | 推定対応様式（参考: 国税庁 別表の連番表記） |
|---|---|---|
| 04 | 売上高/仕入高等の事業所別内訳 | 別表四記載に近い |
| 09 | 借入金及び支払利子等 | 別表九 |
| 10 | 役員報酬等の人件費内訳 | 別表十 |
| 15 | 雑損失等内訳 | 別表十五 |
| 16 | 雑益等内訳 | 別表十六 |
| その他 | 個別の Output クラスで対応 | 別表一〜十七の残り |

> 注: 上記対応は元コードの命名・配置からの推測。**移植時は最新の e-Tax 別表番号と必ず照合すること**。

### 青色申告書（個人事業主向け）
- クラス: `Jpn_2012_public_BlueSheet*`、`Jpn_BlueSheetOutput`
- 様式: 年度別 BLOB（`accountingBlueSheetJpn.numYearSheet`）
- 移植: ▲ 国税庁様式の年度更新がほぼ毎年あるため、**個別に作り直し**

## D. 帳票出力の技術スタック

| 出力先 | 元実装 | 推奨置換 |
|---|---|---|
| PDF | dompdf（純PHP・遅め・日本語フォント要設定） | TCPDF または Chrome Headless / Playwright PDF |
| CSV | 自前文字列生成 | League\\Csv |
| 印刷HTML | Smarty テンプレート | Twig もしくは Bladeに置換 |
| 画像PDF | 内部の画像合成 | imagemagick / pdftk-php |

## E. 移植優先度サマリ

| 優先度 | 帳票 |
|---|---|
| **最優先** | 仕訳帳・元帳・残高試算表・PL/BS・出納帳・固定資産台帳・消費税集計表 |
| **必須** | 製造原価報告書（製造業）/ 株主資本等変動計算書（法人） |
| **任意** | 各種分析帳票・比較決算・販管費明細 |
| **作り直し** | 17種類の内訳明細書・法人事業概況説明書・青色申告書（**最新様式に合わせて再設計**） |
