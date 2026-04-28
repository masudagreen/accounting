# 既存コードの既知の問題・要確認事項

> 移植中に Calc 系・Lib 系を読み解く過程で気づいた、**バグまたはバグの疑いがある箇所** と **設計上の懸念点** を一覧化する。新ドメイン層を作るときの「これを直すべき/同じ挙動を再現すべき」の判断材料。

## A. 会計ロジックの疑義（要検証）

### A-1. CalcLogConsumptionTax::_getCalcConsumptionTax — 内税/外税で同一の計算式
**場所**: `back/class/else/plugin/accounting/jpn/CalcLogConsumptionTax.php:4029-4055`

```php
if ($flagConsumptionTaxWithoutCalc == 1) {  // 内税
    $numValueConsumptionTax = $numValue * $numRateConsumptionTax / (100 + $numRateConsumptionTax);
    // floor / round / ceil で端数処理
} elseif ($flagConsumptionTaxWithoutCalc == 2) {  // 外税
    //this is ok not wrong
    $numValueConsumptionTax = $numValue * $numRateConsumptionTax / (100 + $numRateConsumptionTax);
    // 同じ式、同じ端数処理
}
```

- `flagConsumptionTaxWithoutCalc` は **1=内税** / **2=外税** / **3=別記** を表すフラグ。
- 通常、外税(net→gross)では `tax = net * rate / 100` を使うが、ここでは内税と同じ `gross * rate / (100 + rate)` を使っている。
- `// this is ok not wrong` というコメントが付けられているため、**作者は意図して同じ式を使った可能性**。前段階で外税入力時にも numValue を gross に正規化している前提なら正しい。
- **検証必要**: 外税で numValue がどの時点で gross に正規化されるかを追跡し、もし正規化が無いケースがあれば計算結果が約9%過小評価される。
- 新ドメインでは `TaxTreatment::Exclusive` で `tax = net * rate / 100` を採用済み（理論上の正しい式）。本番データとの集計値比較で差分が出たらここを精査する。

### A-2. 端数処理は float 演算
**場所**: `back/class/else/lib/Display.php::getNumDisplay`、各 Calc クラス

```php
$arr['num'] = ceil($arr['num'] * $numLevel) / $numLevel;
```

- `$numValue * $numRateConsumptionTax / (100 + $numRateConsumptionTax)` も float 演算。
- 一般に float の二進浮動小数点誤差（`0.1 + 0.2 != 0.3`）が発生する余地がある。
- 累積する集計（年間の消費税合計）で 1 円単位のズレが発生し得る。
- **新ドメインでは BigDecimal (brick/math) を採用済**。

### A-3. `'8_reduced'` ⇄ 8 の文字列/数値変換
**場所**: `CalcConsumptionTax::_getReceiveValueConsumptionTaxReduced`、`_getSendValueConsumptionTaxReduced`

軽減税率(8%)と旧標準税率(8%)を区別するため `'8_reduced'` 文字列を使い、保存時に 8 + flag に分解、読込時に再結合する設計。コメントに「数字 == 文字だと通過するため 文字で統一」とあり、PHP 8 アップグレード時に修正された痕跡。同種の `==` 比較は他にも残っている可能性が高い（要全件確認）。

### A-4. `flagConsumptionTaxCalc` が定数文字列ではなく整数
- `1=floor / 2=round / 3=ceil` の対応がコード内マジックナンバー。文書化なし。
- 別途 `flagFractionDep / flagFraction` 等で **`'floor' / 'round' / 'ceil'` 文字列**を使っている箇所もあり、不統一。
- 新ドメインでは `RoundingMode` enum に統一済み。

---

## B. 仕様上の制約

### B-1. 期内の消費税率変更（インボイス、軽減税率廃止等）
- 各仕訳行に `numRateConsumptionTax`（または `'8_reduced'`）を持たせるため、**取引単位での税率切替は可能**。
- ただし「いつ何%が適用されるか」の **発効日マスタは存在しない** — 利用者が手動で正しい税率を選ぶ必要がある。
- **改善余地**: 税率テーブル（発効日/失効日/種別/料率）を新ドメインに持たせ、`stampBook` から自動推奨できるようにする。

