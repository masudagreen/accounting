<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Database;

use PDO;
use PDOException;
use Rucaro\Infrastructure\Database\Exception\DatabaseConnectionException;
use Throwable;

/**
 * Centralised factory for PDO connections.
 *
 * Why here?
 *   - Every caller gets the same ADR-002 session contract:
 *     `SET NAMES <charset> COLLATE <collation>` and
 *     `SET time_zone = '+00:00'` (UTC at storage layer).
 *   - Connection failures surface as {@see DatabaseConnectionException}
 *     instead of raw PDOException, keeping our error taxonomy stable.
 *   - Config may come from a {@see DatabaseConfig} DTO, an associative
 *     array (from `config/database.php`), or the process environment.
 */
final class ConnectionFactory
{
    /**
     * Create a PDO from a fully validated {@see DatabaseConfig}. This is the
     * single method that actually calls `new PDO(...)`; the other two create*
     * helpers normalise input and delegate here.
     */
    public static function createFromConfig(DatabaseConfig $config): PDO
    {
        $dsn = $config->toDsn();
        $options = $config->effectiveOptions();

        try {
            $pdo = new PDO($dsn, $config->username, $config->password, $options);
        } catch (PDOException $e) {
            throw DatabaseConnectionException::fromPdoFailure($e->getMessage(), $e);
        } catch (Throwable $e) {
            // Belt-and-braces for driver-level errors that might not extend PDOException.
            throw DatabaseConnectionException::fromPdoFailure($e->getMessage(), $e);
        }

        self::applySessionContract($pdo, $config);

        return $pdo;
    }

    /**
     * Build from a `config/database.php`-shaped array.
     *
     * Accepted keys (all required unless noted):
     *   - driver    (optional; default "mysql")
     *   - host
     *   - port      (optional; default 3306)
     *   - database
     *   - username
     *   - password
     *   - charset   (optional; default "utf8mb4")
     *   - collation (optional; default "utf8mb4_unicode_ci")
     *   - options   (optional; PDO::ATTR_* => value)
     *
     * @param array<string, mixed> $config
     */
    public static function createFromArray(array $config): PDO
    {
        return self::createFromConfig(self::configFromArray($config));
    }

    /**
     * Build from environment variables (typically `$_ENV` after dotenv load).
     * Pass `null` to read directly from `$_ENV`.
     *
     * Mandatory: DB_HOST, DB_NAME, DB_USER, DB_PASSWORD (DB_PASSWORD may be "").
     * Optional:  DB_DRIVER, DB_PORT (DB_PORT_INTERNAL wins if both set),
     *            DB_CHARSET, DB_COLLATION.
     *
     * @param array<string, string|int|bool|null>|null $env
     */
    public static function createFromEnv(?array $env = null): PDO
    {
        return self::createFromConfig(self::configFromEnv($env));
    }

    // ---------------------------------------------------------------------
    // Configuration normalisation (testable in isolation; no PDO involved)
    // ---------------------------------------------------------------------

    /**
     * @param array<string, mixed> $config
     */
    public static function configFromArray(array $config): DatabaseConfig
    {
        $require = static function (array $cfg, string $key): string {
            if (!array_key_exists($key, $cfg)) {
                throw DatabaseConnectionException::invalidConfig(
                    sprintf('missing required key: %s', $key),
                );
            }
            /** @var mixed $value */
            $value = $cfg[$key];
            if (!is_scalar($value) && $value !== null) {
                throw DatabaseConnectionException::invalidConfig(
                    sprintf('key must be scalar: %s', $key),
                );
            }
            return (string) $value;
        };

        $options = $config['options'] ?? [];
        if (!is_array($options)) {
            throw DatabaseConnectionException::invalidConfig('options must be an array');
        }
        /** @var array<int, mixed> $normalisedOptions */
        $normalisedOptions = [];
        foreach ($options as $k => $v) {
            if (!is_int($k)) {
                throw DatabaseConnectionException::invalidConfig(
                    'options keys must be PDO::ATTR_* integer constants',
                );
            }
            $normalisedOptions[$k] = $v;
        }

        $port = $config['port'] ?? DatabaseConfig::DEFAULT_PORT;
        if (!is_int($port) && !(is_string($port) && ctype_digit($port))) {
            throw DatabaseConnectionException::invalidConfig('port must be an integer');
        }

        return new DatabaseConfig(
            host: $require($config, 'host'),
            dbname: $require($config, 'database'),
            username: $require($config, 'username'),
            password: $require($config, 'password'),
            port: (int) $port,
            charset: (string) ($config['charset'] ?? DatabaseConfig::DEFAULT_CHARSET),
            collation: (string) ($config['collation'] ?? DatabaseConfig::DEFAULT_COLLATION),
            driver: (string) ($config['driver'] ?? DatabaseConfig::DEFAULT_DRIVER),
            options: $normalisedOptions,
        );
    }

