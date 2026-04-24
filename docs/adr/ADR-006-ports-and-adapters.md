# ADR-006: Ports & Adapters

- ステータス: 提案中 (2026-04-21)
- 決定者: (ユーザ承認待ち)
- 関連: ADR-001（新ディレクトリ構成）, ADR-005（レイヤドアーキテクチャ）, ADR-007
- 参照資料: `docs/INTERNAL_ARCHITECTURE.md` §4, `docs/internal/external-integrations.md`, `docs/api/openapi.yaml`, `src/Domain/**`, `src/Infrastructure/**`

---

## 1. 文脈 (Context)

Phase 3 までは「HTTP 入力 → UseCase → Repository → PDO/MariaDB」という単一の I/O 経路で閉じており、外界との接点は **DB のみ**だった。ADR-001 / ADR-005 で決めた 5 層構成（Domain / Application / Infrastructure / Http / Support）でも十分に対応できた。

しかし Phase 4 から Phase 5 にかけて、外部境界が一気に増える:

| 新規境界 | 方向 | 用途 |
|---|---|---|
| Claude API (Anthropic) | 外向き | 領収書 OCR・仕訳下書き生成（Phase 5） |
| 5 銀行取込（CSV → 公式 API） | 外向き | 明細取込（Phase 5、`docs/internal/external-integrations.md`） |
| 中央プロキシ `rucaro.org/banks.php` | 外向き | 旧銀行スクレイピング互換（Phase 5、TLS 検証無効の既知問題あり） |
| IMAP サーバ（任意） | 外向き（pull） | メール取込（Phase 5、`ext-imap` 追加予定） |
| SMTP サーバ | 外向き | 領収書承認メール・決算書通知（Phase 5） |
| LINE / Slack | 外向き | 承認メッセージングチャネル（Phase 5） |
| ローカルファイル / S3 互換 | 双方向 | 領収書ストレージ、PDF 出力（Phase 5） |
| タイムスタンプ局（TSA） | 外向き | 決算書の長期署名検証（Phase 5 任意） |

これらすべてを `Rucaro\Infrastructure\**` に **素直な実装クラス**として置き、`Rucaro\Application\**UseCase` から直接 `new` すると、domain/application 層に外部 SDK（`GuzzleHttp\Client`, `PhpImap\Mailbox`, `Aws\S3\S3Client` 等）の型が染み出す。これは ADR-005 が禁じる「層の逆流」であり、かつ以下の具体的な痛みを発生させる:

1. **Unit テスト不可**: UseCase を動かすためだけにネットワーク / ファイルシステム / DB を立てる羽目になる。
2. **環境切替不能**: `.env` で `MAIL_ADAPTER=null`（テスト用）/ `smtp`（本番）を切り替えたいが、UseCase が具象に依存していると不可能。
3. **ライブラリロックイン**: Guzzle を捨てて Symfony HttpClient に移行したくなっても、全 UseCase を触ることになる。
4. **domain 例外 vs infra 例外のリーク**: `GuzzleHttp\Exception\ConnectException` が UseCase 層まで飛んできてしまい、HTTP 層で何を 502 / 503 / 504 に割り振るかの責務が曖昧になる。

本 ADR は、上記を根本的に封じるために **ヘキサゴナルアーキテクチャ（Ports & Adapters）** をアプリ全体で正式採用することを宣言する。ADR-005 が「どの層に何を置くか」を決めたのに対し、本 ADR は「**層の境界を interface（ポート）で切り、実装（アダプタ）を差し替え可能にする**」という **依存方向の規律**を決める。

---

## 2. 決定 (Decision)

### 2.1 2 種類のポート

Alistair Cockburn の原論文に倣い、ポートを 2 種類に分ける:

| 種別 | 別名 | 役割 | 本プロジェクトでの所在 |
|---|---|---|---|
| **Primary（Driving）ポート** | Input Port / Inbound | **外界がアプリを駆動する入口**。HTTP / CLI / Cron / Queue から呼ばれる | `Rucaro\Application\**UseCase`（クラス自体がポート） |
| **Secondary（Driven）ポート** | Output Port / Outbound | **アプリが外界を駆動するための出口**。DB・API・Mail・File への依頼 | `Rucaro\Domain\**RepositoryInterface` および `Rucaro\Application\Port\**Interface` |

#### 2.1.1 Primary ポートはクラス

Primary ポートは専用の `Interface` を作らず、UseCase クラス自体を契約とする。理由:

