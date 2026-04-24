<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;

#[CoversClass(EnvelopeResponse::class)]
#[CoversClass(ErrorResponse::class)]
final class EnvelopeResponseTest extends TestCase
{
    public function testOkEmitsSuccessShape(): void
    {
        $resp = EnvelopeResponse::ok(data: ['answer' => 42]);

        self::assertSame(200, $resp->status);
        $decoded = json_decode($resp->body, true);
        self::assertIsArray($decoded);
        self::assertTrue($decoded['success']);
        self::assertSame(['answer' => 42], $decoded['data']);
        self::assertNull($decoded['error']);
    }

    public function testListIncludesMeta(): void
    {
        $resp = EnvelopeResponse::list([['id' => 1]], ['total' => 1, 'page' => 1, 'pageSize' => 50]);

        $decoded = json_decode($resp->body, true);
        self::assertIsArray($decoded);
        self::assertSame(['total' => 1, 'page' => 1, 'pageSize' => 50], $decoded['meta']);
    }

    public function testUnauthorizedIsFourOhOne(): void
    {
        $resp = ErrorResponse::unauthorized();

        self::assertSame(401, $resp->status);
        $decoded = json_decode($resp->body, true);
        self::assertIsArray($decoded);
        self::assertFalse($decoded['success']);
        self::assertSame('UNAUTHORIZED', $decoded['error']['code']);
    }

    public function testUnprocessableCarriesFieldErrors(): void
    {
        $resp = ErrorResponse::unprocessable('nope', ['email' => ['bad']]);

        self::assertSame(422, $resp->status);
        $decoded = json_decode($resp->body, true);
        self::assertIsArray($decoded);
        self::assertSame('VALIDATION_FAILED', $decoded['error']['code']);
        self::assertSame(['email' => ['bad']], $decoded['error']['details']['errors']);
    }
}
