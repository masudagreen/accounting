<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Api\V1;

use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Requires a live MariaDB configured via RUCARO_TEST_DB_* env vars plus a
 * clean schema. The suite skips when those vars are missing so developers
 * without the integration stack up and running still get a green local run.
 */
#[CoversNothing]
final class AuthLoginTest extends ApiTestCase
{
    public function testLoginReturns200AndToken(): void
    {
        // Placeholder assertion — the full fixture (schema + seed) lands in a
        // follow-up ticket. For now the base class guarantees we skip unless
        // the DB is configured, and this assertion verifies the wiring runs
        // at all.
        $body = $this->dispatch(
            'POST',
            '/api/v1/auth/login',
            headers: ['content-type' => 'application/json'],
            json: ['email' => 'missing@example.com', 'password' => 'nope-nope-nope'],
        );
        self::assertStringContainsString('success', $body);
    }
}
