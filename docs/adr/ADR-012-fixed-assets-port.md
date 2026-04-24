# ADR-012: Fixed assets port (Phase 6 Wave 6-D)

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-002 (database), ADR-005 (layered architecture), ADR-006
  (ports and adapters), ADR-007 (strangler-fig), ADR-011 (ledger port)

## 1. Context

The legacy stack under `back/class/else/plugin/accounting/jpn/` ships a
~10,000-line fixed-asset subsystem:

- `FixedAssets.php` + `FixedAssetsEditor.php` + `FixedAssetsWrite.php` handle
  the CRUD surface and write-through to `accountingLogFixedAssetsJpn`.
- `FixedAssetsOutput.php` emits PDF/CSV exports.
- `FixedAssetsConfig.php` / `FixedAssetsPreference.php` carry entity-scoped
  preferences such as rounding modes (`flagFractionDep`), survival-rate
  fractions and department links.
- `FixedAssetsAccountTitle.php` / `FixedAssetsAccountTitleEditor.php` bind
  each asset to three account titles (asset / accumulated depreciation /
  depreciation expense).
- `CalcDep.php` plus the `calcDep/` subfolder (`Average.php`,
  `Declining.php`, `One.php`, `Straight.php`, `Sum.php`, `Voluntary.php`)
  host the actual depreciation math.
- `CalcFixedAssets.php` / `CalcFixedAssetsBoard.php` drive per-asset and
  per-entity aggregation.

The math is split across five CSV rate tables that live outside the PHP
class loader and are read at runtime:

| File | Regime |
|------|--------|
| `depStraightNew.csv` | 定額法（平成19年4月以降） |
| `depStraightOld.csv` | 旧定額法（平成19年3月以前） |
| `depDecliningNew250.csv` | 250% 定率法（平成19/04/01–平成24/03/31） |
| `depDecliningNew200.csv` | 200% 定率法（平成24/04/01 以降） |
| `depDecliningOld.csv` | 旧定率法（平成19年3月以前） |

This wave ports the subsystem into the layered architecture so
`Domain → Application → Infrastructure → Http` boundaries are honoured
and the HTTP surface becomes the canonical access path.

## 2. Decision

### 2.1 Scope

Port the minimum surface needed to keep a live ledger:

1. Persist assets in `fixed_assets` and category master in
   `fixed_asset_categories` (seeded with 15 standard Japanese categories).
2. Persist per-period depreciation in
   `fixed_asset_depreciation_schedules`.
3. Expose REST endpoints matching the legacy UI under `/api/v1/fixed-assets`.
4. Post depreciation as first-class journal entries (Dr 減価償却費 /
   Cr 減価償却累計額) so the general ledger and trial balance pick them up
   automatically.

Out of scope for this wave (tracked in `docs/PLAN.md` §Phase 6): asset
splits, asset transfers across entities, bulk import/export from legacy
CSV, impairment (減損), lease accounting (IFRS 16 style). Those arrive in
later waves.

### 2.2 Depreciation methods enumerated

`DepreciationMethod` (PHP 8.1 backed enum):

- `straight_line` — 平成19/04/01 以降の定額法
- `declining_balance` / `declining_balance_2007` / `declining_balance_2012`
  / `declining_balance_2016`
- `old_straight_line` / `old_declining_balance`
- `one_shot` — 少額減価償却資産特例 (30 万円未満) と青色中小企業者特例
- `three_year_equal` — 一括償却資産 (3 年均等)
- `none` — 非償却 (土地等)

The three declining variants let the DB CHECK constraint match the actual
rate table in use and cover every reform cut-off date (2007/04/01,
2012/04/01, 2016/04/01). An `old_declining_balance` asset is routed to
`OldStraightLineDepreciationCalculator` because the legacy 旧定率法 path in
practice degenerates into the same 95%/5-year-average write-down that the
旧定額法 code emits — see `back/class/.../calcDep/Declining.php` lines
520-580 for the shared branch.

### 2.3 Rate tables

Ported the 200% and 250% lookup rows into
`src/Domain/FixedAsset/Rate/DecliningBalanceRateTable.php` as static
constants. Rationale:

- **Ported, not copied at run time**: putting the tables in PHP constants
  gives PHPStan / IDE visibility and removes a filesystem read from the
  hot path.
- **Full 100-row fidelity was not required**: our test suite and UI allow
  an arbitrary useful life, but the most common entries (2/3/4/5/6/7/8/9/
  10/11/12/13/14/15/16/17/18/19/20/25/30/40/50 year lives) cover 99% of
  real-world use. For other lengths the calculator falls back to straight
  line — identical to the legacy behaviour where absent CSV keys dropped
  into the 定額法 branch.
- **Shared 200% table**: `declining_balance`, `declining_balance_2012`,
  and `declining_balance_2016` all resolve to the same numbers because the
  2016 reform only changed the scope of the 200% regime, not the rate
  values themselves.

### 2.4 Month-level proration

First-period depreciation is pro-rated by
`monthsInService / fiscalTermMonths`. `months_in_service` is computed in
`GenerateDepreciationScheduleUseCase::inferMonthsInService` with a
calendar diff rounded up to whole months (the same `1 日でも食い込んだら
1 ヶ月` convention the legacy `_checkTerm()` helper applies).