- 1 UseCase = 1 操作 = 1 クラスの原則（ADR-005 §3.2）を既に採用済みで、クラスシグネチャが実質的な契約になっている
- PHP には構造的型がないため、interface を作ると UseCase 実装を差し替えたくなったときに interface 側の `execute()` シグネチャ変更が波及してしまう
- HTTP Controller と CLI Command が同じ UseCase を呼ぶだけならクラスで十分
- Mock 化が必要な稀なケースは PHPUnit の `createMock(CreateJournalUseCase::class)` で対応可能（PHP のクラスは `final` でなければ継承モック可）

ただし UseCase クラスは `final readonly` で宣言し、副作用は外部化された Secondary ポートに閉じ込める。

#### 2.1.2 Secondary ポートは interface

Secondary ポートは **必ず interface を作り**、具象を使わない。これは ADR-005 の「Application は Infrastructure を参照してはならない」ルールを技術的に強制する。

### 2.2 レイヤとポートの対応図

```
                ┌───────────────────────────────────────┐
                │  Driving Adapters (外界がアプリを叩く) │
                │  - Http\Controller\**                  │
                │  - Http\Cli\**Command (Phase 5)        │
                │  - 将来: Queue Consumer, Cron          │
                └──────────────┬────────────────────────┘
                               │ calls
                               ▼
                ┌──────────────────────────────────────┐
                │  Primary Ports                        │
                │  Rucaro\Application\**UseCase         │ ← 入口
                └──────────────┬────────────────────────┘
                               │ uses
                               ▼
                ┌──────────────────────────────────────┐
                │  Domain                               │
                │  Rucaro\Domain\**                     │
                │  （エンティティ・集約・VO・不変条件）   │
                └──────────────┬────────────────────────┘
                               │ depends on (via interface)
                               ▼
                ┌──────────────────────────────────────┐
                │  Secondary Ports                      │
                │  Rucaro\Domain\**RepositoryInterface  │
                │  Rucaro\Application\Port\**Interface  │ ← 出口
                └──────────────┬────────────────────────┘
                               │ implemented by
                               ▼
                ┌──────────────────────────────────────┐
                │  Driven Adapters (アプリが外界を叩く) │
                │  - Infrastructure\**\Pdo*Repository   │
                │  - Infrastructure\AI\Guzzle*Client    │
                │  - Infrastructure\Mail\Smtp*Sender    │
                │  - Infrastructure\BankImport\Csv*     │
                │  - Infrastructure\Storage\Local*      │
                └──────────────────────────────────────┘
```

### 2.3 ポート Interface の所在ルール

| ポート | 所在 | 理由 |
|---|---|---|
| Entity を永続化する `XxxRepositoryInterface` | `src/Domain/{Aggregate}/` | Entity と密接で、不変条件の一部（例: 重複防止）を表現することがあるため Domain に置く |
| 純粋に外部依存を抽象化する `XxxInterface` | `src/Application/Port/{Capability}/` | Domain には I/O の概念を持ち込まないため。Application が「自分の依頼先」として定義する |
| 時計・UUID など横断抽象 | `src/Support/{Capability}/` | どの層からも参照可能なインフラ抽象 |

**分岐基準**:

- 集約のライフサイクル（作成・検索・更新・削除）を表現するなら **Domain に Repository** として置く
- 外部サービスへの「依頼（tell）」を表現するなら **Application/Port に Interface** として置く
- 時間・乱数・ID など処理系の要素は **Support に Interface** として置く

### 2.4 命名規則

| 種別 | パターン | 例 |
|---|---|---|
| Primary ポート | `{Action}{Aggregate}UseCase` | `CreateJournalUseCase`, `ExtractReceiptUseCase` |
| Secondary ポート（Repository） | `{Aggregate}RepositoryInterface` | `JournalRepositoryInterface`, `UserRepositoryInterface` |
| Secondary ポート（Capability） | `{Capability}Interface` | `ClaudeClientInterface`, `MailSenderInterface`, `ReceiptStorageInterface` |
| Driven Adapter（DB） | `Pdo{Aggregate}Repository` | `PdoJournalRepository`, `PdoUserRepository` |
| Driven Adapter（ライブラリ依存） | `{Technology}{Capability}` | `GuzzleClaudeClient`, `SmtpMailSender`, `DompdfFinancialStatementGenerator` |
| Driven Adapter（Null / Stub） | `Null{Capability}` / `Stub{Capability}` / `InMemory{Aggregate}Repository` | `NullMailSender`, `StubClaudeClient`, `InMemoryJournalRepository` |

**接尾辞 `Port` は使わない**: PHP コミュニティでは `Interface` 接尾辞が一般的で、既存コードベース（ADR-001 で確定済）との整合性を優先する。

