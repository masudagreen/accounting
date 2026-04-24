<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Application\Approval;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Approval\ApprovalToken;
use Rucaro\Infrastructure\Approval\PdoApprovalTokenRepository;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Infrastructure\Database\DatabaseConfig;

/**
 * DB integration coverage for {@see PdoApprovalTokenRepository}.
 *
 * Uses the same host-provided MariaDB instance as the Journal tests and
 * self-skips when RUCARO_TEST_DB_USER is unset so developer laptops without
 * a DB still keep the Unit suite green.
 */
#[CoversClass(PdoApprovalTokenRepository::class)]
final class PdoApprovalTokenRepositoryTest extends TestCase
{
    private string $host = '';
    private int $port = 3306;
    private string $dbname = '';
    private string $username = '';
    private string $password = '';
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        $user = getenv('RUCARO_TEST_DB_USER');
        if ($user === false || $user === '') {
            $this->markTestSkipped('RUCARO_TEST_DB_USER is not set; skipping DB integration test.');
        }
        $this->username = (string) $user;
        $this->host     = ((string) getenv('RUCARO_TEST_DB_HOST')) ?: '127.0.0.1';
        $portEnv        = getenv('RUCARO_TEST_DB_PORT');
        $this->port     = $portEnv !== false && $portEnv !== '' ? (int) $portEnv : 3306;
        $this->dbname   = ((string) getenv('RUCARO_TEST_DB_NAME')) ?: 'rucaro_test';
        $pwEnv          = getenv('RUCARO_TEST_DB_PASSWORD');
        $this->password = $pwEnv === false ? '' : (string) $pwEnv;

