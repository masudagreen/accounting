# ADR-010 — Phase 6 Wave 6-B: Port of the J-GAAP Cash Flow Statement (Indirect Method)

* Status: Accepted
* Date: 2026-04-21
* Supersedes: the Phase 6.6 CS stub path that rendered `cs = []` via `GenerateFinancialStatementFromMappingUseCase`, and the placeholder `storage/templates/fs/cs.html.tpl` note.
* Relates to: ADR-005 (layered architecture), ADR-006 (ports & adapters), ADR-007 (strangler-fig migration), ADR-009 (Wave 6-A BS/PL port).

## 1. Context

Phase 6 Wave 6-A (ADR-009) ported the J-GAAP Balance Sheet and Profit & Loss
statement onto the layered architecture via two master tables
(`fs_section_definitions`, `account_title_fs_mappings`) and a pure domain
service (`FinancialStatementBuilder`). Cash Flow was left untouched:

> 2.2 Domain … Cash flow sections are not represented here … Cash flow remains
> stubbed in the Simplified port until Wave 6-C.

The simplified path reported CS as a flat 営業/投資/財務 triple, carrying only
the period's net income under 営業CF. That is not a legal J-GAAP CS.

The legacy PHP code (`back/class/else/plugin/accounting/jpn/`):

| File | LOC | Role |
| --- | --- | --- |
| `FinancialStatementCS.php`         |   488 | Entry + navi / filter glue (間接法 / 直接法) |
| `FinancialStatementCSOutput.php`   |   873 | Print + CSV output |
| `CalcAccountTitleFSCS.php`         |   319 | **Calculation loop** |
| `AccountTitleFSCS.php`             |   692 | CS master editing |
| `AccountTitleFSCSEditor.php`       | 1,109 | CS master editing view |
| `AccountTitleCS.php`               |   611 | CS section master |
| `AccountTitleCSEditor.php`         |   701 | CS section master editor |

…carried the CS structure in a JSON column (`jsonJgaapFSCS`) on
`accountingFSValueJpn`, with a sidecar JSON `jsonJgaapAccountTitle{BS,PL,CR}`
supplying the per-account CS mapping (via `varsJgaapCS.varsInDirect` /
`varsDirect`). The calculation folded debit/credit totals per account, flipped
sign on the "Minus" leg, and walked the section tree with
`flagCalc = sum / net`.

Wave 6-B lifts the **indirect method** path onto the new architecture —
direct method has materially higher operational cost (every journal line
must be tagged with a CS bucket, doubling the chart-of-accounts work) and
is not the default legal filing form in Japan. Direct method remains a
follow-up after mappings coverage is broader.

## 2. Decision

Introduce one section-definition table and one mapping table, plus a pure
domain service that jointly replace the legacy `jsonJgaapFSCS` /
`CalcAccountTitleFSCS` pair.

### 2.1 Schema (`scripts/migrate/0009_fs_cs_mappings.sql`)

```
fs_cs_section_definitions        -- CS hierarchy (seeded; no entity_id — shared master)
  id, code, parent_code, label, sort_order,
  is_subtotal, is_total, formula (+code/-code list)

account_title_cs_mappings        -- account → CS section (per entity)
  id, entity_id, account_title_id, cs_section_code,
  sort_order, display_label, sign (+1 / -1),
  flow_category ('operating' / 'investing' / 'financing'),
  is_working_capital (0/1)
```

Seed (`0009_fs_cs_mappings_seed.sql`) populates 29 rows covering the full
日本基準 間接法 structure:

- I. 営業 CF : `operating_cf` (parent), `operating_pretax_income`,
  `depreciation`, `provision`, `wc_receivables`, `wc_inventory`,
  `wc_payables`, `operating_cf_subtotal`, `interest_received`,
  `interest_paid`, `tax_paid`, `operating_cf_total`.
- II. 投資 CF : `investing_cf` (parent), `investing_ppe_purchase`,
  `investing_ppe_sale`, `investing_securities_purchase`,
  `investing_securities_sale`, `investing_loan_given`,
  `investing_loan_received`, `investing_cf_total`.
- III. 財務 CF : `financing_cf` (parent), `financing_debt_proceeds`,
  `financing_debt_repayment`, `financing_equity_proceeds`,
  `financing_dividends_paid`, `financing_cf_total`.
- Reconciliation : `net_change_in_cash`, `beginning_cash`, `ending_cash`.

`formula` follows the same `+code/-code` grammar as Wave 6-A so the two
builders share the parser. Example: `operating_cf_total` =
`+operating_cf_subtotal+interest_received-interest_paid-tax_paid`.

`flow_category` is redundant with the section-code prefix but is surfaced
explicitly so editor UIs can filter mappings by bucket without parsing the
code. `is_working_capital` = 1 signals operating-side accounts whose balance
change is sign-flipped at calculation time (an increase in receivables is a
cash decrease).

### 2.2 Domain (`src/Domain/FinancialStatement/Port/Cs/`)

New readonly DTOs + ports (all under the `Cs` subnamespace so BS/PL code
remains untouched):

