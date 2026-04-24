<?php

declare(strict_types=1);

/**
 * Application-level configuration.
 *
 * Values are resolved from environment variables so the same artefact can
 * run in dev / test / production with nothing but a different `.env`.
 *
 * @return array{
 *     name: string,
 *     env: string,
 *     debug: bool,
 *     url: string,
 *     timezone: string,
 *     locale: string,
 * }
 */
return [
    'name' => (string) ($_ENV['APP_NAME'] ?? 'Rucaro Accounting'),
    'env' => (string) ($_ENV['APP_ENV'] ?? 'production'),
    'debug' => filter_var(
        $_ENV['APP_DEBUG'] ?? false,
        FILTER_VALIDATE_BOOLEAN,
        FILTER_NULL_ON_FAILURE,
    ) ?? false,
    'url' => (string) ($_ENV['APP_URL'] ?? 'http://localhost:8080'),
    'timezone' => (string) ($_ENV['TZ'] ?? 'Asia/Tokyo'),
    'locale' => (string) ($_ENV['APP_LOCALE'] ?? 'ja'),
];
