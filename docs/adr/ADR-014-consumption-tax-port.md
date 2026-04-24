# ADR-014: Consumption Tax port (Phase 6 Wave 6-F)

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-005 (layered architecture), ADR-006 (ports and adapters),
  ADR-007 (strangler-fig migration), ADR-009 (FS port), ADR-010 (CS port),
  ADR-011 (ledger port), ADR-012 (fixed assets port),
  ADR-013 (cash plan / BEP port)

## 1. Context

Wave 6-F ports Japan's consumption tax (消費税) subsystem. The legacy
PHP implementation lives at:

- `back/class/else/plugin/accounting/jpn/ConsumptionTax.php`
- `back/class/else/plugin/accounting/jpn/ConsumptionTaxList.php`
- `back/class/else/plugin/accounting/jpn/ConsumptionTaxSheet.php`
- `back/class/else/plugin/accounting/jpn/CalcConsumptionTax.php`
- `back/class/else/plugin/accounting/jpn/CalcLogConsumptionTax.php`
- legacy tables: `accountingConsumptionTaxJpn`, the
  `jsonConsumptionTax` / `jsonConsumptionTaxDetail` columns on
  `accountingFSValueJpn`, and the per-line
  `flagRateConsumptionTaxReduced` column on
  `accountingLogCalcJpn`.
- `back/dat/version/Batch14800.php` — the 2019-10-01 patch that added
  軽減税率 support on top of the pre-existing 5% / 8% flow.

The legacy design serialises everything into opaque JSON columns
(`jsonConsumptionTax*`) and decides calculation branches with
stringly-typed flags (`flagConsumptionTaxGeneralRule`,
`flagConsumptionTaxDeducted`, `flagConsumptionTaxIncluding`). That
makes PHP's `CalcConsumptionTax::_getValueAddLoop` 250+ lines of
deeply-nested `if / preg_match` that are hard to reason about and
impossible to unit-test without standing up an entire legacy entity
snapshot.

For the Rucaro port we needed:

1. A first-class master of tax rates with施行日付 so past journals
   (3%, 5%, 旧 8%, 標準 10%, 軽減 8%) can be classified deterministically.
2. A first-class master of 取引区分 (課税売上 / 非課税 / 免税 / 不課税 /
   課税仕入 / 非登録事業者からの仕入) so calculators don't branch on
   free-form strings.
3. An aggregate for a 課税期間 (tax period) that carries the chosen
   calculation method (原則 / 簡易 / 2割特例) and, when simplified,
   the 事業区分.
4. An invoice-registration table for 取引先 so the インボイス制度
   経過措置 (80% / 50% / 0%) can be applied per transaction date.
5. A pure settlement calculator that takes a period + classified
   journal lines and returns a {@see ConsumptionTaxSettlement}
   aggregate, unit-testable in isolation.

The legacy `journal_entry_lines.tax_rate_percent`,
`journal_entry_lines.tax_amount`, and `journal_entry_lines.is_tax_reduced`
columns (migration 0003) already carry the per-line tax info we need;
Wave 6-F does not touch them.

## 2. Decision

### 2.1 Scope

Port the minimum surface needed to produce a 消費税申告書イメージ from
a year's worth of posted journals:

1. Five new tables (migration 0014):
   - `consumption_tax_rates`        — rate master with
     `effective_from/until`, `is_taxable`, `is_reduced` flags.
   - `consumption_tax_categories`   — 取引区分 master with
     `side` (sales/purchase) and `deductible` flag.
   - `account_title_consumption_tax_defaults` — per-entity default
     mapping `account_title → (category, rate)` used as the initial
     value when journaling and the join key when aggregating.
   - `consumption_tax_invoice_registrations` — counter-party invoice
     registration records (`T\d{13}` numbers + validity window).
   - `consumption_tax_periods`      — taxable periods with calculation
     method and 事業区分.
2. Three calculator classes implementing
   `ConsumptionTaxCalculatorInterface`:
   - `PrincipleConsumptionTaxCalculator`  — output tax − deductible
     input tax (with インボイス経過措置 adjustment on
     `taxable_purchase_non_registered` lines).
   - `SimplifiedConsumptionTaxCalculator` — output tax × (1 − みなし仕入率);
     みなし仕入率 driven off a `SimplifiedBusinessCategory` enum with
     values matching National Tax Agency guidance
     (卸 90% / 小売 80% / 製造等 70% / その他 60% /
      サービス 50% / 不動産 40%).
   - `TwoPercentConsumptionTaxCalculator` — output tax × 20%
     (インボイス登録した元-免税事業者救済).
3. A `ConsumptionTaxSettlement` readonly aggregate that carries every
   number the 消費税申告書 needs: sales-by-rate, output-tax-by-rate,
   purchases-by-rate, input-tax-by-rate, total-sales, 課税売上割合,
   national/local split (国税 78% + 地方 22% for 10%, 78% + 22% for
   軽減 8%, 63/80 + 17/80 for 旧 8%, 4/5 + 1/5 for 旧 5%).
