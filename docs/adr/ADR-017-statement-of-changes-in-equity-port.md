# ADR-017: Statement of Changes in Equity port (Phase 6 Wave 6-H-2)

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-005 (layered architecture), ADR-006 (ports and
  adapters), ADR-007 (strangler-fig migration), ADR-009 (FS port),
  ADR-015 (budget port), ADR-016 (blue-return port, parallel wave)

## 1. Context

The legacy PHP plugin ships a 株主資本等変動計算書 (Statement of
Changes in Equity, shortened "SS" in the codebase) view under:

- `back/class/else/plugin/accounting/jpn/FinancialStatementSS.php` —
  the form + navigation entry, responsible for laying out the
  natively-rendered `accountingBalanceJpn` data under the six equity
  columns.
- `back/class/else/plugin/accounting/jpn/FinancialStatementSSOutput.php`
  — the PDF / CSV rendering extension, stamped out from the same
  `_getVarsLoop*` recursion that the BS / PL outputs share.

What the legacy implementation actually gives us — distilled from
the ~1.2 kloc of recursion — is a table with:

- six equity columns: 資本金 / 資本剰余金 / 利益剰余金 / 自己株式 /
  評価換算差額等 / 新株予約権, plus a derived 合計 column;
- three fixed "anchor" rows: 期首残高, 当期変動額合計, 期末残高;
- a variable set of intermediate 変動 rows (剰余金の配当, 新株の発行,
  自己株式の取得・処分, 当期純利益, その他).

The **intermediate rows are where the legacy code falls over**. Two
subtle behaviours bleed through the inheritance chain:

1. `_checkUseLog` scans a JGAAP-specific `accountingLogCalc*` log
   table that no longer exists in the new ports-and-adapters schema.
   In practice every installation we audited either had that table
   empty or used it inconsistently, so the legacy view silently
   rendered `期首 === 期末` even when equity had moved.
2. The `unappropriatedRetainedEarnings` branch folds `P&L
   currentTermProfitOrLossNet` into the retained-earnings column
   *implicitly*. The reviewer cannot tell from the output whether a
   value is machine-derived or hand-entered, which hurts audit
   confidence.

Wave 6-H-2 ports the SS report while explicitly fixing both pain
points:

- the change-row set is driven by a small, typed table
  (`ss_manual_adjustments`) instead of a free-form log scan;
- net income is folded into 利益剰余金 as an explicit `SsChange`
  tagged `source = journal_auto`, so the reviewer sees exactly which
  rows are derived vs entered.

## 2. Decision

### 2.1 Scope

Port enough of the SS report to replace the legacy view end-to-end
for the JGAAP decision-making workflow:

1. Persist manual equity adjustments in a new
   `ss_manual_adjustments` table (migration 0017).
2. Expose REST endpoints for review UIs:
   - `GET /api/v1/statement-of-changes-in-equity?entityId=&fiscalTermId=&format=json|pdf`
   - `GET    /api/v1/ss-adjustments?entityId=&fiscalTermId=`
   - `POST   /api/v1/ss-adjustments`
   - `PATCH  /api/v1/ss-adjustments/{id}`
   - `DELETE /api/v1/ss-adjustments/{id}`
3. Render the report as an A4-landscape PDF via dompdf + Smarty,
   sharing the IPAex-font / chroot plumbing that ADR-009, ADR-011,
   and ADR-015 already use.
4. Keep the builder pure and easy to extend: when a later wave adds
   real journal-based auto-detection for dividends or treasury-stock
   moves, it slots in by producing additional `SsChange` rows with
   `source = journal_auto`. **The shape of `SsChange` is the
   contract**; the UseCase knows nothing about the Journal port.

Out of scope:

- Journal-driven auto-detection for dividend, treasury-stock, or
  valuation-adjustment moves. Wave 6-H-2 takes net income from the
  caller (normally sourced upstream from the PL builder) and treats
  every other change row as manual.
- Multi-entity / consolidated SS.
- CSV export (JSON + PDF only, matching the rest of Phase 6).

### 2.2 Aggregate shape

Two independent aggregates:

1. **`StatementOfChangesInEquity`** (read model) — holds a list of
   `SsSection`, one per `SsSectionCode`. Each section carries
   `openingBalance`, `changes: list<SsChange>`, and a derived
   `endingBalance`. The aggregate is built once per request by
   `StatementOfChangesInEquityBuilder`, handed to the HTTP layer
   unchanged, and thrown away.
2. **`SsManualAdjustment`** (persistent) — one row per manual change
   entry the reviewer types into the UI. This is the *only* mutable
   aggregate in the wave.

