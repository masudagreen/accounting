# ADR-016: Blue Return port (Phase 6 Wave 6-H-1)

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-005 (layered architecture), ADR-006 (ports and adapters),
  ADR-007 (strangler-fig migration), ADR-009 (FS port), ADR-011 (ledger
  port), ADR-012 (fixed assets port), ADR-015 (budget port)

## 1. Context

Wave 6-H-1 ports the 青色申告決算書 (Blue Return) generator. Unlike the
other Phase 6 statements, the Blue Return is **not** a regular
accounting output; it is a **tax-authority filing** for individual
entrepreneurs (個人事業主) only. Corporate (法人) entities never file
a Blue Return — they file 法人税申告書 instead, which is out of scope
for this wave.

The legacy implementation lives in three loose classes:

- `back/class/else/plugin/accounting/jpn/BlueSheet.php` — stub (~22
  lines) with only a corporation-class guard.
- `back/class/else/plugin/accounting/jpn/BlueSheetOutput.php` — stub
  (~14 lines).
- `back/class/else/plugin/accounting/jpn/2012/public/BlueSheet.php`,
  `BlueSheetEditor.php`, `BlueSheetOutput.php` — the real implementation
  (~4,900 lines combined), keyed by `numYearSheet` (2014, 2015, 2016,
  2017, 2018, 2019, 2020, ...). Each year the layout changes slightly
  because the tax authority reissues the form.

Two production problems drove the rewrite:

1. **Year-keyed PHP branching** makes every form update a source-code
   change. We want a JSON payload so the front end can hold the layout
   and the back end stores whatever columns the current form requires.
2. **No type-level guarantee that Blue Returns belong to individual
   entrepreneurs**. Corporate entities could, in theory, fetch a Blue
   Return URL and have the system pretend to generate one. Moving the
   check into a column (`entities.is_corporate`) + a UseCase guard
   (`CreateBlueReturnUseCase`) closes that hole.

## 2. Decision

### 2.1 Scope

Port the minimum surface needed to author, review, and file a Blue
Return end-to-end:

1. Persist forms in `blue_return_forms` keyed by `(entity_id,
   fiscal_term_id)`, with the four-page payload stored as
   `snapshot_json` (LONGTEXT).
2. Add `entities.is_corporate TINYINT NOT NULL DEFAULT 1` so every
   existing entity keeps treating itself as a corporate filer; the user
   toggles it off explicitly when creating a sole-proprietor entity.
3. Expose REST endpoints matching the legacy UI verbs:
   - `GET / POST /api/v1/blue-returns`
   - `GET / PATCH / DELETE /api/v1/blue-returns/{id}`
   - `POST /api/v1/blue-returns/{id}/finalize`
   - `GET /api/v1/blue-returns/{id}?format=json|pdf`
4. Render the 4-page 青色申告決算書 via dompdf + Smarty, sharing the
   chroot / IPAex-font plumbing used by ADR-009 / ADR-011 / ADR-013 /
   ADR-015.
5. Provide a pure `BlueReturnBuilder` service that composes a snapshot
   from already-classified trial-balance buckets + monthly aggregates
   + fixed-asset schedules. Classification (which account belongs to
   収入 / 売上原価 / 経費 / 資産 / 負債 / 元入金) stays outside the domain —
   we hand the buckets in as plain arrays so the domain never grows a
   chart-of-accounts dependency.

Out of scope for this wave:

- Electronic filing (e-Tax XML generation).
- 税務署 / 区分 lookup tables. Legacy pulled these from CSV; the new
  UI can carry its own reference list.
- Departmental / sub-entity splits.
- Multi-year comparison tables.

### 2.2 Why `snapshot_json` instead of wide columns

Cash Plan and Budget use 12-wide month columns because the aggregate
*shape* is stable. The Blue Return aggregate shape is explicitly
unstable: the tax authority reissues the form in 2014, 2015, 2016,
2017, 2018, 2019, 2020, and so on. Legacy survived this by keying PHP
classes on `numYearSheet`, which meant every form revision required a
source deploy.

Storing the 4 pages as a single JSON blob lets us:

