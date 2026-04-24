# ADR-009 — Phase 6-A: Proper Port of the J-GAAP Financial Statement Pipeline

* Status: Accepted
* Date: 2026-04-21
* Supersedes: portions of ADR-005 / ADR-007 that described the simplified Phase 6.6 placeholder FS use case
* Relates to: ADR-005 (layered architecture), ADR-006 (ports & adapters), ADR-007 (strangler-fig migration)

## 1. Context

Phase 6.6 shipped a transitional `GenerateFinancialStatementUseCase` that
grouped `TrialBalanceRow` entries by `AccountTitle::category` into five flat
Sections (assets / liabilities / equity / revenue / expenses). That was
sufficient to exercise the HTTP pipeline and dompdf templates, but the
output is *not* a legal Japanese 決算書:

| Legacy feature | Simplified Phase 6.6 coverage |
| --- | --- |
| 伝統的 T 字型 BS（流動／固定／繰延 分割） | No — flat 資産 list |
| PL 段階利益（売上総利益 → 営業利益 → 経常利益 → 税引前 → 当期純利益） | No — revenue/expenses only |
| 控除項目（貸倒引当金 等）への符号反転 | No — every line added |
| 勘定科目ごとの FS 項目マッピング | No — implicit via category |
| セクション階層（asset → 流動資産 → 現金 …） | No — single depth |

The legacy PHP code (`back/class/else/plugin/accounting/jpn/…`):

* `FinancialStatement.php`           (595 LOC) — entry + navi / filter glue
* `FinancialStatementOutput.php`     (919 LOC) — print + CSV output
* `CalcAccountTitleFS.php`           (364 LOC) — **calculation loop**
* `AccountTitleFS.php`               (690 LOC) — FS master editing
* `AccountTitleFSEditor.php`         (1078 LOC) — FS master editing view

…carried the real FS structure in two JSON columns on `accountingFSJpn`
(`jsonJgaapFSBS`, `jsonJgaapFSPL`) plus a sidecar JSON mapping column
(`jsonJgaapAccountTitle{BS,PL,CR}`). The recursive calculation combined each
account's `flagDebit` (0 = credit side, 1 = debit side) with the FS line's
`flagDebit` to determine the sign, then rolled parent subtotals (`flagCalc`
= `sum`) and sibling subtotals (`flagCalc` = `net`) up the tree.

Phase 6-A exists to lift that model onto the new layered architecture
without keeping JSON blobs in the DB.

## 2. Decision

Introduce two master tables and a domain service that jointly replace the
legacy `accountingFSJpn` / `CalcAccountTitleFS` pair.

### 2.1 Schema (`scripts/migrate/0008_fs_mappings.sql`)

```
fs_section_definitions           -- J-GAAP BS / PL hierarchy (seeded)
  id, fs_kind, code, parent_code, label, sort_order,
  is_subtotal, is_total, formula (+code/-code list)

account_title_fs_mappings        -- account → FS section (per entity)
  id, entity_id, account_title_id, fs_kind, fs_section_code,
  sort_order, display_label, sign (+1 / -1)
```

* `fs_section_definitions` is seeded with the J-GAAP standard structure
  via `0008_fs_mappings_seed.sql` (流動資産 → 固定資産 → 繰延資産 →
  資産合計 / 流動負債 → 固定負債 → 負債合計 / 株主資本 →
  評価・換算差額等 → 新株予約権 → 純資産合計 / 売上高 → 売上原価 →
  売上総利益 → 販管費 → 営業利益 → 営業外 → 経常利益 → 特別 →
  税引前 → 法人税等 → 当期純利益).
* `formula` stores the deterministic rule for computed subtotals
  (`gross_profit = +operating_revenue - cost_of_sales`,
  `operating_income = +gross_profit - sga`, etc.). The legacy
  `flagCalc = sum / net` is converted to explicit formulas.
* `account_title_fs_mappings.sign` captures whether a line is added or
  subtracted inside its section (vs. the legacy pair of `flagDebit` flags).

