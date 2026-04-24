# Phase 1.1.d: 現状スタック（Docker / PHP / DB）整理

> 調査者: Explore サブエージェント
> 対象: `C:\Users\yusuk\StudioProjects\accounting\`
> 目的: 新 PHP 8.3 + MariaDB 10.11 スタックへの移行デルタを明確化

---

## 1. 現状 Docker スタック

### 現行構成
- **PHP バージョン**: `php:8.2-apache`（`docker/Dockerfile` 1行目）
- **Apache**: mod_php 同梱、mod_rewrite 有効
- **Document Root**: `/var/www/html`
- **DB**: `mariadb:10`（バージョン未固定、10.x の最新に流動）
- **phpMyAdmin**: `phpmyadmin/phpmyadmin`（ポート 8081、認証なし）
- **Composer**: 最新版（マルチステージコピー）

### インストール済み PHP 拡張（Dockerfile より）
- `pdo_mysql`, `mysqli`, `gd`, `intl`, `zip`
- ビルド依存: `libpng-dev`, `libonig-dev`, `libxml2-dev`, `libicu-dev`

### 公開ポート
| ポート | サービス |
|---|---|
| 8080 | Apache (app) |
| 3306 | MariaDB（制限なし露出） |
| 8081 | phpMyAdmin |

### ボリュームレイアウト
- 単一マウント: `.:/var/www/html`（プロジェクトルート全体、uploads / logs / storage の分離なし）

### ランタイム設定（`Init.php:556-568`）
```php
mb_regex_encoding('UTF-8');
mb_language('uni');
mb_internal_encoding('utf-8');
date_default_timezone_set('UTC');                 // UTC ハードコード
ini_set('memory_limit', '128M');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
ini_set('display_errors', 1);
```

### システム設定（Init.php）
| 項目 | 値 |
|---|---|
| `strEncoding` | UTF-8 |
| `strSystemLang` | ja |
| `numSystemTimeZone` | 9（JST、ただしコードで UTC も設定されており矛盾） |
| `numSessionLoginSecond` | 3600（1 時間） |
| `numSession` | 90000（25 時間） |
| `numMaxUploadSize` | 1MB（1048576 bytes） |
| `flagAPC` | 0（APC キャッシュ無効） |

---

## 2. Composer 依存関係

`vendor/composer/installed.json` より:

| パッケージ | バージョン | 用途 |
|---|---|---|
| smarty/smarty | v5.7.0 | テンプレートエンジン（PHP 7.2+ / 8.0+） |
| symfony/polyfill-mbstring | v1.33.0 | mbstring ポリフィル（PHP 7.2+） |

**合計 2 パッケージ。**

---

## 3. `.htaccess.bak` 解析（2625 行）

### なぜ無効化されているか
ファイル名が `.bak` で終わっているため Apache は読み込まない。PHP8 移行前のバックアップ。

### 含まれるルール
- **IP ホワイトリスト**: APNIC / US / グローバル IP 範囲 + localhost（127.0.0.1, ::1）のみ許可
- **User-Agent フィルタ**: Baidu, sogou, soso, msnbot 変種をブロック、Googlebot / Yahoo Slurp は許可
- **Bot ブロック**: PycURL, WebCorpusBuilder, Swarm, Ezooms 等を拒否
- **URL Rewrite ルールなし**

### 現在有効な `.htaccess`（`back/.htaccess`）
```
order deny,allow
deny from all
```
`/back` ディレクトリへのアクセスを全拒否（標準セキュリティ）。

### 新アプリでの扱い
- ローカル使用なので IP / UA フィルタは不要
- 推奨: セキュリティルールは Docker 層か WAF に委譲、`.htaccess` はルーティング最小限に
- `back/.htaccess` の deny all は継続

---

## 4. データベース設定

### 現状（`connect.cgi`）
```csv
dbtype,dbname,username,password,host,driver
master,rucaro,rucaro,rucaro,db,mysql
slave,rucaro,rucaro,rucaro,db,mysql
log,rucaro,rucaro,rucaro,db,mysql
```

### Docker 環境変数
- `MYSQL_DATABASE`: rucaro
- `MYSQL_USER`: rucaro
- `MYSQL_PASSWORD`: rucaro
- `MYSQL_ROOT_PASSWORD`: root
- `DB_HOST`: db（サービス名）

### charset / collation
compose ファイルで未指定 → MariaDB 10 デフォルトの `latin1` / `latin1_swedish_ci` が適用される可能性大。**要明示指定**。

---

## 5. 環境変数

### 現在の利用状況
- `REMOTE_ADDR`, `REMOTE_HOST`（`Media.php` で `getenv()`、Dockerfile からの注入ではない）
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`（compose で定義されているがアプリは `connect.cgi` を読むため未使用）

