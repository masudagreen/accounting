# 消費税の期中変更対応性 — 調査と移行案

## 1. 背景: 2026年〜2027年で確定/可能性のある期中変更

### 1-1. インボイス制度の経過措置控除率変更（**ほぼ確定的に発生**）

仕入税額控除の経過措置は、令和8年度税制改正で **2年延長＋70%段階追加** された:

| 期間 | 控除率 |
|---|---:|
| 2023/10/01 〜 2026/09/30 | 80% |
| **2026/10/01 〜 2028/09/30** | **70%** |
| 2028/10/01 〜 2029/09/30 | 50% |
| 2029/10/01 〜 2030/09/30 | 50% |
| 2030/10/01 〜 2031/09/30 | 30% |
| 2031/10/01 〜 | 0% |

→ **2026/10/01に控除率が80% → 70%に変更**。3月決算/12月決算の事業体では期中変更が発生。

### 1-2. 食料品消費税ゼロ（**政策議論中・発生確率高め**）

- 高市内閣が「**食料品消費税2年間ゼロ**」を 2025年衆院選で公約として提示。  
- 国民会議で議論を進め、**2026年夏に中間まとめ**、**秋の国会で法案提出**の見通し。  
- 実施時期は早ければ **2026年度後半 (2026/10) または 2027年度開始 (2027/04)** が候補。  
- 2年限定の場合、終了時に再度期中変更が発生する可能性。

### 1-3. その他の進行中の論点
- 外食を「ゼロ」に含めるか（一物二価問題）
- 標準税率10% / 軽減税率8% / ゼロ税率0% の3区分体制になる可能性
- 簡易課税のみなし仕入率の見直し議論

---

## 2. 期中変更が会計システムに要求するもの

### 2-1. **取引日 (`stampBook`) ベースの税率自動判定**
- 現行: 利用者が `numRateConsumptionTax` を **手動選択**。
- 必要: 「2026/10/01以降は10%」「食料品は0%」など、**発効日付を持つ税率マスタ**から自動選択。

### 2-2. **税率マスタの履歴管理**
発効日 (`effectiveFrom`) / 失効日 (`effectiveUntil`) / 区分 (標準/軽減/ゼロ/旧) / 料率(数値) を持つ。

### 2-3. **税率別集計**
消費税申告書では税率別の課税売上/課税仕入を分けて集計。期中で税率切替がある場合も期全体で税率別に分かれた集計が必要。  
→ **元実装は既に `accountingFSValueJpn.jsonConsumptionTax` で税率別集計**を行っており、ここは適切。

### 2-4. **インボイス区分**
取引相手ごとに:
- 適格請求書発行事業者番号 (T+13桁)
- 適格 / 非適格（=免税事業者）
- 仕入時の経過措置控除率 (80/70/50/30/0%)

→ **現スキーマには無い**。新規追加必要。

### 2-5. **食料品ゼロ税率対応**
- 軽減税率8%(`'8_reduced'`) と区別する **ゼロ税率ラベル** を追加。
- 食料品判別フラグ（勘定科目単位 or 補助科目単位 or 取引明細単位のいずれか）。

---

## 3. 現スキーマで「対応可能なこと / 不可能なこと」

### ✅ 対応可能 (改修最小)

| 要件 | 現状 |
|---|---|
| 仕訳明細単位での税率切替 | `accountingLog.arrCommaRateConsumptionTaxDebit/Credit` で対応済 |
| 軽減税率と通常税率の区別 | `'8_reduced'` ラベルで区別済 |
| 期内に複数税率を含む集計 | `accountingFSValueJpn.jsonConsumptionTax` 税率別集計 |
| 旧税率(5%, 旧8%) 経過取引 | `numRateConsumptionTax` で任意の数値が入る |
| 期中の制度変更 | 取引日と税率を別保持なので、データ的には期内に複数税率が混在しても OK |

### ❌ 対応困難 (スキーマ追加が必要)

| 要件 | 不足 | 対応案 |
|---|---|---|
| 発効日付き税率マスタ | テーブル無し | `consumptionTaxRate` 新規 (effective_from / rate_label / rate_percent / kind) |
| 取引相手 (法人/個人) マスタ | テーブル無し | `accountingPartner` 新規 (id, name, qualified_invoice_number, effective_from/until 等) |
| 仕訳に「取引相手ID」紐付け | 列無し | `accountingLog.idPartner` 追加 |
| インボイス経過措置控除率 | 列無し | `accountingLog.numRateInvoiceTransition`（または取引相手＋日付から導出） |
| 食料品ゼロ税率 | ラベル無し | `'0_food'` ラベル追加（または品目分類連携） |
| 取引日に応じた税率の自動推奨 | ロジック無し | ドメイン層で `TaxRateResolver` 新規 |

### ⚠️ 不確実

| 要件 | 状況 |
|---|---|
| 食料品ゼロのスコープ判定（外食含むか等） | 政府方針未定。仕様確定後に対応 |
| 簡易課税のみなし仕入率変更 | 議論中。`flagConsumptionTaxBusinessType` と料率テーブルを分離すれば対応可 |

---

## 4. 推奨される新ドメイン設計（追加分）