---

## 3. ポート／アダプタ マトリクス

Phase 3 完了時点と Phase 4〜5 予定を一覧化する。

| # | ポート Interface | 役割 | Adapter | Adapter 所在 | 実装状況 |
|---|---|---|---|---|---|
| 1 | `JournalRepositoryInterface` | 仕訳集約の永続化・検索 | `PdoJournalRepository` | `src/Infrastructure/Journal/` | Phase 3 で最小実装済。Phase 4 で `RecalcBalancesUseCase` 用に拡張 |
| 2 | `UserRepositoryInterface` | ユーザ永続化 | `PdoUserRepository` | `src/Infrastructure/User/` | Phase 3 実装済 |
| 3 | `EntityRepositoryInterface` | 会計主体（複数企業対応） | `PdoEntityRepository` | `src/Infrastructure/Entity/` | Phase 3 実装済 |
| 4 | `AccountTitleRepositoryInterface` | 勘定科目マスタ | `PdoAccountTitleRepository` | `src/Infrastructure/AccountTitle/` | Phase 3 実装済 |
| 5 | `ApiTokenRepositoryInterface` | Bearer token（SHA-256 ハッシュ保存） | `PdoApiTokenRepository` | `src/Infrastructure/Auth/` | Phase 3 実装済 |
| 6 | `CipherInterface` | 対称鍵暗号（AAD 付き） | `AesGcmCipher` / `VersionedCipher` / `LegacyBlowfishDecryptor` | `src/Infrastructure/Crypto/` | Phase 2 実装済（ADR-003） |
| 7 | `ClockInterface` | 現在時刻取得 | `SystemClock` | `src/Support/Clock/` | Phase 1 実装済 |
| 8 | `TrialBalanceReadModelInterface` | 試算表 read model（ReadOnly） | `PdoTrialBalanceQueryService` | `src/Infrastructure/TrialBalance/` | **Phase 4 で実装** |
| 9 | `LedgerQueryServiceInterface` | 総勘定元帳 read model | `PdoLedgerQueryService` | `src/Infrastructure/Ledger/` | Phase 4 予定 |
| 10 | `FinancialStatementGeneratorInterface` | 決算書（BS / PL）生成 | `DompdfFinancialStatementGenerator` / `HtmlFinancialStatementGenerator` | `src/Infrastructure/FinancialStatement/` | Phase 5 予定 |
| 11 | `BankImportClientInterface` | 銀行取込（CSV + Web） | `CsvBankImportClient` / `ProxyBankImportClient`（`rucaro.org/banks.php` 互換、公式 API 提供後に差替） | `src/Infrastructure/BankImport/` | Phase 5 予定 |
| 12 | `ImapMailboxInterface` | 受信メール pull（IMAP4） | `PhpImapMailbox` / `FakeImapMailbox`（fixture 固定 mbox） | `src/Infrastructure/Mail/Imap/` | Phase 5 予定（`ext-imap` 追加後） |
| 13 | `MailSenderInterface` | メール送信（SMTP） | `SmtpMailSender` / `NullMailSender` / `ArrayMailSender`（テスト用、送信内容を配列に蓄積） | `src/Infrastructure/Mail/` | Phase 5 予定 |
| 14 | `MessagingChannelInterface` | LINE / Slack 通知 | `LineMessagingChannel` / `SlackChannel` / `NullChannel` | `src/Infrastructure/Messaging/` | Phase 5 予定 |
| 15 | `ClaudeClientInterface` | Claude API（Messages API） | `GuzzleClaudeClient` / `StubClaudeClient`（固定レスポンスを返す） | `src/Infrastructure/AI/` | Phase 5 予定 |
| 16 | `ReceiptStorageInterface` | 領収書ファイルストレージ（content-addressed） | `LocalReceiptStorage` / `S3ReceiptStorage`（将来） | `src/Infrastructure/Storage/` | Phase 5 予定 |
| 17 | `TimestampAuthorityInterface` | RFC 3161 タイムスタンプ取得 | `RemoteTimestampAuthority` / `NullTimestampAuthority` | `src/Infrastructure/Tsa/` | Phase 5 任意 |
| 18 | `UlidGeneratorInterface`（※現状クラス） | ULID 発行 | `UlidGenerator`（既存） | `src/Infrastructure/Ulid/` | Phase 3 実装済。Phase 4 で interface 化を検討 |

### 3.1 実装済ポートの表現例

