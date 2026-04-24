# ADR-021: Legacy → V2 Data Migration

- Status: Accepted
- Date: 2026-04-21
- Scope: One-shot migration from `rucaro_legacy` (MariaDB, 59 tables, INT auto_increment keys, UTF8MB3, epoch BIGINT timestamps) to `rucaro` (MariaDB, Phase 1–6 schema, BINARY(16) ULID keys, UTF8MB4, TIMESTAMP(6)).
- Supersedes / extends: ADR-002 (new schema), ADR-003 (crypto), ADR-020 §2.2 (legacy UI retirement matrix).

---

## 1. Decision

Ship a self-contained Symfony Console CLI —
**`scripts/import/legacy_to_v2.php`** — that drives a set of staged, per-stage-transactional importers under `src/Infrastructure/Import/LegacyImport/`. The CLI:

- runs against a live legacy DB (`--source-db`) and the new DB (`--target-db`);
- is idempotent via a persistent `legacy_id_mapping` table (`(legacy_table, legacy_id) -> BINARY(16)` ULID);
- supports `--dry-run` for SQL previews and `--apply` for writes;
- supports `--stage=all|users|entities|terms|account-titles|sub-accounts|journals|fixed-assets|fs-mappings` for partial runs;
- resets every migrated user's password to a single `--placeholder-password` (Argon2id) because the legacy password cipher is opaque and unreversible in practice.

No existing migration SQL is touched. No existing business code (Domain / Application / legacy bin) is modified. The only DDL added at runtime by the CLI is the `legacy_id_mapping` bookkeeping table.

---

## 2. Scope and non-scope

### 2.1 In scope (P0–P2)

| Legacy | → New | Count |
|---|---|---|
| `baseAccount` | `users` | 1 |
| `accountingEntity` | `entities` | 2 |
| `accountingEntityJpn` (per-period config) | `fiscal_terms` | 15 |
| `accountingLogCalcJpn` distinct `idAccountTitle` | `account_titles` | ~30 per entity |
| `accountingSubAccountTitleJpn` | `sub_account_titles` | 0 in this dataset |
| `accountingLog` + `accountingLogCalcJpn` | `journal_entries` + `journal_entry_lines` | 1613 headers, ~3.7k lines |
| `accountingLogFixedAssetsJpn` | `fixed_assets` | 0 in this dataset |
| derived from account_titles + classifier | `account_title_fs_mappings` | ~30 per entity |

### 2.2 Out of scope (P3, explicit deferral)

- All access-log / session / apply-forgot / login-miss / api-account tables (`baseAccessLog`, `basePublish`, `baseSession`, `baseToken`, `baseLoginIdLogin`, `baseLoginMiss`, `baseLoginPassword`, `baseLock`, `baseApiAccount`, `baseAccessUnknown`).
- Derivable caches: `accountingLogCalcJpn` (used as *source* for line amounts, not re-imported), `accountingFSValueJpn`, `accountingSubAccountTitleValueJpn`, `accountingDetailedAccountJpn`, `accountingEntityDepartmentFSValueJpn`, `accountingSummaryStatementJpn` — all recomputable from journals.
- Mail/bank ingestion pipelines: `accountingLogMailJpn`, `accountingLogImport*`, `accountingCash*`, `accountingBanks*`, `accountingLogHouseJpn`.
- Zero-row tables: `accountingBudgetJpn`, `accountingBlueSheetJpn`, `accountingNotesFSJpn`.
- Encrypted columns (`baseAccount.strPassword`, `accountingLogBanksAccount.blobDetail`, `accountingBlueSheetJpn.blobData`, `accountingLogMailJpn.strPassword`, `accountingFile.strPassword`) — keys unavailable in this environment; all users reset to placeholder password (see §5).

---

## 3. Column mapping

### 3.1 `baseAccount` → `users`

