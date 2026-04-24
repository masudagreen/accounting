# ADR-008: 承認ワークフロー（メール/メッセージ承認基盤）

- ステータス: 提案中 (2026-04-22)
- 決定者: (ユーザ承認待ち)
- 関連: [PLAN.md §Phase 5](../PLAN.md), [ADR-003](ADR-003-crypto-strategy.md), [ADR-005](ADR-005-layered-architecture.md), [ADR-006](ADR-006-ports-and-adapters.md), [ADR-007](ADR-007-strangler-fig-migration.md)
- 参照資料: `scripts/migrate/0004_create_receipts_and_approvals.sql`, `src/Infrastructure/Auth/BearerTokenGenerator.php`, `docs/api/openapi.yaml` (`/approvals/{token}`)

---

## 1. 文脈 (Context)

旧 Rucaro Accounting（PHP 5 系）の誤仕訳対策は、登録後の **PHP 側 Web UI による目視確認** に依存していた。手入力の仕訳が中心だった時代は辛うじて成立していたが、Phase 6 で予定している **領収書 AI パイプライン**（Claude Sonnet → Opus → 仕訳ドラフト自動生成）を載せると、以下の条件が同時に成立してしまう:

1. 人手で確認しないまま DB に `draft` 仕訳が大量流入する
2. `draft` と `posted` を視覚的に区別する UI が旧アプリにしか存在しない
3. AI の誤抽出（税率、勘定科目、金額、貸借反転）を検知する仕組みがない

[PLAN.md §0](../PLAN.md) でユーザ方針として以下が確定済:

| 確定事項 | 内容 |
|---|---|
| 誤仕訳対策 | 登録前に **メール／メッセージ** でユーザ承認を経る。Web UI では承認しない |
| セキュリティ強度 | ローカル運用前提のため最低限で OK（健全性は維持） |
| 期限 | なし。じっくり取り組む |
| 承認チャネル詳細 | planner 裁量（本 ADR で確定） |

本 ADR は **Phase 5** でこの承認基盤を「仕訳ドラフト（`Journal` 集約）向け」に完成させ、**Phase 6** で「領収書ドラフト（`Receipt` 集約）向け」に自然拡張できることを保証するための設計決定をまとめる。

### 1.1 既存の土台

Phase 1〜4 時点で以下が揃っている。本 ADR はこれらを前提にする:

| 資産 | 所在 | 本 ADR での利用 |
|---|---|---|
| `approval_tokens` テーブル | `scripts/migrate/0004_create_receipts_and_approvals.sql` | 既存 DDL をそのまま利用（必要カラムを Phase 5.1 で追加） |
| `BearerTokenGenerator` | `src/Infrastructure/Auth/` | 同じパターン（32B 乱数 + SHA-256 hex + prefix）を流用 |
| `AesGcmCipher` / `CipherInterface` | `src/Infrastructure/Crypto/`（ADR-003） | AAD 付き暗号の先行パターン（本 ADR では `responseDetail` の暗号化は行わないが参考） |
| 5 層 + Ports & Adapters | ADR-005 / ADR-006 | Primary=UseCase、Secondary=Port Interface、Adapter=Infrastructure の三層構造をそのまま適用 |
| `/approvals/{token}` スケルトン | `docs/api/openapi.yaml` L1345〜L1396 | Phase 5 で実装を埋める |

### 1.2 非ゴール

- 多段承認（2 名以上の承認者が順に確認）は対象外。Phase 7 以降で必要になったら拡張する
- 承認者ごとの権限マトリクス（この仕訳は田中だけが承認可、等）は対象外
- リマインダメール（締切 24h 前に再送）は Phase 6 以降の課題
- 法令準拠（電帳法等）は PLAN.md §0 より不要

---

## 2. 決定サマリ (Decision)

1. **トークン方式**: 32 バイトランダム `random_bytes()` → **hex 64 文字**。DB には **SHA-256 hex のみ保存**（`approval_tokens.token_hash CHAR(64)`）。既存 `BearerTokenGenerator` と同一方式で `ApprovalTokenGenerator` を新設。
2. **URL 形式**: `{APP_URL}/api/v1/approvals/{token}`。`token` は hex 64 文字。平文トークンは **1 回だけメールで送信、DB には残さない**。
3. **有効期限**: 既定 **72 時間**、`.env` の `APPROVAL_TTL_HOURS` で上書き可能（1〜720 の範囲）。
4. **応答**: 1 トークンにつき 1 回のみ。`responded_at` セット後は **HTTP 410 Gone**。期限切れは **GET で 200 + `status=expired`**、POST で **410 Gone**。
5. **チャネル**: Phase 5 は **Mail (SMTP)** + **Null (NoOp)** のみ実装。`MessagingChannelInterface` は Phase 5 で **定義だけ** 行い、LINE / Slack / Discord の実 adapter は Phase 6 以降で追加する（ADR-006 §3 の #14 と整合）。
6. **対象多態**: `ApprovalTargetInterface` を **Domain 層** に定義。Phase 5 では `JournalApprovalTarget` のみ、Phase 6 で `ReceiptApprovalTarget` を追加。承認基盤コード本体（UseCase / Port / Adapter / Controller）は Phase 6 で一切変更しない。
7. **Port 配置**: `MailSenderInterface`・`MessagingChannelInterface`・`ApprovalNotifierInterface` は **Application 層の Port**（ADR-006 §2.3）。`ApprovalTokenRepositoryInterface` は **Domain 層**。
8. **トランザクション境界**: ADR-005 §8 に従い UseCase 層で `beginTransaction` / `commit` / `rollBack`。メール送信は **コミット後**（§5.3 参照）。
9. **Smarty 5 テンプレ**: メール本文は `storage/templates/mail/` 配下の Smarty 5 テンプレを **Infrastructure 層** でレンダリング（Domain / Application からテンプレート ID を指定するのみ）。
10. **監査**: 発行・送信・応答を `receipt_action_logs` 類似の `approval_event_logs` テーブル（Phase 5.1 で追加）に記録。

---

## 3. ドメインモデル

### 3.1 集約 `ApprovalToken`

