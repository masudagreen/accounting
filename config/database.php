<?php

declare(strict_types=1);

/**
 * Database connection definitions.
 *
 * A single "mysql" connection covers the new app. Additional connections
 * (legacy read-only, analytics slave, etc.) can be layered on later without
 * changing the consumers.
 *
 * @return array{
 *     default: string,
 *     connections: array<string, array{
 *         driver: string,
 *         host: string,
 *         port: int,
 *         database: string,
 *         username: string,
 *         password: string,
 *         charset: string,
 *         collation: string,
 *         options: array<int, mixed>,
 *     }>,
 * }
 */
return [
    'default' => (string) ($_ENV['DB_CONNECTION'] ?? 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => (string) ($_ENV['DB_DRIVER'] ?? 'mysql'),
            'host' => (string) ($_ENV['DB_HOST'] ?? 'db'),
            'port' => (int) ($_ENV['DB_PORT_INTERNAL'] ?? $_ENV['DB_PORT'] ?? 3306),
            'database' => (string) ($_ENV['DB_NAME'] ?? 'rucaro'),
            'username' => (string) ($_ENV['DB_USER'] ?? 'rucaro'),
            'password' => (string) ($_ENV['DB_PASSWORD'] ?? 'rucaro'),
            'charset' => (string) ($_ENV['DB_CHARSET'] ?? 'utf8mb4'),
            'collation' => (string) ($_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci'),
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
            ],
        ],
    ],
];
