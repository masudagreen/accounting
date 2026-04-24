# ADR-015: Budget port (Phase 6 Wave 6-G)

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-005 (layered architecture), ADR-006 (ports and adapters),
  ADR-007 (strangler-fig migration), ADR-009 (FS port), ADR-011 (ledger
  port), ADR-013 (cash plan and BEP port), ADR-014 (consumption-tax
  port)

## 1. Context

Wave 6-G completes the "planning" half of the legacy PHP stack by
porting the 予算管理 (budget) subsystem:

- `back/class/else/plugin/accounting/jpn/Budget.php` — the top-level
  plugin entry, navigation, and form plumbing.
- `back/class/else/plugin/accounting/jpn/BudgetEditor.php` — POST /
  save pipeline writing into `accountingBudgetJpn` / the per-month
  columns in `accountingBudgetItemJpn`.
- `back/class/else/plugin/accounting/jpn/BudgetOutput.php` — the 予実
  対比 rendering path (budget vs actual vs 差異 vs 消化率).

The legacy schema stored one row per (budget × account × month). In
production we saw two concrete problems with that shape:

1. Month-level rows fragment a single logical "budget line" across 12
   SELECTs, multiplying chart-of-accounts joins by 12 per variance
   report. Cash Plan already ate this lesson; ADR-013 moved to a
   12-column-wide row.
2. The legacy output embeds aggregation into PHP template code, making
   the "消化率 when budget is zero" rule impossible to reason about
   without running the whole PHP request (BudgetOutput divides by zero
   silently and renders `inf%`).

## 2. Decision

### 2.1 Scope

Port the minimum surface needed to run budget planning, approval, and
variance review end-to-end:

1. Persist budgets in `budgets` (header) + `budget_line_items` (12
   monthly columns per account × sub-account tuple).
2. Expose REST endpoints matching the legacy UI verbs:
   - `GET / POST / PATCH / DELETE /api/v1/budgets`
   - `GET /api/v1/budgets/{id}?format=json|pdf`
   - `POST /api/v1/budgets/{id}/approve`
   - `POST /api/v1/budgets/{id}/lock`
   - `GET /api/v1/budgets/{id}/variance-analysis?asOf=&format=json|pdf`
3. Render 予算書 and 予実対比表 PDFs via dompdf + Smarty, sharing the
   chroot / IPAex-font plumbing used by ADR-009 / ADR-011 / ADR-013.
4. Derive actuals by composing over `QueryTrialBalanceUseCase`
   (ADR-013 already uses the same trick for BEP); no direct journal
   SQL leaks into Wave 6-G.

Out of scope for this wave:

- Scenario modelling ("what-if" copies of approved budgets with deltas).
- Department / sub-entity split budgets — legacy supports a "部門"
  column that the port ignores because it was never reached from the
  REST surface.
- Budget CSV / XLSX export (PDF + JSON only).

### 2.2 Aggregate shape

`Budget` is the aggregate root. It holds:

- the header identity (`id`, `entityId`, `fiscalTermId`, `name`);
- lifecycle state (`status`, `approvedBy`, `approvedAt`);
- the line-item list (`BudgetLineItem[]`);
- provenance (`createdBy`, `createdAt`, `updatedAt`, `deletedAt`).

`BudgetLineItem` is a readonly value object carrying:

- the account coordinate (`accountTitleId`, optional `subAccountTitleId`);
- 12 scale-4 decimal strings indexed 0..11 for fiscal-term months 1..12;
- `memo` and `sortOrder`.

Monthly and annual totals are always derived (`Budget::monthlyTotal`,
`BudgetLineItem::totalAmount`). We never store them, so drift between
"sum of cells" and "displayed total" is impossible.

### 2.3 Why 12 wide columns, not 12 rows

We reuse the ADR-013 Cash Plan decision intentionally:

- A variance report needs `SUM(budget_jan..budget_dec) WHERE
  account_title_id = :a`. 12 columns → one row; 12 rows → 12 rows to
  aggregate. On MariaDB 10 with the (budget_id, account_title_id, sub)
  unique key, the 12-column shape lets us return a full budget in a
  single `SELECT *` plus one ORDER BY.
- Legacy's month-column layout was fine for a planning UI that edited
  one cell at a time. The new UI (out of scope for this wave but
  surfaced through `PATCH /api/v1/budgets/{id}`) replaces the whole
  row at once, so there is no write-amplification argument for 12
  narrow rows.
- PhPStan and tests stay cheaper: one `monthlyAmounts: list<string>`
  invariant is easier than 12 per-field validators.

### 2.4 Lifecycle

State machine is strictly one-way:

```
Draft  ──approve──▶ Approved ──lock──▶ Locked
```

Guards live inside the `Budget` aggregate (`approve()`, `lock()`, the
`withHeader()` / `withLineItems()` editable assertion) rather than in
the UseCases. The UseCases stay mechanical; the domain owns the state
machine so nothing outside the aggregate can sneak a Locked budget back
into Draft.

- `Draft` is the only editable state. `UpdateBudgetUseCase` touches
  header and line-items via the aggregate, which raises
  `InvariantViolationException` if the status isn't Draft.
- `Approved` freezes header + line items, but still allows variance
  reports. Unlocking or re-editing requires a brand-new budget — we
  deliberately do not add a "reject back to Draft" transition because
  the legacy system had no such operation and the audit trail benefits
  from a Draft → Approved fan-out.
