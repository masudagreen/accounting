<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Api\V1;

use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class JournalsCreateTest extends ApiTestCase
{
    public function testJournalsCreateRequiresAuth(): void
    {
        $body = $this->dispatch('POST', '/api/v1/journals', json: [
            'entityId' => '01HW7K9B2QV7C8Y4ZENTITY0001',
            'fiscalTermId' => '01HW7K9B2QV7C8Y4ZFTTERM0001',
            'journalDate' => '2026-04-21',
            'lines' => [],
        ]);
        $decoded = json_decode($body, true);
        self::assertIsArray($decoded);
        self::assertFalse($decoded['success']);
    }
}
