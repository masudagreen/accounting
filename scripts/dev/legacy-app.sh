#!/usr/bin/env bash
#
# 旧アプリ(legacy RUCARO)を使い捨てコンテナで起動/停止するヘルパー。
#
# 旧アプリは renewal の Docker(新アプリ)と同じ DB コンテナを共有し、
# DB `rucaro_legacy` を読み書きする。新アプリ(public/)とは別ポートで動かす。
#
# 使い方:
#   scripts/dev/legacy-app.sh up      # 起動 (http://localhost:9090)
#   scripts/dev/legacy-app.sh down    # 停止・削除
#   scripts/dev/legacy-app.sh logs    # Apache エラーログ
#
# 前提: 先に `docker compose up -d db` で renewal の DB が起動していること。
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
CONTAINER="accounting_legacy"
IMAGE="accounting-renewal-app"          # `docker compose build` で作られるイメージ
NETWORK="accounting-renewal_accounting" # docker-compose.yml の project 名(accounting-renewal)由来
VENDOR_VOL="accounting-renewal_app_vendor"
PORT="${LEGACY_PORT:-9090}"
CONNECT="$REPO_ROOT/back/dat/db/connect.cgi"

cmd="${1:-up}"

case "$cmd" in
  up)
    # 旧アプリの DB 接続先を rucaro_legacy に向ける(このファイルはローカル設定)。
    cat > "$CONNECT" <<'CFG'
"dbtype","dbname","username","password","host","driver"
"master","rucaro_legacy","rucaro","rucaro","db","mysql"
"slave","rucaro_legacy","rucaro","rucaro","db","mysql"
"log","rucaro_legacy","rucaro","rucaro","db","mysql"
CFG
    # 旧アプリのテンプレートコンパイル等の書き込み先を確保。
    # ディレクトリだけ 777 にする(ファイルに +x を付けると git のモード差分ノイズになるため)。
    find "$REPO_ROOT"/back/tpl/cache "$REPO_ROOT"/back/tpl/configs \
         "$REPO_ROOT"/back/tpl/templates_c "$REPO_ROOT"/back/dat \
         -type d -exec chmod 777 {} + 2>/dev/null || true

    docker rm -f "$CONTAINER" >/dev/null 2>&1 || true
    docker run -d --name "$CONTAINER" \
      --network "$NETWORK" \
      -p "${PORT}:80" \
      -v "$REPO_ROOT:/var/www/html" \
      -v "${VENDOR_VOL}:/var/www/html/vendor" \
      -v "$REPO_ROOT/docker/apache/legacy.conf:/etc/apache2/sites-available/000-default.conf:ro" \
      "$IMAGE" >/dev/null
    echo "legacy app up -> http://localhost:${PORT}/"
    ;;
  down)
    docker rm -f "$CONTAINER" >/dev/null 2>&1 && echo "legacy app removed" || echo "not running"
    ;;
  logs)
    docker exec "$CONTAINER" tail -n 50 /var/log/apache2/error.log
    ;;
  *)
    echo "usage: $0 {up|down|logs}" >&2; exit 1 ;;
esac
