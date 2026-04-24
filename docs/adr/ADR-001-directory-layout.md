# ADR-001: 新ディレクトリ構成と PSR-4 マッピング

- ステータス: 提案中 (2026-04-20)
- 決定者: (ユーザ承認待ち)
- 関連: [PLAN.md](../PLAN.md), ADR-002 (新DBスキーマ方針), ADR-003 (暗号化方針)

---

## 1. 文脈 (Context)

現行 Rucaro Accounting は PHP5 〜 7.4 時代の設計を継承しており、`back/class/else/**` 以下に約 **450 クラス** が格納されている（Plugin/Accounting 368、Core/Base 35、Lib 16 ほか。出典: [legacy-routing.md](../phase1/legacy-routing.md) §4）。コードベースは以下の構造的制約を抱えている:

| 観点 | 現状の実態 | 影響 |
|------|----------|------|
| オートロード | **不在**。必要時に `require_once` で 1 ファイルずつ読み込み | 依存関係が不透明、循環読込が発生 |
| クラス命名 | `Code_Else_{Tier}_{Domain}_{Module}`（4 セグメントのアンダースコア区切り、PHP4 風） | 名前空間機構が使えず、PSR-4 と非互換 |
| グローバル状態 | `Init.php` が `$varsAccount` / `$varsRequest` / `$varsMedia` / `$varsModule` / `$varsPreference` / `$varsApiAccounts` / `$varsTerm` を populate | DI 不可、テスト困難、副作用追跡不能 |
| ルーティング | ルータ不在。4 エントリポイント（`index.php` / `api.php` / `output.php` / `confirm.php`）が `?class=`, `?module=`, `?type=` を `ucwords()` でクラス名に変換して `new` | ミドルウェア挿入不可、REST に不向き |
| テンプレート | Smarty を各クラスから `sendHtml()` で直接呼ぶ。テンプレートパスに `<strLang>` プレースホルダ | ビュー層の責務が混在 |
| 設定 | `back/dat/db/connect.cgi` と `Init.php` ハードコード。`.env` 未使用 | 環境差分の管理不能 |
| テスト | PHPUnit / phpstan / psalm いずれも未導入 | 回帰検出の自動化手段なし |
| Composer | `smarty/smarty` + `symfony/polyfill-mbstring` の 2 パッケージのみ | 現代的エコシステムを利用できない |
| エンジン拘束 | DDL に `ENGINE=InnoDB` のみ、`CHARACTER SET` 指定なし、PK 以外のインデックスゼロ、外部キーゼロ、JSON は `longtext` | 新基盤では utf8mb4 既定と適切なインデックスが必須 |

本 ADR はこの上で **旧コードを一切書き換えずに温存** し、隣に新アプリを「Strangler Fig」で構築するための、レポジトリ全体のディレクトリ構成と PSR-4 マッピングを決定する。

---

## 2. 決定 (Decision)

### 2.1 方針サマリ

1. **レポジトリのトップレベルに `src/` を新設** し、PSR-4 名前空間 `Rucaro\\` を割り当てる。
2. `src/` は **ヘキサゴナル + レイヤ分離** として以下 5 層に分割:
   - `src/Domain/` — エンティティ / 値オブジェクト / ドメインサービス / ドメイン例外
   - `src/Application/` — ユースケース（CQRS-lite）、入出力 DTO、Port 定義
   - `src/Infrastructure/` — リポジトリ実装・PDO・Claude Client・Monolog など外界 I/O
   - `src/Http/` — HTTP Kernel / ミドルウェア / Controller / Request-Response 変換
   - `src/Support/` — Result 型・Clock・Uuid 生成などの横断的ユーティリティ
3. **テストは `tests/{Unit,Integration,E2E}/`** を `src/` のミラー構造で配置。
4. **公開エントリは `public/`** に集約し、Docker の `DocumentRoot` を `public/` に切り替える（旧 `back/.htaccess` の deny all は維持）。
5. **旧 `back/` は一切触らず温存**。Composer の `autoload.classmap` に旧クラスを登録して必要時のみロード可能にする。
6. CLI / マイグレーション / 補助スクリプトはそれぞれ `bin/` / `scripts/migrate/` に分離。
7. アップロード・ログ・キャッシュ等ランタイム生成物は `storage/` 配下に集約（Docker ボリュームマウント候補）。