### B-2. 簡易課税の事業区分
- `accountingEntityJpn.flagConsumptionTaxBusinessType` は **1事業体1区分**。
- 実務では「卸売業と小売業を兼業」など複数区分を持つケースが多い。元実装は明細単位で `flagConsumptionTaxSimpleRule` を切替えるため対応可能だが、UI で気付きにくい。

### B-3. インボイス制度（2023/10/01〜）対応
- `accountingLog`/`accountingLogCash` には **適格請求書発行事業者番号（T+13桁）の列が無い**。
- 取引相手ごとの登録番号管理も無い。
- **要追加**: 取引相手マスタ + 登録番号 + 適格/非適格フラグ。免税事業者からの仕入の経過措置（80%控除→50%控除→0%）にも対応必要。

### B-4. 部門別 FS の利用状況不明
- `accountingEntityDepartmentFSValueJpn` テーブルは存在するが、UI 上は部門別決算書の出力箇所が見当たらない。
- 実装が中途半端な可能性。利用実績の確認が必要。

---

## C. PHP 8.x への移行残課題

### C-1. `exit;` の散在
- `back/class/else/plugin/accounting/jpn/` 配下だけで **1,055 箇所**。
- 例外を使わず process kill するため、テストでアサーションが取れない / リクエスト境界以外でも死ぬ。
- 新ドメインでは例外（`InvalidArgumentException` / `DomainException` / 専用例外）を投げる。

### C-2. `@func_get_arg(0)` の `@`
- `Code_Else_Lib_Db::__construct` 等。エラーを silent swallow する。
- PHP 8 では `@` の挙動が変わった項目もあり、要点検。
- 新ドメインでは `__construct(?array $arr = null)` の形式に統一（既に Db.php は修正済み）。

### C-3. `global $classDb;` 等のグローバル多用
- `back/class/else/plugin/accounting/Init.php` で 8 種類のグローバルをロード。
- DI なし → 単体テスト不可。
- 新ドメインでは引数渡し / コンストラクタ注入に統一。Repository インターフェースで永続化を分離。

### C-4. 暗黙の null/数値比較
- アップグレードコミット (`2a0c5d5 php8 & mariadb10`) で多数の `(int)$value` キャストが追加されている。
- すべてが網羅されたとは限らず、`!$varsRequest['query']['ext']` のような未定義キーアクセス + 暗黙比較が残る可能性。
- 影響範囲: Editor 系の入力ハンドリング全般。

### C-5. `preg_split` で空配列
- `Time.php:72,112` で `$dateTime->format("s,i,H,j,m,Y,w,d")` を `,` で分割し `list()` に展開。
- 区切り文字が予期しない数だった場合、`Undefined array key warning` で PHP 8.1+ では `Warning` レベル。例外への移行は要件次第。

### C-6. `mb_strlen(null)` 警告
- `Display::getNumDisplay`: 入力に `.` が含まれる場合のみ `preg_split` で `$str` を取得し `mb_strlen($str)` するため、現状は安全。ただし PHP 8.1+ で `mb_strlen(null)` は deprecation 警告対象 — 防衛的に `?? ''` を入れた方が安全。

### C-7. `pow()` の整数オーバーフロー
- `Display::getNumDisplay:99` で `pow(10, $arr['numLevel'])`。`numLevel` が大きすぎるとオーバーフローして float に。会計用途は通常 0〜2 桁なので実害は無いが、明示的なバリデーションが無い。

---

## D. セキュリティ観点

### D-1. CSRF / トークン
- ログイン後の操作で `setToken()` がポータル初期ロード時に発行され、後続リクエストで照合される設計。フォーム単位のトークンか、セッション全体で1つかは要確認。

### D-2. パスワード保管
- `baseAccount.strPassword` に保存。`Code_Else_Core_Login_Login` でハッシュ方式を確認する必要あり。古い場合は `password_hash(PASSWORD_BCRYPT)` または `PASSWORD_ARGON2ID` に移行。

