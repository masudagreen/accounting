<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\User;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\User\User;
use Rucaro\Domain\User\UserRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed implementation of {@see UserRepositoryInterface}.
 *
 * Reads soft-deleted rows so callers can distinguish "not registered" from
 * "account disabled" if they need to — but {@see self::hydrate()} leaves that
 * decision to the application layer by always returning the full row.
 */
final class PdoUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findByEmail(string $email): ?User
    {
        $sql = 'SELECT id, login_id, display_name, email, password_hash, is_active,
                       last_login_at, created_at, updated_at, deleted_at
                FROM users
                WHERE email = :email AND deleted_at IS NULL
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findById(string $id): ?User
    {
        $sql = 'SELECT id, login_id, display_name, email, password_hash, is_active,
                       last_login_at, created_at, updated_at, deleted_at
                FROM users
                WHERE id = :id AND deleted_at IS NULL
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function touchLastLogin(string $id, DateTimeImmutable $at): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET last_login_at = :at WHERE id = :id');
        $stmt->execute([
            ':at' => $at->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.u'),
            ':id' => UlidGenerator::decode($id),
        ]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): User
    {
        return new User(
            id: self::stringifyId($row['id'] ?? ''),
            loginId: (string) ($row['login_id'] ?? ''),
            displayName: (string) ($row['display_name'] ?? ''),
            email: (string) ($row['email'] ?? ''),
            passwordHash: (string) ($row['password_hash'] ?? ''),
            isActive: self::toBool($row['is_active'] ?? false),
            lastLoginAt: self::parseTimestamp($row['last_login_at'] ?? null),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? new DateTimeImmutable('@0'),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? new DateTimeImmutable('@0'),
            deletedAt: self::parseTimestamp($row['deleted_at'] ?? null),
        );
    }

    private static function stringifyId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function toBool(mixed $v): bool
    {
        if (is_bool($v)) {
            return $v;
        }
        if (is_int($v)) {
            return $v !== 0;
        }
        if (is_string($v)) {
            return $v !== '' && $v !== '0';
        }
        return (bool) $v;
    }

    private static function parseTimestamp(mixed $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (!is_string($raw)) {
            return null;
        }
        // MariaDB TIMESTAMP(6) comes back as "YYYY-MM-DD HH:MM:SS.uuuuuu".
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }
}