```
src/Domain/ConsumptionTax/
├── TaxRate.php              ← 既存 (率と区分のみ)
├── TaxTreatment.php         ← 既存
├── TaxCalculator.php        ← 既存
├── TaxRateRegistry.php      ← 新規: 発効日付き税率マスタ
├── InvoiceTransitionRate.php ← 新規: 経過措置控除率 (80/70/50/30/0)
└── Partner/
    ├── Partner.php          ← 新規: 取引相手
    └── QualifiedInvoiceNumber.php ← 新規: T+13桁

src/Domain/Journal/
└── JournalLine.php          ← partnerId, invoiceTransitionRate を追加
```

### 4-1. TaxRateRegistry の API イメージ

```php
$registry = TaxRateRegistry::ofDefault();        // 標準セット
$rate = $registry->resolve(
    date: new DateTimeImmutable('2026-10-15'),
    kind: TaxRateKind::Standard,                 // 標準/軽減/ゼロ
);
// → TaxRate::standardTen()
```

### 4-2. インボイス経過措置の API イメージ

```php
$transition = InvoiceTransitionRate::resolve(
    date: new DateTimeImmutable('2026-10-15'),
    isPartnerQualified: false,
);
// → 70 (=70% 控除)

$deductibleTax = TaxCalculator::deductibleTax(
    paidTax: Money::ofYen(1000),
    transitionPercent: 70,
);
// → Money::ofYen(700)
```

### 4-3. 期中変更を扱うロジック

```
仕訳入力時:
  1. 取引日 stampBook を確定
  2. 取引相手 partnerId から適格/非適格を確定
  3. TaxRateRegistry::resolve(date, kind) で適用税率を決定
  4. TaxCalculator::computeTax(net, rate, treatment, mode) で税額計算
  5. 仕入の場合: InvoiceTransitionRate::resolve(date, partnerQualified) で控除率
  6. 税額 × 控除率 / 100 = 控除可能額

期末申告時:
  期内のすべての仕訳を税率ラベル別に集計
  食料品ゼロ税率 → 該当無し (申告書の「免税」枠)
  軽減税率8% → 軽減税率欄
  標準税率10% → 標準税率欄
  旧税率(8%/5%) → 経過措置欄
```

---

## 5. マイグレーションの段階

| 段階 | 対応 | リリース時期目安 |
|---|---|---|
| **Phase 0 (現状)** | 利用者手動で税率を選択 | 〜2026/09 |
| **Phase 1** | 発効日付き税率マスタ・自動推奨 | 2026/10前 |
| **Phase 2** | インボイス取引相手マスタ + 経過措置控除率 | 2026/10前 |
| **Phase 3** | 食料品ゼロ税率対応 | 法案成立後速やかに |
| **Phase 4** | 過去仕訳の税率自動補正（任意・確認画面付き） | Phase 1〜3完了後 |

---

## 6. 結論

- **期中の税率変更は、現行スキーマでも「データ上は」格納可能**。仕訳ごとに税率を別個に持つため。
- **しかし利用者が手動で正しい税率を選ぶ必要があり、ミスや漏れの温床**。発効日付き税率マスタの導入が望ましい。
- **インボイス経過措置控除率の段階変更（2026/10/01の80→70%）も、データ上は仕訳ごとに保持できる**。

## 7. 採用方針（2026-04-28 決定）

ターゲット層（個人事業主・小規模法人で簡易課税中心、月次の取引）では期中変更の影響が小さいため、**現行の「仕訳ごとの税率切替」方式をそのまま継続**する。

- **TaxRateRegistry / Partner / InvoiceTransitionRate の自動化機能は実装しない**。
- ただし新ドメイン `TaxRate` には食料品ゼロ用ラベル `'0_food'` 等の拡張余地を残しておく。
- 発効日付き税率マスタは **将来の希望的目標** として 05_test_strategy.md のロードマップ最後尾に置く。

この方針で問題が出たら（具体的には: 利用者が制度変更時に税率選択ミスする、申告書の税率別集計が合わない 等）、その時点で TaxRateRegistry 導入を再検討する。

---

## 出典

- [食料品消費税ゼロは2026年中に実施される？高市新内閣が掲げる物価高対策 - MONEYIZM](https://www.all-senmonka.jp/moneyizm/news/314055/)
- [高市首相､食料品消費税率｢2年間ゼロ｣の実現に意欲 - Bloomberg](https://www.bloomberg.com/jp/news/articles/2026-02-09/TA6K7WKJH6V400)
- [No.6498 適格請求書等保存方式（インボイス制度）｜国税庁](https://www.nta.go.jp/taxes/shiraberu/taxanswer/shohi/6498.htm)
- [インボイス制度について｜国税庁](https://www.nta.go.jp/taxes/shiraberu/zeimokubetsu/shohi/keigenzeiritsu/invoice_about.htm)
- [【2026年10月改正】仕入税額控除の経過措置が2年延長！80→70→50→30→0%の新スケジュール - 起業の「わからない」を「できる」に](https://sogyotecho.jp/inputtaxcredit-extension/)
- [インボイス制度に関するＱ＆Ａ目次一覧｜国税庁](https://www.nta.go.jp/taxes/shiraberu/zeimokubetsu/shohi/keigenzeiritsu/qa_invoice_mokuji.htm)