        $root = new PDO(
            sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->host, $this->port),
            $this->username,
            $this->password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $root->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->dbname));
        $root->exec(sprintf(
            'CREATE DATABASE `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $this->dbname,
        ));

        $this->pdo = ConnectionFactory::createFromConfig($this->config());
        $this->migrate($this->pdo);
    }

    protected function tearDown(): void
    {
        if ($this->dbname === '' || $this->username === '') {
            return;
        }
        try {
            $root = new PDO(
                sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->host, $this->port),
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );
            $root->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->dbname));
        } catch (PDOException) {
            // best-effort cleanup
        }
    }

    public function testSaveThenFindByHashRoundTrip(): void
    {
        $repo = new PdoApprovalTokenRepository($this->requirePdo());
        $token = $this->token(hash: str_repeat('a', 64), prefix: 'abcd0123abcd0123');
        $repo->save($token);

        $loaded = $repo->findByTokenHash(str_repeat('a', 64));
        self::assertNotNull($loaded);
        self::assertSame($token->id, $loaded->id);
        self::assertSame(ApprovalTargetKind::Journal, $loaded->targetKind);
        self::assertSame(ApprovalChannel::Email, $loaded->channel);
        self::assertSame('abcd0123abcd0123', $loaded->tokenPrefix);
        self::assertSame('reviewer@example.com', $loaded->recipient);
    }

    public function testFindByPrefixPicksLatest(): void
    {
        $repo = new PdoApprovalTokenRepository($this->requirePdo());
        $repo->save($this->token(
            id: '01HW7K9B2QV7C8Y4ZAPPR0000001',
            hash: str_repeat('1', 64),
            prefix: 'aaaabbbbccccdddd',
            issuedIso: '2026-04-20T00:00:00Z',
        ));
        $repo->save($this->token(
            id: '01HW7K9B2QV7C8Y4ZAPPR0000002',
            hash: str_repeat('2', 64),
            prefix: 'aaaabbbbccccdddd',
            issuedIso: '2026-04-22T00:00:00Z',
        ));

        $loaded = $repo->findByPrefix('aaaabbbbccccdddd');
        self::assertNotNull($loaded);
        self::assertSame('01HW7K9B2QV7C8Y4ZAPPR0000002', $loaded->id);
    }

    public function testSaveUpdatesResponseOnSecondCall(): void
    {
        $repo = new PdoApprovalTokenRepository($this->requirePdo());
        $token = $this->token(hash: str_repeat('b', 64), prefix: 'bbbbbbbbbbbbbbbb');
        $repo->save($token);

        $responded = $token->respond(
            ApprovalDecision::Rejected,
            'missing receipt',
            new DateTimeImmutable('2026-04-22T01:00:00Z', new DateTimeZone('UTC')),
        );
        $repo->save($responded);

        $loaded = $repo->findByTokenHash(str_repeat('b', 64));
        self::assertNotNull($loaded);
        self::assertTrue($loaded->isResponded());
        self::assertSame(ApprovalDecision::Rejected, $loaded->decision);
        self::assertSame('missing receipt', $loaded->responseDetail);
    }

    public function testExpirePastDueCountsOnlyUnrespondedExpired(): void
    {
        $repo = new PdoApprovalTokenRepository($this->requirePdo());
        // expired, unresponded
        $repo->save($this->token(
            id: '01HW7K9B2QV7C8Y4ZAPPR0000001',
            hash: str_repeat('c', 64),
            prefix: 'ccccdddd11112222',
            expiresIso: '2026-04-20T00:00:00Z',
        ));
        // expired, responded
        $responded = $this->token(
            id: '01HW7K9B2QV7C8Y4ZAPPR0000002',
            hash: str_repeat('d', 64),
            prefix: 'ddddddddddddddd0',
            expiresIso: '2026-04-19T00:00:00Z',
        )->respond(
            ApprovalDecision::Approved,
            'ok',
            new DateTimeImmutable('2026-04-19T01:00:00Z', new DateTimeZone('UTC')),
        );
        $repo->save($responded);
        // still active
        $repo->save($this->token(
            id: '01HW7K9B2QV7C8Y4ZAPPR0000003',
            hash: str_repeat('e', 64),
            prefix: 'eeee111122223333',
            expiresIso: '2026-04-30T00:00:00Z',
        ));

        $now = new DateTimeImmutable('2026-04-22T00:00:00Z', new DateTimeZone('UTC'));
        self::assertSame(1, $repo->expirePastDue($now));
    }

    private function requirePdo(): PDO
    {
        self::assertNotNull($this->pdo);
        return $this->pdo;
    }

    private function config(): DatabaseConfig
    {
        return new DatabaseConfig(
            host: $this->host,
            dbname: $this->dbname,
            username: $this->username,
            password: $this->password,
            port: $this->port,
        );
    }

    private function migrate(PDO $pdo): void
    {
        $pdo->exec(<<<'SQL'
            CREATE TABLE approval_tokens (
              id               BINARY(16) NOT NULL,
              journal_entry_id BINARY(16) NULL,
              receipt_id       BINARY(16) NULL,
              target_kind      VARCHAR(16) NOT NULL DEFAULT 'journal',
              token_hash       CHAR(64) NOT NULL,
              token_prefix     VARCHAR(16) NOT NULL DEFAULT '',
              channel          VARCHAR(16) NOT NULL,
              recipient        VARCHAR(255) NOT NULL,
              issued_by_user_id BINARY(16) NULL,
              issued_at        TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
              expires_at       TIMESTAMP(6) NOT NULL,
              responded_at     TIMESTAMP(6) NULL,
              response         VARCHAR(16) NULL,
              response_detail  VARCHAR(512) NOT NULL DEFAULT '',
              created_at       TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
              updated_at       TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
              PRIMARY KEY (id),
              UNIQUE KEY uq_approval_tokens__hash (token_hash),
              KEY idx_approval_tokens__prefix (token_prefix),
              KEY idx_approval_tokens__kind (target_kind, journal_entry_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);
    }

    private function token(
        string $id = '01HW7K9B2QV7C8Y4ZAPPR0000001',
        string $hash = '',
        string $prefix = '',
        string $issuedIso = '2026-04-21T00:00:00Z',
        string $expiresIso = '2026-04-24T00:00:00Z',
    ): ApprovalToken {
        $tz = new DateTimeZone('UTC');
        $issued = new DateTimeImmutable($issuedIso, $tz);
        $expires = new DateTimeImmutable($expiresIso, $tz);
        return new ApprovalToken(
            id: $id,
            targetKind: ApprovalTargetKind::Journal,
            targetId: '01HW7K9B2QV7C8Y4ZJRNL000001',
            tokenHash: $hash,
            tokenPrefix: $prefix,
            channel: ApprovalChannel::Email,
            recipient: 'reviewer@example.com',
            issuedAt: $issued,
            expiresAt: $expires,
            respondedAt: null,
            decision: null,
            responseDetail: '',
            issuedByUserId: '01HW7K9B2QV7C8Y4ZUSER000001',
            createdAt: $issued,
            updatedAt: $issued,
        );
    }
}