```php
<?php
// src/Domain/Approval/ApprovalToken.php
declare(strict_types=1);

namespace Rucaro\Domain\Approval;

use DateTimeImmutable;
use Rucaro\Domain\Common\Ulid;

final readonly class ApprovalToken
{
    public function __construct(
        public Ulid $id,
        public string $targetKind,          // 'journal' | 'receipt'
        public Ulid $targetId,
        public string $tokenHash,           // SHA-256 hex (64 chars)
        public string $tokenPrefix,         // 最初の 8 文字（運用補助）
        public ApprovalChannel $channel,
        public string $recipient,           // メールアドレス / チャネル ID
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public ?DateTimeImmutable $respondedAt,
        public ?ApprovalDecision $decision,
        public string $responseDetail,      // 却下理由等、最大 512 文字
        public Ulid $issuedByUserId,
    ) {}

    public function isExpired(DateTimeImmutable $now): bool
    {
        return $this->respondedAt === null && $now >= $this->expiresAt;
    }

    public function isResponded(): bool
    {
        return $this->respondedAt !== null;
    }

    public function status(DateTimeImmutable $now): ApprovalStatus
    {
        if ($this->isResponded())            return ApprovalStatus::Responded;
        if ($this->isExpired($now))          return ApprovalStatus::Expired;
        return ApprovalStatus::Pending;
    }

    /** 応答セット後の新インスタンスを返す（immutable pattern）。 */
    public function withResponse(
        ApprovalDecision $decision,
        string $detail,
        DateTimeImmutable $at,
    ): self {
        if ($this->isResponded()) {
            throw new Exception\ApprovalAlreadyRespondedException($this->id);
        }
        if ($this->isExpired($at)) {
            throw new Exception\ApprovalExpiredException($this->id);
        }
        return new self(
            $this->id, $this->targetKind, $this->targetId,
            $this->tokenHash, $this->tokenPrefix, $this->channel, $this->recipient,
            $this->issuedAt, $this->expiresAt,
            respondedAt: $at,
            decision: $decision,
            responseDetail: $detail,
            issuedByUserId: $this->issuedByUserId,
        );
    }
}
```

### 3.2 Enum `ApprovalChannel` / `ApprovalDecision` / `ApprovalStatus`

```php
enum ApprovalChannel: string {
    case Email   = 'email';
    case Line    = 'line';
    case Slack   = 'slack';
    case Discord = 'discord';
    case Null    = 'null';      // テスト・開発用 NoOp
}

enum ApprovalDecision: string {
    case Approved = 'approved';
    case Rejected = 'rejected';
}

enum ApprovalStatus: string {
    case Pending   = 'pending';
    case Responded = 'responded';
    case Expired   = 'expired';
}
```

### 3.3 Interface `ApprovalTargetInterface`

承認対象（仕訳・領収書・将来の他集約）を多態化する契約。**Domain 層** に配置する理由は「承認適用は集約のビジネスルール」であり、UseCase や Infrastructure が対象ごとの条件分岐を持つのを避けるため。

```php
<?php
// src/Domain/Approval/ApprovalTargetInterface.php
declare(strict_types=1);

namespace Rucaro\Domain\Approval;

use DateTimeImmutable;
use Rucaro\Domain\Common\Ulid;

interface ApprovalTargetInterface
{
    /** 'journal' | 'receipt' | ... */
    public function kind(): string;

    public function id(): Ulid;

    /** メール件名 1 行 (〜80 文字、改行なし)。 */
    public function summary(): string;

    /**
     * メール本文用の key-value。VO は __toString() で人間可読化しておくこと。
     * @return array<string, string>
     */
    public function details(): array;

    /** 承認受理時のドメインルール適用（status 遷移、イベント記録 etc）。 */
    public function applyApproval(Ulid $actorUserId, DateTimeImmutable $at): void;

    /** 却下受理時のドメインルール適用（status 遷移、reason 記録）。 */
    public function applyRejection(Ulid $actorUserId, DateTimeImmutable $at, string $reason): void;
}
```

### 3.4 Factory（対象側 Aggregate が自身を wrap）

```php
// src/Domain/Journal/JournalApprovalTarget.php
final readonly class JournalApprovalTarget implements ApprovalTargetInterface
{
    public function __construct(private Journal $journal) {}

    public function kind(): string  { return 'journal'; }
    public function id(): Ulid      { return $this->journal->id(); }

    public function summary(): string
    {
        return sprintf(
            '仕訳承認依頼: %s / %s / %s',
            $this->journal->date()->format('Y-m-d'),
            $this->journal->summary(),
            $this->journal->totalDebit()->format(),
        );
    }

    public function details(): array
    {
        return [
            '仕訳日付'     => $this->journal->date()->format('Y-m-d'),
            '摘要'         => $this->journal->summary(),
            '借方合計'     => $this->journal->totalDebit()->format(),
            '貸方合計'     => $this->journal->totalCredit()->format(),
            '明細数'       => (string) count($this->journal->lines()),
            '起票者'       => $this->journal->issuedBy()->toString(),
            'ステータス'   => $this->journal->status()->value,
        ];
    }

    public function applyApproval(Ulid $actorUserId, DateTimeImmutable $at): void
    {
        $this->journal->approve($actorUserId, $at);  // draft → approved → posted
    }

    public function applyRejection(Ulid $actorUserId, DateTimeImmutable $at, string $reason): void
    {
        $this->journal->reject($actorUserId, $at, $reason);
    }
}
```

Phase 6 では同じ形で `ReceiptApprovalTarget implements ApprovalTargetInterface` を追加するだけで、承認基盤側のコードは 1 行も変更する必要がない。

---

## 4. ポート（Application Port 定義）

ADR-006 の命名規則（§2.4）と所在ルール（§2.3）に従う。

| Port | 所在 | 役割 |
|---|---|---|
| `ApprovalTokenRepositoryInterface` | `src/Domain/Approval/` | 集約ライフサイクル（作成・検索・更新・期限切れ一括失効） |
| `ApprovalTargetRepositoryInterface` | `src/Domain/Approval/` | `targetKind + targetId` から `ApprovalTargetInterface` を解決 |
| `MailSenderInterface` | `src/Application/Port/Mail/` | SMTP への依頼（I/O 概念） |
| `MessagingChannelInterface` | `src/Application/Port/Messaging/` | LINE / Slack / Discord への依頼（Phase 5 では定義のみ） |
| `ApprovalNotifierInterface` | `src/Application/Port/Approval/` | チャネル選択を束ねる薄いオーケストレータ |
| `MailTemplateRendererInterface` | `src/Application/Port/Mail/` | 件名・HTML・プレーンの 3 成果物を返す |

