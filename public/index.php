<?php

declare(strict_types=1);

/**
 * Rucaro Accounting v2 - front controller.
 *
 * NOTE: This is the NEW entry point introduced in Phase 1.3.
 * It does NOT fall back to the legacy back/ router. The legacy app at
 * ../index.php / ../api.php remains available through its own entrypoints
 * while Strangler Fig migration proceeds.
 */

use Dotenv\Dotenv;
use Rucaro\Http\Kernel;

$projectRoot = dirname(__DIR__);

$autoload = $projectRoot . '/vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'data'    => null,
        'error'   => [
            'code'    => 'autoload_missing',
            'message' => 'vendor/autoload.php not found. Run `composer install` first.',
        ],
    ], JSON_UNESCAPED_UNICODE);
    return;
}
require $autoload;

// Load .env if it exists. In production the host may inject env vars directly,
// so missing .env must not be fatal.
if (class_exists(Dotenv::class) && is_file($projectRoot . '/.env')) {
    Dotenv::createImmutable($projectRoot)->safeLoad();
}

// Fail fast on server misconfig.
mb_internal_encoding('UTF-8');
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Tokyo');

// DI container bootstrap (stub for Phase 1; real container lands in Phase 3).
// Today: just instantiate the kernel directly.
$kernel = new Kernel();

$kernel->handle()->send();