### 2.2 Domain (`src/Domain/FinancialStatement/Port/`)

New readonly DTOs + ports:

* `FsKind` enum (`bs` / `pl`)
* `FsSectionCode` const class (registry of canonical codes)
* `FsSectionDefinition` + `FsSectionDefinitionRepositoryInterface`
* `AccountTitleFsMapping` + `AccountTitleFsMappingRepositoryInterface`
* `Service\FinancialStatementBuilder` (pure service)

The builder reproduces `CalcAccountTitleFS::_loopVarsCalc()` in three
passes:
1. Fold mappings into the leaf section each refers to (signed SUM).
2. Roll child sections into their `parent_code` (tangible_asset →
   noncurrent_asset → asset).
3. Compute formula-driven subtotals (gross_profit, operating_income,
   ordinary_income, pretax_income, net_income, *_total).

### 2.3 Application (`src/Application/FinancialStatement/`)

Three use cases live here now:

| Class | Purpose |
| --- | --- |
| `Simplified\SimplifiedGenerateFinancialStatementUseCase` | The old Phase 6.6 logic, preserved verbatim. |
| `Port\GenerateFinancialStatementFromMappingUseCase` | Mapping-driven port (primary path). |
| `GenerateFinancialStatementUseCase` | Transitional dispatcher. If the requested entity has one or more `account_title_fs_mappings` rows, it delegates to the port; otherwise it falls back to the simplified path. |

The dispatcher keeps the public constructor shape that existing callers
(the HTTP controller, the Phase 6.6 integration tests) already rely on —
extra dependencies are optional. DI now passes the port components in
production; unit tests can omit them and get the simplified behaviour.

### 2.4 Infrastructure (`src/Infrastructure/FinancialStatement/Port/`)

* `PdoAccountTitleFsMappingRepository` — joins `account_titles` for a
  deterministic `code ASC` secondary ordering.
* `PdoFsSectionDefinitionRepository` — plain `fs_section_definitions`
  reader.

No changes to `DompdfFinancialStatementGenerator` constructor — new
Smarty view-model data (`bsOrder`, `plOrder`, `hasJgaap`) is supplied by
the generator itself so templates can render either the J-GAAP layout or
the simplified fallback.

### 2.5 Templates (`storage/templates/fs/`)

* `_jgaap_bs.tpl` — T-form BS (流動 / 固定 / 繰延 on the left, 流動 / 固定
  負債 / 株主資本・評価換算差額・新株予約権 on the right).
* `_jgaap_pl.tpl` — step-wise PL.
* `bs.html.tpl` / `pl.html.tpl` / `all.html.tpl` — dispatch to the J-GAAP
  templates when `hasJgaap` is true, otherwise keep rendering the existing
  flat sections. Totals strip at the bottom uses the flat aliases already
  present in `$fs.totals` so both paths agree.
* `fs-common.css.tpl` — added `.subtotal` / `.total` / `.indent-1` /
  `.indent-2` styling so the J-GAAP layout has the traditional double-line
  under 当期純利益 / 資産合計.

## 3. Legacy → New Mapping

| Legacy concept | New home |
| --- | --- |
| `accountingFSJpn.jsonJgaapFSBS/PL` | `fs_section_definitions` rows |
| `accountingFSJpn.jsonJgaapAccountTitleBS/PL` | `account_title_fs_mappings` rows |
| `varsFSValue[...]['sumNext']` | `TrialBalanceRow::$balance` (scale-4) |
| `CalcAccountTitleFS::_getValueFS` (sign combining flagDebit pairs) | `AccountTitleFsMapping::$sign` precomputed at mapping time |
| `CalcAccountTitleFS::_loopVarsCalc` (`flagCalc = sum/net`) | `FsSectionDefinition::$formula` + `FinancialStatementBuilder::computeFormulaSubtotals` |
| `flagUnit` / `flagCalc = floor/round/ceil` (千円単位表示) | **Deferred**; belongs in a view-layer filter, not the domain |
| `flagZero` (zero-balance hide) | **Deferred**; view-layer concern |
| `varsDepartment` (部門別) | **Not ported in 6-A**. Department dimension lives in Wave 6-C |
| CR (製造原価報告書) | **Not ported in 6-A**. Wave 6-B |
| CS (キャッシュフロー) | **Not ported in 6-A**. Simplified stub remains |

