<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Web;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Web\FlashMessageBag;

#[CoversClass(FlashMessageBag::class)]
final class FlashMessageBagTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testAddSuccessAndConsumeReturnsSingleMessage(): void
    {
        $bag = new FlashMessageBag();
        $bag->addSuccess('ログインしました。');

        $messages = $bag->consume();

        self::assertCount(1, $messages);
        self::assertSame('success', $messages[0]['kind']);
        self::assertSame('ログインしました。', $messages[0]['message']);
    }

    public function testConsumeDrainsBag(): void
    {
        $bag = new FlashMessageBag();
        $bag->addError('失敗しました');

        $bag->consume();

        self::assertSame([], $bag->consume(), 'Second consume must be empty.');
    }

    public function testDifferentKindsAreKeptInInsertionOrder(): void
    {
        $bag = new FlashMessageBag();
        $bag->addInfo('a');
        $bag->addWarning('b');
        $bag->addSuccess('c');

        $messages = $bag->consume();

        self::assertSame(['info', 'warning', 'success'], array_column($messages, 'kind'));
        self::assertSame(['a', 'b', 'c'], array_column($messages, 'message'));
    }

    public function testMalformedSessionBagIsIgnored(): void
    {
        $bag = new FlashMessageBag();

        $_SESSION['rucaro_flash_messages'] = 'nope';

        self::assertSame([], $bag->consume());
    }
}
