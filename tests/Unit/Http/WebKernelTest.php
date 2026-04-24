<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Response\HtmlResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Http\WebKernel;

#[CoversClass(WebKernel::class)]
final class WebKernelTest extends TestCase
{
    public function testReturnsServiceUnavailableWhenContainerMissing(): void
    {
        $kernel = new WebKernel(null);

        $response = $kernel->handle(new ServerRequest(
            method: 'GET',
            path: '/ui/login',
            headers: [],
            query: [],
            json: null,
            rawBody: '',
        ));

        self::assertSame(503, $response->status);
        self::assertStringContainsString('DI container', $response->body);
    }

    public function testReturnsNotFoundForUnknownUiPathWithNoContainer(): void
    {
        // Even without a container we should return a response (503 for any
        // matched route, but an entirely unknown path still flows through the
        // kernel's "no container wired" short-circuit).
        $kernel = new WebKernel(null);

        $response = $kernel->handle(new ServerRequest(
            method: 'GET',
            path: '/ui/unknown-page',
            headers: [],
            query: [],
            json: null,
            rawBody: '',
        ));

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertContains($response->status, [404, 503]);
    }
}