### 2.2 旧コードとの境界

| 領域 | 旧 (温存) | 新 (構築) |
|------|----------|-----------|
| Web UI | `/index.php` → `back/class/else/core/**` | `public/index.php`（段階退役） |
| Web API | `/api.php` → `back/class/else/plugin/accounting/**` | `public/api/v1/index.php` → `Rucaro\Http\Api\V1\**` |
| ドメイン | `Code_Else_Plugin_Accounting_*` | `Rucaro\Domain\**` + `Rucaro\Application\**` |
| DB 接続 | `back/dat/db/connect.cgi` + `Code_Else_Lib_Db` | `Rucaro\Infrastructure\Database\ConnectionFactory` + `.env` |
| 暗号 | `Code_Else_Lib_Crypte`（Blowfish CBC、mcrypt 互換） | `Rucaro\Infrastructure\Crypto\AesGcmCipher` |
| テンプレート | Smarty 直呼び | Phase 1〜3 では Smarty を `Rucaro\Http\View\SmartyRenderer` 経由で再利用、将来的に退役 |

---

## 3. ディレクトリツリー

```
accounting/                          # レポジトリルート
├── back/                            # [旧] レガシーコード (一切触らず温存)
│   ├── class/else/core/**           # 旧 Core (認証・ルーティング)
│   ├── class/else/plugin/**         # 旧 Plugin (会計ドメイン 368 クラス)
│   ├── class/else/lib/**            # 旧 Lib (Db, Check, Escape ほか 16 ユーティリティ)
│   ├── dat/                         # 旧データファイル (マイグレーション履歴、鍵、テンプレ)
│   └── tpl/                         # 旧 Smarty テンプレート
├── bin/                             # CLI エントリ (Symfony Console)
│   ├── cowork                       # Phase 5 領収書取込 CLI (bin/cowork receipts:ingest ...)
│   └── migrate                      # scripts/migrate/ を呼ぶ薄いラッパー
├── config/                          # typed config (PHP ファイル、.env から注入)
│   ├── app.php                      # アプリ全般 (タイムゾーン、ロケール、環境)
│   ├── database.php                 # PDO DSN / プール設定
│   ├── logging.php                  # Monolog ハンドラ
│   ├── crypto.php                   # AES-256-GCM 鍵 ID / HKDF ソルト
│   ├── routing.php                  # FastRoute ルート定義束ね
│   └── services.php                 # DI コンテナ定義
├── docker/                          # Dockerfile, Apache 設定
│   ├── Dockerfile                   # php:8.3-apache マルチステージ
│   ├── apache/000-default.conf      # DocumentRoot=/var/www/html/public
│   └── php/zz-rucaro.ini            # memory_limit / upload_max_filesize
├── docs/                            # ドキュメント (ADR, OpenAPI, 内部解析)
│   ├── PLAN.md
│   ├── adr/                         # 本 ADR を含む決定記録
│   ├── phase1/                      # 旧アプリ調査レポート
│   ├── internal/                    # Phase 3 で追加する ERD / 認証フロー / 外部連携
│   └── api/openapi.yaml             # OpenAPI 3.1 仕様書 (Phase 3)
├── public/                          # Web エントリポイント (DocumentRoot)
│   ├── index.php                    # 新ルータ or 旧 back/** への互換ブリッジ
│   ├── api/v1/index.php             # 新 REST API エントリ (FastRoute)
│   ├── assets/                      # 静的資産 (CSS/JS、将来 Vite ビルド出力)
│   └── .htaccess                    # mod_rewrite、back/ への直アクセスブロック
├── scripts/
│   └── migrate/                     # DB マイグレーション (raw SQL or Phinx 検討)
│       ├── 0001_init_utf8mb4.sql
│       └── 0002_journal_v2.sql
├── src/                             # [新] PSR-4 `Rucaro\\`
│   ├── Domain/                      # エンティティ・値オブジェクト・ドメインサービス
│   │   ├── Journal/                 # Journal 集約 (Phase 4)
│   │   ├── TrialBalance/            # 試算表読取モデル (Phase 4)
│   │   ├── AccountTitle/            # 勘定科目
│   │   ├── Entity/                  # 事業体
│   │   ├── FinancialStatement/      # 決算書 (Phase 5)
│   │   ├── Receipt/                 # 領収書ドラフト (Phase 5)
│   │   └── Shared/                  # 共通 VO (Money, Amount, DateRange ほか)
│   ├── Application/                 # ユースケース層 (CQRS-lite)
│   │   ├── Journal/
│   │   │   ├── CreateJournalUseCase.php
│   │   │   ├── SearchJournalUseCase.php
│   │   │   └── Ports/JournalRepositoryInterface.php
│   │   ├── Receipt/
│   │   └── Shared/Dto/
│   ├── Infrastructure/              # 外界 I/O 実装
│   │   ├── Database/
│   │   │   ├── ConnectionFactory.php
│   │   │   └── Repository/PdoJournalRepository.php
│   │   ├── Crypto/AesGcmCipher.php
│   │   ├── Logging/LoggerFactory.php
│   │   ├── AI/ClaudeClient.php
│   │   ├── Mail/SmtpMailer.php
│   │   └── Storage/ReceiptFileStore.php
│   ├── Http/                        # HTTP 層
│   │   ├── Kernel.php               # ミドルウェア実行器
│   │   ├── Middleware/              # Auth, RateLimit, Cors, JsonBody
│   │   ├── Api/V1/                  # 新 REST API コントローラ
│   │   │   ├── AuthController.php
│   │   │   ├── JournalController.php
│   │   │   └── ...
│   │   ├── Web/                     # 旧 UI 互換ハンドラ (必要最小限)
│   │   ├── View/SmartyRenderer.php  # 旧 Smarty を DI で包む
│   │   └── Response/ApiResponse.php # {success, data, error, meta}
│   └── Support/                     # 横断ユーティリティ
│       ├── Result.php               # 成功/失敗の直和型代替
│       ├── Clock.php                # 時刻抽象 (テスト可能)
│       ├── Uuid.php                 # UUIDv7 / ULID 発行
│       └── Validation/              # 入力検証 VO
├── storage/                         # ランタイム生成物 (.gitignore)
│   ├── receipts/YYYY/MM/<sha256>.*  # Phase 5 領収書 (content-addressed)
│   ├── logs/                        # Monolog 出力
│   ├── cache/                       # opcache 以外のファイルキャッシュ
│   └── tmp/                         # 一時ファイル
├── tests/
│   ├── Unit/                        # src/ をミラー。外部依存なし
│   ├── Integration/                 # DB + Docker 必要 (testcontainers or 専用 compose)
│   ├── E2E/                         # Playwright 経由のブラウザ / API スモーク
│   └── Fixtures/                    # ゴールデンデータ、サンプル領収書
├── vendor/                          # Composer 生成物 (.gitignore)
├── .env.example                     # 環境変数テンプレ (秘密値のダミー)
├── .gitignore
├── .php-cs-fixer.dist.php           # PSR-12 + Symfony risky
├── composer.json
├── composer.lock
├── docker-compose.yml
├── phpstan.neon                     # level 6→8 段階引き上げ
├── phpunit.xml.dist                 # testsuites: Unit / Integration / E2E
├── psalm.xml                        # level 3
├── rector.php                       # Php83Sets + CodeQualitySet
└── README.md
```

