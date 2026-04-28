<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use PDO;

/**
 * Manages the schema_migrations tracking table.
 *
 * Schema: schema_migrations (
 *   version    VARCHAR(16) PRIMARY KEY,
 *   applied_at BIGINT NOT NULL
 * )
 *
 * Intentionally uses only portable SQL that works on both SQLite and MariaDB.
 */
final class MigrationRecord implements MigrationRecordInterface
{
    public const TABLE_NAME = 'schema_migrations';

    public function __construct(private readonly PDO $pdo)
    {
    }

    public static function createTableSql(): string
    {
        return sprintf(
            'CREATE TABLE IF NOT EXISTS %s ('
            . 'version VARCHAR(16) NOT NULL, '
            . 'applied_at BIGINT NOT NULL, '
            . 'PRIMARY KEY (version)'
            . ')',
            self::TABLE_NAME,
        );
    }

    /**
     * Creates the tracking table if it does not exist.
     */
    public function ensureTable(): void
    {
        $this->pdo->exec(self::createTableSql());
    }

    /**
     * Returns all applied version strings in ascending order.
     *
     * @return list<string>
     */
    public function getAppliedVersions(): array
    {
        $stmt = $this->pdo->query(
            'SELECT version FROM ' . self::TABLE_NAME . ' ORDER BY version ASC'
        );

        if ($stmt === false) {
            return [];
        }

        /** @var list<string> */
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function isApplied(string $version): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM ' . self::TABLE_NAME . ' WHERE version = ?'
        );
        $stmt->execute([$version]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function markApplied(string $version): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ' . self::TABLE_NAME . ' (version, applied_at) VALUES (?, ?)'
        );
        $stmt->execute([$version, time()]);
    }

    public function markRolledBack(string $version): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM ' . self::TABLE_NAME . ' WHERE version = ?'
        );
        $stmt->execute([$version]);
    }
}