現行の `CipherInterface`（抜粋、実コード `src/Infrastructure/Crypto/CipherInterface.php`）は、本 ADR の「Secondary ポートは AAD 等のコンテキストを含めて契約する」考え方の具体例:

```php
interface CipherInterface
{
    public function encrypt(string $plaintext, string $aad = ''): string;
    public function decrypt(string $ciphertext, string $aad = ''): string;
}
```

AAD（Additional Authenticated Data）を契約に含めることで、「どのテーブルのどの行の暗号文か」というコンテキストをアダプタが必ず受け取るよう強制している。

### 3.2 interface 所在の例外

`CipherInterface` は Domain ではなく `src/Infrastructure/Crypto/` に置かれている（既存実装）。これは Phase 2 時点で「Crypto は純粋なインフラ機能である」と判断したためで、ADR-003 の決定を尊重する。本 ADR で再配置はしない。Phase 4 以降の新規ポートは §2.3 の分岐基準に従う。

---

## 4. テスト戦略

### 4.1 Unit テスト: Fake / Stub Adapter を使う

**原則**: UseCase の unit テストは、DB / ネットワーク / ファイルシステムに触れない。

- `tests/Support/Fake/` にテスト専用の in-memory / null / stub adapter をまとめる（Phase 4 で新設）
- Fake は **domain の不変条件はそのまま検証する**（例: `InMemoryJournalRepository::save()` は balance 不一致を受け取ったら例外を投げる）が、永続化は `array` で済ませる

典型的な fake 一覧（Phase 4 以降に追加予定）:

| Fake | 対象ポート | 実装方針 |
|---|---|---|
| `InMemoryJournalRepository` | `JournalRepositoryInterface` | `private array $storage`、`searchByEntity()` は array_filter |
| `InMemoryUserRepository` | `UserRepositoryInterface` | 同上 |
| `FakeClock` | `ClockInterface` | コンストラクタで `DateTimeImmutable` を固定、`tick(int $seconds)` で進められる |
| `FakeUlidGenerator` | `UlidGeneratorInterface` | 単調増加のシーケンスを返す（テストでアサート可能） |
| `StubClaudeClient` | `ClaudeClientInterface` | `setResponse(array $fixture)` で次の呼出しの戻り値を注入 |
| `ArrayMailSender` | `MailSenderInterface` | `sentMessages()` で送信履歴を取得 |
| `NullChannel` | `MessagingChannelInterface` | 何もしない（notification OFF 検証用） |
| `InMemoryReceiptStorage` | `ReceiptStorageInterface` | `sha256 => content` の連想配列 |

### 4.2 Integration テスト: 実 Adapter を使う

- `tests/Integration/Infrastructure/**` に、`PdoJournalRepository` 等の実 adapter を MariaDB 10.11 サービス（`docker compose`）に接続して走らせる
- 外部 API 呼出は実行せず、HTTP レベルの mock（Guzzle の `MockHandler`）で閉じる
- IMAP / SMTP はローカルの `GreenMail` や `MailHog` を起動する（Phase 5 で compose profile を追加）

### 4.3 E2E テスト: 全 Adapter を本物で

- `tests/E2E/**` では Adapter 設定を本番と同等にし、Playwright + FastRoute で end-to-end シナリオを回す
- 外部サービス（Claude / LINE / Slack）は **テストモード API キー** を利用し、`.env.e2e` で切替

### 4.4 テスト用 Adapter の配置ルール

| 用途 | 配置 | Autoload |
|---|---|---|
| Fake（本物の置き換え） | `tests/Support/Fake/` | `autoload-dev.psr-4: Rucaro\Tests\Support\Fake\` |
| Stub（固定応答） | `tests/Support/Stub/` | 同上 |
| Fixture（入力データ） | `tests/Fixtures/` | PSR-4 対象外（ファイル直読み） |

production bundle には決して混ざらないよう、`composer install --no-dev` で落ちることを CI で確認する。

---

## 5. 依存解決（DI）

### 5.1 既存 ContainerBootstrap の拡張

Phase 3 で導入済の `src/Support/Container/ContainerBootstrap.php` を Phase 4 で拡張する。現状は PDO と UseCase をハードコードで `set()` しているが、Phase 4 では以下を追加:

- 環境変数で adapter を切替えるロジック（§5.2）
- 各 Secondary ポートに対応する Closure を登録
- 遅延生成（`set()` の第 2 引数は factory）により、実際に使われるまで Claude SDK などを new しない

### 5.2 環境変数による Adapter 切替

`.env` の feature flag で Adapter を切替可能にする:

```bash
# メール送信（smtp=本番, null=開発, array=unit テスト）
MAIL_ADAPTER=smtp
SMTP_HOST=localhost
SMTP_PORT=1025