- absorb new fields without a schema migration;
- keep the API surface stable (always `{page1_pl, page2_monthly,
  page3_breakdown, page4_bs}`);
- let the front end evolve the form template independently of
  migrations.

The cost is that we cannot SELECT on inner fields efficiently. That's
fine for this aggregate: the only query is "give me the single Blue
Return for (entity, fiscal term)".

### 2.3 Individual-entrepreneur enforcement

Three defences, in order of strictness:

1. **Schema.** `entities.is_corporate TINYINT NOT NULL DEFAULT 1`. The
   default is 1 so the migration is null-safe — existing rows, which
   were all corporate by virtue of the legacy product being 法人
   first, stay corporate unless explicitly flipped.
2. **UseCase.** `CreateBlueReturnUseCase::execute()` loads the entity
   and raises `ValidationException` if `isCorporate === true`. The
   HTTP layer returns 422 instead of 500.
3. **Domain.** The `BlueReturnForm` aggregate does NOT encode an
   entity type; it trusts the UseCase to have already filtered. This
   keeps the aggregate reusable if we ever relax the guard (e.g. for
   dry-run simulations).

Rejected alternative: "make the check a CHECK constraint on
`blue_return_forms`". Rejected because CHECK cannot reference another
table in MariaDB and a trigger would drag in operational overhead we
don't want during migrations.

### 2.4 Lifecycle

```
Draft ──finalize──▶ Finalized
```

- `Draft` is editable via PATCH. `UpdateBlueReturnUseCase` delegates
  to `BlueReturnForm::withSnapshot()` / `withFormType()`.
- `Finalized` is terminal. Once a form is finalized it represents a
  filed tax return, so the snapshot is frozen. Re-opening requires
  creating a new form (legacy worked the same way: it wrote a new row
  rather than editing the finalized one).

Deletion only works while `Draft`. Attempting to delete a Finalized
form raises `InvariantViolationException` so the compliance trail
stays intact.

### 2.5 Form type enum

Three values, mirroring the tax-authority form catalogue:

- `general` — 一般用 (the default and by far the most common);
- `agricultural` — 農業所得用;
- `real_estate` — 不動産所得用.

An enum rather than a free-form string because the value participates
in template selection (page 1 layout differs across form types) and
we want PhpStan to catch typos at the call site.

### 2.6 Which legacy version did we port?

The legacy repository ships two BlueSheet paths:

- `jpn/BlueSheet.php` (top-level, ~22 lines): a near-empty shim that
  just delegates with a corporation-class guard.
- `jpn/2012/public/BlueSheet*.php` (~4,900 lines): the real code,
  keyed by `numYearSheet` from 2014 to 2020.

The port follows the *2012/public* version because that is where the
field catalogue and output templates actually live. The top-level
shim only contributed the corporation-class guard, which is now the
`CreateBlueReturnUseCase` / `entities.is_corporate` pair.

Fields ported (page-by-page):

- **Page 1 P&L** — 収入金額 / 売上原価 / 経費 / 所得金額. 2020 layout.
- **Page 2 月別** — 月別売上・仕入・給料賃金.
- **Page 3 内訳** — 減価償却費 / 貸倒引当金 / 地代家賃 / 利子割引料 / 税理士・弁護士報酬.
- **Page 4 BS** — 資産 / 負債 / 元入金 (individual-business format).

## 3. Mapping to the legacy code

| Legacy PHP                                                   | New ports                                          |
|--------------------------------------------------------------|----------------------------------------------------|
| `Jpn_BlueSheet::_checkCorporationClass(flagChild: 0)`        | `entities.is_corporate` + UseCase guard            |
| `Jpn_BlueSheet_2012_Public::run()` navigation                | `ApiKernel` route table                            |
| `Jpn_BlueSheetEditor::_save()` raw SQL                       | `CreateBlueReturnUseCase` / `UpdateBlueReturnUseCase` |
| `Jpn_BlueSheet_2012_Public::_getVarsFS()` fetch              | `BlueReturnBuilder::build()` + caller-classified inputs |
| `Jpn_BlueSheetOutput::_render()` template muxer              | `DompdfBlueReturnGenerator` + 4 page templates     |
| `accountingBlueSheetJpn.blobData` crypte blob                | `blue_return_forms.snapshot_json` plaintext JSON   |
| `numYearSheet` PHP branching                                 | snapshot JSON + versionless storage                |
| `Jpn_BlueSheetEditor::_setFlagDone()` state flag             | `BlueReturnStatus::Finalized` enum                 |