### 4.1 `ApprovalTokenRepositoryInterface`

```php
<?php
// src/Domain/Approval/ApprovalTokenRepositoryInterface.php
declare(strict_types=1);

namespace Rucaro\Domain\Approval;

use DateTimeImmutable;
use Rucaro\Domain\Common\Ulid;

interface ApprovalTokenRepositoryInterface
{
    public function save(ApprovalToken $token): void;

    /** 平文は一切受け取らない。呼出側で SHA-256 ハッシュ化して渡す。 */
    public function findByTokenHash(string $tokenHash): ?ApprovalToken;

    /** 運用 CLI 用。prefix は 8 文字の hex。複数ヒット可能性あり。 */
    public function findByPrefix(string $tokenPrefix, int $limit = 10): array;

    public function findById(Ulid $id): ?ApprovalToken;

    /** 期限切れの pending を一括失効（responded_at = expires_at）。 */
    public function expirePastDue(DateTimeImmutable $now): int;
}
```

### 4.2 `MailSenderInterface`

```php
<?php
// src/Application/Port/Mail/MailSenderInterface.php
declare(strict_types=1);

namespace Rucaro\Application\Port\Mail;

use Rucaro\Application\Port\Mail\Exception\MailSendFailedException;

interface MailSenderInterface
{
    /** @throws MailSendFailedException */
    public function send(MailEnvelope $envelope): void;
}
```

`MailEnvelope` は `from / fromName / to / subject / html / text / headers` を持つ readonly DTO。

### 4.3 `MessagingChannelInterface`（Phase 5 では NoOp 実装のみ）

```php
<?php
// src/Application/Port/Messaging/MessagingChannelInterface.php
declare(strict_types=1);

namespace Rucaro\Application\Port\Messaging;

interface MessagingChannelInterface
{
    public function channel(): string;   // 'line' | 'slack' | 'discord' | 'null'
    public function send(MessagingMessage $message): void;
}
```

Phase 5 では `NullMessagingChannel` のみ実装。`LineMessagingChannel` 等は Phase 6 で interface に対して追加する（ADR-006 §3 #14）。

### 4.4 `ApprovalNotifierInterface`

チャネル選択を束ねる。**ビジネスロジックはゼロ**で、単なるルーティング。

```php
<?php
// src/Application/Port/Approval/ApprovalNotifierInterface.php
declare(strict_types=1);

namespace Rucaro\Application\Port\Approval;

use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Domain\Approval\ApprovalTargetInterface;

interface ApprovalNotifierInterface
{
    /**
     * 平文トークンは 1 回だけこの呼び出しで受け取り、送信後は参照を保持しない。
     * @param string $tokenPlaintext hex 64 文字
     */
    public function notifyIssued(
        ApprovalToken $token,
        ApprovalTargetInterface $target,
        string $tokenPlaintext,
    ): void;
}
```

実装（`DefaultApprovalNotifier`, Infrastructure）は `channel=Email` なら `MailSender`、`channel=Line|Slack|Discord|Null` なら適切な `MessagingChannel` にディスパッチ。

---

## 5. データフロー

### 5.1 承認トークン発行から応答までのシーケンス

```mermaid
sequenceDiagram
    autonumber
    participant C as Client (Authed)
    participant Ctl as JournalController::requestApproval
    participant IUC as IssueApprovalTokenUseCase
    participant Gen as ApprovalTokenGenerator
    participant Repo as ApprovalTokenRepositoryInterface
    participant TRepo as ApprovalTargetRepositoryInterface
    participant Not as ApprovalNotifierInterface
    participant Mail as MailSender (SMTP)
    participant R as Recipient (外部ユーザ)
    participant PCtl as ApprovalController
    participant FUC as FindApprovalByTokenUseCase
    participant RUC as RespondToApprovalUseCase

    C->>Ctl: POST /api/v1/journals/{id}/request-approval\n{channel, recipient, ttlHours?}
    Ctl->>IUC: execute(input)
    IUC->>Gen: generate() -> {plaintext, hash, prefix}
    IUC->>TRepo: findTarget('journal', id)  -> ApprovalTargetInterface
    IUC->>Repo: save(ApprovalToken{hash, prefix, ...})
    IUC->>IUC: commit()
    IUC->>Not: notifyIssued(token, target, plaintext)
    Not->>Mail: send(MailEnvelope{subject, html, text})
    Mail-->>Not: ok
    IUC-->>Ctl: IssueApprovalTokenOutput{tokenPrefix, expiresAt}
    Ctl-->>C: 201 Created (tokenPlaintext は返さない)

    Note over R: メール受信、リンククリック
    R->>PCtl: GET /api/v1/approvals/{token}
    PCtl->>FUC: execute({tokenPlaintext})
    FUC->>FUC: hash = sha256(tokenPlaintext)
    FUC->>Repo: findByTokenHash(hash)
    FUC->>TRepo: findTarget(token.targetKind, token.targetId)
    FUC-->>PCtl: Output{status, summary, details, expiresAt}
    PCtl-->>R: 200 OK (pending) / 200 + status=expired / 404

    R->>PCtl: POST /api/v1/approvals/{token}\n{decision, responseDetail}
    PCtl->>RUC: execute(input)
    RUC->>Repo: findByTokenHash(hash)
    RUC->>TRepo: findTarget(...)
    alt expired or responded
        RUC-->>PCtl: throw ApprovalExpiredException / AlreadyRespondedException
        PCtl-->>R: 410 Gone
    else ok
        RUC->>RUC: beginTransaction()
        RUC->>Target: applyApproval() or applyRejection()
        RUC->>Repo: save(token.withResponse(...))
        RUC->>RUC: commit()
        RUC-->>PCtl: RespondOutput{decision}
        PCtl-->>R: 200 OK
    end
```

### 5.2 失効バッチ

`bin/cowork approvals:expire` は `ExpirePastDueApprovalsUseCase` を呼び、`expires_at < now` かつ `responded_at IS NULL` の行に対して `responded_at = expires_at`, `response = NULL`, `response_detail = '(expired)'` をセットする。Phase 5 は手動実行、Phase 6 以降で cron 化を検討。