# Claude API（guzzle=本番, stub=開発）
CLAUDE_ADAPTER=guzzle
CLAUDE_API_KEY=sk-ant-...

# メッセージング（line / slack / null）
MESSAGING_ADAPTER=null

# 銀行取込（csv=CSV のみ, proxy=旧 rucaro.org 経由, hybrid=公式 API 対応銀行は公式優先）
BANK_IMPORT_ADAPTER=csv

# 領収書ストレージ（local=ローカルFS, s3=S3 互換）
RECEIPT_STORAGE_ADAPTER=local
RECEIPT_STORAGE_PATH=/var/www/html/storage/receipts
```

ContainerBootstrap の該当箇所（Phase 4 でのイメージ）:

```php
$c->set(MailSenderInterface::class, static function (Container $c): MailSenderInterface {
    $driver = $_ENV['MAIL_ADAPTER'] ?? 'null';
    return match ($driver) {
        'smtp'  => new SmtpMailSender(
            host: $_ENV['SMTP_HOST'],
            port: (int) $_ENV['SMTP_PORT'],
            clock: $c->getTyped(ClockInterface::class),
        ),
        'null'  => new NullMailSender(),
        'array' => new ArrayMailSender(),
        default => throw new InvalidAdapterException("Unknown MAIL_ADAPTER: {$driver}"),
    };
});
```

### 5.3 起動時検証

boot 時に必須 Adapter（例: production で `MAIL_ADAPTER=null` は禁止）を検証する。これは `public/index.php` と `public/api/v1/index.php` の冒頭、Dotenv 読込直後に `EnvironmentValidator::assertProductionReady()` を呼ぶ形で実装する（Phase 4 で追加）。

---

## 6. 境界を守る規約

### 6.1 import 禁止事項

| 層 | import 禁止 | 許可 |
|---|---|---|
| `Rucaro\Domain\**` | `\PDO`, `\GuzzleHttp\*`, `\PhpImap\*`, `\Aws\*`, `\Dompdf\*`, `\Psr\Log\*` を含むあらゆる infra 型 | 標準 PHP 型（`DateTimeImmutable` など）、`Rucaro\Support\**`（※ interface のみ） |
| `Rucaro\Application\**` | 上記 infra 型、ならびに `Rucaro\Infrastructure\**` の具象クラス | `Rucaro\Domain\**`、`Rucaro\Application\Port\**Interface`、`Rucaro\Support\**` の interface |
| `Rucaro\Http\**` | `Rucaro\Infrastructure\**` の具象（※ DI コンテナ経由でのみ参照） | `Rucaro\Application\**UseCase`、`Rucaro\Support\**` |
| `Rucaro\Infrastructure\**` | Http 層の型 | Domain / Application の interface、外部 SDK |

### 6.2 静的検証

Phase 4 で `deptrac`（PHP の層違反検出ツール）を導入し、`.deptrac.yaml` で以下を検証:

```yaml
layers:
  - name: Domain
    collectors: [{ type: classLike, regex: ^Rucaro\\Domain\\.* }]
  - name: Application
    collectors: [{ type: classLike, regex: ^Rucaro\\Application\\.* }]
  - name: Infrastructure
    collectors: [{ type: classLike, regex: ^Rucaro\\Infrastructure\\.* }]
  - name: Http
    collectors: [{ type: classLike, regex: ^Rucaro\\Http\\.* }]

ruleset:
  Domain: []
  Application: [Domain]
  Http: [Application]
  Infrastructure: [Domain, Application]
```

CI に組み込み、違反は PR ブロック。

### 6.3 例外の取り扱い

外部依存は infra 固有の例外を投げる。これを **そのまま** UseCase や Controller に伝搬させない。ルールは以下:

1. Adapter 内部で try/catch し、infra 例外（`GuzzleHttp\Exception\ConnectException`, `PDOException` 等）を捕捉する
2. Adapter が定義する infra 例外（例: `ClaudeApiException extends \RuntimeException`）にラップして再送出
3. Application 層（UseCase）は infra 例外を受け取ったら、domain 例外（`ReceiptExtractionFailedException extends DomainException`）に変換して投げる
4. Http Controller は domain 例外をマッピング規則（Phase 3 で確立済、ADR-005 参照）で HTTP ステータスに変換する

例外マッピングの所在は **UseCase**（Application 層）。理由は「HTTP ステータス or CLI 終了コードへの翻訳は駆動側の関心事だが、domain 的に何が起きたかの判断は Application が握るべき」であるため。Domain 層は自身の domain 例外のみを知る。

---

## 7. 具体コード例: Claude API ポート

### 7.1 ポート定義（Application 層）

```php
<?php
// src/Application/Port/AI/ClaudeClientInterface.php
declare(strict_types=1);

