<?php

declare(strict_types=1);

/**
 * Rucaro Accounting v2 - REST API entry point (/api/v1/*).
 *
 * Boots Composer, loads .env, builds the DI container from the PDO
 * connection, and hands off to {@see \Rucaro\Http\ApiKernel}.
 */

use Dotenv\Dotenv;
use Rucaro\Http\ApiKernel;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Support\Container\ContainerBootstrap;

$projectRoot = dirname(__DIR__, 3);

$autoload = $projectRoot . '/vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'data'    => null,
        'error'   => [
            'code'    => 'AUTOLOAD_MISSING',
            'message' => 'vendor/autoload.php not found. Run `composer install` first.',
        ],
    ], JSON_UNESCAPED_UNICODE);
    return;
}
require $autoload;

if (class_exists(Dotenv::class) && is_file($projectRoot . '/.env')) {
    Dotenv::createImmutable($projectRoot)->safeLoad();
}

mb_internal_encoding('UTF-8');
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Tokyo');

$kernel = null;
try {
    $pdo = ConnectionFactory::createFromEnv();
    $container = ContainerBootstrap::build($pdo);
    $kernel = new ApiKernel($container);
} catch (\Throwable $e) {
    // For healthz we don't actually need the DB, but we still want a sensible
    // response when the DB is unreachable.
    $kernel = new ApiKernel(null);
}

$kernel->handle()->send();