### 5.3 メール送信のタイミング（コミット後送信）

ADR-005 §8 に従い `beginTransaction` / `commit` は UseCase 内で閉じる。メール送信は **`commit()` 成功後** に行う。理由:

1. DB コミット前に送ると「メールは届いたが DB に token がない」状態が発生しうる
2. 送信失敗時はコミット済みレコードを残したまま `MailSendFailedException` を投げ、Controller は 502 Bad Gateway を返す。Client は `POST /approvals/{tokenPrefix}/resend` で再送できる
3. Rails の `after_commit :send_mail` に相当する運用

```php
public function execute(IssueApprovalTokenInput $input): IssueApprovalTokenOutput
{
    $this->pdo->beginTransaction();
    try {
        $target = $this->targetRepo->findTarget($input->targetKind, $input->targetId);
        $generated = $this->generator->generate();
        $token = ApprovalToken::issue(/* ... */);
        $this->tokenRepo->save($token);
        $this->pdo->commit();
    } catch (\Throwable $e) {
        $this->pdo->rollBack();
        throw $e;
    }

    // after-commit: 失敗時も DB 状態は保たれる
    $this->notifier->notifyIssued($token, $target, $generated['plaintext']);

    return new IssueApprovalTokenOutput(
        tokenPrefix: $token->tokenPrefix,
        expiresAt:   $token->expiresAt,
    );
}
```

---

## 6. セキュリティ

PLAN.md §0「ローカル運用、最低限の健全性」および ADR-003 / `BearerTokenGenerator` の既存パターンに合わせる。

| 項目 | 決定 | 根拠 |
|---|---|---|
| トークン長 | 32 バイト → hex 64 文字 | 256 bit エントロピー。`BearerTokenGenerator` と揃え、実装のミスを避ける |
| 保存形式 | DB は **SHA-256 hex のみ**（`token_hash CHAR(64)`）。平文は保存しない | DB 漏洩時に有効トークンが露出しない |
| ハッシュ比較 | `hash_equals($expected, $actual)` | タイミング攻撃対策。`BearerTokenGenerator::hashEquals` を再利用 |
| 有効期限 | 既定 72h（`APPROVAL_TTL_HOURS`） | ユーザ方針で指定 |
| 応答回数 | 1 回（`responded_at` セット後は 410） | 二重投稿・リプレイ防止 |
| HTTPS | ローカル運用では HTTP 許容、`APP_URL` で切替 | PLAN.md §0 |
| レート制限 | Phase 5 では未実装、Phase 6 以降で検討（IP 別 60 req/min 目安） | ローカル運用前提、過剰実装回避 |
| IP 制限 | なし | 同上 |
| `responseDetail` の暗号化 | 行わない（平文 512 文字まで） | 個人情報・機密情報は入力しない運用ルールで対応 |
| ログ | 発行・送信・応答・失効を `approval_event_logs` に記録 | 監査・再送の根拠 |

### 6.1 トークンが URL に含まれる問題

`GET /api/v1/approvals/{token}` は token を URL パスに含む。メール送信時は以下の緩和策を採る:

- HTTPS 前提（`APP_URL=https://...`）のときはブラウザ履歴・プロキシログ経由の漏洩は限定的
- HTTP 運用（ローカルのみ）のときは送信先が LAN 内メール（ローカル Dovecot 等）に限定されることをユーザが承知
- Referrer ヘッダで外部に漏れないよう、承認画面の HTML には `<meta name="referrer" content="no-referrer">` を付与（Phase 5.2 で実装）

---

## 7. UseCase 定義

ADR-006 §2.1.1 に従い、UseCase クラス自体を Primary ポートとする。全 UseCase は `final readonly` で副作用を Port に閉じ込める。

### 7.1 `IssueApprovalTokenUseCase`

| Input |  |
|---|---|
| `targetKind` | `'journal'` \| `'receipt'`（Phase 6） |
| `targetId` | Ulid |
| `channel` | `ApprovalChannel` |
| `recipient` | メールアドレス / チャンネル ID |
| `issuedByUserId` | Ulid（認証済み発行者） |
| `ttlHours` | `?int`、未指定は `.env` の既定値 |

| Output |  |
|---|---|
| `tokenPrefix` | hex 8 文字（運用補助） |
| `expiresAt` | DateTimeImmutable |

**平文トークンは戻り値に含めない**。通知チャネルに直接渡され、Controller 以降には到達しない。

### 7.2 `FindApprovalByTokenUseCase`

| Input | `tokenPlaintext` (hex 64) |
| Output | `status: ApprovalStatus`, `targetKind`, `targetSummary: string`, `targetDetails: array`, `expiresAt`, `channel`, `recipient`（マスク済） |

期限切れは `status = Expired` で **200** を返す。404 にしない理由は「既に期限切れ」であることを受信者に明示するため。ただし `status = Responded` も 200 で返す（すでに応答済みであることを明示）。

### 7.3 `RespondToApprovalUseCase`

| Input | `tokenPlaintext`, `decision`, `responseDetail` (≤512), `actorUserId?`（認証なしのケースあり） |
| Output | `token: ApprovalToken`, `targetKind`, `decision` |

- 期限切れ → `ApprovalExpiredException` → 410
- 応答済 → `ApprovalAlreadyRespondedException` → 410
- 未知 token → `ApprovalTokenNotFoundException` → 404
- `decision = Rejected` で `responseDetail` 空 → 422（却下は理由必須）

### 7.4 `ResendApprovalUseCase`

| Input | `tokenPrefix`, `issuedByUserId`（発行者のみ再送可） |
| 挙動 | 既存トークンが **24h 以内に発行** されていれば **平文を再生成できない**（ハッシュしか持っていない）ため「旧トークンを失効→新トークンを発行→送信」となる。24h 超えで応答なしなら新トークン発行のみ |

Phase 5 は「プレフィックスが 1 件にユニーク一致」するケースのみ実装。複数ヒット時は 409 を返して CLI での絞り込みを促す。

### 7.5 `ExpirePastDueApprovalsUseCase`

| Input | 無し（`Clock` から `now` 取得） |
| Output | 失効件数 |

`approval_tokens` の `expires_at < now` かつ `responded_at IS NULL` を一括更新。CLI `bin/cowork approvals:expire` から呼ぶ。

---

