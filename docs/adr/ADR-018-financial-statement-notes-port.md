# ADR-018: Financial Statement Notes port (Phase 6 Wave 6-H-3)

- Status: Accepted
- Date: 2026-04-21
- Deciders: masudagreen
- Supersedes: none
- Related: ADR-005 (layered architecture), ADR-006 (ports and adapters),
  ADR-007 (strangler-fig migration), ADR-009 (FS port), ADR-013 (cash
  plan / BEP port), ADR-015 (budget port), ADR-017 (statement of changes
  in equity port)

## 1. Context

Waves 6-H-1 (青色申告決算書) and 6-H-2 (株主資本等変動計算書) ported the
two remaining settlement documents that sit alongside BS/PL/CS. The
final piece required by 会社法 施行規則 Article 101〜129 is the **注記表
(Financial Statement Notes)** — a free-text appendix where the entity
discloses policies, encumbrances, related-party transactions, contingent
liabilities, etc.

The legacy implementation lived in three files:

- `back/class/else/plugin/accounting/jpn/NotesFS.php` — plugin entry
  point that dispatches to editor / output handlers.
- `back/class/else/plugin/accounting/jpn/NotesFSEditor.php` — the
  POST / save pipeline that wrote one row per note to
  `accountingNotesFS*`, with the HTML body stored verbatim.
- `back/class/else/plugin/accounting/jpn/NotesFSOutput.php` — the
  render path that emitted a single HTML lump with embedded inline
  styles.

Concrete problems with that shape:

1. **No category axis.** Every note was a flat string. The user had no
   way to say "this paragraph is a 会計方針 vs this one is a 偶発債務",
   so the printed 注記表 flipped between random sections on every
   re-render.
2. **No reusable starter text.** Each fresh 決算 required the
   accountant to re-type the standard "棚卸資産の評価は総平均法…"
   boilerplate.
3. **Output was entangled with the plugin dispatcher.** You could not
   invoke "render just the notes" from another surface (a scheduler,
   email attachment, unit test). The dispatcher expected a full
   web-session context.
4. **No sort control.** The rendered order was insertion order from the
   editor, which meant re-ordering required a full re-type.

## 2. Decision

### 2.1 Scope

Port the minimum surface needed to edit, order, and render the 注記表
end-to-end:

1. Persist notes in two tables:
   - `fs_note_templates` — ship-time library of ~16 standard Japanese
     disclosure boilerplates, keyed by a stable `code`.
   - `fs_notes` — per-(entity, fiscal term) rows; each can either be a
     plain custom entry or carry a `template_code` linking it back to
     the template it was cloned from.
2. Expose REST endpoints:
   - `GET  /api/v1/fs-note-templates`
   - `GET  /api/v1/fs-notes?entityId=&fiscalTermId=&onlyActive=`
   - `GET  /api/v1/fs-notes/{id}`
   - `POST /api/v1/fs-notes`
   - `POST /api/v1/fs-notes/bulk-import` — body: `{entityId, fiscalTermId, templateCodes: []}`
   - `PATCH /api/v1/fs-notes/{id}` (partial, PATCH semantics)
   - `DELETE /api/v1/fs-notes/{id}`
   - `POST /api/v1/fs-notes/reorder` — body: `{entityId, fiscalTermId, orderedIds: []}`
   - `GET /api/v1/fs-notes/export?entityId=&fiscalTermId=&format=json|pdf`
3. Render the 注記表 through dompdf + Smarty, reusing the chroot /
   IPAex-font registration already present in `DompdfFinancialStatementGenerator`.

Out of scope for this wave:

- 個別注記表 vs 連結注記表 distinction (Rucaro only targets 単体 for now).
- Rich text (Markdown, image attachments). The body is plain text
  with preserved whitespace; HTML escaping happens in the Smarty layer.
- Per-note approval workflow. The 注記表 ships together with its 決算書;
  approval gating lives on the FS aggregate (ADR-009) rather than per-note.

### 2.2 Template + entity override (two-tier model)

Templates are **read-only** at runtime. `fs_note_templates` is seeded by
`0018_fs_notes_seed.sql` and referenced by code (`AP_INVENTORY`,
`BS_PLEDGED_ASSETS`, …). When the user imports a template via
`bulk-import`, the UseCase inserts a **copy** of the template's label and
body into `fs_notes` with `template_code` set. From that point on the
note is free to diverge — editing it never mutates the template.

This is cheaper than the "write-through via override table" pattern we
considered:

- 単一の `fs_notes` query returns the final body without a LEFT JOIN on
  templates, keeping PDF rendering O(n).
- Template updates (on future upgrades) never silently mutate notes in
  closed fiscal terms, matching the 監査 expectation that a finalized
  決算書 does not change retroactively.

### 2.3 Bulk-import idempotency

