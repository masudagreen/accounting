# Sprint 12: マイグレーション基盤 + Entity 2 FS 補完

## 1. マイグレーション基盤の概要

`migrations/` ディレクトリにファイルベースの SQL マイグレーションを格納します。
ランナー (`MigrationRunner`) が `schema_migrations` テーブルで適用済みバージョンを管理します。

### ディレクトリ構成

```
migrations/
├── README.md
├── 0001_initial_schema.up.sql
├── 0001_initial_schema.down.sql
├── 0002_add_invoice_columns.up.sql
├── 0002_add_invoice_columns.down.sql
└── ...

src/Infrastructure/Migration/
├── MigrationException.php
├── MigrationRecord.php           PDO ベースの tracking table 操作
├── MigrationRecordInterface.php  モック可能なインターフェース
└── MigrationRunner.php           ファイル探索・実行ロジック

bin/
├── migrate.php        マイグレーション CLI
└── repair-fs-tree.php Entity 2 FS 補完スクリプト
```

## 2. マイグレーションの追加手順

```bash
# 1. 新しいマイグレーションファイルを生成
php bin/migrate.php new <description>
# 例: php bin/migrate.php new add_vendor_master

# 2. up.sql を編集
# migrations/0003_add_vendor_master.up.sql
#   → CREATE TABLE / ALTER TABLE ADD COLUMN

# 3. down.sql を編集
# migrations/0003_add_vendor_master.down.sql
#   → DROP TABLE / ALTER TABLE DROP COLUMN IF EXISTS

# 4. テスト DB で動作確認
docker compose exec -T -e DB_HOST=db -e DB_NAME=rucaro_test ... \
  app php bin/migrate.php up

# 5. git に追加してコミット
git add migrations/0003_add_vendor_master.*.sql
```

## 3. 命名規約

| 要素 | 規約 | 例 |
|------|------|---|
| バージョン番号 | 4桁ゼロパディング | `0001`, `0042` |
| 説明 | スネークケース、簡潔に | `add_invoice_columns` |
| 方向サフィックス | `.up.sql` / `.down.sql` | 必ず対になる |

## 4. ロールバック注意点

- `down --target=NNNN` は **NNNN より大きい** バージョンをロールバックします（NNNN 自身は残る）
- `DROP TABLE` や `DELETE` は **データが失われる** — 必ずバックアップを先に取る
- `ALTER TABLE DROP COLUMN IF EXISTS` を使うと冪等性が保てる（MariaDB 10.4+）
- ロールバック後は `php bin/migrate.php status` で状態を確認する

## 5. Entity 2 FS 補完運用フィックス

**背景**: G-9-1 問題。`accountingFSJpn.jsonJgaapAccountTitlePL` に
`rents` / `taxesAndDues` / `commissionPaid` / `badMiscellaneousExpenses` /
`insuranceExpenses` 等の科目が未定義のため、仕訳の TrialBalance で借方≠貸方になる。

### dry-run (読み取り専用)

```bash
docker compose exec -T \
  -e DB_HOST=db -e DB_NAME=rucaro_golden \
  -e DB_USER=rucaro -e DB_PASS=rucaro \
  app php bin/repair-fs-tree.php
```

出力例:

```markdown
# FS Tree Repair Report

## Entity 2 / Fiscal Period 3

| # | Missing Account Title ID |
|---|--------------------------|
| 1 | `rents` |
| 2 | `taxesAndDues` |
| 3 | `commissionPaid` |

Proposed action: Add to `jsonJgaapAccountTitlePL` under
`sellingGeneralAndAdministrationExpenses` (default placement).
```

### apply モード (DB 更新)

```bash
# 1. 必ず先にバックアップ
mysqldump -u rucaro -p rucaro_golden > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. dry-run で内容確認

# 3. apply 実行
docker compose exec -T \
  -e DB_HOST=db -e DB_NAME=rucaro_golden \
  -e DB_USER=rucaro -e DB_PASS=rucaro \
  app php bin/repair-fs-tree.php --apply
# => "Have you taken a backup? Type 'yes' to continue: " と表示される
# => "yes" と入力すると実行

# 4. dry-run で差分がなくなったか確認
docker compose exec ... app php bin/repair-fs-tree.php
# => "No missing account titles found. All entities are consistent."
```

## 6. 本番 DB 移行チェックリスト

```
[ ] 1. DB バックアップ取得
       mysqldump -u rucaro -p rucaro > backup_YYYYMMDD.sql

[ ] 2. repair-fs-tree.php dry-run を実行し、差分を確認

[ ] 3. 差分が多い場合は個別に科目配置先を判断
       (スクリプトはデフォルトで sellingGeneralAndAdministrationExpenses 配下に追加)

[ ] 4. repair-fs-tree.php --apply を実行 (yes で確認)

[ ] 5. repair-fs-tree.php (dry-run) で差分が 0 になったことを確認

[ ] 6. php bin/migrate.php status で現在のマイグレーション状態を確認

[ ] 7. php bin/migrate.php up で新スキーマを適用

[ ] 8. Golden Master テストを本番データで実行し、借方=貸方を確認

[ ] 9. Entity 1, Entity 2 両方で TrialBalance が一致することを確認
```

## 7. SQLite 互換性 (テスト環境)

統合テスト (`tests/Integration/Migration/`) は SQLite in-memory で動作します。
MariaDB が不要なため CI 環境でも常に実行されます。

`migrations/*.sql` はプロダクション用 (MariaDB 専用 DDL を含む) のため、
統合テストでは直接使用しません。テスト用の SQL はテスト内にインライン記述します。

## 8. PHPStan

```bash
docker compose exec -T app vendor/bin/phpstan analyse --memory-limit=1G
# => No errors
```

新規追加クラス一覧:
- `App\Infrastructure\Migration\MigrationException`
- `App\Infrastructure\Migration\MigrationRecord`
- `App\Infrastructure\Migration\MigrationRecordInterface`
- `App\Infrastructure\Migration\MigrationRunner`