### D-3. 銀行接続情報
- `accountingLogBanksAccount.blobDetail` に銀行ID/パスワードを格納。`back/class/else/lib/Crypte.php` を使った独自暗号化と思われるが、AES-GCM など authenticated encryption への移行を検討。

### D-4. 監査ログ容量
- `baseAccessLog` に全リクエストを記録。長期運用で巨大化。ローテーション/外部ストレージ移行が必要。

### D-5. SQL インジェクション
- `$classDb->updateRow()` 経由は PDO バインドで安全。
- ただし旧コード `$strSql = '... where idAccount = ' . $varsRequest['query']['idAccount']` のような連結があった場合は危険。**要全文検索**で確認。

---

## E. 設計上の懸念（バグではない）

### E-1. JSON カラムの多用
- `jsonVersion / jsonChargeHistory / jsonPermitHistory / jsonWriteHistory` などで履歴を JSON 配列として longtext に蓄積。
- 監査要件・検索要件・MariaDB の longtext 上限（4GB）に依存。
- 仕訳1件で1キロバイト以上になるケースもあり、年間数万件で集計が遅くなる懸念。

### E-2. `arrComma*` カンマ区切り検索
- 例: `accountingLog.arrCommaIdAccountTitleDebit = ',1,2,3,'`（前後にカンマで囲む特殊形式）。
- `LIKE '%,1,%'` で検索する設計。フルテーブルスキャンになりインデックスが効かない。
- 数十万行になれば顕在化する性能問題。

### E-3. 採番の自前管理
- `accountingPreference.jsonIdAutoIncrement` と `accountingFSIdJpn` で自前採番。並列INSERTで競合し得る。
- 新ドメインでは AUTO_INCREMENT または UUID v7 推奨。

### E-4. 再計算チェーン
- 仕訳1件編集 →
   `accountingLog → accountingLogCalcJpn (削除&再生成) → accountingFSValueJpn (再集計) → accountingSubAccountTitleValueJpn → accountingEntityDepartmentFSValueJpn → accountingCashValue`  
- 同期処理。年間数万件の編集を遡行すると秒〜十秒級の遅延。
- イベントソーシング/差分更新/非同期再集計を検討。

---

## F. 確認の優先順

| 優先度 | 項目 | 対応 |
|---|---|---|
| **最優先** | A-1 (内税/外税の式同一) | 新ドメイン Golden Master テストで本番データ照合し、差分があれば旧実装の挙動を再現するか「修正後の正しい計算」を採用するか判断 |
| **高** | B-3 (インボイス対応) | スキーマ追加（取引先マスタ・登録番号・経過措置）が必要 |
| **高** | C-1〜C-3 (テスト容易性) | 移植時に解消されるので個別対応不要 |
| **中** | A-3 (`==` 比較) | Rector で `==` → `===` 自動変換を試みる |
| **中** | D-2 (パスワードハッシュ) | login 流入経路をテストでカバーした上で更新 |
| **低** | E-1〜E-4 (設計問題) | ドメイン完成後の永続化層リファクタで対応 |

---

## G. スプリント別の特異点（移植中に発見）

### Sprint 2: FiscalPeriod
- **G-2-1**: `Jpn::_getVarsFiscalPeriod` (line 853-) で `numFiscalTermMonth != 12` の場合、`f1` 通期しかサポートせず **半期/四半期 (`f21`/`f22`/`f41`-`f44`) は暗黙に無視**。短期事業年度では半期集計が壊れる可能性。新ドメインでは将来対応。

### Sprint 3: AccountTitle
- **G-3-1**: 既存 `JgaapAccountTitlePL.php` は **27 root** が並ぶ。「売上総利益」「営業利益」「経常利益」「税引前当期純利益」「当期純利益」など計算結果ノードも独立 root として配置。新ドメインでは「計算結果」と「分類カテゴリ」を本来分けるべきだが、互換性のため一旦 Revenue 区分で取り込み中。後段で計算結果ノード専用クラスに分離予定。
- **G-3-2**: 標準科目 ID にスペル誤りが残る（例: `accoutsReceivable` ← `accountsReceivable` の typo / `Inventries` ← `Inventories` の typo）。互換性のため新ドメインでも同じIDを許容しているが、**マイグレーション時に正字へ移行する判断が必要**。