## 8. Infrastructure（Adapter 実装）

ADR-006 §2.4 の命名規則に準拠。

| クラス | 実装ポート | Phase |
|---|---|---|
| `PdoApprovalTokenRepository` | `ApprovalTokenRepositoryInterface` | 5.2 |
| `PdoApprovalTargetRepository` | `ApprovalTargetRepositoryInterface` | 5.2（`targetKind='journal'` のみ） |
| `SymfonyMailSender` | `MailSenderInterface` | 5.2 (`symfony/mailer` ^6.4 を追加導入、SMTP DSN) |
| `NullMailSender` | `MailSenderInterface` | 5.2 |
| `ArrayMailSender` (test) | `MailSenderInterface` | 5.2（`tests/Support/Fake/`） |
| `NullMessagingChannel` | `MessagingChannelInterface` | 5.2 |
| `LineMessagingChannel` | `MessagingChannelInterface` | **Phase 6** |
| `SlackMessagingChannel` | `MessagingChannelInterface` | **Phase 6** |
| `DiscordMessagingChannel` | `MessagingChannelInterface` | **Phase 6** |
| `DefaultApprovalNotifier` | `ApprovalNotifierInterface` | 5.2 |
| `SmartyMailTemplateRenderer` | `MailTemplateRendererInterface` | 5.2（既存 `smarty/smarty` ^5.7 を `storage/templates/mail/` で使用） |
| `ApprovalTokenGenerator` | （クラス単体、`BearerTokenGenerator` と並置） | 5.2 |

### 8.1 composer 追加

```json
{
  "require": {
    "symfony/mailer": "^6.4"
  }
}
```

`symfony/mailer` の DSN（`smtp://user:pass@host:587` 等）をそのまま使用。ADR-006 §3 #13 の `SmtpMailSender` と命名が異なるが、本 ADR では `SymfonyMailSender` を採用し、ADR-006 の一覧は Phase 5 実装時に同期する。

---

## 9. エンドポイント

ADR-005 §3.4 の Controller 責務（Request → UseCase DTO → Response Envelope）に従う。

### 9.1 `POST /api/v1/journals/{id}/request-approval`

- **認証**: 必要（Bearer token）
- **Body**: `{channel: 'email'|'line'|'slack'|'discord'|'null', recipient: string, ttlHours?: int}`
- **成功**: **201 Created**、`data = {tokenPrefix, expiresAt}`
- **トークン平文は返さない**（§3.2 の原則）
- **エラー**: 401（未認証）、403（対象仕訳へアクセス権なし）、404（対象仕訳なし）、409（対象が既に `approved`/`posted`）、422（recipient 形式不正 / ttl 範囲外）、502（メール送信失敗：DB はコミット済みなので `resend` で復旧可）

### 9.2 `GET /api/v1/approvals/{token}`

- **認証**: **不要**（token 自体が capability）
- **成功 (pending)**: 200、`data = {status:'pending', targetKind, targetSummary, targetDetails, expiresAt, channel}`
- **成功 (expired)**: 200、`data = {status:'expired', expiresAt, ...}`（`targetSummary` は含める）
- **成功 (responded)**: 200、`data = {status:'responded', decision, respondedAt}`
- **エラー**: 404（未知トークン）、500

### 9.3 `POST /api/v1/approvals/{token}`

- **認証**: **不要**
- **Body**: `{decision: 'approved'|'rejected', responseDetail?: string}`
- **成功**: 200、`data = {tokenId, decision, respondedAt, channel, journalEntryId, receiptId}`（`openapi.yaml` の `ApprovalResult` と一致）
- **エラー**: 400（body 不正）、404（未知トークン）、**410（期限切れ / 応答済み）**、422（`decision=rejected` かつ `responseDetail` 空）、500

### 9.4 `POST /api/v1/approvals/{tokenPrefix}/resend`

- **認証**: 必要（発行者本人のみ）
- **Body**: なし
- **成功**: 200、`data = {tokenPrefix, expiresAt}`（新トークン発行の場合はプレフィックスが変わる点を返却値に明示）
- **エラー**: 401、403（別ユーザ）、404（prefix ヒットなし）、409（prefix が複数ヒット）、422（対象が応答済み）、502（再送失敗）

### 9.5 OpenAPI との整合

`docs/api/openapi.yaml` の既存定義（`ApprovalResponse`, `ApprovalResult`, `/approvals/{token}` の POST）は Phase 5.2 で拡張:

- `GET /approvals/{token}` 追加
- `POST /approvals/{token}/resend` 追加
- `POST /journals/{id}/request-approval` 追加
- `ApprovalChannel` に `null` を追加
- `ApprovalResult` に `targetKind` フィールド追加（Phase 6 で利用）

---

## 10. メールテンプレート方針

### 10.1 配置

```
storage/templates/mail/
├── approval-request.ja/
│   ├── subject.tpl       (件名, 1 行)
│   ├── body.html.tpl     (HTML)
│   └── body.text.tpl     (plain text fallback)
├── approval-resend.ja/
│   └── ...
└── approval-expired.ja/   (Phase 6 以降、参考通知)
    └── ...
```

### 10.2 テンプレ変数

Smarty 5 のエスケープは `{$var|escape:'html'}` をデフォルトにする（`$smarty->setEscapeHtml(true)` で全文字列を HTML エスケープ）。

| 変数 | 型 | 用途 |
|---|---|---|
| `{$summary}` | string | 件名・本文冒頭。`ApprovalTarget::summary()` |
| `{$details}` | `array<string,string>` | 本文の key-value テーブル |
| `{$approveUrl}` | string | `APPROVAL_APPROVE_URL_TEMPLATE` 展開後 |
| `{$rejectUrl}` | string | `APPROVAL_REJECT_URL_TEMPLATE` 展開後 |
| `{$expiresAt}` | string | `Y-m-d H:i` JST |
| `{$issuerName}` | string | 発行ユーザ表示名 |
| `{$tokenPrefix}` | string | 運用補助（本文末尾に「トークン: `ab12cd34`」） |

`{$approveUrl}` は **クエリパラメータ方式** でワンクリック承認を可能にする:

```
{APP_URL}/api/v1/approvals/{token}?decision=approved
{APP_URL}/api/v1/approvals/{token}?decision=rejected
```