    /**
     * @param array<string, string|int|bool|null>|null $env
     */
    public static function configFromEnv(?array $env = null): DatabaseConfig
    {
        /** @var array<string, mixed> $source */
        $source = $env ?? $_ENV;

        $pick = static function (array $src, string $key): ?string {
            if (!array_key_exists($key, $src)) {
                return null;
            }
            /** @var mixed $v */
            $v = $src[$key];
            if ($v === null || $v === '') {
                return null;
            }
            if (!is_scalar($v)) {
                throw DatabaseConnectionException::invalidConfig(
                    sprintf('env var must be scalar: %s', $key),
                );
            }
            return (string) $v;
        };

        $host = $pick($source, 'DB_HOST') ?? throw DatabaseConnectionException::missingEnv('DB_HOST');
        $database = $pick($source, 'DB_NAME') ?? throw DatabaseConnectionException::missingEnv('DB_NAME');
        $username = $pick($source, 'DB_USER') ?? throw DatabaseConnectionException::missingEnv('DB_USER');

        // DB_PASSWORD may legitimately be empty; `?? ''` preserves that.
        $password = array_key_exists('DB_PASSWORD', $source) && $source['DB_PASSWORD'] !== null
            ? (string) $source['DB_PASSWORD']
            : '';

        $portRaw = $pick($source, 'DB_PORT_INTERNAL') ?? $pick($source, 'DB_PORT');
        $port = $portRaw !== null ? (int) $portRaw : DatabaseConfig::DEFAULT_PORT;

        return new DatabaseConfig(
            host: $host,
            dbname: $database,
            username: $username,
            password: $password,
            port: $port,
            charset: $pick($source, 'DB_CHARSET') ?? DatabaseConfig::DEFAULT_CHARSET,
            collation: $pick($source, 'DB_COLLATION') ?? DatabaseConfig::DEFAULT_COLLATION,
            driver: $pick($source, 'DB_DRIVER') ?? DatabaseConfig::DEFAULT_DRIVER,
            options: [],
        );
    }

    /**
     * Apply the per-connection session defaults mandated by ADR-002 §2.4 and §2.9:
     *   - UTC storage (`SET time_zone = '+00:00'`)
     *   - utf8mb4 + the requested collation (`SET NAMES`)
     *
     * These are idempotent and safe to run even if the server defaults already
     * match; we always run them so behaviour is identical across hosts.
     */
    private static function applySessionContract(PDO $pdo, DatabaseConfig $config): void
    {
        try {
            $names = $pdo->prepare('SET NAMES ' . self::quoteIdent($pdo, $config->charset)
                . ' COLLATE ' . self::quoteIdent($pdo, $config->collation));
            $names->execute();

            $tz = $pdo->prepare("SET time_zone = '+00:00'");
            $tz->execute();
        } catch (PDOException $e) {
            throw DatabaseConnectionException::fromPdoFailure(
                'failed to apply session defaults: ' . $e->getMessage(),
                $e,
            );
        }
    }

    /**
     * `SET NAMES` does not accept placeholders, so we still need to quote
     * charset / collation identifiers. We whitelist-validate them rather than
     * rely on PDO::quote because server names are restricted to
     * `[A-Za-z0-9_]` by spec.
     */
    private static function quoteIdent(PDO $pdo, string $identifier): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            throw DatabaseConnectionException::invalidConfig(
                sprintf('invalid identifier: %s', $identifier),
            );
        }
        unset($pdo); // reserved for future per-driver quoting; keeps signature stable
        return $identifier;
    }
}