各ディレクトリの役割は 1 行コメントで明示した。`src/` 配下は 1 ファイル 1 クラス原則、ファイル名とクラス名を一致させる。

---

## 4. 命名規則

### 4.1 新コード (PSR-4)

| 要素 | ルール | 例 |
|------|--------|------|
| 名前空間 | PascalCase、`Rucaro\\{Layer}\\{BoundedContext}\\{SubModule}` | `Rucaro\Domain\Journal\Journal` |
| クラス | PascalCase、責務を表す名詞 | `CreateJournalUseCase`, `PdoJournalRepository` |
| インターフェース | 末尾 `Interface`（一般型）または Port 系は末尾 `Port` も可 | `JournalRepositoryInterface` |
| trait | 末尾 `Trait` | `LogsActivityTrait` |
| 例外 | 末尾 `Exception`、Domain 系は `DomainException` 派下 | `JournalBalanceMismatchException` |
| ファイル | クラス名 + `.php`、1 ファイル 1 クラス原則 | `Journal.php` |
| 定数 | `UPPER_SNAKE_CASE` | `const MAX_UPLOAD_BYTES = 50_000_000;` |
| メソッド / 変数 | camelCase、boolean は `is`/`has`/`can`/`should` 接頭辞 | `isBalanced()` |
| テストクラス | 対象クラス名 + `Test` | `JournalTest`, `CreateJournalUseCaseTest` |

