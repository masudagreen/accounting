# 運用・引き継ぎ手順（新旧アプリ / DB バックアップ）

このリポジトリには **新アプリ（renewal）** と **旧アプリ（legacy RUCARO）** の 2 つが同居している。
本書だけ読めば、別端末でも「起動・ログイン・取り込み・バックアップ・復元」が一通りできる。

---

## 1. 全体像

| 区分 | 実体 | DB | URL（既定ポート） | エントリ |
|---|---|---|---|---|
| **新アプリ** | `src/`（PHP 8.3 / Ports & Adapters） | `rucaro` | http://localhost:9080 | `public/` |
| **旧アプリ** | `back/`（レガシー RUCARO） | `rucaro_legacy` | http://localhost:9090 | リポジトリ直下 `index.php` |
| phpMyAdmin | コンテナ | 両 DB を閲覧 | http://localhost:9081 | - |

- DB は **1 つの MariaDB コンテナ**（`accounting_renewal_db`）内に `rucaro` と `rucaro_legacy` の 2 データベースとして共存。
- データは共有されない。旧→新は **取り込みコマンド**（`legacy_to_v2`）で一方通行に流す（→ 第5章）。
- 新アプリの Docker は `/public` を公開し `/back` を遮断する（ADR-001）。旧アプリは**専用の使い捨てコンテナ**で別ポートに立てる（→ 第4章）。

```
            ┌─────────────── accounting_renewal_db (MariaDB) ───────────────┐
            │   DB: rucaro            DB: rucaro_legacy                      │
            └───────▲──────────────────────▲────────────────────────────────┘
                    │ 読み書き              │ 読み書き
        新アプリ (9080, public/)     旧アプリ (9090, back/, 使い捨てコンテナ)
                    ▲
                    │ legacy_to_v2 で取り込み（rucaro_legacy → rucaro）
                    └──────────────────────┘
```

---

## 2. 前提

- Docker Desktop（`docker compose` が使えること）、git、`zip`/`unzip`。
- `.env` が必要。無ければ `cp .env.example .env`（DB 認証は既定値 `rucaro/rucaro`, root は `root` のままで動く）。
- 主要ポート（`.env` で変更可）: `APP_PORT=9080` / `PMA_PORT=9081` / `DB_PORT=3307`（ホスト公開用） / 旧アプリ `9090`。

---

## 3. 新アプリの起動

```bash
docker compose up -d            # app(9080) + db を起動
# phpMyAdmin も使うなら:
docker compose --profile dev up -d phpmyadmin   # 9081
```

- 初回や Dockerfile 変更後は `docker compose up -d --build`。
- 起動確認: http://localhost:9080/ui/login

### ログイン（新アプリ）
- **メール: `masudagreen@gmail.com` / パスワード: `rucaro2026`**
  - これは legacy 取り込み時に設定される **プレースホルダ**。取り込み直後はこの値。ログイン後に変更推奨。
  - 取り込みの `--placeholder-password` を変えると別の値になる（→ 第5章）。

### phpMyAdmin
- http://localhost:9081 / ユーザー `root` パスワード `root`（または `rucaro`/`rucaro`）。
- `rucaro`=新、`rucaro_legacy`=旧。
- ※ 8081 番に別の phpMyAdmin が動く場合があるが、それは別ブランチ(master)の別 DB。renewal は **9081**。

---

## 4. 旧アプリ（legacy）の起動

旧アプリは新アプリの Docker とは別の**使い捨てコンテナ**で立てる。ヘルパースクリプトを使う。

```bash
docker compose up -d db                 # 先に DB を起動しておく
scripts/dev/legacy-app.sh up            # 旧アプリ起動 -> http://localhost:9090
scripts/dev/legacy-app.sh logs          # Apache エラーログ
scripts/dev/legacy-app.sh down          # 停止・削除
```

スクリプトが内部でやっていること（手動でやる場合の参考）:
1. `back/dat/db/connect.cgi` を **`rucaro_legacy`** 向けに書き換える（このファイルは旧アプリ専用の接続設定）。
2. テンプレートのコンパイル先 `back/tpl/{cache,configs,templates_c}`・`back/dat` を書き込み可能にする。
3. 新アプリのイメージ（`accounting-renewal-app`、PHP8.3＋mysqli 入り）を流用し、DocumentRoot をリポジトリルートにした vhost（`docker/apache/legacy.conf`）で 9090 番に起動。`vendor` は compose の名前付きボリュームを流用。

### ログイン（旧アプリ）
- 旧アプリ（`rucaro_legacy`）の利用者アカウントでログインする（新アプリとは別管理）。
- パスワードが不明なら phpMyAdmin で `rucaro_legacy` の利用者テーブルを確認（ハッシュ保存のため照合は別途）。

> 注意: 旧アプリのコードは元々 PHP7.4 想定。PHP8.3 で動かしているため、特定機能でエラーが出たら `scripts/dev/legacy-app.sh logs` を確認。

---

## 5. 旧 → 新 データ取り込み（legacy_to_v2）

旧アプリ（`rucaro_legacy`）に入力したデータを新アプリ（`rucaro`）へ取り込む。
**新側に過去取り込みのゴミが残っていると重複するため、新 DB をクリーンに作り直してから 1 回だけ取り込むのが確実。**

### 手順（クリーン再取り込み）

