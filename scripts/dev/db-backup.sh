#!/usr/bin/env bash
#
# renewal の DB をダンプして db/ に zip 保存するヘルパー。
#
# 使い方:
#   scripts/dev/db-backup.sh                # 両方(rucaro と rucaro_legacy)
#   scripts/dev/db-backup.sh rucaro         # 新アプリのみ
#   scripts/dev/db-backup.sh rucaro_legacy  # 旧アプリのみ
#
# 出力: db/<dbname>_YYYYMMDD.sql.zip
# 前提: `docker compose up -d db` で DB コンテナが起動していること。
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DB_CONTAINER="accounting_renewal_db"
DATE="$(date +%Y%m%d)"
OUT_DIR="$REPO_ROOT/db"
mkdir -p "$OUT_DIR"

dump_one() {
  local db="$1"
  local sql="$OUT_DIR/${db}_${DATE}.sql"
  local zip="$OUT_DIR/${db}_${DATE}.sql.zip"
  echo "dumping ${db} ..."
  docker exec "$DB_CONTAINER" sh -c \
    "exec mysqldump -uroot -proot --default-character-set=utf8mb4 --single-transaction --no-tablespaces --skip-dump-date --add-drop-table ${db}" \
    > "$sql"
  ( cd "$OUT_DIR" && zip -q -9 "$(basename "$zip")" "$(basename "$sql")" && rm -f "$(basename "$sql")" )
  echo "  -> ${zip} ($(du -h "$zip" | cut -f1))"
}

if [ "$#" -eq 0 ]; then
  dump_one rucaro
  dump_one rucaro_legacy
else
  for db in "$@"; do dump_one "$db"; done
fi
