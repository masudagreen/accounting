<?php

declare(strict_types=1);

/**
 * Rucaro Accounting v2 — Web UI entry point (/ui/*).
 *
 * Mirrors public/api/v1/index.php but hands off to {@see \Rucaro\Http\WebKernel}
 * which returns HTML instead of JSON. The two kernels live side by side and
 * never share routes so the REST API surface stays byte-stable while the UI
 * evolves (see ADR-022).
 */

use Dotenv\Dotenv;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\WebKernel;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Support\Container\ContainerBootstrap;
use Rucaro\Support\Web\SessionStore;

$projectRoot = dirname(__DIR__, 2);

$autoload = $projectRoot . '/vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>500</title>'
        . '<h1>500 Internal Server Error</h1>'
        . '<p>vendor/autoload.php not found. Run <code>composer install</code> first.</p>';
    return;
}
require $autoload;

if (class_exists(Dotenv::class) && is_file($projectRoot . '/.env')) {
    Dotenv::createImmutable($projectRoot)->safeLoad();
}

mb_internal_encoding('UTF-8');
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Tokyo');

// Harden the UI session cookie before session_start fires anywhere.
if (!headers_sent()) {
    session_name('rucaro_ui_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => (($_SERVER['HTTPS'] ?? '') === 'on'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

try {
    $pdo = ConnectionFactory::createFromEnv();
    $container = ContainerBootstrap::build($pdo);

    // Start the session before controllers run so SessionStore#start() inside
    // individual flows is idempotent. Done here — not in the kernel — so that
    // unit tests can build a kernel without forcing session_start.
    $sessionSvc = $container->get(SessionStore::class);
    if ($sessionSvc instanceof SessionStore) {
        $sessionSvc->start();
    }

    $kernel = new WebKernel($container);
} catch (\Throwable $e) {
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>503</title>'
        . '<h1>503 Service Unavailable</h1>'
        . '<p>初期化に失敗しました。DB 接続設定や .env を確認してください。</p>';
    return;
}

$response = $kernel->handle();
if ($response instanceof HtmlResponse) {
    $response->send();
}