### 4.2 旧 → 新 対比

| 旧クラス | 新相当 |
|----------|--------|
| `Code_Else_Plugin_Accounting_Jpn_Log` | `Rucaro\Domain\Journal\Journal`（集約） + `Rucaro\Application\Journal\SearchJournalUseCase` |
| `Code_Else_Plugin_Accounting_Jpn_AccountTitle` | `Rucaro\Domain\AccountTitle\AccountTitle` |
| `Code_Else_Plugin_Accounting_Jpn_Banks` | `Rucaro\Infrastructure\Bank\BankConnectorInterface` + 実装 |
| `Code_Else_Core_Base_Root` | `Rucaro\Http\Kernel` + `Rucaro\Http\Middleware\*` |
| `Code_Else_Core_Login_Login` | `Rucaro\Http\Api\V1\AuthController` |
| `Code_Else_Lib_Db` | `Rucaro\Infrastructure\Database\ConnectionFactory` |
| `Code_Else_Lib_Crypte` | `Rucaro\Infrastructure\Crypto\AesGcmCipher` |
| `Code_Else_Lib_Escape` | 新コードでは不要（prepared statement + htmlspecialchars ビルトイン） |

1 対 1 の機械的置換ではなく、責務を分解して再配置する点が重要。詳細マップは Phase 3 の `docs/internal/class-table-matrix.md` で確定させる。

---

## 5. 旧コードとの並走戦略

### 5.1 共存ルール

1. **旧ディレクトリ `back/` は一切編集しない**（読取専用運用）。Batch マイグレーションは旧アプリ側で完結。
2. 旧 `index.php` / `api.php` / `output.php` / `confirm.php` は当面維持。`public/.htaccess` で `/back/` への直アクセスは 403 とするが、エントリポイントはそのまま動く。
3. 新機能はすべて `public/api/v1/*` 以下に実装。旧 URL（`?class=Plugin&module=...`）と衝突しないよう、新 API はパスベース・旧 UI はクエリベースで分離。
4. 旧クラスが必要な場面（旧 DB を読み出して新 API に渡すなど）は、Composer `autoload.classmap` 経由でロード可能にする（§6）。ただし **新コードから旧クラスを直接 `new` しない**。必要なら `Rucaro\Infrastructure\Legacy\*` に薄いアダプタを置き、そこから呼ぶ。
5. 旧 DB 接続は継続稼働、新 DB は `ADR-002` で定義するスキーマで別スキーマ or 別 DB として構築。データ同期はバッチで行う（Phase 4 で設計）。

### 5.2 Strangler Fig 退役順序（想定）

1. Phase 1: 基盤構築のみ、旧アプリは無傷
2. Phase 3: 新 REST API 5 エンドポイント（参考実装）
3. Phase 4: Journal / TrialBalance の ReadModel を新 API で配信、UI はまだ旧
4. Phase 5: 領収書フローは新 API 単独。旧アプリには対応機能なし
5. 将来: フロント刷新後に旧 `index.php` を退役（スコープ外）

---

## 6. autoload 方針

### 6.1 `composer.json` の autoload セクション（サンプル）