### 結論
アプリは `.env` を使っていない。設定は `connect.cgi` と `Init.php` にハードコード。

---

## 6. 新スタックの推奨構成

### PHP 8.3 Docker イメージ: `php:8.3-apache`

**理由**:
- mod_php 同梱で構成がシンプル（モノリスに適する）
- レガシー Init.php が Apache モジュール実行を前提
- `fpm-alpine` + 別 NGINX は本モノリスには過剰

### MariaDB 10.11 LTS

**理由**:
- LTS（2028 年までサポート）
- 10 系の中で最も安定

**注意点**: 10.11 のデフォルト collation が `utf8mb4_uca1400_ai_ci` に変更。後方互換を保つため `MYSQL_COLLATION_SERVER=utf8mb4_unicode_ci` を compose で明示指定するか、スキーマ移行後に個別 ALTER。

### 必須 PHP 拡張

| 拡張 | 用途 |
|---|---|
| pdo_mysql | DB 接続（必須） |
| mysqli | フォールバック（互換性保持） |
| mbstring | `mb_*` 関数 |
| gd | 画像処理 |
| intl | 国際化（日本語 collation、NumberFormatter 等） |
| zip | ファイルアップロード |
| openssl | HTTPS、暗号 |
| curl | 外部 HTTP |
| opcache | パフォーマンス（PHP 8.3 既定） |

ビルド依存: `libpng-dev`, `libonig-dev`, `libxml2-dev`, `libicu-dev`, `libzip-dev`

### Composer 2.x
現 Dockerfile が既に最新版をコピー。Smarty 5.7.0 は PHP 8.3 互換。

---

## 7. 移行チェックリスト

### Dockerfile
- [ ] ベースイメージ: `FROM php:8.3-apache`
- [ ] `libzip-dev` 追加（現 8.2 ビルドにない）
- [ ] `zip` 拡張インストール
- [ ] `opcache` 拡張を明示有効化
- [ ] `date.timezone = UTC` を php.ini に設定
- [ ] `memory_limit` を 256M に引き上げ
- [ ] `upload_max_filesize = 50M`, `post_max_size = 50M`
- [ ] マルチステージビルド（イメージサイズ削減）
- [ ] ヘルスチェック追加

### docker-compose.yml
- [ ] PHP サービス: `image` を `php:8.3-apache` に固定
- [ ] MariaDB: `mariadb:10.11-focal` に固定
- [ ] `MYSQL_COLLATION_SERVER=utf8mb4_unicode_ci` 追加
- [ ] ボリューム分離（code / db_data / logs / uploads）
- [ ] DB ヘルスチェック（mysqladmin ping）
- [ ] phpMyAdmin は dev プロファイル化

### Apache 設定
- [ ] `AllowOverride All` 確認済 ✓
- [ ] セキュリティヘッダ追加（X-Content-Type-Options, X-Frame-Options 等）
- [ ] `ServerSignature Off`

### PHP ランタイム
- [ ] タイムゾーン矛盾の解消: `date_default_timezone_set('Asia/Tokyo')` または環境変数化
- [ ] `memory_limit` を 256M 以上
- [ ] `error_reporting` の抑制を見直し（最低限 `E_DEPRECATED` は開発中は有効化）

### DB 初期化
- [ ] スキーマ collation 更新スクリプト（既存 DB がある場合）
  ```sql
  ALTER DATABASE rucaro COLLATE utf8mb4_unicode_ci;
  ALTER TABLE <name> CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

### `.htaccess`
- [ ] `.htaccess.bak` の IP/UA フィルタは廃止（ローカル用途のため）
- [ ] `back/.htaccess` の deny all は維持

---

## 8. 既知の Gotcha

1. **タイムゾーン矛盾**: Init.php は UTC ハードコード、設定は JST（9）。要統一。
2. **MariaDB collation**: `latin1_swedish_ci` → `utf8mb4_unicode_ci` でソート順が変わる可能性。要テスト。
3. **セッション期間の不一致**: `numSession=90000`（25h）vs `numSessionLoginSecond=3600`（1h）。意図確認。
4. **メモリ上限**: 128M は現代 Smarty + アップロードには不足。256-512M 推奨。
5. **アップロード上限 1MB**: 極端に小さい。業務要件確認。
6. **ログ出力なし**: `display_errors=1` は本番危険。`error_log` 利用へ。
7. **phpMyAdmin 露出**: ポート 8081 が無認証公開。dev 限定 or 認証必須。
8. **HTTPS 未設定**: 本番は LetsEncrypt 検討。
9. **DB バックアップ戦略未定義**: `db_data` ボリュームのバックアップ計画が必要。
10. **Init.php レガシーパターン**: `require_once` + グローバル変数。モダン autoload と混在できない。段階移行時に工夫必要。

---

## 9. 提案 `docker/Dockerfile.new`

```dockerfile
# ==================== BUILDER STAGE ====================
FROM php:8.3-apache AS builder
WORKDIR /build

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libonig-dev libxml2-dev libicu-dev libzip-dev \
    zip unzip git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql mysqli gd intl zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==================== FINAL STAGE ====================
