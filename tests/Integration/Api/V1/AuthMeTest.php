<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Api\V1;

use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class AuthMeTest extends ApiTestCase
{
    public function testMeRequiresAuthentication(): void
    {
        $body = $this->dispatch('GET', '/api/v1/auth/me');
        $decoded = json_decode($body, true);
        self::assertIsArray($decoded);
        self::assertFalse($decoded['success']);
        self::assertSame('UNAUTHORIZED', $decoded['error']['code']);
    }
}