| Legacy | New | Transform |
|---|---|---|
| `id` INT | `id` BINARY(16) | ULID via `IdMapping` |
| `idLogin` text | `login_id` VARCHAR(64) | trim |
| `strCodeName` varchar(100) | `display_name` VARCHAR(128) | trim; fallback to `login_id` |
| `strMailPc` text | `email` VARCHAR(255) | trim (UNIQUE) |
| `strPassword` text (encrypted) | `password_hash` VARCHAR(255) | **discard**; set `PasswordHasher::hash($placeholder)` |
| `flagLock` int | `is_active` BOOLEAN | `= (flagLock == 0)` |
| `stampRegister` BIGINT | `created_at` TIMESTAMP(6) | `@epoch` → UTC `Y-m-d H:i:s.u` |
| `stampUpdate` BIGINT | `updated_at` TIMESTAMP(6) | same |

### 3.2 `accountingEntity` + `accountingEntityJpn` → `entities` + `fiscal_terms`

`entities.owner_user_id` = the single migrated user (dataset-specific; `accountingAccountEntity` authority table is not mined in this pass).

`fiscal_terms` is produced directly from `accountingEntityJpn` because the legacy `baseTerm` table is an unrelated "time bracket" artefact. Each `(idEntity, numFiscalPeriod)` becomes one fiscal term; dates are synthesised from `(numFiscalBeginningYear, numFiscalBeginningMonth, numFiscalTermMonth)`.

### 3.3 Account titles

The legacy schema has **no explicit CoA master**. The universe is the set of distinct `idAccountTitle` values in `accountingLogCalcJpn`, which are camelCase English codes (e.g. `conferenceExpense`, `shortTermLoansPayable`) 10–35 characters long.

Two problems:

1. The new `account_titles.code` is `VARCHAR(16)` → long legacy codes don't fit.
2. Category (`asset / liability / equity / revenue / expense`) and `normal_side` (`debit / credit`) are required by the schema but absent from the legacy row.

Decision:

- Assign synthetic short codes `L0001`, `L0002`, …, per entity.
- Preserve the original camelCase in `name` (human label: `"売上高 (netSales)"`).
- Classify category/side via a static map in `AccountTitleClassifier` (baked from a single inspection of `accountingFSJpn.jsonJgaapFSBS/PL`). Covers every code observed in the 1613 legacy journals; unknown codes fall back to `('expense', 'debit')`.
- Track mapping keyed on `"{entityId}:{camelCode}"` in `legacy_id_mapping` so the journal importer resolves lines cleanly.

### 3.4 `accountingLog` → `journal_entries` + `journal_entry_lines`

Critical finding: the `arrComma*` columns on `accountingLog` carry **codes only, not per-line amounts**. The per-line amounts live in `accountingLogCalcJpn` (flag `flagDebit` 1/0 + `idAccountTitle` + `numValue`). So the importer uses calcJpn as the authoritative line source and `accountingLog` for the header metadata (date, summary, fiscal term, creator).

Header mapping:

| Legacy | New | Notes |
|---|---|---|
| `stampBook` | `journal_date`, `booked_at` | `journal_date` = Asia/Tokyo DATE; `booked_at` = UTC TIMESTAMP(6) |
| `strTitle` | `summary` | trimmed |
| `idLog` + `numFiscalPeriod` | business key | IdMapping key `"{entityId}-{period}-{idLog}"` |
| `numValue` | (ignored) | replaced by `max(debit_sum, credit_sum)` from calcJpn |
| `flagRemove=1` rows | skipped | soft-deleted in legacy |
| `status` | constant `'posted'` | legacy journals were always committed |
| `source` | constant `'manual'` | legacy pipeline labelling is not preserved |
| `created_by` | first migrated user | matches single-user dataset |

Line mapping (one line per calcJpn row for that `(idLog, entityId, period)`):