4. REST endpoints:
   - `GET /api/v1/consumption-tax/rates`
   - `GET /api/v1/consumption-tax/categories`
   - `GET/PUT /api/v1/consumption-tax/account-title-defaults`
   - `GET/POST/PATCH /api/v1/consumption-tax/invoice-registrations[/{id}]`
   - `GET/POST /api/v1/consumption-tax/periods`
   - `POST /api/v1/consumption-tax/periods/{id}/calculate`
   - `GET  /api/v1/consumption-tax/periods/{id}/report?format=json|pdf`
5. A dompdf + Smarty `DompdfConsumptionTaxReportGenerator` rendering
   消費税申告書イメージ at A4 portrait.

### 2.2 Non-goals

- Fractional-period filings (四半期 / 月次中間申告): we carry
  `is_interim` on the period row and will surface per-period interim
  payments in a follow-up wave.
- 旧税率 5% / 3% are persisted for historical compatibility but the
  申告書 template does not render them unless they appear in the
  computed breakdown.
- 課税売上割合 that drops below 95% triggers 個別対応 / 一括比例配分
  in the real 申告書; Rucaro flags the ratio but leaves the
  per-account allocation knobs to a future wave.

## 3. Design

### 3.1 Layered placement (ADR-005 / ADR-006)

```
Http / Controller / ConsumptionTax    // JSON/PDF surface
  ↓
Application / ConsumptionTax          // UseCases
  ↓
Domain / ConsumptionTax               // Rate + Category + Period +
                                      // Settlement + Calculator ports
  ↓
Domain / ConsumptionTax / Service     // Three calculator implementations
                                      // + InvoiceDeductionCalculator
  ↑
Infrastructure / ConsumptionTax       // PDO repos + Smarty/dompdf PDF
                                      //  + PDO TaxableTransactionQuery
```

The calculators are pure: they take a period + list of
`TaxableTransaction` value objects and return a `ConsumptionTaxSettlement`.
Tests assemble the transaction list directly; integration supplies
`PdoTaxableTransactionQueryService` which aggregates from
`journal_entry_lines` using the per-account mapping.

### 3.2 Rate resolution

The old `CalcConsumptionTax::_getValueAddLoop` hard-coded the rate list
`[5, 8, 10]`. We replace that with a master table keyed on a short
code (`standard_10`, `reduced_8`, `old_8`, `old_5`, `old_3`, `exempt`,
`non_taxable`, `untaxed`). Resolution happens either by code or by
`effective_from/until` + date. The `rateCodeOf()` helper on
`PrincipleConsumptionTaxCalculator` turns `(tax_rate_percent,
is_tax_reduced)` pairs straight off a journal line into the right
rate code — the same pair the journal UI already writes at entry
time.

### 3.3 Invoice transition measure

`InvoiceDeductionCalculator` encapsulates the three-stage schedule
(100% → 80% → 50% → 0%) with boundary dates embedded as constants.
Non-registered purchases carry the full tax amount in
`journal_entry_lines.tax_amount` (same as registered purchases) and
are filtered into the transitional-measure bucket by the
`taxable_purchase_non_registered` category. The deductible portion
flows into `inputTaxByRate`; the disallowed portion surfaces on
`ConsumptionTaxSettlement::adjustmentForNonRegistered` so the
申告書 can show it as a separate line.

### 3.4 PDF layout

The 申告書 template (`storage/templates/consumption_tax/settlement.html.tpl`)
is organised to mirror the real NTA form:

- §I — 売上区分別内訳 (rows per rate code)
- §II — 仕入区分別内訳 (rows per rate code)
- §III — 売上内訳 & 課税売上割合
- §IV — 納付税額 (output, input, non-registered adjustment,
  net payable, national/local split)

Rendering reuses the same IPAex Gothic + dompdf chroot pattern
already in place for FS / ledger / cash-plan / fixed-asset PDFs.

## 4. Legacy → new mapping

| Legacy (JpnPlugin)                                                | New (`Rucaro\Domain\ConsumptionTax`)              |
|-------------------------------------------------------------------|----------------------------------------------------|
| `accountingConsumptionTaxJpn` rate table                          | `consumption_tax_rates`                            |
| `accountingFSValueJpn.jsonConsumptionTax` / `*Detail`              | `ConsumptionTaxSettlement` aggregate (computed)    |
| `flagConsumptionTaxGeneralRule/Deducted/Including`                | `ConsumptionTaxCalculationMethod` enum             |
| 簡易課税 事業区分 hard-coded in `CalcConsumptionTax`                | `SimplifiedBusinessCategory` enum                  |
| `flagRateConsumptionTaxReduced` on accountingLogCalcJpn            | `journal_entry_lines.is_tax_reduced` (already)     |
| `numRateConsumptionTax` + `_getReceiveValueConsumptionTaxReduced`  | `journal_entry_lines.tax_rate_percent` + `rateCodeOf()` |
| ConsumptionTaxList / ConsumptionTaxSheet UI screens                | `ListConsumptionTaxRates/Categories/Periods*`      |
| インボイス登録事業者                                              | `consumption_tax_invoice_registrations`            |
| 経過措置 80% / 50% (added post-2023 by Jpn patches)                | `Service\InvoiceDeductionCalculator`               |
| 勘定科目ごとの既定区分 in `accountingAccountTitleJpn.jsonConsumptionTax` | `account_title_consumption_tax_defaults` table |
| `ConsumptionTaxSheet` PDF                                         | `DompdfConsumptionTaxReportGenerator` + Smarty tpl |
| `CalcConsumptionTax::_getValueAddLoop`                            | Three pure calculator classes + `InvoiceDeductionCalculator` |