GET でも POST と同じ `RespondToApprovalUseCase` を呼べるようにする（ただし GET はブラウザのプリフェッチで誤動作する懸念があるため、`/confirm?decision=approved` のように **一度確認画面を挟む HTML を返す** のが既定。Phase 5.3 で実装）。

### 10.3 レンダリング Adapter

```php
final readonly class SmartyMailTemplateRenderer implements MailTemplateRendererInterface
{
    public function __construct(private \Smarty\Smarty $smarty, private string $templateRoot) {}

    public function render(string $templateId, array $variables, string $locale = 'ja'): RenderedMail
    {
        $dir = $this->templateRoot . '/' . $templateId . '.' . $locale;
        $this->smarty->setTemplateDir($dir);
        foreach ($variables as $k => $v) $this->smarty->assign($k, $v);

        return new RenderedMail(
            subject: trim($this->smarty->fetch('subject.tpl')),
            html:    $this->smarty->fetch('body.html.tpl'),
            text:    $this->smarty->fetch('body.text.tpl'),
        );
    }
}
```

---

## 11. 設定・環境変数

`.env.example` に以下を追加（Phase 5.1 着手時）:

```bash
# -----------------------------
# Mail (Phase 5)
# -----------------------------
MAIL_MAILER=smtp                        # smtp | null
MAIL_DSN=smtp://user:pass@host:587       # symfony/mailer DSN。MAIL_MAILER=null のとき空で OK
MAIL_FROM=no-reply@example.com
MAIL_FROM_NAME="Rucaro Accounting"

# -----------------------------
# Approval workflow (Phase 5)
# -----------------------------
APPROVAL_TTL_HOURS=72                                                        # 1..720
APPROVAL_APPROVE_URL_TEMPLATE={APP_URL}/api/v1/approvals/{token}?decision=approved
APPROVAL_REJECT_URL_TEMPLATE={APP_URL}/api/v1/approvals/{token}?decision=rejected
APPROVAL_DEFAULT_CHANNEL=email                                               # email | line | slack | discord | null

# -----------------------------
# Base URL（承認リンク組立に利用）
# -----------------------------
APP_URL=http://localhost:8080
```

### 11.1 起動時検証

ADR-006 §5.3 の `EnvironmentValidator::assertProductionReady()` に以下を追加:

- `MAIL_MAILER ∈ {smtp, null}`
- `MAIL_MAILER=smtp` なら `MAIL_DSN` が `smtp://...` 形式
- `APPROVAL_TTL_HOURS ∈ [1, 720]`
- `APP_URL` が有効な URL
- `APPROVAL_APPROVE_URL_TEMPLATE` / `APPROVAL_REJECT_URL_TEMPLATE` に `{token}` プレースホルダを含む

### 11.2 `ContainerBootstrap` 追加バインド（ADR-006 §5.1 拡張）

```php
$c->set(MailSenderInterface::class, static function (Container $c): MailSenderInterface {
    return match ($_ENV['MAIL_MAILER'] ?? 'null') {
        'smtp' => new SymfonyMailSender(
            dsn:      $_ENV['MAIL_DSN'],
            fromAddr: $_ENV['MAIL_FROM'],
            fromName: $_ENV['MAIL_FROM_NAME'] ?? 'Rucaro Accounting',
        ),
        'null' => new NullMailSender(),
        default => throw new InvalidAdapterException('Unknown MAIL_MAILER'),
    };
});

$c->set(ApprovalNotifierInterface::class, static fn (Container $c) =>
    new DefaultApprovalNotifier(
        mail:      $c->getTyped(MailSenderInterface::class),
        messaging: $c->getTyped(MessagingChannelInterface::class),
        renderer:  $c->getTyped(MailTemplateRendererInterface::class),
        config:    ApprovalUrlConfig::fromEnv($_ENV),
    )
);
```

---

## 12. Phase 6 への拡張余地

| 拡張項目 | 必要な変更 | 承認基盤本体への影響 |
|---|---|---|
| 領収書ドラフト承認 | `src/Domain/Receipt/ReceiptApprovalTarget.php` 追加（`implements ApprovalTargetInterface`） | **なし**（多態のみ） |
| LINE 承認 | `src/Infrastructure/Messaging/LineMessagingChannel.php` 追加 | なし（interface は Phase 5 で完成） |
| Slack 承認 | `src/Infrastructure/Messaging/SlackMessagingChannel.php` 追加 | なし |
| Discord 承認 | `src/Infrastructure/Messaging/DiscordMessagingChannel.php` 追加 | なし |
| 承認リンクのクリック率計測 | `approval_event_logs` に `opened_at` 追加、1x1 gif トラッキング | なし（Phase 6 以降検討） |
| 再送通知（締切 24h 前） | `RemindPendingApprovalsUseCase` 新設 + cron | 既存 UseCase 非変更 |
| 承認者ごとの権限 | `ApprovalPolicyInterface` 追加、Issue 前に `policy->canApprove()` | UseCase 1 箇所に追加のみ |

Phase 6 のタスク見積もり:

- `ReceiptApprovalTarget` 実装: 約 2 h
- LINE/Slack adapter 実装: 各 4 h
- 承認統合テスト: 4 h

---

## 13. 結果 (Consequences)

### 13.1 Pros

1. **誤仕訳防止**: AI 自動仕訳（Phase 6）が DB に到達する前に必ず人間の目が入る
2. **監査ログ**: `approval_event_logs` により「誰がいつ何を承認／却下したか」が追える
3. **Phase 6 への土台完成**: `ApprovalTargetInterface` 実装を追加するだけで領収書承認が載る
4. **チャネル抽象**: メール → LINE / Slack / Discord の差替えが `.env` 1 行 + 新 adapter 追加のみで済む
5. **既存資産の再利用**: `BearerTokenGenerator` / `approval_tokens` テーブル / `/approvals/{token}` スケルトンを流用
6. **ADR-005 / ADR-006 の原則と整合**: 依存方向 (inward)、ポート配置 (Domain/Application/Support の分岐基準)、UseCase = Primary、Interface = Secondary を違反しない

### 13.2 Cons / トレードオフ

