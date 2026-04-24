# ADR-011 — Phase 6 Wave 6-C: Port of the J-GAAP General Ledger (総勘定元帳)

* Status: Accepted
* Date: 2026-04-21
* Supersedes: the legacy `Ledger.php` + `LedgerOutput.php` pair in `back/class/else/plugin/accounting/jpn/` for the general-ledger view.
* Relates to: ADR-005 (layered architecture), ADR-006 (ports & adapters), ADR-007 (strangler-fig migration), ADR-009 (Wave 6-A FS port), ADR-010 (Wave 6-B CS port).

## 1. Context

The legacy RUCARO Accounting codebase renders a general ledger (総勘定元帳) via
two PHP classes:

| File | LOC | Role |
| --- | --- | --- |
| `back/class/else/plugin/accounting/jpn/Ledger.php`       | 1,102 | Entry + navi / filter glue (account, sub-account, department) |
| `back/class/else/plugin/accounting/jpn/LedgerOutput.php` |   ~700 | Print + CSV output |

Both rely on an intermediate table `accountingLogCalcJpn` that the
legacy calculation loop rewrites on every journal save: each row already
carries `numBalance`, `numBalanceSubAccount`, `numBalanceDepartment`,
`numBalanceDepartmentSubAccount`, the counter account (`idAccountTitleContra`
with a sentinel value `'else'` for multi-line journals), and the legacy
`flagFS` discriminator that switched the query between "fiscal term" and
"calendar month" modes.

The Phase 6 target architecture (ADR-005 / ADR-006) instead treats the
general ledger as a **read model** built on demand from
`journal_entries` + `journal_entry_lines`, with zero mutable aggregation
state in the DB. Wave 6-A (FS / BS / PL, ADR-009) and Wave 6-B (CS,
ADR-010) already established the pattern:

1. A Domain aggregate with readonly value objects.
2. A Domain port for the projection (`*QueryInterface`).
3. An Application UseCase that composes the port with any ancillary
   masters (mappings, snapshots, opening balances, …) and returns the
   aggregate unchanged.
4. An Infrastructure adapter (`Pdo*QueryService`) that issues one or two
   SELECT statements, and a second adapter (`Dompdf*Generator`) that
   turns the aggregate into PDF via Smarty.

Wave 6-C lifts the general ledger onto that architecture.

## 2. Decision

Introduce a `Ledger` aggregate, a `LedgerQueryInterface` port, a thin
`QueryLedgerUseCase` that layers opening balances on top of the raw
projection, and dedicated PDO / dompdf adapters.

### 2.1 Domain

```
Ledger           entityId, fiscalTermId, fromDate, toDate, currencyCode,
                 books: LedgerBook[], generatedAt
LedgerBook       accountTitleId, accountTitleCode, accountTitleName,
                 normalSide, openingBalance, entries: LedgerEntry[],
                 debitTotal, creditTotal, closingBalance
LedgerEntry      journalEntryId, journalEntryLineId, entryDate,
                 summary, memo, counterAccountCode, counterAccountName,
                 debitAmount, creditAmount, runningBalance
```

All three are `final readonly class`. `LedgerBook::compute()` is the sole
factory — given an opening balance and a pre-sorted list of raw entries,
it emits the entries with final running balances and the closing balance,
picking sign semantics from `normalSide`:

* Debit-normal accounts: `balance = opening + Σ(debit − credit)`.
* Credit-normal accounts: `balance = opening + Σ(credit − debit)`.

### 2.2 Counter account resolution

The legacy `Ledger.php::_updateSearch()` special-cased the multi-line
journal case by writing `idAccountTitleContra = 'else'` and rendering
the sentinel as `$vars['varsItem']['strSundries']` ("諸口"). We preserve
that behaviour at the query boundary:

| # of OTHER lines in the same entry | Counter fields |
| --- | --- |
| 0 (degenerate — should not happen) | `('', '')` |
| 1 | `(code, name)` of the other account |
| ≥ 2 | `('', '諸口')` via `LedgerEntry::COUNTER_SUNDRIES` |

The constant `LedgerEntry::COUNTER_SUNDRIES` is the single source of
truth so renderers and test fakes stay in sync.

### 2.3 Opening balances

The ledger view needs a 期首繰越 (opening balance) per account so running
balances roll forward correctly. Rather than recompute it from the prior
fiscal term every request, we materialise it in a new master table:

```sql
-- scripts/migrate/0010_opening_balances.sql
CREATE TABLE opening_balances (
    id, entity_id, fiscal_term_id, account_title_id, amount, currency_code,
    created_at, updated_at,
    UNIQUE (entity_id, fiscal_term_id, account_title_id)
);
```

The table ships empty. Wave 6-C wires the default
`OpeningBalanceRepositoryInterface` binding to
`ZeroOpeningBalanceRepository`, which returns `0.0000` for every account.
A `PdoOpeningBalanceRepository` is present so a later wave can flip the
binding without renaming the interface. TODO: close-fiscal-term workflow
(snapshot period-end balances into `opening_balances` for BS accounts of
the subsequent term).

### 2.4 Application layer

```
QueryLedgerUseCase(
    LedgerQueryInterface,
    OpeningBalanceRepositoryInterface,
    ClockInterface,
)
```

Input: `{entityId, fiscalTermId, accountTitleId?, fromDate, toDate, currencyCode?}`.

The use case requires explicit `fromDate` / `toDate`. The HTTP layer
resolves those from the fiscal term's `start_date` / `end_date` when
query params are missing so the Application layer has a single, clean
contract. The use case:

1. Calls `LedgerQueryInterface::query()` to get a raw projection. The
   projection carries placeholder opening balances (= 0); this is
   intentional — the port is explicitly allowed to skip that step.
