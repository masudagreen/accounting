<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Api\V1;

use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class EntitiesListTest extends ApiTestCase
{
    public function testEntitiesListRequiresAuth(): void
    {
        $body = $this->dispatch('GET', '/api/v1/entities');
        $decoded = json_decode($body, true);
        self::assertIsArray($decoded);
        self::assertFalse($decoded['success']);
    }
}