1. **チャネル障害時は承認フロー停止**: SMTP ダウン時は発行できても受信者が応答できない。`resend` と手動 `bin/cowork approvals:expire` で運用回避
2. **メール到達性依存**: 受信者が迷惑メールに振り分けた場合に気付かない。SPF / DKIM の設定は Phase 5 のスコープ外（ローカル運用前提）
3. **1 トークン 1 回応答の非可逆性**: 誤承認の取り消しは別 UseCase（Phase 7 以降の `ReverseJournalUseCase`）で対応
4. **Smarty テンプレ依存**: 旧アプリ温存のため Smarty 5 を採用済みだが、別エンジン（Twig など）に乗り換える場合は `MailTemplateRendererInterface` 実装差替えで対応可能
5. **ファイル数増加**: Phase 5 で +18 ファイル（Domain 4, Application 6, Infrastructure 8 程度）

### 13.3 既知の制約

- `approval_tokens.channel` の CHECK 制約に `'null'` が含まれていない（Phase 5.1 で `ALTER TABLE` で追加する必要あり）
- `token_prefix` カラムが DDL 未定義のため Phase 5.1 で `ALTER TABLE approval_tokens ADD COLUMN token_prefix CHAR(8) NOT NULL DEFAULT ''` が必要
- `target_kind` カラムも未定義で、現状は `journal_entry_id` / `receipt_id` の nullable 2 列で多態化している。本 ADR 採用時は `target_kind VARCHAR(16) NOT NULL` + `target_id BINARY(16) NOT NULL` に整理することを **Phase 5.1 で検討**（後方互換移行 SQL は ADR-007 Strangler Fig に従い段階移行）
- `issued_by_user_id BINARY(16) NOT NULL` カラムも Phase 5.1 で追加

---

## 14. 代替案と却下理由

### 14.1 PHP UI 内で承認（旧アプリ踏襲）

- **利点**: 既存の目視確認フローを流用可能、実装コスト最小
- **却下理由**:
  1. PLAN.md §0 のユーザ方針と真っ向から反する
  2. AI 自動仕訳（Phase 6）が大量に流れ込んだときに UI での確認が追いつかない
  3. Web UI を開かないと承認できない＝スマホ外出先からの承認が不可能
  4. 旧アプリの `front/else/` はスコープ外（PLAN.md §0）

### 14.2 JWT（self-contained token）

- **利点**: DB 不要、ステートレス
- **却下理由**:
  1. **失効が難しい**: 応答済みトークンの二重投稿を検知するためには結局 DB が必要
  2. 有効期限変更・鍵ローテ時の扱いが opaque + DB 保管より複雑
  3. `BearerTokenGenerator` と方式が乖離し、コードベース内に 2 種類のトークン実装が生まれる
  4. ローカル運用で JWT のスケール利点（ステートレス）を享受する場面がない

### 14.3 Webhook コールバック（外部承認サービス連携）

- **利点**: Slack Bot や外部承認 SaaS を呼び出すだけ
- **却下理由**:
  1. ローカル運用前提で外部到達性がない
  2. 承認 SaaS は月額課金で PLAN.md のローカル無料運用と不整合
  3. 個人会計データを外部 SaaS に送信するのは最小情報原則に反する

### 14.4 OAuth 委任（承認者の外部 IdP ログインを要求）

- **利点**: 承認者が強く認証される
- **却下理由**:
  1. ローカル運用で OAuth を立てる意味がない
  2. 1 人運用（個人会計）で委任の概念が過剰
  3. token capability + 1 回応答の組み合わせで「知っているだけで承認できる」が既に十分

### 14.5 自動承認（AI 信頼度閾値で通す）

- **利点**: 完全自動化、運用コストゼロ
- **却下理由**:
  1. PLAN.md §R4「信頼度 threshold 未満は自動承認不可」と合致するが、**閾値以上でも人間の目を挟む** 本 ADR の方針が誤仕訳防止に必要
  2. 税区分・勘定科目の誤りは AI 信頼度と相関しない（むしろ過信のほうが危険）
  3. 税務調査時の説明責任が果たせない

### 14.6 カプセル化した承認 UI を新アプリで提供

- **利点**: スマホ・PC 統合 UX
- **却下理由**:
  1. ユーザ方針で明示的に除外（「Web UI では承認しない」）
  2. フロント実装（`front/else/`）は PLAN.md §0 でスコープ外
  3. 本 ADR は **将来この方針が変わっても `RespondToApprovalUseCase` を UI から呼べば済む** 設計

---

## 15. 実装チェックリスト（Phase 5.2 キックオフ時）

以下を全て満たした状態で Phase 5.3（統合テスト）に進む。

### 15.1 DB / マイグレーション

- [ ] **1.** `scripts/migrate/0005_extend_approval_tokens.sql` 新規: `token_prefix CHAR(8)`, `target_kind VARCHAR(16)`, `target_id BINARY(16)`, `issued_by_user_id BINARY(16)` を追加
- [ ] **2.** `approval_tokens.channel` の CHECK 制約を `('email','line','slack','discord','null')` に拡張
- [ ] **3.** `approval_event_logs` テーブル新設（`id, approval_token_id, event, detail JSON, occurred_at`）
- [ ] **4.** 既存 `journal_entry_id` / `receipt_id` の FK 制約を Phase 5 の後方互換期間は維持、Phase 6 完了後に落とすマイグレーションを検討

### 15.2 Domain 層

- [ ] **5.** `src/Domain/Approval/ApprovalToken.php`（readonly 集約、`isExpired` / `isResponded` / `status` / `withResponse`）
- [ ] **6.** `src/Domain/Approval/ApprovalChannel.php`, `ApprovalDecision.php`, `ApprovalStatus.php`（enum）
- [ ] **7.** `src/Domain/Approval/ApprovalTargetInterface.php`
- [ ] **8.** `src/Domain/Approval/ApprovalTokenRepositoryInterface.php`
- [ ] **9.** `src/Domain/Approval/ApprovalTargetRepositoryInterface.php`
- [ ] **10.** `src/Domain/Approval/Exception/ApprovalExpiredException.php`, `ApprovalAlreadyRespondedException.php`, `ApprovalTokenNotFoundException.php`（`DomainException` 派下）
- [ ] **11.** `src/Domain/Journal/JournalApprovalTarget.php`（`implements ApprovalTargetInterface`）
- [ ] **12.** `src/Domain/Journal/Journal` に `approve(Ulid, DateTimeImmutable)` / `reject(Ulid, DateTimeImmutable, string)` メソッド追加

