# ADR-013: Cash Plan & Break-Even Point port (Phase 6 Wave 6-E)

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-005 (layered architecture), ADR-006 (ports and adapters),
  ADR-007 (strangler-fig migration), ADR-009 (FS port), ADR-010 (CS port),
  ADR-011 (ledger port), ADR-012 (fixed assets port)

## 1. Context

Wave 6-E closes out the "planning & analysis" slice of the legacy PHP
stack by porting two subsystems:

1. **資金繰り表 (Cash Plan)** under
   `back/class/else/plugin/accounting/jpn/CashPlan*.php`,
   `CashAnalyze*.php`, and the supporting `Cash*.php` / `CashPay.php`
   / `CashDefer.php` files. The legacy UI lets users enter monthly
   planned inflows and outflows (営業収入, 営業支出, 財務収入, 財務支出),
   copy from the prior period, and render a wide A4-landscape PDF per
   fiscal term.
2. **損益分岐点 (Break-Even Point, CVP)** under
   `BreakEvenPoint.php`, `BreakEvenPointAccountTitle.php`,
   `BreakEvenPointAccountTitleEditor.php`, `BreakEvenPointOutput.php`,
   and the arithmetic engine in `CalcBreakEvenPoint.php`. Legacy stores
   per-account 分類 in `accountingBreakEvenPoint*Jpn` with a
   `flagType` column (`sales`/`variable`/`fixed`) driven off the same
   FS mapping that powers 損益計算書.

Both features are "forward-looking": they do not alter a single journal
entry, but consume / project the same account-title catalog. Folding
them into one wave avoids duplicating the per-account bookkeeping that
both need (account categories, classification rows, fiscal-term bounds)
and lets the two UIs share a single template-plumbing story.

## 2. Decision

### 2.1 Scope

Port the minimum surface needed to run both features end-to-end:

1. Persist cash plans in `cash_plans` + `cash_plan_entries`.
2. Persist per-account CVP classifications in
   `account_title_cvp_classifications`.
3. Expose REST endpoints matching the legacy UI:
   - `GET/POST/PATCH/DELETE /api/v1/cash-plans` and
     `GET /api/v1/cash-plans/{id}?format=json|pdf`
   - `GET /api/v1/break-even-point?…&format=json|pdf`
   - `GET/PUT /api/v1/cvp-classifications`
4. Render both PDFs via dompdf + Smarty so they match the FixedAsset /
   Ledger / FS families (ADR-009/011/012).

Out of scope for this wave:

- CVP scenario modelling / what-if sliders (legacy "限界利益率 +5%" knobs).
- Full budget vs. actual visualisation (the 資金繰り実績比較 output is
  a follow-up wave).
- Break-even 2D / 3D charts — dompdf has no native chart support; the
  PDF report is a numeric summary only.
- Import from the legacy `accountingBreakEvenPoint*Jpn` dump.

### 2.2 CashPlan: 12-column row layout

Each `cash_plan_entries` row stores exactly 12 `DECIMAL(18, 4)` columns
`month_1 … month_12`. We considered a normalised layout
(`(plan_id, month_number, amount)` with 12 rows per entry) but
rejected it:

| Aspect | 12-column row (chosen) | Normalised (month_number) |
|--------|------------------------|---------------------------|
| Query shape | 1 row per entry → natural mapping to the UI table | 12 rows per entry → every read must GROUP BY |
| Write shape | 1 INSERT per entry | 12 INSERTs per entry |
| Reporting SQL | No aggregation needed | Requires SUM(month_1)… PIVOT |
| Index bloat | 1 PK + 1 sort key | PK (plan_id, entry_id, month) — 12× row count |
| Future flexibility | Hardcoded 12 | Can do 13/15 months trivially |

Since Japanese fiscal terms are always exactly 12 months by design (any
shorter term closes the books and opens a new one), the hardcoded 12
columns are a deliberate simplification. A 13-month scenario would
require a migration anyway.