* `CsFlowCategory` enum (`Operating` / `Investing` / `Financing`)
* `CsSectionCode` const registry
* `CsSectionDefinition` + `CsSectionDefinitionRepositoryInterface`
* `AccountTitleCsMapping` + `AccountTitleCsMappingRepositoryInterface`
* `Service\CashFlowStatementBuilder` (pure service)

The builder reproduces `CalcAccountTitleFSCS::_loopVarsCalc()` and
`_getValueFS()` in four passes:

1. Fold every mapping into its leaf section as a signed line. Working-capital
   mappings flip sign (the asset/liability-side balance change is the
   opposite of the cash impact).
2. Seed `operating_pretax_income` from the PL builder's net income input and
   `beginning_cash` from a caller-supplied prior-period value.
3. Roll children up into their `parent_code` parent (deepest first).
4. Compute formula subtotals in `sort_order` order so
   `operating_cf_subtotal` is resolved before `operating_cf_total` is,
   and before `net_change_in_cash` before `ending_cash`.

### 2.3 Application (`src/Application/FinancialStatement/Port/`)

`GenerateFinancialStatementFromMappingUseCase` grew three optional
dependencies (`csMappings`, `csDefinitions`, `csBuilder`). When they are
wired AND the caller asked for CS or ALL, the port:

1. Always builds the PL when CS is requested — CS needs the pretax-income
   subtotal, so we compute PL even for `kind=CS` and then drop it from the
   output if the caller only asked for CS.
2. Builds CS via `CashFlowStatementBuilder::build()`.
3. Exposes five CS totals under `$fs->totals` (`operating_cf_total`,
   `investing_cf_total`, `financing_cf_total`, `net_change_in_cash`,
   `ending_cash`) — matching the pattern Wave 6-A uses for
   `gross_profit` / `operating_income` / `ordinary_income`.

The dispatching `GenerateFinancialStatementUseCase` gains matching
optional parameters and passes them through to the port when mappings
exist.

### 2.4 Infrastructure (`src/Infrastructure/FinancialStatement/Port/Cs/`)

* `PdoCsSectionDefinitionRepository` — plain `fs_cs_section_definitions`
  reader, ordered by `(sort_order, code)`.
* `PdoAccountTitleCsMappingRepository` — joins `account_titles` for
  deterministic `code ASC` secondary ordering.

`DompdfFinancialStatementGenerator` gains `csOrder()` (mirroring
`bsOrder()` / `plOrder()`) plus a `hasJgaapCs` view-model flag, so Smarty
can switch between the J-GAAP renderer and the legacy simplified note.

### 2.5 Templates (`storage/templates/fs/`)

* `_jgaap_cs.tpl` — full indirect-method CS renderer walking `$csOrder`.
* `cs.html.tpl` — dispatches to `_jgaap_cs.tpl` when `hasJgaapCs` is true,
  otherwise keeps the original simplified fallback note.
* `all.html.tpl` — dispatch to `_jgaap_cs.tpl` when present, under
  「キャッシュフロー計算書」.

### 2.6 DI (`src/Support/Container/ContainerBootstrap.php`)

Three new bindings:

* `AccountTitleCsMappingRepositoryInterface` → `PdoAccountTitleCsMappingRepository`
* `CsSectionDefinitionRepositoryInterface` → `PdoCsSectionDefinitionRepository`
* `CashFlowStatementBuilder` → no-arg constructor

`GenerateFinancialStatementUseCase` now receives all six FS-related
dependencies (three for BS/PL, three for CS).

## 3. Legacy → New Mapping

| Legacy concept | New home |
| --- | --- |
| `accountingFSValueJpn.jsonJgaapFSCS` | `fs_cs_section_definitions` rows |
| `varsJgaapCS.varsInDirect` (mapping per account) | `account_title_cs_mappings` rows |
| `varsJgaapCS.varsDirect` (direct method) | **Not ported.** See §6 Follow-ups. |
| `CalcAccountTitleFSCS::_getValueFS` Plus/Minus split | `AccountTitleCsMapping.sign` precomputed at mapping time |
| `flagMethod = net / sumDebit / sumCredit` | Always `net` (=`balance`) in the new model; debit/credit split is available from `TrialBalanceRow` if a future extension needs it |
| Legacy "working capital" flip (sign reversal for 売掛金↑) | `AccountTitleCsMapping.isWorkingCapital` toggles sign flip |
| `CalcAccountTitleFSCS::_loopVarsCalc` (`flagCalc = sum / net`) | `CsSectionDefinition.formula` + `CashFlowStatementBuilder::computeFormulaSubtotals` |
| 期首残高 (`cashOpening`) | `CashFlowStatementBuilder::$beginningCash` param |
| 期末残高 (`cashClosing`) | Computed via `ending_cash = net_change_in_cash + beginning_cash` |

## 4. Consequences

