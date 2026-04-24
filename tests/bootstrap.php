<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap.
 *
 * - Loads Composer's autoloader (including Rucaro\Tests\ mapping from
 *   composer.json :: autoload-dev).
 * - Loads `.env.testing` when present so integration tests can override DB
 *   credentials without touching the developer's local `.env`.
 */

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Composer autoloader not found. Run `composer install` first.\n");
    exit(1);
}

require $autoload;

$repoRoot = dirname(__DIR__);

if (class_exists(\Dotenv\Dotenv::class)) {
    $envFile = is_file($repoRoot . '/.env.testing') ? '.env.testing' : null;

    if ($envFile !== null) {
        \Dotenv\Dotenv::createImmutable($repoRoot, $envFile)->safeLoad();
    } elseif (is_file($repoRoot . '/.env')) {
        \Dotenv\Dotenv::createImmutable($repoRoot)->safeLoad();
    }
}

date_default_timezone_set($_ENV['TZ'] ?? 'Asia/Tokyo');