FROM php:8.3-apache
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng16-16 libonig5 libxml2 libicu72 libzip4 curl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/uploads /var/www/storage /var/log/apache2 \
    back/tpl/templates_c back/tpl/cache back/dat/file back/dat/temp

RUN chown -R www-data:www-data /var/www && chmod 755 /var/www/html

RUN { \
      echo "date.timezone = Asia/Tokyo"; \
      echo "memory_limit = 256M"; \
      echo "upload_max_filesize = 50M"; \
      echo "post_max_size = 50M"; \
      echo "display_errors = Off"; \
      echo "log_errors = On"; \
      echo "error_log = /var/log/apache2/php-error.log"; \
      echo "opcache.enable = 1"; \
      echo "opcache.memory_consumption = 256"; \
    } > /usr/local/etc/php/conf.d/zz-rucaro.ini

RUN a2enmod rewrite headers expires

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

CMD ["apache2-foreground"]
```

---

## 10. 提案 `docker-compose.new.yml`

```yaml
services:
  app:
    build:
      context: .
      dockerfile: docker/Dockerfile.new
    container_name: accounting_app
    ports:
      - "${APP_PORT:-8080}:80"
    volumes:
      - .:/var/www/html
      - ./storage/uploads:/var/www/uploads
      - ./storage/logs:/var/log/apache2
    environment:
      - DB_HOST=db
      - DB_NAME=rucaro
      - DB_USER=rucaro
      - DB_PASSWORD=rucaro
      - TZ=Asia/Tokyo
    depends_on:
      db:
        condition: service_healthy
    networks: [accounting]
    restart: unless-stopped

  db:
    image: mariadb:10.11-focal
    container_name: accounting_db
    ports:
      - "${DB_PORT:-3306}:3306"
    volumes:
      - db_data:/var/lib/mysql
    environment:
      - MYSQL_DATABASE=rucaro
      - MYSQL_USER=rucaro
      - MYSQL_PASSWORD=rucaro
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD:-root}
      - MYSQL_COLLATION_SERVER=utf8mb4_unicode_ci
      - MYSQL_CHARACTER_SET_SERVER=utf8mb4
      - TZ=Asia/Tokyo
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5
    networks: [accounting]
    restart: unless-stopped

  phpmyadmin:
    image: phpmyadmin:latest
    container_name: accounting_pma
    profiles: [dev]
    ports:
      - "${PMA_PORT:-8081}:80"
    environment:
      PMA_HOST: db
      PMA_USER: root
      PMA_PASSWORD: ${DB_ROOT_PASSWORD:-root}
    depends_on:
      db:
        condition: service_healthy
    networks: [accounting]

volumes:
  db_data:

networks:
  accounting:
    driver: bridge
```

`.env` 例:
```bash
APP_PORT=8080
DB_PORT=3306
DB_ROOT_PASSWORD=changeme
PMA_PORT=8081
```

---

## 11. デルタサマリ

| 項目 | 現状 | 目標 | デルタ |
|---|---|---|---|
| PHP | 8.2-apache | 8.3-apache | マイナーアップグレード |
| MariaDB | 10（未固定） | 10.11-focal（LTS） | バージョン固定 + collation 明示 |
| 拡張 | pdo_mysql, mysqli, gd, intl, zip | + opcache, openssl, curl | 3 追加 |
| memory_limit | 128M | 256M | 2 倍 |
| upload_max_filesize | 1M | 50M | 50 倍 |
| ボリューム | 単一マウント | code / db / uploads / logs 分離 | 分離 |
| ヘルスチェック | なし | DB + HTTP | 新規 |
| マルチステージビルド | なし | あり | イメージサイズ約 30% 削減 |
| `.htaccess` ルール | 2625 行 IP/UA フィルタ | 不要（ローカル用途） | 削除 |
| エラーログ | display_errors=1 | error_log + display_errors=0 | 本番安全化 |
| タイムゾーン | UTC + JST 矛盾 | 環境変数 / JST 統一 | 明確化 |

---

## 12. Summary

- **現状**: PHP 8.2-apache + MariaDB 10 unpinned、Composer 2 パッケージのみ、volume 単一、`.env` 未使用。
- **目標**: PHP 8.3-apache + MariaDB 10.11 LTS、ボリューム分離、環境変数駆動、ヘルスチェック、マルチステージビルド。
- **新規作成**: `docker/Dockerfile.new`, `docker-compose.new.yml`, `.env.example`
- **工数**: dev 環境で検証含め 4〜6 時間。