2. For every book, fetches the real opening balance via
   `OpeningBalanceRepositoryInterface` and rebuilds the book with
   `LedgerBook::compute()`, producing final running balances.

The result is wrapped in a minimal `QueryLedgerUseCaseOutput` envelope
for parity with the other UseCases.

### 2.5 Infrastructure

Two SELECT statements, neither of them surprising:

1. Account metadata (all accounts of the entity, optionally filtered to
   one by id) — ordered by code ASC. Empty books are emitted for accounts
   with no movement so the HTTP "all accounts" view is stable.
2. Journal lines: `journal_entry_lines ⨝ journal_entries` restricted to
   `status IN ('posted','approved') AND deleted_at IS NULL AND journal_date BETWEEN :from AND :to`,
   ordered by `(journal_date, entry_id, line_no)`. Counter accounts are
   resolved in PHP by grouping lines by `entry_id`.

The SELECT never writes; the adapter is stateless and safe under
concurrent requests.

### 2.6 Rendering (Smarty + dompdf)

`DompdfLedgerGenerator` mirrors the FS renderer from Wave 6-A:

* Templates live under `storage/templates/ledger/` (`layout.html.tpl`,
  `ledger-common.css.tpl`, `ledger.html.tpl`).
* IPAex Gothic TTF is registered for all four weight/style variants so
  `font-family: ipaexg` resolves whether or not Smarty's HTML happens to
  request bold inside `<th>`.
* Each book renders as a dedicated HTML block with an opening-balance
  row ("前期繰越"), the period's journal rows, and a closing summary row
  ("期中合計 / 期末残高"). Multiple books are separated by
  `page-break-before: always` so the PDF opens one book per page.

### 2.7 HTTP

```
GET /api/v1/ledger
    ?entityId= (required, ULID)
    &fiscalTermId= (required, ULID)
    &accountTitleId= (optional, ULID — single book)
    &from= (optional, YYYY-MM-DD — defaults to fiscal term start)
    &to= (optional, YYYY-MM-DD — defaults to fiscal term end)
    &format= (optional, json|pdf; default json)
```

JSON response follows the standard envelope; PDF response sets
`Content-Disposition: attachment; filename="ledger.pdf"`.

## 3. Legacy ↔ new correspondence

| Legacy concept | Wave 6-C equivalent |
| --- | --- |
| `Ledger.php::_getSearch()` raw SELECT on `accountingLogCalcJpn` | `PdoLedgerQueryService::query()` |
| `flagFiscalPeriod` (`f1` / month) | HTTP `from` / `to` query params (fiscal term bounds resolved server-side) |
| `idDepartment` filter | Out of scope — reintroduced when the Departments aggregate lands |
| `idSubAccountTitle` filter | Out of scope — belongs to a future SubAccount port |
| `numBalance` / `numBalanceSubAccount` … | `LedgerBook::closingBalance` + per-entry `runningBalance` |
| `numPrev` (prior period carry-forward) | `LedgerBook::openingBalance` via `OpeningBalanceRepositoryInterface` |
| Counter `idAccountTitleContra = 'else'` | `LedgerEntry::COUNTER_SUNDRIES` ("諸口") |
| `LedgerOutput.php` print/CSV | `DompdfLedgerGenerator::render()` + `LedgerJsonSerializer::toArray()` |

## 4. Implementation checklist

- [x] Domain: `Ledger`, `LedgerBook`, `LedgerEntry`, `LedgerQueryInterface`, `OpeningBalanceRepositoryInterface`, `LedgerGeneratorInterface`.
- [x] Application: `QueryLedgerUseCase` + input/output envelope.
- [x] Infrastructure: `PdoLedgerQueryService`, `ZeroOpeningBalanceRepository`, `PdoOpeningBalanceRepository`, `LedgerJsonSerializer`, `DompdfLedgerGenerator`.
- [x] HTTP: `GetLedgerController` (`GET /api/v1/ledger`).
- [x] Routing + DI wired in `ApiKernel` and `ContainerBootstrap`.
- [x] Migration `0010_opening_balances.sql` (+ `down.sql`).
- [x] Smarty templates `storage/templates/ledger/{layout,ledger,ledger-common.css}.tpl`.
- [x] Unit tests: `LedgerTest`, `LedgerBookTest`, `LedgerEntryTest`, `QueryLedgerUseCaseTest` + in-memory fakes.
- [x] Integration test: `PdoLedgerQueryServiceTest` (skips cleanly without DB).
- [ ] TODO (post-Wave 6-C): `SubAccount` filter, `Department` filter, close-fiscal-term workflow that populates `opening_balances` for BS accounts.

## 5. Risks & alternatives considered

* **Materialised ledger table (legacy `accountingLogCalcJpn`)**: rejected.
  Mutable aggregation state is the root cause of the legacy code's
  complexity — every journal mutation had to rewrite tens of rows, and a
  bug in that path would silently corrupt balance columns. The
  read-model approach lets us compute balances on demand with a
  well-indexed SELECT; at today's traffic levels the cost is immaterial
  and can be revisited with caching only if profiling warrants it.
* **Put opening balance on `account_titles`**: rejected. The opening
  balance is per `(entity, fiscal_term, account)` — it changes each
  term. A dedicated table makes the relationship explicit and lets a
  close-fiscal-term workflow UPSERT rows in one transaction.
* **Compute counter accounts in SQL (GROUP_CONCAT)**: rejected. The
  `諸口` sentinel needs to appear only when there are ≥ 2 other lines,
  which is simpler to express in PHP than in portable SQL. The per-entry
  grouping in `PdoLedgerQueryService` is O(n) over the returned line set.
