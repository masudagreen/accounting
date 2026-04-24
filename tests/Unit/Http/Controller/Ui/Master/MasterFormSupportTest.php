<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Master;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Controller\Ui\Master\MasterFormSupport;
use Rucaro\Http\ServerRequest;

#[CoversClass(MasterFormSupport::class)]
final class MasterFormSupportTest extends TestCase
{
    public function testParseFormDecodesUrlEncodedBody(): void
    {
        $req = new ServerRequest(
            method: 'POST',
            path: '/ui/masters/account-titles/new',
            headers: [],
            query: [],
            json: null,
            rawBody: 'code=A101&name=' . rawurlencode('現金') . '&category=asset',
        );
        $bag = MasterFormSupport::parseForm($req);
        self::assertSame('A101', $bag['code']);
        self::assertSame('現金', $bag['name']);
        self::assertSame('asset', $bag['category']);
    }

    public function testStrFallsBackToDefaultWhenMissing(): void
    {
        self::assertSame('', MasterFormSupport::str([], 'x'));
        self::assertSame('default', MasterFormSupport::str([], 'x', 'default'));
        self::assertSame('hello', MasterFormSupport::str(['x' => '  hello  '], 'x'));
    }

    public function testIntParsesNegativeAndDefault(): void
    {
        self::assertSame(0, MasterFormSupport::int([], 'x'));
        self::assertSame(5, MasterFormSupport::int(['x' => '5'], 'x'));
        self::assertSame(-3, MasterFormSupport::int(['x' => '-3'], 'x'));
        self::assertSame(7, MasterFormSupport::int(['x' => 'abc'], 'x', 7));
    }

    public function testBoolCheckboxSemantics(): void
    {
        // Checkbox unchecked → key missing → default (false)
        self::assertFalse(MasterFormSupport::bool([], 'is_active'));
        // Checkbox checked → key present with '1'
        self::assertTrue(MasterFormSupport::bool(['is_active' => '1'], 'is_active'));
        // Literal 'off' -> false
        self::assertFalse(MasterFormSupport::bool(['is_active' => 'off'], 'is_active'));
    }

    public function testOptionalStrReturnsNullForBlank(): void
    {
        self::assertNull(MasterFormSupport::optionalStr(''));
        self::assertNull(MasterFormSupport::optionalStr('   '));
        self::assertSame('abc', MasterFormSupport::optionalStr('  abc  '));
    }
}