```bash
docker compose up -d db
scripts/dev/legacy-app.sh up     # コマンド実行用に PHP+vendor が入った legacy コンテナを使う

# 1) dry-run で内容を確認（書き込まない）
docker exec accounting_legacy sh -c "cd /var/www/html && php scripts/import/legacy_to_v2.php \
  --dry-run --source-db=rucaro_legacy --target-db=rucaro \
  --db-host=db --db-port=3306 --db-user=root --db-password=root \
  --placeholder-password='rucaro2026'"

# 2) 新 DB をマイグレーションから作り直す（※ rucaro の既存データは消える）
docker exec accounting_renewal_db mysql -uroot -proot -e "DROP DATABASE IF EXISTS rucaro;"
docker exec -i accounting_renewal_db mysql -uroot -proot < scripts/migrate/0000_init_database.sql
docker exec -e DB_USER=root -e DB_PASSWORD=root accounting_legacy \
  sh -c "cd /var/www/html && php scripts/migrate/_run_all.php"

# 3) 空の新 DB へ取り込み（apply）
docker exec accounting_legacy sh -c "cd /var/www/html && php scripts/import/legacy_to_v2.php \
  --apply --source-db=rucaro_legacy --target-db=rucaro \
  --db-host=db --db-port=3306 --db-user=root --db-password=root \
  --placeholder-password='rucaro2026'"
```

### 取り込み後の検算

```bash
docker exec accounting_renewal_db mysql -uroot -proot rucaro -N -e "
SELECT 'entries',COUNT(*) FROM journal_entries
UNION ALL SELECT 'lines',COUNT(*) FROM journal_entry_lines
UNION ALL SELECT '孤立行',COUNT(*) FROM journal_entry_lines l LEFT JOIN journal_entries e ON l.entry_id=e.id WHERE e.id IS NULL
UNION ALL SELECT '貸借不一致',(SELECT COUNT(*) FROM (SELECT entry_id FROM journal_entry_lines GROUP BY entry_id HAVING SUM(CASE WHEN side='debit' THEN amount ELSE -amount END)<>0) x);"
```
- 期待: **孤立行 = 0 / 貸借不一致 = 0**。
- 最後に http://localhost:9080/ui/trial-balance で「借方合計＝貸方合計（一致）」を目視確認。

> `--placeholder-password`（8 文字以上・必須）は移行ユーザー全員の暫定パスワードになる。これが新アプリのログインパスワードになる点に注意。
> 既存の取り込み済み行だけ消して入れ直す `--truncate-target` もあるが、過去のゴミが残っていると綺麗にならないため、上記の「作り直し」を推奨。

---

## 6. DB バックアップ

```bash
docker compose up -d db
scripts/dev/db-backup.sh                 # rucaro と rucaro_legacy 両方
scripts/dev/db-backup.sh rucaro          # 新のみ
scripts/dev/db-backup.sh rucaro_legacy   # 旧のみ
```
- 出力: `db/<dbname>_YYYYMMDD.sql.zip`（mysqldump を zip 圧縮）。
- git に載せて別端末へ持っていく場合はそのまま `git add db/*.zip && git commit && git push`。
  - 生の `.sql` は大きいので **zip でコミット**する。

---

## 7. DB 復元（別端末や巻き戻し）

```bash
docker compose up -d db
scripts/dev/db-restore.sh db/rucaro_20260629.sql.zip rucaro
scripts/dev/db-restore.sh db/rucaro_legacy_20260629.sql.zip rucaro_legacy
```
- 対象 DB が無ければ作成し、ダンプ（`--add-drop-table` 付き）で上書き復元する。

---

## 8. 別端末でのセットアップ（ゼロから）

```bash
git clone <repo> && cd accounting
git checkout renewal
cp .env.example .env                       # .env は git 管理外

docker compose up -d --build               # 新アプリ(9080) + db
docker compose --profile dev up -d phpmyadmin   # 9081（任意）

# データを復元（リポジトリに同梱した zip から）
scripts/dev/db-restore.sh db/rucaro_legacy_YYYYMMDD.sql.zip rucaro_legacy
scripts/dev/db-restore.sh db/rucaro_YYYYMMDD.sql.zip rucaro
# ↑ 新 DB の zip が無い場合は第5章の「取り込み」で rucaro_legacy から再生成してもよい

# 旧アプリも使うなら
scripts/dev/legacy-app.sh up               # 9090
```

---

## 9. 停止・後始末

```bash
scripts/dev/legacy-app.sh down                 # 旧アプリ(使い捨て)を削除
docker compose --profile dev down              # 新アプリ + phpMyAdmin + db を停止
# データを残したまま止めるだけなら:
docker compose stop
```
- DB の中身は名前付きボリューム `accounting-renewal_db_data` に残る（`down` だけでは消えない）。
- 完全削除（データも消す）は `docker compose down -v`（**取り扱い注意**）。

---

## 10. よくあるトラブル

| 症状 | 対処 |
|---|---|
| 旧アプリで `vendor/autoload.php` not found | `scripts/dev/legacy-app.sh` は compose の vendor ボリュームを使う。先に `docker compose up -d --build` で vendor を用意。 |
| 旧アプリが新 DB(`rucaro`)に繋いで壊れる | `back/dat/db/connect.cgi` の `dbname` が `rucaro_legacy` か確認（`legacy-app.sh up` が自動設定）。 |
| 取り込み後に行数が合わない/重複 | 第5章の「作り直し」で新 DB をクリーンにしてから再取り込み。 |
| 新アプリにログインできない | パスワードは取り込み時の `--placeholder-password`（既定 `rucaro2026`）。 |
| 旧アプリの DB が空 | `rucaro_legacy` が未復元。第7章で zip から復元。 |

---

## 関連 ADR
- ADR-001（ディレクトリ構成 / `/public` 公開・`/back` 遮断）
- ADR-007（ストラングラーフィグ移行）
- ADR-021（レガシーデータ移行）