### Sprint 4: Ledger
- 顕著な発見なし

### Sprint 5: TrialBalance
- 顕著な発見なし。元実装の `accountingFSValueJpn.jsonJgaapAccountTitle*` の `sumPrev / sumDebit / sumCredit / sumNext` 構造は新ドメインの TrialBalanceRow と同型なので、Repository を後から実装すれば既存データから素直に読み込める見込み。

### Sprint 6: 減価償却
- **G-6-1**: `back/class/else/plugin/accounting/jpn/calcDep/Voluntary.php` は `_setVarsCalc` 内で **計算ロジックがほぼ空**。`numValueDepCalc` を設定しないため、実体は「user-supplied 額をそのまま DB 列に保持して終了」。新ドメインでは `Voluntary::compute(requestedAmount: ...)` として明示的に仕様化。
- **G-6-2**: **平成19年4月1日以前取得分の旧定額/旧定率は新ドメインで未実装**。元 `Straight.php` には `flag20070401` / `flag20070331` で残存価額 5%/10% 控除付きの旧計算が分岐実装されている。新方式 (1円残価) のみ採用したため、`accountingLogFixedAssetsJpn.numSurvivalRate > 0` の旧資産が DB に残っている場合は新ドメインで計算できない。
  - **要確認**: `SELECT COUNT(*) FROM accountingLogFixedAssetsJpn WHERE numSurvivalRate > 0 OR stampStart < UNIX_TIMESTAMP('2007-04-01')`
  - 0件なら対応不要。1件以上なら `StraightLineLegacy` / `DecliningBalanceLegacy` を追加実装する。
- **G-6-3**: 元実装は `flagDepRateType` (1=通常 / 0=改定) を **資産単位で永続化**し、保証額切替後の状態を記憶している。新ドメインは毎期 `previousAccumulated` から計算しなおすので **このフラグは不要**。マイグレーション時にカラム削除候補。
- **G-6-4**: 元実装の償却率テーブル (`depStraightNew.csv`/`depDecliningNew200.csv` 等) は **耐用50年まで** しか定義されていない。建物の耐用年数は最長47年なので実用上は問題ないが、特殊資産で50年超があると壊れる。新ドメインでもまずは50年までで実装。

### Sprint 7: 決算書 (PL/BS/CR)
- **G-7-1**: 元 `JgaapAccountTitlePL.php` には **27 root** に「売上総利益」「営業利益」「経常利益」「税引前当期純利益」「当期純利益」など計算結果ノードが root レベルで並ぶ。新ドメインでは `PlSection` enum (Sales/CostOfSales/SellingAndAdmin/...) で分類し、計算結果は `ProfitAndLossStatement` のメソッドとして導出。元データ構造との互換性は `StandardChartLoader::PL_ROOT_TO_SECTION` でマップ。
- **G-7-2**: `JgaapAccountTitleCR.php` の root に **`workInProcessOpeningInventoryWrap` / `workInProcessClosingInventoryWrap` / `workInProcessRemoveWrap`** といった `Wrap` サフィックス付きノードがある。これは「仕掛品関連の集計枠」を意味し、その配下に実際の科目が並ぶ構造。新ドメインの `CrSection` enum で意図を明確化。
- **G-7-3**: 元 `accountingFSValueJpn.jsonJgaapFS{PL,BS,CR,CS}` は **集計値を JSON で永続化**しているが、新ドメインでは集計を都度計算する設計。本番DBの値と一致するか Golden Master test で要検証。差分があれば、丸めポリシーや「期末締切前/後」の扱いの違いが原因の可能性。
- **G-7-4**: BS の不変条件 `資産 = 負債 + 純資産 + 当期純利益` は **期末締切前** の試算表に対するもの。元実装は確定後 (`flagFiscalReport='f1'`) に当期純利益を `netAssets` 配下の科目 (利益剰余金/個人事業の元入金 等) に振替えている可能性が高い。マイグレーション時の境界条件として要確認。
- **G-7-5**: 株主資本等変動計算書 (SS) と キャッシュフロー計算書 (CS) は **本スプリント未着手**。SS は純資産の期首・変動・期末の遷移、CS は間接法/直接法で営業/投資/財務の3区分のキャッシュフロー。元実装の `accountingFSJpn.jsonJgaapFSCS` を流用するか新規設計するか、後で判断。