namespace Rucaro\Application\Port\AI;

use Rucaro\Application\Port\AI\Dto\ClaudeMessage;
use Rucaro\Application\Port\AI\Dto\ClaudeResponse;
use Rucaro\Application\Port\AI\Exception\ClaudeApiException;

interface ClaudeClientInterface
{
    /**
     * Send a chat completion request.
     *
     * @param list<ClaudeMessage> $messages
     * @throws ClaudeApiException on transport / 5xx / timeout failures.
     */
    public function sendMessages(
        string $model,
        array $messages,
        int $maxTokens = 4096,
    ): ClaudeResponse;
}
```

### 7.2 Adapter 実装（Infrastructure 層）: 本番用

```php
<?php
// src/Infrastructure/AI/GuzzleClaudeClient.php
declare(strict_types=1);

namespace Rucaro\Infrastructure\AI;

use GuzzleHttp\ClientInterface as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Rucaro\Application\Port\AI\ClaudeClientInterface;
use Rucaro\Application\Port\AI\Dto\ClaudeMessage;
use Rucaro\Application\Port\AI\Dto\ClaudeResponse;
use Rucaro\Application\Port\AI\Exception\ClaudeApiException;

final readonly class GuzzleClaudeClient implements ClaudeClientInterface
{
    public function __construct(
        private HttpClient $http,
        private string $apiKey,
        private string $baseUri = 'https://api.anthropic.com/v1/',
    ) {}

    public function sendMessages(string $model, array $messages, int $maxTokens = 4096): ClaudeResponse
    {
        try {
            $response = $this->http->request('POST', $this->baseUri . 'messages', [
                'headers' => [
                    'x-api-key'         => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    'model'      => $model,
                    'max_tokens' => $maxTokens,
                    'messages'   => array_map(
                        static fn (ClaudeMessage $m): array => ['role' => $m->role, 'content' => $m->content],
                        $messages,
                    ),
                ],
                'timeout' => 60,
            ]);
        } catch (GuzzleException $e) {
            throw new ClaudeApiException('Claude API transport error: ' . $e->getMessage(), 0, $e);
        }

        $body = (string) $response->getBody();
        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        return ClaudeResponse::fromArray($decoded);
    }
}
```

### 7.3 Adapter 実装（Infrastructure 層）: テスト用

```php
<?php
// src/Infrastructure/AI/StubClaudeClient.php
declare(strict_types=1);

namespace Rucaro\Infrastructure\AI;

use Rucaro\Application\Port\AI\ClaudeClientInterface;
use Rucaro\Application\Port\AI\Dto\ClaudeResponse;

final class StubClaudeClient implements ClaudeClientInterface
{
    private ?ClaudeResponse $nextResponse = null;

    public function setNextResponse(ClaudeResponse $response): void
    {
        $this->nextResponse = $response;
    }

    public function sendMessages(string $model, array $messages, int $maxTokens = 4096): ClaudeResponse
    {
        return $this->nextResponse ?? ClaudeResponse::empty();
    }
}
```

### 7.4 UseCase が interface 経由で呼ぶ（Application 層）

```php
<?php
// src/Application/Receipt/ExtractReceiptUseCase.php
declare(strict_types=1);

namespace Rucaro\Application\Receipt;

use Rucaro\Application\Port\AI\ClaudeClientInterface;
use Rucaro\Application\Port\AI\Dto\ClaudeMessage;
use Rucaro\Application\Port\AI\Exception\ClaudeApiException;
use Rucaro\Domain\Receipt\Exception\ReceiptExtractionFailedException;
use Rucaro\Domain\Receipt\ExtractedReceipt;

