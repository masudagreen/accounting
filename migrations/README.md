# Migration Guide

## Overview

File-based, version-ordered SQL migrations for the RUCARO Accounting system.

```
migrations/
├── README.md                           this file
├── 0001_initial_schema.up.sql          create all tables
├── 0001_initial_schema.down.sql        drop all tables
├── 0002_add_invoice_columns.up.sql     add invoice (インボイス) columns
├── 0002_add_invoice_columns.down.sql
└── ...
```

## Naming Convention

```
NNNN_description.{up,down}.sql
```

- `NNNN`: 4-digit zero-padded version number (0001, 0002, …)
- `description`: snake_case, concise description of the change
- `.up.sql`: forward migration (CREATE / ALTER ADD)
- `.down.sql`: rollback (DROP / ALTER DROP)

## Setup

Requires environment variables:

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_HOST` | `db` | Database host |
| `DB_PORT` | `3306` | Database port |
| `DB_NAME` | — | Database name (required) |
| `DB_USER` | — | Database user (required) |
| `DB_PASS` | — | Database password (required) |

## CLI Commands

```bash
# Show migration status
docker compose exec -T \
  -e DB_HOST=db -e DB_NAME=rucaro_test -e DB_USER=rucaro -e DB_PASS=rucaro \
  app php bin/migrate.php status

# Apply all pending migrations
docker compose exec -T \
  -e DB_HOST=db -e DB_NAME=rucaro_test -e DB_USER=rucaro -e DB_PASS=rucaro \
  app php bin/migrate.php up

# Apply up to a specific version
docker compose exec -T ... app php bin/migrate.php up --target=0002

# Rollback to a specific version (exclusive: 0001 stays, 0002+ are reverted)
docker compose exec -T ... app php bin/migrate.php down --target=0001

# Create new empty migration files
docker compose exec -T app php bin/migrate.php new add_invoice_columns
```

## Adding a New Migration

1. Run `php bin/migrate.php new <description>` to generate skeleton files.
2. Edit the generated `NNNN_description.up.sql` with your DDL.
3. Edit the generated `NNNN_description.down.sql` with the rollback DDL.
4. Test on a disposable database first.
5. Commit both files.

## SQL Compatibility Notes

- Production targets **MariaDB 10** (InnoDB, utf8mb4).
- Integration tests run against **SQLite in-memory** — do NOT use:
  - `LONGTEXT` / `MEDIUMTEXT` (use `TEXT` in test helpers)
  - `ENGINE=InnoDB`, `CHARSET=utf8mb4` (ignored/unsupported by SQLite)
  - `AUTO_INCREMENT` (use `AUTOINCREMENT` for SQLite, or omit in integration fixtures)
- When writing a migration that must work on both, keep it to portable DDL
  (CREATE TABLE, DROP TABLE, basic ALTER TABLE ADD/DROP COLUMN).

## Rollback Considerations

- Always write a `.down.sql` immediately when writing `.up.sql`.
- `DROP COLUMN IF EXISTS` is safer than `DROP COLUMN` for partial rollbacks.
- Data-destructive operations (DROP TABLE, DELETE) cannot be undone — always
  back up before executing these in production.
- The `schema_migrations` table records which versions have been applied.

## Production Checklist

1. **Backup first**: `mysqldump -u root -p rucaro > backup_$(date +%Y%m%d_%H%M%S).sql`
2. **Dry-run on staging** before production.
3. Run `php bin/migrate.php status` to confirm current state.
4. Run `php bin/migrate.php up` to apply.
5. Run `php bin/migrate.php status` again to verify all are applied.
6. Run application smoke tests.
7. If something goes wrong: `php bin/migrate.php down --target=<previous_version>`