* **Positive**
  - Full J-GAAP 間接法 CS — 税引前当期純利益 → 非資金調整 → 運転資本増減 →
    小計 → 利息・税支払 → 営業 CF → 投資 CF → 財務 CF → 期末残高 with the
    legal line-ordering.
  - Pure domain service — unit-testable in isolation (13 unit scenarios
    in `CashFlowStatementBuilderTest`).
  - CS calculation is explicit: working-capital sign flip, explicit `+1/-1`
    sign — no `flagDebit` XOR logic.
  - The port coexists with the Phase 6.6 simplified path; entities without
    CS mappings still render via the fallback note.

* **Negative / Trade-offs**
  - `fs_cs_section_definitions` has no `entity_id`; entity-specific overrides
    are not supported yet. This matches the current `fs_section_definitions`
    decision. A future extension can either add `entity_id` or a sibling
    `fs_cs_section_overrides` table.
  - `beginning_cash` currently defaults to `'0.0000'` in the use case.
    Wiring prior-period snapshot lookup is tracked as a follow-up; it
    doesn't block the current data model.
  - The existing migration-runner glob bug (shared with Wave 6-A) keeps
    the seed `_seed.sql` file ineligible for the standard `up()` pipeline.
    The integration test re-applies both files manually to compensate.
    Fixing the runner is out of scope for this ADR.
  - Direct method is not ported. `varsDirect` in the legacy code is a
    second axis of mapping that multiplies the editor surface; punting
    is deliberate.

## 5. Alternatives Considered

1. **Direct method first** — matches the ADR-009 pattern of "lift exactly
   what the legacy UI builds". Rejected: every account would need a CS
   inflow/outflow tag, roughly doubling the chart-of-accounts editing burden
   for entities that mostly want "just give me a CS". Indirect method
   derives most lines from the PL + balance deltas.
2. **Single wide JSON column on `entities`** — keeps the legacy shape but
   loses join-ability / index-ability. Rejected for the same reason as
   Wave 6-A.
3. **Compute CS as a transform over the BS/PL sections already built** —
   would avoid a second mapping table, but forces CS to be sensitive to BS/PL
   classification changes and makes edge cases (interest received shown
   separately from its PL bucket) impossible to model. Rejected: explicit
   CS mapping is required by J-GAAP for 利息受取/支払 and 法人税等支払.

## 6. Follow-ups

* **Beginning cash from prior-period snapshot**: plumb
  `PdoTrialBalanceSnapshotRepository` through the use case so
  `beginning_cash` reflects the actual prior-period ending balance rather
  than `0`. Tracked separately.
* **Direct-method CS** — requires a second mapping table
  (`account_title_cs_mappings_direct`) or an extra `direct_section_code` on
  the existing one, plus UI work for the editor. Out of scope for 6-B.
* **CR (製造原価報告書)** — **the legacy codebase has no dedicated
  `FinancialStatement*CR` pipeline**. The only references to `CR` in
  `back/class/else/plugin/accounting/jpn/` are `flagCR` flags that gate
  whether a nation ships manufacturing, not a full calculation module. A
  `_grep "製造原価"_` pass turns up only 2012-era dormant code in
  `2012/public/`. Wave 6-C (which ADR-009 §6 flagged as the CR wave) is
  therefore **not** required to port existing logic; when CR is needed, it
  will be a new feature rather than a port and will get its own ADR.
* **Seed script for demo entity** (`scripts/seed/fs_cs_mapping_demo.sql`)
  to wire an example set of CS mappings. Tracked separately.
* **Migration-runner glob fix** so `_seed.sql` files don't override their
  corresponding CREATE TABLE migration. Shared issue with Wave 6-A.
* **Integration test against MariaDB** for `PdoAccountTitleCsMappingRepository`
  now lives under `tests/Integration/Infrastructure/FinancialStatement/Port/Cs/`
  and self-skips when `RUCARO_TEST_DB_USER` is unset.

## 7. Implementation Checklist

* [x] `scripts/migrate/0009_fs_cs_mappings.sql` (+ `.down.sql` + `_seed.sql`)
* [x] Domain DTOs + repository interfaces (`src/Domain/FinancialStatement/Port/Cs/`)
* [x] `CsFlowCategory` enum + `CsSectionCode` registry
* [x] `CashFlowStatementBuilder` domain service
* [x] `GenerateFinancialStatementFromMappingUseCase` extended (CS branch)
* [x] `GenerateFinancialStatementUseCase` dispatcher passes CS deps
* [x] PDO repositories (`src/Infrastructure/FinancialStatement/Port/Cs/`)
* [x] J-GAAP Smarty template (`_jgaap_cs.tpl`)
* [x] `cs.html.tpl` / `all.html.tpl` switch on `hasJgaapCs`
* [x] DI wiring (`ContainerBootstrap`)
* [x] Unit tests (builder — 13 scenarios, use case — 3 scenarios)
* [x] Integration tests (PDO repos — 3 scenarios; self-skipping without DB)
* [x] PHPStan level 6 green (baseline unchanged)
* [x] ADR-010 (this document)
* [ ] Prior-period snapshot → `beginning_cash` (tracked separately)
* [ ] Direct method port (tracked separately)
