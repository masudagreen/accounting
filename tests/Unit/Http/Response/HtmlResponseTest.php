<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Response\HtmlResponse;

#[CoversClass(HtmlResponse::class)]
final class HtmlResponseTest extends TestCase
{
    public function testOkReturns200WithHtmlContentType(): void
    {
        $r = HtmlResponse::ok('<p>hi</p>');

        self::assertSame(200, $r->status);
        self::assertSame('text/html; charset=utf-8', $r->headers['Content-Type']);
        self::assertSame('<p>hi</p>', $r->body);
    }

    public function testRedirectDefaultsTo303(): void
    {
        $r = HtmlResponse::redirect('/ui/login');

        self::assertSame(303, $r->status);
        self::assertSame('/ui/login', $r->headers['Location']);
        self::assertSame('no-store', $r->headers['Cache-Control']);
    }

    public function testRedirectCanUseCustomStatus(): void
    {
        $r = HtmlResponse::redirect('/ui/dashboard', 302);

        self::assertSame(302, $r->status);
    }

    public function testNotFoundEscapesMessage(): void
    {
        $r = HtmlResponse::notFound('<script>x</script>');

        self::assertSame(404, $r->status);
        self::assertStringContainsString('&lt;script&gt;', $r->body);
        self::assertStringNotContainsString('<script>', $r->body);
    }
}