## 4. Consequences

* **Positive**
  - Real step-wise PL (売上総利益 → 営業利益 → 経常利益 → 税引前 → 当期純利益) — closes the biggest gap vs the legacy app.
  - Traditional T-form BS with 流動 / 固定 / 繰延 splits.
  - Sign reversal for contra items (貸倒引当金 etc.) is first-class.
  - Calculation is a pure domain service — unit-testable in isolation.
  - Coexists with the Phase 6.6 simplified path; no entity is broken by the upgrade.

* **Negative / Trade-offs**
  - `fs_section_definitions` is seeded master data; entity-specific
    overrides (e.g. 独自の中科目) are NOT supported yet. A future extension
    can either (a) add `entity_id` to the definitions table, or (b) add a
    sibling `fs_section_overrides` table.
  - Requires seed script (`scripts/seed/fs_mapping_demo.sql` — deferred)
    to wire up existing demo entities before the port path kicks in. Until
    that lands, demo entities keep rendering via the simplified fallback.
  - CS is still the stub from Phase 6.6. The current port emits `cs = []`
    when hit directly — the dispatcher falls back to the simplified path
    when a caller requests `kind=CS`.

## 5. Alternatives Considered

1. **Single wide JSON column on `entities`** — keeps the legacy shape but
   loses join-ability / index-ability. Rejected: every mapping lookup would
   need a JSON_TABLE traversal.
2. **Hard-coded mappings inside `FinancialStatementBuilder`** — simplest
   but forces every entity into the same chart of accounts. Rejected: per-
   entity account titles already exist (`account_titles` has
   `entity_id`), so FS mappings must be per-entity too.
3. **Rector-driven auto-port of `CalcAccountTitleFS`** — would preserve
   the legacy `sum/net` flagCalc model verbatim but also preserve its
   fragility (mutable globals, `flagDebit` XOR logic). Rejected: the
   formula + sign split is more legible to the next contributor.

## 6. Follow-ups

* **Wave 6-B**: CS (キャッシュフロー計算書) port — mirrors this ADR with
  a `fs_cs_section_definitions` + `account_title_cs_mappings` pair.
* **Wave 6-C**: Manufacturing cost (製造原価報告書) + department
  dimension (`idDepartment`).
* Seed script `scripts/seed/fs_mapping_demo.sql` to wire the demo entity.
* PDF font issue: IPAex Gothic remains optional; no change in 6-A.

## 7. Implementation Checklist

* [x] `scripts/migrate/0008_fs_mappings.sql` (+ `.down.sql` + `_seed.sql`)
* [x] Domain DTOs + repository interfaces (`src/Domain/FinancialStatement/Port/`)
* [x] `FinancialStatementBuilder` domain service
* [x] `GenerateFinancialStatementFromMappingUseCase` (Application)
* [x] Dispatching `GenerateFinancialStatementUseCase` (transition)
* [x] Simplified path preserved as `Simplified\SimplifiedGenerateFinancialStatementUseCase`
* [x] PDO repositories (`src/Infrastructure/FinancialStatement/Port/`)
* [x] J-GAAP Smarty templates (`_jgaap_bs.tpl`, `_jgaap_pl.tpl`)
* [x] DI wiring (`ContainerBootstrap`)
* [x] Unit tests (builder + UseCase)
* [x] Golden tests (9-scenario PL + contra-asset BS)
* [x] ADR-009 (this document)
* [ ] Seed script for demo entity (tracked separately)
* [ ] Integration test against MariaDB (tracked separately — requires live DB fixture work)
