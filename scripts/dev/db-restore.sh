#!/usr/bin/env bash
#
# db/ の zip ダンプから DB を復元するヘルパー。
#
# 使い方:
#   scripts/dev/db-restore.sh db/rucaro_20260629.sql.zip rucaro
#   scripts/dev/db-restore.sh db/rucaro_legacy_20260629.sql.zip rucaro_legacy
#
# 第1引数: zip もしくは .sql ファイル / 第2引数: 復元先 DB 名
# 前提: `docker compose up -d db` で DB コンテナが起動していること。
# 注意: 対象 DB の既存データは --add-drop-table により上書きされる。
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DB_CONTAINER="accounting_renewal_db"

FILE="${1:?usage: $0 <dump.sql|dump.sql.zip> <dbname>}"
DB="${2:?usage: $0 <dump.sql|dump.sql.zip> <dbname>}"
[ -f "$FILE" ] || { echo "file not found: $FILE" >&2; exit 1; }

# 復元先 DB を用意(無ければ作成)。
docker exec "$DB_CONTAINER" mysql -uroot -proot \
  -e "CREATE DATABASE IF NOT EXISTS \`${DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "restoring ${FILE} -> ${DB} ..."
case "$FILE" in
  *.zip) unzip -p "$FILE" | docker exec -i "$DB_CONTAINER" mysql -uroot -proot "$DB" ;;
  *)     docker exec -i "$DB_CONTAINER" mysql -uroot -proot "$DB" < "$FILE" ;;
esac
echo "done."