### 15.3 Application 層

- [ ] **13.** `src/Application/Port/Mail/MailSenderInterface.php`, `MailEnvelope.php`, `MailTemplateRendererInterface.php`, `RenderedMail.php`
- [ ] **14.** `src/Application/Port/Messaging/MessagingChannelInterface.php`, `MessagingMessage.php`
- [ ] **15.** `src/Application/Port/Approval/ApprovalNotifierInterface.php`, `ApprovalUrlConfig.php`
- [ ] **16.** `src/Application/Approval/IssueApprovalTokenUseCase.php` + Input/Output DTO
- [ ] **17.** `src/Application/Approval/FindApprovalByTokenUseCase.php` + Input/Output DTO
- [ ] **18.** `src/Application/Approval/RespondToApprovalUseCase.php` + Input/Output DTO
- [ ] **19.** `src/Application/Approval/ResendApprovalUseCase.php` + Input/Output DTO
- [ ] **20.** `src/Application/Approval/ExpirePastDueApprovalsUseCase.php` + Output DTO
- [ ] **21.** 全 UseCase が ADR-005 §8 のトランザクション境界を守り、メール送信は commit 後

### 15.4 Infrastructure 層

- [ ] **22.** `src/Infrastructure/Approval/ApprovalTokenGenerator.php`（`BearerTokenGenerator` と同方式、`tokenPrefix` 8 文字を返す）
- [ ] **23.** `src/Infrastructure/Approval/PdoApprovalTokenRepository.php`
- [ ] **24.** `src/Infrastructure/Approval/PdoApprovalTargetRepository.php`（Phase 5 は `journal` のみ、Phase 6 で `receipt` 分岐追加）
- [ ] **25.** `src/Infrastructure/Approval/DefaultApprovalNotifier.php`
- [ ] **26.** `src/Infrastructure/Mail/SymfonyMailSender.php`
- [ ] **27.** `src/Infrastructure/Mail/NullMailSender.php`
- [ ] **28.** `src/Infrastructure/Mail/SmartyMailTemplateRenderer.php`
- [ ] **29.** `src/Infrastructure/Messaging/NullMessagingChannel.php`
- [ ] **30.** `composer require symfony/mailer:^6.4`
- [ ] **31.** `storage/templates/mail/approval-request.ja/{subject,body.html,body.text}.tpl`

### 15.5 Http 層

- [ ] **32.** `POST /api/v1/journals/{id}/request-approval` Controller + ルート
- [ ] **33.** `GET /api/v1/approvals/{token}` Controller + ルート（認証不要）
- [ ] **34.** `POST /api/v1/approvals/{token}` Controller + ルート（認証不要）
- [ ] **35.** `POST /api/v1/approvals/{tokenPrefix}/resend` Controller + ルート（認証必要）
- [ ] **36.** `docs/api/openapi.yaml` に 4 エンドポイント全てを反映、`ApprovalChannel` enum に `null` 追加、`ApprovalResult` に `targetKind` 追加

### 15.6 CLI

- [ ] **37.** `bin/cowork approvals:resend <tokenPrefix>` コマンド登録（`ResendApprovalUseCase` を呼ぶ）
- [ ] **38.** `bin/cowork approvals:expire` コマンド登録（`ExpirePastDueApprovalsUseCase` を呼ぶ）

### 15.7 設定・起動

- [ ] **39.** `.env.example` に §11 の全環境変数を追記
- [ ] **40.** `EnvironmentValidator` に §11.1 のバリデーションを追加
- [ ] **41.** `ContainerBootstrap` に §11.2 の全バインドを追加

### 15.8 テスト

- [ ] **42.** `tests/Unit/Domain/Approval/ApprovalTokenTest.php`（`withResponse` の不変条件、期限切れ判定）
- [ ] **43.** `tests/Unit/Application/Approval/IssueApprovalTokenUseCaseTest.php`（`ArrayMailSender` + `InMemoryApprovalTokenRepository`）
- [ ] **44.** `tests/Unit/Application/Approval/FindApprovalByTokenUseCaseTest.php`（pending / expired / responded の 3 分岐）
- [ ] **45.** `tests/Unit/Application/Approval/RespondToApprovalUseCaseTest.php`（二重応答 410、期限切れ 410、却下理由必須 422）
- [ ] **46.** `tests/Integration/Infrastructure/Approval/PdoApprovalTokenRepositoryIntegrationTest.php`（実 MariaDB）
- [ ] **47.** `tests/Unit/Infrastructure/Mail/SymfonyMailSenderTest.php`（`symfony/mailer` の `NullTransport` でアサート）
- [ ] **48.** `tests/E2E/Approval/ApprovalFlowE2ETest.php`（仕訳作成 → 承認依頼 → GET → POST approved → Journal.status=posted）

### 15.9 静的解析・規約

- [ ] **49.** PHPStan level 6 green、新規エラーゼロ
- [ ] **50.** ADR-006 §6.2 の deptrac ルールに違反がないこと（`Application/Approval/**` が `Infrastructure/**` の具象に依存しない）
- [ ] **51.** ADR-005 §7.2 の grep ベース層チェックを新規 Approval ファイルに対して CI で実行
- [ ] **52.** `code-reviewer` agent と `security-reviewer` agent の両方でレビュー通過

---

## 16. 参考

- [PLAN.md §Phase 5](../PLAN.md)
- [ADR-003](ADR-003-crypto-strategy.md) §2.1（SHA-256 hex、constant-time 比較のパターン）
- [ADR-005](ADR-005-layered-architecture.md) §3（各層の責務）、§8（トランザクション境界）
- [ADR-006](ADR-006-ports-and-adapters.md) §2（Primary / Secondary Port）、§3（Port マトリクス）、§5.2（環境変数切替）、§6.3（例外翻訳）
- [ADR-007](ADR-007-strangler-fig-migration.md) — 既存 `approval_tokens` DDL の段階拡張
- `scripts/migrate/0004_create_receipts_and_approvals.sql` — 既存 DDL
- `src/Infrastructure/Auth/BearerTokenGenerator.php` — トークン生成の先行パターン
- `docs/api/openapi.yaml` L1345〜L1396, L2669〜L2707 — 既存エンドポイント・スキーマ定義
- Symfony Mailer Documentation — https://symfony.com/doc/current/mailer.html