## 4. Migration

`scripts/migrate/0016_blue_returns.sql`:

- `ALTER TABLE entities ADD COLUMN is_corporate TINYINT NOT NULL
  DEFAULT 1 AFTER fiscal_start_mmdd`. The default keeps existing rows
  corporate so nothing breaks at apply time.
- `CREATE TABLE blue_return_forms (id, entity_id, fiscal_term_id,
  form_type, snapshot_json LONGTEXT, status, finalized_at, created_by,
  created_at, updated_at, deleted_at)`.
- `UNIQUE (entity_id, fiscal_term_id)` — one active form per fiscal
  term.
- `CHECK status IN ('draft','finalized')` and `CHECK form_type IN
  ('general','agricultural','real_estate')`.
- FKs to `entities(id)` and `fiscal_terms(id)` with `ON UPDATE
  CASCADE`.

Rollback (`0016_blue_returns.down.sql`) drops the table and the
column in reverse dependency order.

## 5. Testing strategy

- Unit (domain):
  - `BlueReturnFormTest` covers the Draft → Finalized state machine,
    `withSnapshot` immutability after finalize, and snapshot
    round-tripping through `toArray` / `fromArray`.
  - `BlueReturnBuilderTest` covers net-income arithmetic, negative
    incomes, and the empty-input path.
- Unit (UseCases):
  - `CreateBlueReturnUseCaseTest` — individual-entrepreneur happy
    path, corporate rejection (422), duplicate (entity, fiscal term),
    missing entity.
  - `UpdateFinalizeBlueReturnUseCaseTest` — update while Draft,
    finalize, reject update after finalize, reject delete on
    finalized, idempotent delete while draft.
  - `GenerateBlueReturnSnapshotUseCaseTest` — thin wrapper smoke.
- Integration: `PdoBlueReturnRepositoryTest` round-trips the snapshot
  JSON payload and verifies soft-delete + finalize transitions against
  migration 0016. Skips when `RUCARO_TEST_DB_*` is unset so CI outside
  Docker stays green.
- E2E: a synthetic "demo" individual entrepreneur is created, a Blue
  Return is generated, and the 4-page PDF is rendered to the operator
  desktop.

## 6. Implementation checklist

- [x] Migration 0016 (up + down).
- [x] `entities.is_corporate` reflected in `Entity` aggregate +
      `PdoEntityRepository`.
- [x] Domain (`BlueReturnForm`, `BlueReturnSnapshot`,
      `BlueReturnStatus`, `BlueReturnFormType`,
      `BlueReturnRepositoryInterface`,
      `BlueReturnPdfGeneratorInterface`,
      `Service\BlueReturnBuilder`).
- [x] Application (Create / Update / Finalize / Delete / Get / List /
      GenerateSnapshot).
- [x] Infrastructure (`PdoBlueReturnRepository`,
      `BlueReturnJsonSerializer`, `DompdfBlueReturnGenerator`).
- [x] HTTP controllers + route wiring (FastRoute + fallback).
- [x] Smarty templates (layout, 4 pages, common CSS).
- [x] Tests (Domain, UseCase, Integration).
- [x] DI wiring in `ContainerBootstrap`.

## 7. Open questions

- Should we expose a `POST /api/v1/blue-returns/{id}/generate-snapshot`
  endpoint that composes over trial balance + fixed assets to
  auto-build the snapshot? Deferred: out of scope for this wave.
  Callers currently feed the snapshot directly via PATCH.
- Should the front end be able to view a finalized Blue Return in
  edit mode for proof-reading? Yes — GET already returns the payload.
  We do not surface PATCH once `status=finalized`; the UI needs to
  show an informational banner.