- `Locked` is terminal. Intended to be set once the fiscal term closes
  and the variance has been signed off.

### 2.5 Delete policy

Only `Draft` budgets can be soft-deleted (`deleted_at = NOW()`). An
attempt to delete an Approved or Locked budget raises
`InvariantViolationException` so finance teams cannot silently erase
history. Delete on an already-deleted or unknown ID is a no-op — the
HTTP layer surfaces a 200 so retries from flaky mobile clients don't
flap between 200 and 404.

### 2.6 Variance analysis

`AnalyzeBudgetVarianceUseCase` is the new read model. It:

1. Loads the budget. Computes `monthsElapsed = months between
   fiscalTermStartDate and asOf (clamped to 1..12)`.
2. For each line item, sums `cumulativeAmount(monthsElapsed)` into
   `budgetByAccount` so multiple line items on the same account (e.g.
   two sub-accounts) collapse into one variance row.
3. Calls `QueryTrialBalanceUseCase` with `(entityId, fiscalTermId,
   fiscalTermStartDate, asOf)`. We use the existing UseCase rather
   than a direct `SELECT SUM() FROM journal_entry_lines ...` because:
   - The variance view inherits the snapshot + tail caching the
     TrialBalance UseCase already provides.
   - The "natural side balance" computation is non-trivial (revenue
     shows up on credit side) and we do NOT want to duplicate that
     logic. We let `TrialBalanceRow::compute()` do it once.
4. Produces one `BudgetVarianceRow` per account:
   - `varianceAmount = actual - budget` (positive = over budget).
   - `usageRatePercent = actual / budget * 100` to two decimals, or
     `null` when budget is zero.

Rejected alternative: "query journal lines directly and bucket by
account". Rejected because (a) it would rebuild the natural-side logic
and (b) it would bypass the snapshot cache we just added in Phase 5.

### 2.7 `usageRatePercent` null contract

Zero-budget actuals are a legitimate case (an unplanned expense hitting
an account with no budget line). Legacy rendered `inf%`, which upstream
operators flagged as a user-reported bug. The new type is `?string`:

- string ("112.50") when budget > 0;
- null when budget == 0 regardless of actual;
- HTTP / PDF layers render "N/A" when null.

## 3. Mapping to the legacy code

| Legacy PHP                                          | New ports                                               |
|-----------------------------------------------------|---------------------------------------------------------|
| `Jpn_Budget::run()` navigation                      | `ApiKernel` route table                                 |
| `Jpn_BudgetEditor::_save()` + raw SQL               | `CreateBudgetUseCase` / `UpdateBudgetUseCase` via repo  |
| `Jpn_BudgetEditor` status flag                      | `BudgetStatus` enum + aggregate invariants              |
| `Jpn_BudgetOutput::_render()` template muxer        | `DompdfBudgetGenerator` / `DompdfBudgetVarianceGenerator` |
| `accountingBudgetJpn` row-per-month storage         | `budgets` + `budget_line_items` (12 wide columns)       |
| `Jpn_BudgetOutput::_calcRatio()` 消化率             | `BudgetVarianceRow::safeUsage()` with null on zero       |
| `Jpn_Budget::_fetchActual()` raw SQL SUM            | `QueryTrialBalanceUseCase` composition                  |

## 4. Migration

`scripts/migrate/0015_budgets.sql`:

- `budgets(id, entity_id, fiscal_term_id, name, status, approved_by,
  approved_at, notes, created_by, created_at, updated_at, deleted_at)`
- `budget_line_items(id, budget_id, account_title_id,
  sub_account_title_id, sort_order, month_1..month_12, memo)`
- UNIQUE `(entity_id, fiscal_term_id, name)` to mirror the Cash Plan
  constraint — Phase 5 operators were caught out by duplicate-name
  budgets and used the workaround of appending `" (copy)"`.
- CHECK `status IN ('draft','approved','locked')` plus an FK to
  `users(id)` for `approved_by` so a deleted user cannot orphan the
  approval audit trail.

Rollback (`0015_budgets.down.sql`) drops in reverse dependency order.

## 5. Testing strategy

- Unit (ports):
  - `BudgetTest` covers the state machine — approve from Draft, lock
    from Approved, both reject any other source state.
  - `BudgetLineItemTest` locks down the 12-months invariant and memo
    length.
  - `BudgetVarianceRowTest` covers over / under / zero-budget paths
    including the `usageRatePercent = null` rule.
- Unit (UseCases):
  - Create, Update, Delete, Approve, Lock, AnalyzeVariance — each with
    an in-memory repository (`InMemoryBudgetRepository`) and the
    shared `InMemoryTrialBalanceQuery` + `FrozenClock`.
  - Update-on-Approved and Lock-before-Approve both assert
    `InvariantViolationException`.
- Integration: `PdoBudgetRepository` round-trips a 3-line budget
  against migration 0015 and verifies soft-delete hides the row.
- E2E: a synthetic "demo" budget is created, approved, then the budget
  PDF and the variance PDF are rendered to the operator desktop.

## 6. Open questions

- Should `Locked` budgets still appear in the list endpoint by default?
  Yes — the UI needs them for variance review. A `?status=draft` filter
  is available for plan editors.
- Should we allow editing notes on an Approved budget? Out of scope;
  legacy didn't. Reopen once users ask.