## 5. Consequences

### 5.1 Positive

- Pure calculator classes: easy to unit-test (seven scenarios, zero
  database).
- Rate master table: new rates (e.g. a future 軽減 10% or 5%
  time-limited emergency rate) can be added without code changes.
- Per-account default mapping: unmaps the implicit legacy behaviour
  where the account title name itself was used to guess the 区分.
- Invoice transition measure: a single calculator that any calculator
  can call, so the 原則 path gets the adjustment for free and the
  申告書 has a dedicated line for the disallowed portion.
- PHPStan level 6 green across the full add-on.
- Settlement aggregate is immutable — the PDF generator and the JSON
  serializer both derive the same numbers, no possibility of drift.

### 5.2 Negative

- Five new tables (`consumption_tax_rates`, `_categories`,
  `account_title_consumption_tax_defaults`, `_invoice_registrations`,
  `_periods`) add migration surface.
- Existing journal lines that already have `tax_rate_percent` /
  `tax_amount` set by the legacy UI will produce correct numbers
  only if their paired `account_title_id` has a row in
  `account_title_consumption_tax_defaults`. The UI must gate new
  account titles behind a mapping step (the `PUT` endpoint makes
  that cheap).
- The aggregation query joins `journal_entry_lines` with
  `account_title_consumption_tax_defaults` via `(entity_id,
  account_title_id)`. Accounts without a mapping are silently
  skipped; callers need to surface "unmapped" warnings separately.
  This is deliberate — we do not want an unmapped account to fail
  the whole 申告書 calculation.

### 5.3 Performance

The per-period aggregation is an indexed join on
`(journal_entries.entity_id, journal_entries.journal_date)` +
`journal_entry_lines.entry_id` + `(entity_id, account_title_id)` on
`account_title_consumption_tax_defaults`. For the target
10 万明細 / 1 年 period the query stays well under 2 s on
MariaDB 10.11 with `innodb_buffer_pool_size ≥ 256 MB`.

### 5.4 Security

Read-only endpoints follow the existing `AuthenticateBearer` pattern.
Mutations (`PUT account-title-defaults`, `POST/PATCH
invoice-registrations`, `POST periods`, `POST periods/{id}/calculate`)
require the same token. No user-supplied SQL reaches the database —
all binds are parametrised via PDO prepared statements.

### 5.5 Migration & rollback

- Up: `scripts/migrate/0014_consumption_tax.sql` creates the five
  tables; `0014_consumption_tax_seed.sql` seeds the rate / category
  masters with the canonical rows (3%, 5%, 旧 8%, 標準 10%, 軽減 8%,
  exempt, non-taxable, untaxed; plus nine categories split across
  sales / purchase). Because the project-wide `MigrationRunner` keys
  migrations on version prefix alone, integration tests apply both
  files explicitly (same pattern as 0008 / 0009 / 0011).
- Down: `0014_consumption_tax.down.sql` drops the tables in
  reverse-FK order.
- Rollback implications: none — the tables are additive and no
  existing journal data is rewritten.

## 6. Testing strategy

- **Unit** — 56 tests, 132 assertions. Calculator branches (原則 /
  簡易 / 2 割特例), transition measure thresholds (2023-10-01 →
  2026-10-01 → 2029-10-01), mixed-rate scenarios, invalid-period
  guard rails, rate-code resolution, national/local split ratios,
  validation exceptions.
- **Integration** — PDO repositories round-trip (covered via the
  same `MigrationRunner` pattern used for cash plans).
- **E2E** — `ConsumptionTaxReportSmokeTest` wires the UseCase +
  dompdf generator and asserts the 申告書 PDF renders. The
  `RUCARO_E2E_PDF_OUT` env var lets the operator dump the PDF to
  the desktop for eyeballing.

## 7. Open questions

- Quarterly / monthly 中間申告 workflow: the `is_interim` flag on
  `consumption_tax_periods` is reserved for this but not yet
  implemented in the UI.
- 95% rule / 個別対応 方式 / 一括比例配分 方式: the current calculator
  simply reports `taxableSalesRatio`; it does not yet split deductible
  input tax by 課税対応 / 非課税対応 / 共通対応. This is the next
  natural extension and is planned for Wave 6-G (consumption-tax
  refinement) if it matters enough to ship.
- `2割特例` eligibility: the calculator does not enforce the
  2023-10-01 〜 2026-09-30 filing window; it is up to the
  application layer to refuse to create a `two_percent` period
  after the sunset.
