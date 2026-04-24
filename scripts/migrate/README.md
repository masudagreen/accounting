# Rucaro Accounting - Migrations

このディレクトリには番号付きマイグレーション SQL を格納します。
`MigrationRunner` (`src/Infrastructure/Migration/MigrationRunner.php`) および
`bin/cowork migrate:*` コマンドが本ディレクトリを走査して適用します。

## ファイル命名規則

- 適用: `NNNN_<slug>.sql`
- ロールバック: `NNNN_<slug>.down.sql`
- `NNNN` は連番 4 桁のゼロ埋め（例: `0001`, `0002` ...）。
- `0000_init_database.sql` はブートストラップ専用。`schema_migrations` テーブル自身を含むため、`schema_migrations` には記録されません（MigrationRunner は `0000_` を bootstrap として特別扱いします）。

## 現在のマイグレーション

| 番号 | ファイル | 内容 |
|------|----------|------|
| 0000 | `0000_init_database.sql` | `CREATE DATABASE rucaro` と `schema_migrations` 作成（ブートストラップ） |
| 0001 | `0001_create_users_and_auth.sql` | `users`, `api_tokens` |
| 0002 | `0002_create_fiscal_domain.sql` | `entities`, `fiscal_terms`, `account_titles`, `sub_account_titles` |
| 0003 | `0003_create_journal_tables.sql` | `journal_entries`, `journal_entry_lines` |
| 0004 | `0004_create_receipts_and_approvals.sql` | `receipts`, `receipt_action_logs`, `approval_tokens` |

## 実行手順

```bash
# 初回: データベースと履歴テーブルを作成
mariadb -u root -p < scripts/migrate/0000_init_database.sql

# 未適用の状態を確認
php bin/cowork migrate:status

# すべての未適用マイグレーションを適用
php bin/cowork migrate:up

# 直近 1 件をロールバック
php bin/cowork migrate:down --step=1
```

## 運用方針

### dev (ローカル Docker)

- `docker compose up -d mariadb` 起動後、`php bin/cowork migrate:up` を実行。
- スキーマを一から作り直したい場合は `docker compose down -v` でボリュームごと削除してから再実行。

### CI

- GitHub Actions で MariaDB 10.11 サービスコンテナを起動し、空 DB に対して `migrate:up` を実行。成功することがゲート条件。
- `tests/Integration/Migration/MigrationRunnerTest.php` も CI で走らせる。

### 本番

- ゼロダウンタイム前提でスキーマ変更を書く（破壊的 ALTER は避ける）。
- 必ず **事前バックアップ** → `migrate:up` → スモークテストの順で実施。
- ロールバックは `migrate:down --step=1` を原則とし、複数回連続実行は避ける（データ喪失の可能性があるため）。
- 本番適用前に `migrate:status` で差分を必ず確認する。

## 注意

- 各マイグレーションは **冪等に書かない**（新 DB 構築前提、`DROP TABLE IF EXISTS` 等を書いて destructive にしない）。
- 一度適用済みの SQL ファイルを書き換えないこと。`schema_migrations.checksum` に SHA-256 が記録され、差異があれば MigrationRunner が停止します。
- 変更が必要になった場合は新しい番号で追加マイグレーションを発行する。
