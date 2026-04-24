<?php

declare(strict_types=1);

/**
 * Rucaro Accounting - Legacy → V2 data migration CLI.
 *
 * Thin entry point. Real work happens in
 * {@see \Rucaro\Infrastructure\Import\LegacyImport\LegacyToV2Command}.
 *
 * Usage (run inside the app container or on the host with DB_PORT exposed):
 *   php scripts/import/legacy_to_v2.php \
 *       --source-db=rucaro_legacy \
 *       --target-db=rucaro \
 *       --dry-run \
 *       --placeholder-password='ChangeMe0!'
 */

use Rucaro\Infrastructure\Import\LegacyImport\LegacyToV2Command;
use Symfony\Component\Console\Application;

$repoRoot = dirname(__DIR__, 2);
$autoload = $repoRoot . '/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Composer autoloader not found. Run `composer install` first.\n");
    exit(1);
}
require $autoload;

if (class_exists(\Dotenv\Dotenv::class) && is_file($repoRoot . '/.env')) {
    \Dotenv\Dotenv::createImmutable($repoRoot)->safeLoad();
}

date_default_timezone_set($_ENV['TZ'] ?? 'Asia/Tokyo');

$command = new LegacyToV2Command();
$app = new Application('legacy_to_v2', '1.0.0');
$app->add($command);
$app->setDefaultCommand($command->getName() ?? 'legacy:import', true);
exit($app->run());