### 2.3 Why the minimal auto-detection?

The strangler-fig pattern (ADR-007) lets us ship progressively. The
legacy `_checkUseLog` scan was a correctness hazard (point (1)
above), so reproducing it faithfully would buy nothing and leave an
audit trail we would have to immediately replace. A later wave can
add a `JournalSsChangeDetector` service that turns posted journals
into `journal_auto`-tagged `SsChange` rows without touching the
builder contract.

### 2.4 Invariant: `ending === opening + sum(changes)`

Enforced in `SsSection::fromChanges()` — the ending balance is
always derived, never passed in. Readers never observe a column
whose opening + changes does not reconcile with the ending.

### 2.5 Currency

Inherits the entity's configured currency from the UseCase input;
the PDF renderer formats JPY with no decimals and everything else
with comma-separated integers wrapped in parens for negatives
(mirrors `DompdfFinancialStatementGenerator::formatAmount()` semantics
but simpler — the SS view shows whole yen only).

## 3. Schema (migration 0017)

```sql
CREATE TABLE ss_manual_adjustments (
    id               BINARY(16)     NOT NULL,
    entity_id        BINARY(16)     NOT NULL,
    fiscal_term_id   BINARY(16)     NOT NULL,
    section_code     VARCHAR(32)    NOT NULL,
    change_type_code VARCHAR(32)    NOT NULL,
    amount           DECIMAL(18, 4) NOT NULL,
    label            VARCHAR(128)   NOT NULL,
    sort_order       INT UNSIGNED   NOT NULL DEFAULT 0,
    notes            VARCHAR(255)   NULL,
    created_at       TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at       TIMESTAMP(6)   NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
                     ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    KEY idx_ssma__entity_ft (entity_id, fiscal_term_id, sort_order),
    CONSTRAINT fk_ssma__entity FOREIGN KEY (entity_id) REFERENCES entities (id),
    CONSTRAINT fk_ssma__ft     FOREIGN KEY (fiscal_term_id) REFERENCES fiscal_terms (id)
);
```

Notes:

- No unique constraint on `(entity, fiscal term, section,
  change_type)` — a term can legally record two separate dividend
  events, for example. Callers order with `sort_order` instead.
- No foreign key on `section_code` / `change_type_code` — both live
  in app-side enums (`SsSectionCode`, `SsChangeType`). This keeps
  migration cost low when we add new codes (e.g. when post-wave
  journal detection introduces `acquisition_of_subsidiary` for
  group entities).
- No soft-delete column. Adjustments are reviewer scratch data —
  nothing downstream (ledger, BS) references them. Hard delete keeps
  the table small.

## 4. Consequences

### Positive

- Reviewer sees exactly which rows are machine-derived
  (`source = journal_auto`) vs entered (`source = manual`).
- Builder is pure — testable in a single PHPUnit run without any DB
  fixtures.
- Path is open for a later wave to add journal-driven detection
  without changing the builder signature.
- Column / row ordering is deterministic (`SsSectionCode::ordered()`
  + stable-sorted adjustments), so snapshot tests of the PDF / JSON
  output stay stable across runs.

### Negative

- Reviewer must enter dividends / treasury moves by hand until the
  journal-detection wave lands. The UI surfaces this clearly via the
  legend row in the PDF footer.
- We do not yet reconcile the SS ending balances against the BS
  equity section. A future wave will add a `BsSsReconciler` service
  that surfaces a warning when
  `Σ ss.endingBalance ≠ bs.equity.subtotal`.

### Follow-ups

- Wave 6-H-3 ships `FinancialStatementNotes`; the SS template
  should grow a "See Notes" footer pointing at the relevant note
  IDs once that wave lands.
- Wave 6-I picks up the BS-SS reconciliation story described above.

## 5. Alternatives considered

1. **Replay legacy `_checkUseLog` exactly** — rejected; the legacy
   log table is not in the new schema, and restoring it was judged
   strictly worse than the explicit `ss_manual_adjustments` table
   because the log never distinguished planned vs actual changes.
2. **Store opening / ending balances per (entity, term, section)
   rather than deriving them** — rejected; opening follows from the
   previous period's ending, which already lives in the `balances`
   snapshot.  Duplicating it here invites the two sources to diverge.
3. **Fold SS into `FinancialStatement`** — rejected; the BS / PL / CS
   aggregate already has two code paths (simplified + JGAAP). Adding
   an SS fourth layer would inflate an aggregate that three other
   consumers already rely on.