final readonly class ExtractReceiptUseCase
{
    public function __construct(private ClaudeClientInterface $claude) {}

    public function execute(string $receiptText): ExtractedReceipt
    {
        try {
            $response = $this->claude->sendMessages(
                model: 'claude-opus-4-5',
                messages: [new ClaudeMessage('user', $this->buildPrompt($receiptText))],
                maxTokens: 2048,
            );
        } catch (ClaudeApiException $e) {
            // infra 例外 → domain 例外へ翻訳（§6.3 のルール）
            throw ReceiptExtractionFailedException::fromInfrastructure($e);
        }

        return ExtractedReceipt::fromClaudeResponse($response);
    }

    private function buildPrompt(string $text): string { /* ... */ }
}
```

この 4 ファイルは全体で 30 行程度しか互いを知らない。`ExtractReceiptUseCase` は **`ClaudeClientInterface` しか知らず**、Guzzle も HTTP も JSON も一切意識しない。テストでは `StubClaudeClient` を `new` して `setNextResponse()` するだけで unit test が書ける。

---

## 8. 結果 (Consequences)

### 8.1 Pros

1. **Unit テストの独立性**: UseCase が in-memory fake だけで完結し、`tests/Unit/Application/**` が 0.x 秒で完走できる。
2. **Adapter 差替の容易さ**: 旧銀行プロキシ（`rucaro.org/banks.php`）から公式銀行 API への移行が、`BankImportClientInterface` を実装する新 adapter の追加 + `.env` 1 行変更で済む。
3. **CI での外部依存回避**: Claude / SMTP / IMAP を本番キーでテストする必要がない。CI は `StubClaudeClient` / `NullMailSender` で走る。
4. **例外の層別整理**: infra 例外（ネット断、認証エラー、5xx）と domain 例外（balance 不一致、欠番）が自然に分離され、HTTP ステータス / CLI 終了コードへの翻訳が明確化される。
5. **新規参入者の認知負荷軽減**: ポート一覧（§3 のマトリクス）を見れば「このアプリが外界と何個の境界を持っているか」が一望できる。
6. **deptrac による機械検証**: 規約違反（UseCase が具象 Pdo を使う等）が PR 時に自動検出される。

### 8.2 Cons / トレードオフ

1. **Interface の増加**: Phase 5 末時点でポート interface が 18 個前後になる。`src/Application/Port/` 配下のファイル数が多く、grep 時のノイズが増える。
2. **DTO の往復コスト**: Adapter が返す外部レスポンス（JSON 配列）を `ClaudeResponse` 等の domain/application DTO に詰め替える処理が必要。ランタイムコストは無視できる範囲だが、コード量は増える。
3. **学習コスト**: Junior 開発者が「なぜ UseCase から直接 Guzzle を呼んではいけないのか」を理解するまで時間がかかる。ADR-005 と本 ADR を README からリンクしてオンボーディング資料とする。
4. **過剰抽象のリスク**: 単一実装しかない Secondary ポートも interface を作ることになる（例: `ClockInterface` + `SystemClock` のみ）。これは「テストのために必要」と割り切る（YAGNI に反しない）。
5. **`final readonly` と interface 継承の相性**: PHP 8.4 以降は `final` クラスでも interface を実装できるので問題なし。PHP 8.3 の本プロジェクトでも実用上の支障なし。

### 8.3 却下した緩和案

- 「Interface を廃止して抽象クラスにする」: PHP の単一継承制約があり、複数のポートを実装するクラスが作れなくなる。却下。
- 「Port を 1 箇所（`src/Port/`）に集約する」: Domain と Application の関心分離が失われる。却下。

---

## 9. 代替案と却下理由

### 9.1 Active Record（Eloquent 相当）

- **利点**: 1 テーブル 1 クラスで記述量が少ない。
- **却下理由**:
  1. Domain クラスが ORM 基底クラスを継承するため、Domain が Infrastructure に依存する（層の逆流）。ADR-005 の大前提を破る。
  2. 複雑な不変条件（借方合計 = 貸方合計、is_alive による論理削除、承認フロー）を Active Record で表現すると、ビジネスロジックが setter に散らばる。
  3. ORM のバージョンアップ / 置換が domain まで波及する（ロックイン）。
  4. Unit テストで毎回 DB を立てる運用になりやすい。

### 9.2 直接 PDO 呼出（Repository interface を作らない）

- **利点**: コード量最少、直感的。
- **却下理由**:
  1. UseCase のテストに実 DB が必須になり、テスト時間が爆発する。
  2. 将来 PDO を外したくなった（例: ReadModel を ClickHouse に移す）ときに、全 UseCase を書き直す羽目になる。
  3. `\PDO` 型が domain/application まで染み出す。

### 9.3 フレームワーク直結（Laravel / Symfony Controller で完結）

- **利点**: ボイラープレート最少、求人市場も広い。
- **却下理由**:
  1. Laravel Controller や Symfony Controller に business logic を書くと、HTTP 前提の処理と domain 処理が癒着する。
  2. 同じ domain を CLI から叩きたい（`bin/cowork receipts:ingest`）ときに再利用できない。
  3. ADR-001 で Laravel/Symfony full framework は却下済。本 ADR でもこの判断を踏襲。

### 9.4 Service Locator パターン（Container を引数で渡す）

- **利点**: DI コンストラクタ地獄を回避できる。
- **却下理由**:
  1. UseCase が「自分が何に依存しているか」を外から読み取れなくなる（依存が暗黙になる）。
  2. テストで全 adapter を stub するのが難しくなる。
  3. PHPStan / Psalm が型追跡できない。

---

## 10. 実装チェックリスト

Phase 4〜5 で adapter を追加する際、以下を毎回守る:

### 10.1 新規ポート追加時

- [ ] interface を適切な層に配置したか（§2.3 の分岐基準）
- [ ] interface のメソッドに `@throws` PHPDoc を書き、どの infra 例外をラップするか明示したか
- [ ] interface が受け取る DTO / VO が Domain 層の型のみで構成されているか（外部 SDK 型を含まないか）
- [ ] interface の AAD / コンテキスト（例: `table/column/pk`）を必須パラメータにしたか
- [ ] 少なくとも 1 つの本番 Adapter と 1 つのテスト用 Fake/Stub を用意したか
- [ ] `ContainerBootstrap` に登録したか
- [ ] `.env.example` に切替 flag を追記したか
- [ ] `deptrac` のルールセットに違反がないか確認したか

### 10.2 新規 Adapter 追加時

- [ ] infra 例外を必ず catch し、Adapter 固有の例外にラップしているか
- [ ] ネットワーク依存 Adapter には timeout（デフォルト 60 秒以内）を設定したか
- [ ] 外部 SDK 型が interface 引数 / 戻り値に漏れていないか
- [ ] Integration test（実サービス接続 or mock HTTP）を `tests/Integration/Infrastructure/` に配置したか
- [ ] Secret（API キー、パスワード）を コンストラクタ引数で受け取り、`.env` 経由で注入しているか（ハードコード禁止）

### 10.3 新規 UseCase 追加時

- [ ] Secondary ポート interface のみを依存として受け取り、具象 Adapter を一切知らないか
- [ ] infra 例外を catch し、domain 例外に翻訳しているか
- [ ] Unit test を `tests/Unit/Application/` に書き、実 DB / 実ネットワークを触っていないか
- [ ] `ContainerBootstrap` に登録したか
- [ ] Controller または CLI Command から呼び出しているか

### 10.4 レビュー時に確認

- [ ] `src/Domain/**` から `\PDO`, `\GuzzleHttp\*`, `\Psr\Log\*` 等の import がゼロか（`grep` で機械検査）
- [ ] `src/Application/**` から `Rucaro\Infrastructure\**` の具象クラスへの import がゼロか
- [ ] テスト用 Fake が `tests/Support/Fake/` に配置され、production autoload から外れているか

---

## 付録 A: Primary ポートと Secondary ポートの混同を避けるために

Ports & Adapters を初めて学ぶ開発者が混乱しやすいのが「ポートの方向」である。本プロジェクトのルールを明文化:

| 判断したいこと | 質問 | 答え |
|---|---|---|
| この interface は primary か secondary か？ | 「これを呼ぶのは誰か？」 | **UseCase 自体**ならそれは primary。**UseCase が呼ぶ**なら secondary |
| この interface は Domain / Application / Support どこに置くか？ | 「これは business の概念か、I/O の概念か、処理系の概念か？」 | business → Domain、I/O → Application/Port、処理系 → Support |
| Mock するか Fake するか？ | 「UseCase の振る舞いを検証したいか、ポート契約を検証したいか？」 | 振る舞い → **Fake**（手作り）、契約 → **Mock**（PHPUnit） |

### 付録 B: 既存実装の再分類

Phase 3 までに作られた実装を本 ADR の分類に当てはめると:

- `JournalRepositoryInterface` （Domain 所在）は **Secondary ポート**。`PdoJournalRepository` がアダプタ
- `CipherInterface`（Infrastructure 所在、ADR-003 の決定による例外配置）は **Secondary ポート**。`AesGcmCipher` がアダプタ
- `ClockInterface` （Support 所在）は **Secondary ポート**（処理系）
- `CreateJournalUseCase` は **Primary ポート**（クラス自体が契約）
- `LoginController` などは **Driving Adapter**（HTTP → Primary ポートへの変換）
- `AuthenticateBearer` middleware も **Driving Adapter**（HTTP 層の認証処理）

Phase 4 着手時点で、既存コードは本 ADR と整合しており、大規模リファクタなしに追加実装が可能である。