### Sprint 7B: SS / CS
- **G-7B-1**: 元 `JgaapFSCS.php` は **直接法** (営業収入・仕入支出・人件費支出を個別集計) で記述されている。新ドメインは間接法で実装した。両方式は同一の `CashFlowStatementBuilder` で扱えないため、直接法が必要になれば `DirectCashFlowStatementBuilder` を別クラスで追加する.
- **G-7B-2**: 間接法での法人税等の現金支払は本実装では `CashFlowAdjustment::Operating` 区分への外部入力で対応する設計。テストでは `tax = 0` のケースのみ。複合シナリオは Sprint 8 以降で.
- **G-7B-3**: BS 科目を「現金等/売上債権/棚卸資産/仕入債務」と識別するために、現在は文字列キー (`'cash'`/`'accountsReceivable'`/`'inventory'`/`'accountsPayable'`) を `CashFlowStatementBuilder` の定数で受ける。将来的に `AccountTitle` に `CsClassification` enum を付与して `AccountTree` から自動集計する方向が望ましい.
- **G-7B-4**: `CashFlowStatement` 自体は不変条件 (期末現金 = 期首 + 全CF合計) を **ランタイムでは強制せず**, テスト側で `assertCashFlowInvariant` ヘルパーで担保する設計. 不整合な入力を渡すと CashFlowStatement 内部に矛盾が残る. 利用者責任で整合性を担保するか、Builder で validate するか後で判断.
- **G-7B-5**: 元実装は `accountingFinancialStatementSS.php` クラスがあるが今回はゼロから新ドメインで設計した. 元実装の出力フォーマット・項目順序との互換性は Golden Master test で要検証.

### Sprint 8: Cash / FixedAssets / Banks スタブ
- **G-8-1**: `BankStatement` の方向表現に `App\Domain\Cash\CashDirection` を再利用した. Banks ⇄ Cash の依存が生じるため、将来 Banks が独立すべきなら専用 enum (`BankDirection` 等) に分離する余地. 現時点では YAGNI.
- **G-8-2**: `FixedAssetJournalGenerator` で **配賦の概念 (販管費 / 製造原価 / 非営業費 / 農業費)** を省略し、単一の借方科目 (`depreciationExpenseAccountTitleId`) に集約する設計. 元実装の `accountingLogFixedAssetsJpn.numRatioSellingAdminCost` 等の配賦率を再現するなら、`FixedAssetAccountMapping` に複数科目と配分率を持たせるよう拡張が必要. 個人事業主 / マイクロ法人で配賦が必要なケースは少ないため、Sprint 9 以降の拡張として保留.
- **G-8-3**: 元実装の `Code_Else_Plugin_Accounting_Jpn_CashPay` (消込) は **CashEntry の `status` を Settled に変更し、対応する仕訳を別途記入** する設計. 新ドメインでは `CashEntry::withStatus()` で immutable 遷移するが、消込時に仕訳をどう生成するかは未実装 (現状は Pending → Settled の状態遷移のみテスト).
- **G-8-4**: 5 行 (Japannetbank/Japanpostbank/Jibunbank/Sumisinnetbank/Surugabank) の Web 取込パーサは **本スプリントでは実装しない**. インターフェース (`BankStatementImporter`) と `DryRunBankAdapter` のみ提供. 将来必要になったら同インターフェースを実装する形で追加.

### Sprint 9: Golden Master Test (本番データ照合)

**Golden DB**: `db20260207.sql.zip` を `rucaro_golden` schema にロードして照合. 本番データは `tests/Golden/data/` 配下に置き、`.gitignore` で完全除外済 (`db20*.sql` / `db_dump*.sql` / `tests/Golden/data/` 等).