### 2.3 BEP: CVP classification model

Each entity ships a per-account classification row with three fields:

- `cost_type` ∈ {`variable`, `fixed`, `semi_variable`}
- `variable_ratio` ∈ [0.0000, 1.0000] — the fraction of the balance
  that is treated as variable cost. Canonicalised so that:
  - `Variable` forces ratio = 1.0000
  - `Fixed` forces ratio = 0.0000
  - `SemiVariable` preserves the caller-supplied ratio

The "canonicalise on write" rule means the calculator never has to
branch on `cost_type` when it partitions a balance into variable /
fixed halves. Given an expense row with balance `A`:

```
variablePart = A × variable_ratio
fixedPart    = A - variablePart
```

For `Fixed`, `variable_ratio = 0` → `variablePart = 0`, all fixed.
For `Variable`, `variable_ratio = 1` → `fixedPart = 0`, all variable.
For `SemiVariable`, both parts are non-zero.

### 2.4 Revenue is not classified

Revenue rows (`account_category = 'revenue'`) are **not** represented
in `account_title_cvp_classifications`. The calculator detects them
from the trial-balance row category directly. This matches the legacy
`flagType = 'sales'` path: the data model already carries the revenue
vs. expense distinction, so classifying revenue as "variable" would
double-book the signal.

### 2.5 Unclassified expenses fall back to fixed

If an expense account has no row in
`account_title_cvp_classifications`, the calculator treats it as
**fully fixed**. This is conservative: under-reporting `variable` is
preferable to under-reporting `fixed` when estimating BEP, because
the BEP sales figure would be artificially low otherwise (the
classic mistake of the "all variable" approximation).

### 2.6 Arithmetic

All amounts and ratios are stored as scale-4 decimal strings through
`Rucaro\Support\Decimal\Decimal`. The BEP calculator uses `bc*`
functions when `ext-bcmath` is present and falls back to 64-bit
fixed-point otherwise — same rule as every other decimal-sensitive
domain in the codebase. Ratios like `contributionMarginRate` and
`safetyMarginRatio` are **decimal fractions**, not percentages
(`0.8750` means 87.5%). The PDF template formats them as percentages
at render time only.

Division-by-zero guards:

- `sales == 0` → every ratio is `0.0000`.
- `contributionMarginRate == 0` → `bepSales = 0.0000`.

### 2.7 Legacy ↔ new mapping

| Legacy | New |
|--------|-----|
| `CashPlan.php`, `CashPlanOutput.php` | `src/Domain/CashPlan/CashPlan.php` + `src/Http/Controller/CashPlan/*` + `storage/templates/cash_plan/*` |
| `Cash.php`, `CashDefer.php`, `CashPay.php`, `CashPreference.php`, `CashSearch.php` | Subsumed into `CashPlanRepositoryInterface::save/findById/findByEntity`; the legacy preference rows that survived are represented as `cash_plans.notes` |
| `CalcBreakEvenPoint.php::_setVarsValue` (the flagType/sales/variable/fixed switch) | `BreakEvenPointCalculator::calculate()` |
| `CalcBreakEvenPoint.php::_getVarsDetailValue` (numSafeRate, numPoint math) | `BreakEvenPointCalculator::divideOrZero()` + analysis fields |
| `BreakEvenPointAccountTitle.php` (flagType CRUD) | `AccountTitleCvpClassification` + `PdoAccountTitleCvpClassificationRepository` |
| `BreakEvenPointOutput.php` (PDF) | `DompdfBreakEvenPointGenerator` + `storage/templates/break_even_point/*` |
| `CashAnalyze.php` / `CashAnalyzeOutput.php` | Not ported this wave (actual vs. plan — follow-up). |

## 3. Consequences

### Positive

- Both planning features share a single migration wave and a single
  ADR.
- CVP classification storage mirrors the FS/CS mapping shape so future
  UIs can reuse the bulk-upsert pattern via `PUT /api/v1/cvp-classifications`.
