# ADR-019 — Phase 6 Wave 6-I: Multi-Period Comparison Financial Statements port

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-005 (layered architecture), ADR-006 (ports and adapters),
  ADR-007 (strangler-fig migration), ADR-009 (FS port — single period BS/PL),
  ADR-010 (CS port — single period)

## 1. Context

Waves 6-A and 6-B ported the single-period 決算書 calculation pipeline onto the
layered architecture (ADR-009, ADR-010). The last piece of the FS print family
is the **複数期比較決算書** — a side-by-side rendering where the same BS / PL /
CS sections are printed across 2〜5 fiscal terms with 増減 (variance) and 増減率
(variance %) columns. This is how every Japanese 中小企業 looks at trend data
when preparing a 決算報告 to the board or a tax filing, so the HTTP surface
needs it to keep parity with the legacy system.

The legacy implementation lived in four files under
`back/class/else/plugin/accounting/jpn/`:

- `FinancialStatementMulti.php`          (988 LOC) — dispatcher + navigation glue
- `FinancialStatementMultiOutput.php`    (452 LOC) — CSV / print output
- `FinancialStatementMultiCS.php`        (811 LOC) — CS-specific multi-period loop
- `FinancialStatementMultiCSOutput.php`  (412 LOC) — CS output

The legacy code carried the comparison matrix as a 2-D array hard-coded for
"前々期 / 前期 / 当期 + 増減 + 増減率" (5 columns max, fixed). Sections were
fetched with duplicate SQL (once per term) and then hand-merged in PHP. Because
the comparison rendering was entangled with the plugin dispatcher you could
not render a multi-period report from a scheduler, an email attachment, or a
unit test without standing up the full web session.

Concrete problems with that shape:

1. **Tight coupling to the legacy dispatcher.** The comparison loop lived
   inside `_iniList()`-style methods that read `$varsRequest` globals; there
   was no Input → Output seam.
2. **Hard-coded 3-period cap.** `FinancialStatementMulti` could not render
   more than 3 periods even when the user had data for 5, and you could not
   ask for 2 periods cleanly without pushing null sentinels through the
   matrix.
3. **Duplicated calculation.** Every period re-ran the recursive
   `CalcAccountTitleFS` loop independently even though the code path was
   identical to the single-period one, doubling database round-trips.
4. **No comparison row abstraction.** Variance / variance% were computed
   inline inside the Smarty-equivalent template, so tests had to diff PDF
   bytes to verify numeric correctness.

## 2. Decision

Introduce a thin N-period aggregation layer that delegates the real FS
computation back to the existing Wave 6-A/6-B Port use case. No new
calculation logic; the domain model for "multi-period" stays tiny.

### 2.1 Domain (`src/Domain/FinancialStatement/Multi/`)

Three new readonly value objects:

- `MultiPeriodFinancialStatement`
  - `entityId, kind, periods: list<MultiPeriodEntry>, generatedAt`
  - Validates non-empty and ascending-by-`fromDate` at construction.
- `MultiPeriodEntry`
  - `fiscalTermId, fiscalTermLabel, fromDate, toDate, statement: FinancialStatement`
  - Wraps a single-period {@see FinancialStatement} produced by the Wave 6-A
    Port use case.
- `MultiPeriodSectionRow`
  - `sectionCode, lineCode, label, amounts: array<termId, scale-4 decimal>,
    variance, variancePercent, depth, isSubtotal, isTotal`
  - Produced by the infrastructure flattener, not the domain — kept here
    because it is the shared shape between JSON serializer and Smarty
    renderer.

No new ports or repositories on the domain side beyond the ones Waves 6-A / 6-B
already defined; the multi path re-uses `AccountTitleFsMapping`,
`FsSectionDefinition`, etc. transitively through its provider.

### 2.2 Application (`src/Application/FinancialStatement/Multi/`)

- `GenerateMultiPeriodFinancialStatementInput`
  - `entityId, fiscalTermIds: list<string>, kind, asOf?, currencyCode='JPY'`
  - `MAX_PERIODS = 5`.
- `GenerateMultiPeriodFinancialStatementUseCase`
  - Depends on a new `FinancialStatementProviderInterface` seam (satisfied in
    production by the single-period use case) and a
    `FiscalTermMetadataRepositoryInterface` that resolves each id's period
    label / date range.
  - Validates the id list (size, distinct, resolvable), sorts metas by
    `start_date` ascending, calls the provider once per term, wraps the
    results into a `MultiPeriodFinancialStatement` aggregate.
- `FinancialStatementProviderInterface`
  - Single-method seam over the Wave 6-A/6-B calculation. Keeps the multi use
    case 100% testable without a trial-balance fixture per period.
- `FiscalTermMetadataRepositoryInterface`
  - Single method `findByIds(list<string>): list<FiscalTermMetadata>`; the
    infrastructure impl reads `fiscal_terms` by ULID.

### 2.3 Infrastructure (`src/Infrastructure/FinancialStatement/Multi/`)

- `PdoFiscalTermMetadataRepository` — `fiscal_terms` ↔ `FiscalTermMetadata`.
- `MultiPeriodRowBuilder` — stateless helper that flattens the aggregate into
  `MultiPeriodSectionRow` lists for BS / PL / CS. Computes variance =
  `latest - previous` and variancePercent = `(latest - previous) / previous *
  100` at scale 4. Returns `null` for both when only one period is asked for;
  returns `null` for just the percent when `previous` equals zero.
- `MultiPeriodJsonSerializer` — pure function from aggregate to the JSON
  envelope.