**結果サマリ** (1,579 件の仕訳を再構築):
- **Entity 1 (7期, 計1,049件): 完全一致** ✓
  - 借方=貸方 不変条件すべて成立
  - PL の当期純利益が本番DB値と完全一致 (差分0円)
- **Entity 2 (7期, 計322件): 不一致**
  - **本番DB側のFS設定不整合** が原因 (新ドメイン側の問題ではない)

**G-9-1**: Entity 2 の `accountingFSJpn.jsonJgaapAccountTitlePL` に **`rents` / `taxesAndDues` / `commissionPaid` / `badMiscellaneousExpenses` / `insuranceExpenses` 等の科目が定義されていない**. しかし `accountingLog` ではこれらの科目が実際に使われているため、AccountTree から見ると未知の科目になり TrialBalance で 0 集計される. 結果として Entity 2 は借方≠貸方になる.
- **これは本番DB側のデータ不整合** (FS設定が仕訳科目に追従できていない)
- 移行時の選択肢:
  1. Entity 2 の本番側 FS 設定を補完してから移行 (推奨)
  2. 新ドメイン側で「未知の科目を自動補完」するモードを用意 (推奨しない)
  3. 移行前に本番側で FS 再構築を行う運用フィックス

**G-9-2**: `LegacyJournalReader` は `accountingLog.jsonVersion` の **最新版 (配列の末尾)** から借方/貸方明細を抽出. 編集履歴のある仕訳でも最新状態のみ反映. 履歴を辿って差分監査する用途には別の Reader が必要.

**G-9-3**: `LegacyAccountTreeReader` は `accountingFSJpn.jsonJgaapAccountTitle{BS,PL,CR}` から AccountTree を構築するが、**`PlSection` / `CrSection` の自動判定**は実施しない (不整合データでもとりあえず読めるよう緩く扱う). PL 集計は本来 `PlSection` で分類するため、Legacy データから FS 集計する場合は別途 `StandardChartLoader` の section マップを併用する設計.

**G-9-4**: 本番DBは MariaDB 10.11. ダンプ内のテーブル名・列名は `back/tpl/templates/else/plugin/accounting/db/config.php` の DDL と一致 (移行時に追加カラムは不要).

**G-9-5**: Golden テストは **DB 接続できないと markTestSkipped** する設計. CI で本番DB環境がなくても壊れない. テスト実行には `GOLDEN_DB_HOST=db GOLDEN_DB_PORT=3306 GOLDEN_DB_NAME=rucaro_golden GOLDEN_DB_USER=rucaro GOLDEN_DB_PASS=rucaro` 環境変数が必要.

**結論**: 新ドメインは本番データ (Entity 1) に対して正しく動作することが確認できた. Entity 2 は本番DB側のデータ不整合の問題で、移行前に運用フィックスが必要.

### Sprint 10: アダプタ層
- **G-10-1**: サブエージェントが PHPStan を `src/` のみで実行し「no errors」と報告したが、テストファイルに 13 エラーが残っていた (assertIsArray の冗長 + reset() の `array|false` 取扱). 私が修正済. **教訓**: サブエージェント発注時は `phpstan analyse` を `src/` + `tests/` の両方で実行するよう明示すること.
- **G-10-2**: サブエージェントが namespace を `Tests\` で書いていた (composer.json の `App\Tests\` 規約と不整合). PHPUnit はパス discovery で動くため動作はするが規約違反. 私が `App\Tests\` に統一済.
- **G-10-3**: `DepreciationService::computeForAllAssets` の `accumulatedClosing` / `bookValueClosing` は **近似値** (前期末累計 + 当期償却). 完全に正確な値が必要なら `FixedAssetJournalGenerator::computeDepreciation` を public 化する必要あり.
- **G-10-4**: `BridgeContainer` は **static ファクトリ** で PDO を直接受け取る. 既存 UI が `global $classDb` 経由なので DI コンテナを使わずに済んだ. UI 繋ぎ替え時は `$classDb->getHandle()` を渡すだけ.

### Sprint 11+: 未着手
- (今後ここに追記)