- The 12-column layout keeps cash-plan reads to one row per entry,
  which matches the A4-landscape view 1:1.
- Unclassified expenses fail closed as fixed cost, so stale seed data
  never artificially improves the BEP headline.

### Negative

- `cash_plan_entries` is not month-agnostic; expanding to 13/15-month
  terms needs a migration.
- The BEP PDF carries no chart, only a numeric summary. Real-world
  users who want the classic 売上・変動費・固定費 line chart will need
  to wait for a later wave (probably using Chart.js on the client
  side rather than a server-side SVG pipeline).
- `GenerateCashPlanFromBudgetUseCase` divides the prior period's
  totals evenly across 12 months. Legacy behaviour was to run through
  the monthly snapshot, which gave a more realistic seasonal curve.
  This is the "quick start" tradeoff: users re-edit the 12 monthly
  values once they have a baseline.
- The CVP classification covers expenses only; revenue is derived.
  Users cannot explicitly classify a sales account as "semi-variable
  revenue" (e.g. volume discount models). If that becomes a
  requirement, the schema gains a column instead of a new table.

### Migration / operational impact

- Two new migrations apply cleanly on top of the existing 0000-0011
  chain: `0012_cash_plans.sql` + `0013_cvp_classifications.sql`.
- Both ship with `.down.sql` companion files (DROP TABLE cascades).
- No changes to existing tables, columns, or seed data — this wave is
  strictly additive.

### Testing

- **Unit**: `BreakEvenPointCalculatorTest` carries the spec's golden
  fixture (sales=1,600,000 / variable=200,000 / fixed=17,000 →
  限界利益率 0.8750, BEP売上 19,428.5714, 安全余裕率 0.9878,
  営業利益 1,383,000). It also covers `sales == 0` fallbacks,
  `SemiVariable` splits, and unclassified-expense-as-fixed.
- **Unit**: `CashPlanTest` covers monthly delta / closing balance
  running math, total-by-category aggregation, and name / currency
  invariants.
- **Unit**: `CreateCashPlanUseCaseTest` + `UpdateCashPlanUseCaseTest`
  assert uniqueness per `(entity, fiscal term, name)` and atomic
  entry replacement on update.
- **Integration**: `PdoCashPlanRepositoryTest` round-trips header +
  12-month entries; `PdoCvpClassificationRepositoryTest` round-trips
  the bulk upsert. Both skip cleanly when
  `RUCARO_TEST_DB_*` env vars are absent.
- **E2E**: `bin/smoke-cash-plan-and-bep.php` seeds a demo plan and
  CVP classifications against the running docker-compose stack and
  writes `cash-plan.pdf` + `break-even-point.pdf` under
  `storage/out/`.

## 4. Alternatives considered

### 4.1 Keep CashPlan and BEP in separate waves

Would have doubled the migration count (one per table set) and
required two ADRs. Rejected because both features share the same
legacy access paths (`_getVarsFS`, `_getVarsFSValue`, the fiscal-term
scoping logic) and both need `account_titles` + fiscal-term tables to
already exist. Folding them here keeps the blast radius small.

### 4.2 Normalised `cash_plan_entries(month_number, amount)`

See §2.2. The wide row wins for this domain.

### 4.3 Drop `semi_variable` from CVP

Legacy code only has `sales / variable / fixed`. Adding
`semi_variable` adds one enum value and one `variable_ratio` column
but makes the model honest about real-world 水道光熱費-style accounts
that are neither fully variable nor fully fixed. The cost is tiny
(one CHECK constraint and one DECIMAL column). Rejected the
minimalist option.

### 4.4 Store derived totals in `cash_plans`

Would have added `total_operating_in`, `total_operating_out`, etc. to
`cash_plans` directly. Rejected in favour of deriving at read time
via `CashPlan::totalsByCategory()` — keeps the DB honest, avoids
stale-total bugs, and keeps the aggregate strictly dependent on its
entries.