```json
{
  "name": "rucaro/accounting",
  "type": "project",
  "require": {
    "php": "^8.3",
    "smarty/smarty": "^5.7",
    "vlucas/phpdotenv": "^5.6",
    "monolog/monolog": "^3.7",
    "guzzlehttp/guzzle": "^7.9",
    "dompdf/dompdf": "^3.0",
    "symfony/console": "^7.1",
    "nikic/fast-route": "^1.3",
    "ramsey/uuid": "^4.7"
  },
  "require-dev": {
    "phpunit/phpunit": "^11",
    "phpstan/phpstan": "^1.12",
    "vimeo/psalm": "^5.26",
    "friendsofphp/php-cs-fixer": "^3.64",
    "rector/rector": "^1.2"
  },
  "autoload": {
    "psr-4": {
      "Rucaro\\": "src/"
    },
    "classmap": [
      "back/class/else/"
    ],
    "files": []
  },
  "autoload-dev": {
    "psr-4": {
      "Rucaro\\Tests\\": "tests/"
    }
  },
  "config": {
    "sort-packages": true,
    "optimize-autoloader": true
  }
}
```

### 6.2 ポイント

- `psr-4` は **`Rucaro\\` → `src/` の 1 対 1 のみ**。他の名前空間は設けない（シンプルさ優先）。
- `classmap` に `back/class/else/` を含めることで、旧 `Code_Else_*` クラスも Composer の autoloader で解決可能になる。旧 `require_once` 前提のコードとの互換性のため、`composer dump-autoload -o` でのクラスマップ再生成を運用手順に含める。
- 旧コードは `classmap`、新コードは `psr-4`。名前空間プレフィックスで完全に識別可能。
- `autoload-dev.psr-4` で `Rucaro\Tests\` を `tests/` にマップ。production ビルド時は含まれない。

---

## 7. テスト配置

| 種別 | ディレクトリ | 命名 | 走行条件 |
|------|-------------|------|----------|
| Unit | `tests/Unit/**` (src/ をミラー) | `JournalTest.php`（対象クラス + `Test`） | 外部依存ゼロ、秒未満 |
| Integration | `tests/Integration/**` | `JournalRepositoryIntegrationTest.php` | MariaDB コンテナ必須 |
| E2E | `tests/E2E/**` | `journal_create.spec.ts`（Playwright） | Docker Compose + Apache 起動 |
| Fixtures | `tests/Fixtures/**` | seeds, golden files | データのみ |

- **目標カバレッジ 80%**（`common/testing.md` 準拠、Phase 2 終了時点）。
- `phpunit.xml.dist` に `testsuites` として `Unit`, `Integration`, `E2E` を個別定義し、CI では Unit を常時、Integration を PR 時、E2E を merge queue で実行。
- Property-based test は `tests/Unit/Domain/Journal/JournalPropertyTest.php` 相当（Phase 4 で `eris` 等導入を検討）。

---

## 8. 静的解析 / 整形

| ツール | 設定ファイル | 配置 | 役割 |
|--------|-------------|------|------|
| PHPStan | `phpstan.neon` | ルート | 型解析 (level 6→8 段階) |
| Psalm | `psalm.xml` | ルート | 追加型検査 (level 3) |
| PHP-CS-Fixer | `.php-cs-fixer.dist.php` | ルート | PSR-12 + Symfony risky |
| Rector | `rector.php` | ルート | PHP 8.3 イディオム自動適用 |
| PHPUnit | `phpunit.xml.dist` | ルート | テスト実行 |

解析スコープは **`src/` のみ**。`back/` は解析対象外（`phpstan.neon` の `excludePaths` に登録）。baseline はゼロ件から始め、旧コードのノイズを混入させない。

---

## 9. 公開ファイル (public/)

### 9.1 配置

```
public/
├── index.php           # 旧ルータ互換ブリッジ (当面 back/class/else/core/base/Base.php へディスパッチ)
├── api/v1/index.php    # 新 REST API エントリ (FastRoute + Kernel)
├── assets/             # 静的資産 (dev は Apache 直配信、本番は CDN 想定)
└── .htaccess           # mod_rewrite、back/ ブロック、/api/v1/ を index.php へ
```

### 9.2 `.htaccess`（方針）

```apache
RewriteEngine On

# /back/ への直アクセスは 403
RewriteRule ^back/ - [F,L]

# /api/v1/* は public/api/v1/index.php へ
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/v1/(.*)$ api/v1/index.php [QSA,L]

# それ以外は public/index.php (旧互換層)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

DocumentRoot は `docker/apache/000-default.conf` で `/var/www/html/public` に切り替え。既存の `back/.htaccess` の `deny from all` は維持。

---

## 10. 結果 (Consequences)

### 10.1 Pros

- **PSR-4 準拠** により Composer エコシステム（PHPStan / Psalm / Rector / PHPUnit）をフル活用できる。
- **レイヤ分離**（Domain / Application / Infrastructure / Http）によりテスト容易性が飛躍的に向上。DB を抜いた純粋ドメインテストが書ける。
- **Strangler Fig** により旧アプリを稼働させたまま段階移行可能。リスク逓減型。
- **名前空間で新旧を完全分離** できるため、IDE 補完・静的解析が安定する。
- **storage/ 分離** により Docker ボリューム戦略がクリーンになる（code / data / uploads / logs）。
- **1 ファイル 1 クラス + 800 行上限** により、`common/coding-style.md` および `common/code-review.md` の規律に自然に従える。

### 10.2 Cons / トレードオフ

- 旧クラス約 450 個を classmap 登録するため、`composer dump-autoload` の時間と生成ファイルサイズが増える（実測数百 ms 程度で許容範囲）。
- 旧 `back/` は将来的に「凍結コード」として残り続け、ブランチ保護ルールや CI で誤触を防ぐ運用規律が必要。
- 新旧で DB スキーマが異なるため、Phase 4 までは両系統の並走を誰が master とするかを明確化する必要がある（PLAN.md §R6）。
- 新レイヤ定義が重厚なため、Phase 1〜2 の学習曲線がある。ただし骨格が決まれば後続フェーズの実装は加速する。

### 10.3 移行不可能な残存負債

- 旧 Blowfish CBC 暗号データは、必要になったタイミングで [encrypted-columns.md](../phase1/encrypted-columns.md) §4 の互換レシピで個別復号する。再暗号化バッチは実装しない（PLAN.md §0 確定事項）。

---

## 11. 代替案と却下理由

### 11.1 Laravel（full framework）を導入

- **利点**: Eloquent / Blade / キューイング / Artisan がすぐ使える。求人市場も広い。
- **却下理由**:
  1. 旧 Smarty テンプレート群（数百ファイル）を Blade に機械変換するコストが莫大。
  2. Eloquent は Active Record で、会計ドメインの不変条件（借方合計 = 貸方合計）を集約として表現するのに向かない。DDD との相性が悪い。
  3. サービスプロバイダ / ファサードが増えるとレイヤ境界が曖昧になり、ヘキサゴナル設計との整合性が取りにくい。
  4. フレームワーク更新追従（Laravel 12 以降）がユーザ（個人運用）にとって継続的負担。

### 11.2 Symfony（full framework）を導入

- **利点**: コンポーネント単位で利用可能、DDD フレンドリ、長期サポートリリースあり。
- **却下理由**:
  1. フレームワーク全体を採用すると `src/` の構成が Symfony 流の `App\\` 強制になり、本 ADR のレイヤ分離が借り物になる。
  2. DIC YAML / Attribute 記述量が多く、個人運用のメンテナンス負荷が高い。
  3. 必要なコンポーネント（Console, HttpFoundation 相当）は個別に Composer 追加できる（`symfony/console` は既に採用）。full framework を入れる動機がない。

### 11.3 Slim / Lumen（micro framework）を採用

- **利点**: 軽量、FastRoute ベース、学習コスト低。
- **却下理由**:
  1. Slim 4 は DI コンテナ抽象が PSR-11 準拠で良好だが、`src/` のレイヤ設計までは規定してくれないので、結局本 ADR と同じ判断を自前で下す必要がある。
  2. Lumen は Laravel Zero 系で、Laravel を入れないための選択肢にならない（メンテ終了済に近い）。
  3. 本プロジェクトのスコープ（REST API + CLI + Web UI 互換 + AI パイプライン + PDF 生成）では、micro の利点より統合的な `src/` 設計のほうが重要。
  4. 必要な機能は `nikic/fast-route` + `guzzlehttp/psr7` の直接組合せで十分賄える。

### 11.4 `back/` 直下に新コードを追加して単一ツリーにする

- **却下理由**: 名前空間衝突、classmap 肥大化、解析対象の分離困難。レイヤが混ざって Strangler Fig の境界が曖昧になる。

---

## 12. 実装チェックリスト (Phase 1.3)

以下を Phase 1.3 の作業項目として登録する（PLAN.md §1.3 を具体化）:

- [ ] `src/{Domain,Application,Infrastructure,Http,Support}/` の空ディレクトリ作成と `.gitkeep` 設置
- [ ] `tests/{Unit,Integration,E2E,Fixtures}/` の空ディレクトリ作成
- [ ] `config/{app,database,logging,crypto,routing,services}.php` の typed config スケルトン
- [ ] `public/index.php` に旧 `index.php` へのブリッジを実装（`require_once` で旧エントリを呼ぶだけの 10 行程度）
- [ ] `public/api/v1/index.php` に FastRoute + Kernel の最小実装（404 と `/api/v1/healthz` のみ）
- [ ] `public/.htaccess` を §9.2 の方針で新規作成、DocumentRoot を `public/` に切替
- [ ] `docker/apache/000-default.conf` に `DocumentRoot /var/www/html/public` を反映
- [ ] `composer.json` を §6.1 の内容で作成、`composer install` → `composer dump-autoload -o`
- [ ] `.env.example` と `vlucas/phpdotenv` の読込コードを `public/index.php` 冒頭に追加
- [ ] `phpunit.xml.dist` に Unit/Integration/E2E の testsuites を定義、サンプル Unit test `tests/Unit/Support/ClockTest.php` を追加して PHPUnit が動くことを確認
- [ ] `phpstan.neon` を level 6、`paths: [src]`、`excludePaths: [back]` で初期化
- [ ] `psalm.xml` を level 3、`<projectFiles>` で `src/` のみ指定
- [ ] `.php-cs-fixer.dist.php` を PSR-12 + `@Symfony:risky` で作成、`back/` を除外
- [ ] `rector.php` を `Php83Sets::PHP_83` + `SetList::CODE_QUALITY` で初期化
- [ ] `.github/workflows/ci.yml` に PHP 8.3 + MariaDB 10.11 のマトリクスで Unit → PHPStan → PHP-CS-Fixer (dry) → PHPUnit を走らせるジョブを追加
- [ ] `storage/{receipts,logs,cache,tmp}/` を作成し `.gitignore` に登録（`storage/.gitignore` で negate で `.gitkeep` のみ許可）
- [ ] `back/class/else/lib/Smarty/` 死蔵ディレクトリを削除（Composer vendor 側を使うため、PLAN.md §1.3 の既存タスク）
- [ ] 本 ADR を `docs/adr/ADR-001-directory-layout.md` としてコミットし、後続 ADR-002 / ADR-003 のリンクをプレースホルダで張っておく
- [ ] `README.md` にディレクトリ構成の概要節を追加（本 ADR へのリンク付き）

---

## 付録 A: レイヤ間の依存方向

```
  Http  ─────▶  Application  ─────▶  Domain
   │                 │                 ▲
   │                 ▼                 │
   └────────▶  Infrastructure  ────────┘
                     │
                 (外界 I/O: DB, AI, Mail, File)
```

- `Domain` は他のどの層にも依存しない。
- `Application` は `Domain` にのみ依存（Port を定義）。
- `Infrastructure` は `Application` の Port を実装し、`Domain` を参照する。
- `Http` は `Application` のユースケースを呼び出すだけ。`Domain` には直接触らない（DTO 経由）。
- `Support` はどこからも呼び出し可能（ユーティリティのため）。

依存規律は PHPStan の `deptrac` 追加導入で機械検証を検討する（Phase 2 末）。
