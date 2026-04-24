<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Database;

use PDO;
use Rucaro\Infrastructure\Database\Exception\DatabaseConnectionException;

/**
 * Immutable DTO that captures everything the {@see ConnectionFactory} needs
 * to build a PDO connection for MariaDB / MySQL.
 *
 * Validation is done in the constructor so an invalid config never escapes
 * into the factory layer. `options` holds `PDO::ATTR_*` keys with
 * driver-agnostic values.
 *
 * @phpstan-type PdoOption array<int, mixed>
 */
final readonly class DatabaseConfig
{
    public const DEFAULT_CHARSET = 'utf8mb4';
    public const DEFAULT_COLLATION = 'utf8mb4_unicode_ci';
    public const DEFAULT_PORT = 3306;
    public const DEFAULT_DRIVER = 'mysql';

    /**
     * Secure defaults for every new PDO connection we hand out:
     *  - ERRMODE_EXCEPTION so no silent failures
     *  - FETCH_ASSOC so callers never have to pass the mode explicitly
     *  - EMULATE_PREPARES=false so we always get server-side prepared statements
     *  - STRINGIFY_FETCHES=false to leverage PHP 8.1+ native MySQL type mapping
     *
     * @return PdoOption
     */
    public static function defaultPdoOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];
    }

    /**
     * @param PdoOption $options
     */
    public function __construct(
        public string $host,
        public string $dbname,
        public string $username,
        public string $password,
        public int $port = self::DEFAULT_PORT,
        public string $charset = self::DEFAULT_CHARSET,
        public string $collation = self::DEFAULT_COLLATION,
        public string $driver = self::DEFAULT_DRIVER,
        public array $options = [],
    ) {
        if ($this->host === '') {
            throw DatabaseConnectionException::invalidConfig('host must not be empty');
        }
        if ($this->dbname === '') {
            throw DatabaseConnectionException::invalidConfig('dbname must not be empty');
        }
        if ($this->username === '') {
            throw DatabaseConnectionException::invalidConfig('username must not be empty');
        }
        if ($this->port < 1 || $this->port > 65535) {
            throw DatabaseConnectionException::invalidConfig(
                sprintf('port out of range (1..65535): %d', $this->port),
            );
        }
        if ($this->charset === '') {
            throw DatabaseConnectionException::invalidConfig('charset must not be empty');
        }
        if ($this->collation === '') {
            throw DatabaseConnectionException::invalidConfig('collation must not be empty');
        }
        if ($this->driver === '') {
            throw DatabaseConnectionException::invalidConfig('driver must not be empty');
        }
    }

    /**
     * Build the PDO DSN string, e.g.
     *   "mysql:host=db;port=3306;dbname=rucaro;charset=utf8mb4"
     */
    public function toDsn(): string
    {
        return sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $this->driver,
            $this->host,
            $this->port,
            $this->dbname,
            $this->charset,
        );
    }

    /**
     * Final PDO options = defaults merged with user overrides. User-supplied
     * values win so tests can flip any attribute without losing the safety net.
     *
     * @return PdoOption
     */
    public function effectiveOptions(): array
    {
        // `+` keeps keys from the left operand when both sides have the same
        // key, so user-supplied overrides must come first.
        return $this->options + self::defaultPdoOptions();
    }

    /**
     * Non-destructive copy with additional options merged in (user overrides
     * win). Supports our immutability rule.
     *
     * @param PdoOption $options
     */
    public function withOptions(array $options): self
    {
        return new self(
            host: $this->host,
            dbname: $this->dbname,
            username: $this->username,
            password: $this->password,
            port: $this->port,
            charset: $this->charset,
            collation: $this->collation,
            driver: $this->driver,
            // new `$options` win over the existing ones (non-mutating merge).
            options: $options + $this->options,
        );
    }
}