| Legacy | New |
|---|---|
| `flagDebit` 1/0 | `side` `'debit'/'credit'` |
| `idAccountTitle` | `account_title_id` (via IdMapping) |
| `numValue` | `amount` |
| `numValueConsumptionTax` | `tax_amount` |
| `numRateConsumptionTax` | `tax_rate_percent` |
| `flagRateConsumptionTaxReduced` | `is_tax_reduced` |

Lines whose legacy code is `else` or whose account title has no IdMapping entry (i.e. never actually referenced) are silently skipped — this matches the legacy runtime, which treated `else` as a sentinel placeholder.

### 3.5 FS mappings

`account_title_fs_mappings` is derived directly from the classifier, not from the legacy `accountingFSJpn` tree, because (a) the tree is 3-level nested JSON per term, (b) it is repeated 15 times, (c) a single category→section projection is already sufficient to render BS/PL:

| Category | FS kind | Section code |
|---|---|---|
| `asset` | bs | `current_asset` |
| `liability` | bs | `current_liability` |
| `equity` | bs | `retained_earnings` |
| `revenue` (`miscellaneous*` / `interestAndDiscountReceived`) | pl | `non_operating_revenue` |
| `revenue` (other) | pl | `operating_revenue` |
| `expense` (`corporateInhabitantAndEnterpriseTax`) | pl | `income_tax` |
| `expense` (other) | pl | `sga` |

A future iteration can replace this with a full legacy-tree walk once a dedicated legacy parser exists.

### 3.6 `arrComma*` decomposition (reference)

Although we ultimately use `accountingLogCalcJpn` for line amounts, the `LegacyValueConverter::splitCommaArray()` helper exists to decompose legacy CSV columns when/if a fallback importer needs to operate from `accountingLog` alone (e.g. if calcJpn is corrupted):

```
",cash,salaries,"   => ["cash", "salaries"]
",,"                 => []
" , foo ,  , bar ,"  => ["foo", "bar"]
```

Empty tokens and whitespace-only tokens are discarded.

---

## 4. Execution plan

### 4.1 Default execution (from repo root)

```bash
# Preview without writing
php scripts/import/legacy_to_v2.php --dry-run \
    --source-db=rucaro_legacy --target-db=rucaro \
    --db-host=127.0.0.1 --db-port=3307 \
    --db-user=root --db-password=root \
    --placeholder-password='ChangeMe0!'

# Apply
php scripts/import/legacy_to_v2.php --apply \
    --source-db=rucaro_legacy --target-db=rucaro \
    --db-host=127.0.0.1 --db-port=3307 \
    --db-user=root --db-password=root \
    --placeholder-password='ChangeMe0!'
```

Inside the app container the DB host is `db` and the internal port is `3306`.

### 4.2 Stages

Stages run in this order. Each is transactional: a stage failure rolls back only that stage, not previous commits.

```
users → entities → terms → account-titles → sub-accounts → journals → fixed-assets → fs-mappings
```

Running a single stage (`--stage=journals`) requires that upstream stages already populated `legacy_id_mapping`.

### 4.3 Re-runs and rollback

`legacy_id_mapping` is INSERT-IGNORE; re-running without `--truncate-target` is safe but will fail on existing UNIQUE rows (e.g. `users.login_id`). The standard rollback path is:

```bash
php scripts/import/legacy_to_v2.php --apply --truncate-target \
    --source-db=rucaro_legacy --target-db=rucaro \
    --placeholder-password='ChangeMe0!'
```

`--truncate-target` deletes only rows that appear in `legacy_id_mapping`. Dev/demo rows seeded by `scripts/migrate/` or by test fixtures are preserved.

Manual rollback of just the mapping table:

```sql
DELETE FROM account_title_fs_mappings
 WHERE id IN (SELECT new_ulid FROM legacy_id_mapping);
DELETE FROM journal_entry_lines
 WHERE entry_id IN (SELECT new_ulid FROM legacy_id_mapping WHERE legacy_table='accountingLog');
DELETE FROM journal_entries
 WHERE id IN (SELECT new_ulid FROM legacy_id_mapping WHERE legacy_table='accountingLog');
-- ... repeat per table in reverse FK order ...
DROP TABLE legacy_id_mapping;
```