`BulkImportFsNotesFromTemplatesUseCase` skips any template whose `code`
already has at least one note in the target (entity, fiscal_term). This
lets the UI re-issue "apply these 6 starter templates" after a partial
failure without producing 12 rows on the second call.

The countermeasure is `FsNoteRepositoryInterface::countByTemplateCode()`,
which the use case calls once per candidate template before inserting.
The trade-off (N round-trips per import call) is acceptable because the
templates are ~16 entries and the call is rare (once per fiscal term).

### 2.4 Categorisation and display order

`FsNoteCategory` is a sealed enum with seven cases:

| Value                  | Label (ja)                                   |
|------------------------|----------------------------------------------|
| accounting_policy      | 重要な会計方針                                |
| balance_sheet_notes    | 貸借対照表に関する注記                         |
| pl_notes               | 損益計算書に関する注記                         |
| ss_notes               | 株主資本等変動計算書に関する注記                |
| related_party          | 関連当事者との取引に関する注記                  |
| contingent_liability   | 偶発債務に関する注記                          |
| other                  | その他の注記                                 |

Enum values match the MariaDB `chk_fn__category` CHECK constraint so a
CSV import bug can't smuggle an unknown category past the storage layer.
Display order on the PDF is fixed by `displayOrder()` and does NOT
depend on user-provided sort — the statutory order is regulatory, not
cosmetic.

### 2.5 REST / JSON envelope

All endpoints reuse `EnvelopeResponse` (`{success, data, error, meta}`)
and the `AuthenticateBearer` middleware; no per-route auth is
introduced. PDF export reuses the raw-body path used by the other
Phase 6 PDF exporters (`Content-Disposition: attachment`).

### 2.6 Migration plumbing

The migration number is **0018** because 0016 belongs to 6-H-1 BlueReturn
and 0017 belongs to 6-H-2 StatementOfChangesInEquity. The down
migration drops `fs_notes` before `fs_note_templates` so nothing
dangles on a failed up.

## 3. Consequences

### Pros

- Statutory-ordered, categorised 注記表 finally works without the
  legacy plugin dispatcher.
- Templates give fresh 決算 a starting point in one click.
- Finalized terms stay stable because template changes never propagate.
- PDF generator is decoupled from the plugin runtime and invokable from
  any UseCase.

### Cons / trade-offs

- Editing a note *after* an import loses the automatic update path if
  we ever ship a revised boilerplate. We treat this as a feature, not a
  bug: accountants expect hand-edited text to be sacred.
- `bulk-import` is O(N-templates) round-trips today. If future imports
  approach 50+ templates per call we will revisit with a single
  `INSERT ... ON DUPLICATE KEY UPDATE` guarded by a composite unique
  key on (entity_id, fiscal_term_id, template_code).

## 4. Legacy ↔ new mapping

| Legacy                                                      | Port                                                                               |
|-------------------------------------------------------------|------------------------------------------------------------------------------------|
| `jpn/NotesFS.php` dispatch                                  | `Rucaro\Http\ApiKernel` routes + controllers under `Http\Controller\FinancialStatementNotes` |
| `jpn/NotesFSEditor.php` save pipeline                        | `CreateFsNoteUseCase` / `UpdateFsNoteUseCase` / `DeleteFsNoteUseCase`              |
| `jpn/NotesFSEditor.php` "insert default text" branch         | `BulkImportFsNotesFromTemplatesUseCase` + `fs_note_templates` seed                 |
| `jpn/NotesFSOutput.php` HTML render                          | `DompdfFsNotesGenerator` + `storage/templates/fs_notes/*.tpl`                      |
| `accountingNotesFSJpn` (flat text per entity)                | `fs_notes` (categorised, sort-ordered, template-linkable)                          |
| (none) — flat ordering                                      | `ReorderFsNotesUseCase` with explicit `orderedIds`                                 |
| (none) — inline HTML risked XSS                             | Smarty `escape=true` default + body stored as plain text                           |

## 5. Testing strategy

- Unit tests (InMemory fakes) for every UseCase: create / update /
  delete / reorder / bulk-import (idempotent path, unknown-code path,
  empty-list rejection).
- Domain test for `FinancialStatementNote` invariants (label length,
  empty body, negative sort, oversized template code).
- Integration test hits MariaDB through `MigrationRunner`, seeds the
  `fs_note_templates` via the bundled SQL, round-trips a note, and
  exercises `countByTemplateCode` + `findByEntityAndTerm(onlyActive=true)`.
- PHPStan level 6 passes clean (no ignores / no baseline additions).

## 6. Security notes

- All endpoints sit behind `AuthenticateBearer`.
- Body is stored as plain text and rendered through Smarty's default
  `escape_html = true`, closing the XSS regression surface that existed
  in the legacy inline-HTML path.
- SQL is parameterized; no concat-on-user-input paths.
- No PII / payment data is stored; the free-text field is accountant
  disclosure, not customer content.