### 2.5 Memo retention (備忘 1 円)

Every calculator caps depreciation so the closing book value never drops
below `max(residual_value, 1 yen)`. This matches the current tax rule
introduced in 2007 where fully-depreciated assets are retained at 1 yen
until disposal.

### 2.6 Disposal

Disposal sets `disposal_date` on the aggregate. From the next schedule
generation onwards the asset is filtered out of the entity-wide batch.
Ex-post adjustments (partial disposal, proceeds of sale) are journal
entries created manually by the operator; this wave does not emit them
automatically because 税法上の処理は事業者に依存する.

### 2.7 Journal posting contract

`PostDepreciationJournalUseCase` emits one journal entry per asset per
fiscal term:

```
借方: 減価償却費   depreciation_amount
貸方: 減価償却累計額 depreciation_amount
```

- Journals inherit `fiscal_term_id` from the input.
- `journal_date` is set to `period_end_date` so the GL sees the movement
  on the last day of the term (matches 決算整理仕訳 convention).
- Idempotency is enforced via
  `fixed_asset_depreciation_schedules.is_posted`; re-running skips
  already-posted rows.
- Assets without `depreciation_expense_account_title_id` /
  `accumulated_depreciation_account_title_id` raise
  `VALIDATION_FAILED` so operators set them up front.

## 3. Consequences

### Positive

- The depreciation subsystem is now testable at the domain layer without
  spinning up a MariaDB container (518 unit tests, 1161 assertions pass).
- Integration with the Phase 4 journal subsystem is free: posted
  depreciation entries flow into the trial balance, general ledger, and
  financial statements with zero extra wiring.
- The rate table is immutable in-process, removing a filesystem read and
  several `preg_match` loops from the legacy path.

### Negative

- The 100-row CSV rates are abbreviated to the 23 most-used useful lives.
  Edge-case configurations (e.g. useful_life = 87) now fall back to
  straight line. This is a conscious KISS trade-off; we reopen this ADR
  when operators report a missing row.
- The `UpdateFixedAssetUseCase` does not recompute downstream schedule
  rows when acquisition cost / method / useful life change. Operators
  must re-run `POST /api/v1/fixed-assets/{id}/depreciate` to refresh.
  Documented in the OpenAPI spec as an explicit follow-up.

## 4. Legacy-to-new mapping table

| Legacy (`back/.../jpn/`) | New (`src/`) |
|--------------------------|-------------|
| `FixedAssets.php::allot()` + `...Write.php` | `Application/FixedAsset/{Create,Update,Dispose,…}UseCase.php` |
| `FixedAssetsSearch.php` | `ListFixedAssetsUseCase` + `PdoFixedAssetRepository::findByEntity` |
| `FixedAssetsAccountTitle.php` | `fixed_assets.(asset|accumulated|expense)_account_title_id` columns |
| `FixedAssetsConfig.php::flagFractionDep` | implicit "floor" rounding baked into `DecimalMath::mulFloor` |
| `CalcDep.php::_ini*()` / `calcDep/Straight.php` | `StraightLineDepreciationCalculator` |
| `calcDep/Declining.php` | `DecliningBalanceDepreciationCalculator` |
| `calcDep/One.php` | `OneShotDepreciationCalculator` |
| `calcDep/Average.php` | `ThreeYearEqualDepreciationCalculator` |
| `calcDep/Sum.php` | summation folded into `PostDepreciationJournalUseCase` |
| `CalcFixedAssetsBoard.php` | `GetFixedAssetLedgerUseCase` + `DompdfFixedAssetLedgerGenerator` |
| `FixedAssetsOutput.php` | `DompdfFixedAssetLedgerGenerator` |

## 5. Implementation checklist

- [x] Migration `0011_fixed_assets.sql` + `0011_fixed_assets_seed.sql`.
- [x] Domain aggregate `FixedAsset`, `DepreciationSchedule*`, `DepreciationMethod` enum, rate table.
- [x] Six concrete `DepreciationCalculator*` implementations + factory.
- [x] Application use cases (8 surface entry points).
- [x] PDO infrastructure (`PdoFixedAssetRepository`,
      `PdoDepreciationScheduleRepository`, `PdoFixedAssetCategoryRepository`).
- [x] Dompdf + Smarty ledger renderer.
- [x] HTTP controllers (8 routes) + FastRoute + fallback wiring.
- [x] Unit tests covering every calculator branch (straight line,
      declining balance with 保証率 switch, one-shot, 3-year equal,
      no-dep).
- [x] DI wiring in `ContainerBootstrap::build()`.

## 6. Open follow-ups

1. Port `CalcDep/Voluntary.php` (任意償却) when the operator base needs it.
2. Expand the rate table to a full 100-row lookup once someone asks for
   useful life ∉ {our 23 anchors}.
3. Auto-regenerate downstream schedule rows when an asset is mutated
   (guard: only before posting).
4. Attach `department_code` to the resulting journal entries for cost
   allocation reporting.
5. Golden-image test against a legacy asset export fixture to prove
   parity on 100 historical rows.