---

## 5. Security / crypto consequences

1. **Passwords are reset.** Legacy used an unknown hash/cipher; we cannot rebuild the hashes. Every migrated user lands with an Argon2id hash of the CLI-supplied `--placeholder-password`. The operator **must** force-reset passwords (or trigger `password_reset_required` once that flag exists) immediately after cut-over.
2. **Encrypted blobs (Banks, BlueSheet, Mail IMAP) are skipped.** These require ADR-003 key material we do not have in this environment; partial re-encryption would silently corrupt data. Follow-up work: ADR-022.
3. **`baseLoginMiss` is not migrated.** It contained plaintext failed-login passwords; re-importing those would re-surface a security hazard.

---

## 6. Risks / known limitations

- **Category heuristics for unknown account titles**: any idAccountTitle not in the classifier map falls back to `expense/debit`. Monitor the `account_titles` sanity check after import; add entries to `AccountTitleClassifier::MAP` if new codes appear.
- **Debit/credit imbalance**: legacy journals occasionally had tiny Debit/Credit imbalances from tax-split rounding. We set `total_amount = max(debit_sum, credit_sum)` to preserve the non-negative check constraint; the new trial-balance endpoint will still reconcile because both sides are stored faithfully per line.
- **Single owning user**: `entities.owner_user_id` is set to the first migrated user. Multi-user datasets will need to be extended by mining `accountingAccountEntity` in a follow-up iteration.
- **Timezone**: `journal_date` is computed in Asia/Tokyo; `booked_at` is UTC. This matches the rest of the new stack (ADR-002 §2.9).
- **Fixed assets**: the source table (`accountingLogFixedAssetsJpn`) is empty in this dataset. The importer is implemented and tested, but has had no real-data run.

---

## 7. Verification

Post-apply checks:

1. `SELECT COUNT(*) FROM users` → 1 new row (plus any pre-existing dev users).
2. `SELECT COUNT(*) FROM entities` → 2 new rows.
3. `SELECT COUNT(*) FROM fiscal_terms` → 15 new rows.
4. `SELECT COUNT(*) FROM journal_entries` → 1500+ (only non-deleted legacy rows with calcJpn lines).
5. `SELECT SUM(amount) FROM journal_entry_lines WHERE side='debit' GROUP BY entry_id` — every group should match the matching credit sum (up to legacy rounding drift).
6. `GET /api/v1/entities`, `/api/v1/journals?...`, `/api/v1/financial-statements?kind=pl&format=pdf` — sanity responses.

Outputs stored for inspection:

- `~/OneDrive/デスクトップ/rucaro-out/legacy-migrated-pl.pdf`
- `~/OneDrive/デスクトップ/rucaro-out/legacy-migrated-bs.pdf`
- `~/OneDrive/デスクトップ/rucaro-out/legacy-migrated-ledger.pdf`

---

## 8. Files

- `scripts/import/legacy_to_v2.php` — CLI entry point.
- `src/Infrastructure/Import/LegacyImport/`
  - `LegacyToV2Command.php` — Symfony Console command.
  - `ImportOrchestrator.php` — stage driver.
  - `IdMapping.php` — INT ↔ ULID persistent map.
  - `LegacyValueConverter.php` — pure conversion helpers.
  - `AccountTitleClassifier.php` — camelCode → (category, side, label) map.
  - `LegacyUserImporter.php`, `LegacyEntityImporter.php`, `LegacyFiscalTermImporter.php`, `LegacyAccountTitleImporter.php`, `LegacySubAccountTitleImporter.php`, `LegacyJournalImporter.php`, `LegacyFixedAssetImporter.php`, `LegacyFsMappingImporter.php` — per-table importers.
  - `ImportReport.php` — per-stage counters.
- `tests/Unit/Infrastructure/Import/LegacyImport/` — conversion-logic unit tests.
