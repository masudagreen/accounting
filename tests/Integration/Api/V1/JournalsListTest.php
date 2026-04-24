<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Api\V1;

use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class JournalsListTest extends ApiTestCase
{
    public function testJournalsListRequiresAuth(): void
    {
        $body = $this->dispatch('GET', '/api/v1/journals', query: ['entityId' => '01HW7K9B2QV7C8Y4ZENTITY0001']);
        $decoded = json_decode($body, true);
        self::assertIsArray($decoded);
        self::assertFalse($decoded['success']);
    }
}
