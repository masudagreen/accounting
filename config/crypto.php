<?php

declare(strict_types=1);

/**
 * Crypto configuration (see ADR-003).
 *
 * - `master_key_base64url`: 32-byte master key, base64url encoded, provided
 *   via the APP_ENCRYPTION_KEY environment variable.
 * - `legacy_master_secret`: only set when importing from the old Blowfish
 *   dataset. Normally commented out in `.env`.
 *
 * @return array{
 *     master_key_base64url: string,
 *     legacy_master_secret: string|null,
 *     schema_version: string,
 *     key_version: string,
 *     hkdf_info_prefix: string,
 * }
 */
return [
    'master_key_base64url' => (string) ($_ENV['APP_ENCRYPTION_KEY'] ?? ''),
    'legacy_master_secret' => isset($_ENV['LEGACY_ENCRYPTION_SECRET']) && $_ENV['LEGACY_ENCRYPTION_SECRET'] !== ''
        ? (string) $_ENV['LEGACY_ENCRYPTION_SECRET']
        : null,
    'schema_version' => 'v2',
    'key_version' => 'k1',
    'hkdf_info_prefix' => 'rucaro/accounting/v1',
];
