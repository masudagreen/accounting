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

### Sprint 7+: 未着手
- (今後ここに追記)