- `MultiPeriodFinancialStatementGeneratorInterface` + Dompdf impl —
  A4 **landscape** (the extra width is the decisive reason to cap at 5 periods
  — 2 label columns + up to 5 amount columns + 2 variance columns stays
  within A4 landscape at 9.5pt without clipping).

### 2.4 HTTP (`src/Http/Controller/FinancialStatement/Multi/`)

New endpoint, new controller class (cleaner than overloading the existing
single-period controller):

```
GET /api/v1/financial-statements/multi
  ?entityId=...
  &fiscalTermIds=ULID1,ULID2,...
  &kind=BS|PL|CS|ALL
  &asOf=YYYY-MM-DD
  &format=json|pdf
  &currencyCode=JPY
```

- Bearer auth required.
- `fiscalTermIds` is a comma-separated ULID list, 1〜5 entries.
- `kind` defaults to `ALL`; parse errors return `400 BAD_REQUEST`.
- `format=json` returns the envelope `{success, data:
  MultiPeriodFinancialStatements, error, meta}`.
- `format=pdf` streams the A4-landscape PDF as attachment.

### 2.5 Smarty templates (`storage/templates/fs_multi/`)

- `layout.html.tpl` — `@page { size: A4 landscape; }`.
- `fs-multi-common.css.tpl` — scale-down base font to 9.5pt, fixed column
  table layout, subtotal / total row styling reused from the single-period
  sheet but narrower border radii.
- `_jgaap_bs_multi.tpl`, `_jgaap_pl_multi.tpl`, `_jgaap_cs_multi.tpl` —
  comparison matrices.
- `bs.html.tpl`, `pl.html.tpl`, `cs.html.tpl`, `all.html.tpl` — entrypoints
  per kind.

## 3. Maximum period cap

We cap at 5 periods. Two reasons:

1. **Render constraint.** A4 landscape at 9.5pt fits comfortably up to
   7-column table (label + 5 periods + variance); adding variance% brings us
   to 8 columns which already tightens spacing. 6+ periods require either
   reducing font size below dompdf's practical lower bound (~7.5pt, where
   Japanese glyph legibility drops) or switching to A3.
2. **Legacy compat.** The old `FinancialStatementMulti` hard-coded 3 periods;
   5 is a deliberate superset without blowing past the A4-landscape budget.

## 4. Variance calculation

`latest - previous` where latest is the rightmost period (max start_date) and
previous is its immediate neighbor. The legacy code compared each period
against the preceding sibling, rendering N-1 variance columns; with N up to 5
that is visually heavy, so Wave 6-I renders a single variance column anchored
on the most recent transition — the one accountants explain first in every
報告会.

variancePercent = `(latest - previous) / previous` expressed at scale 4 as a
percentage figure. When `previous = 0` the ratio is undefined and we return
`null`; the renderer prints `-` in that cell.

## 5. Legacy correspondence table

| Legacy artifact                                 | Wave 6-I replacement                                                                    |
| ----------------------------------------------- | --------------------------------------------------------------------------------------- |
| `FinancialStatementMulti::_iniList()`           | `GenerateMultiPeriodFinancialStatementUseCase::execute()`                               |
| `FinancialStatementMulti::_updateVars()`        | `MultiPeriodRowBuilder::buildBs/Pl/Cs()`                                                |
| `FinancialStatementMulti::_updateVarsItem()`    | `PdoFiscalTermMetadataRepository::findByIds()` + provider delegation                    |
| `FinancialStatementMultiOutput::_getCsv()`      | `MultiPeriodJsonSerializer::toArray()` + (future) CSV format branch in the controller   |
| `FinancialStatementMultiCS*`                    | Same code path — the provider returns CS sections when `kind` includes CS              |
| Hard-coded 3-period matrix                      | `MAX_PERIODS = 5` constant on the Input DTO                                             |
| Inline variance computation in `_updateVars()`  | `MultiPeriodRowBuilder::variance()` (unit-tested; handles divide-by-zero)               |

## 6. Consequences

Positive:

- Multi-period rendering costs no new calculation code — it reuses Waves 6-A /
  6-B exactly. Bug fixes in the single-period pipeline automatically flow into
  multi-period output.
- Variance becomes a first-class testable quantity; we can assert on numeric
  correctness without diffing PDFs.
- The provider seam lets unit tests run in <50ms per case even for 5-period
  fixtures (no DB, no Smarty render).
- Landscape layout is isolated to `storage/templates/fs_multi/`; the
  single-period portrait templates remain untouched.

Negative:

- One extra DI binding (`FinancialStatementProviderInterface`) exists solely
  to make the multi use case testable — the indirection has no runtime
  benefit. Accepted: the Phase 6-A use case is `final` and the alternative
  (constructing a full trial-balance fixture per test) is much more painful.
- Beginning-cash carry-over across periods is still out of scope. Each column
  shows `beginning_cash = 0` so CS reconciliation is only correct within a
  period, not across periods. Follow-up wave will land a prior-period
  snapshot port that the provider can seed from.

## 7. Test strategy

- `MultiPeriodFinancialStatementTest` — aggregate invariants (empty / order).
- `GenerateMultiPeriodFinancialStatementUseCaseTest` — happy path, variance
  math, zero-denominator percent, single period (no variance), 6+ periods
  rejected, empty list rejected, duplicates rejected, unresolvable id
  rejected, missing section per period treated as 0, UTC-stamped
  `generatedAt`.
- Dev smoke harness at `scripts/dev/render_multi_period_pdf.php` generates
  one PDF per kind into the rucaro-out desktop folder; pdftotext diff is used
  for visual regression during handoff.
