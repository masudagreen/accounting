<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Journal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\Controller\Ui\Journal\JournalFormSupport;
use Rucaro\Http\ServerRequest;

#[CoversClass(JournalFormSupport::class)]
final class JournalFormSupportTest extends TestCase
{
    public function testParseFormDecodesUrlEncodedBody(): void
    {
        $req = new ServerRequest(
            method: 'POST',
            path: '/ui/journals/new',
            headers: ['content-type' => 'application/x-www-form-urlencoded'],
            query: [],
            json: null,
            rawBody: 'summary=hello+world&journal_date=2025-01-02&_csrf=tok',
        );

        $bag = JournalFormSupport::parseForm($req);

        self::assertSame('hello world', $bag['summary']);
        self::assertSame('2025-01-02', $bag['journal_date']);
        self::assertSame('tok', $bag['_csrf']);
    }

    public function testStrTrimsAndFallsBackToDefault(): void
    {
        $bag = ['summary' => '  padded  ', 'other' => 42];

        self::assertSame('padded', JournalFormSupport::str($bag, 'summary'));
        self::assertSame('default', JournalFormSupport::str($bag, 'missing', 'default'));
        self::assertSame('default', JournalFormSupport::str($bag, 'other', 'default'));
    }

    public function testExtractLinesDropsEmptyRows(): void
    {
        $bag = [
            'lines' => [
                ['side' => 'debit',  'account_title_id' => 'A1', 'amount' => '1000', 'memo' => ''],
                ['side' => '',       'account_title_id' => '',   'amount' => '',     'memo' => ''],
                ['side' => 'credit', 'account_title_id' => 'A2', 'amount' => '1000', 'memo' => '売上'],
            ],
        ];

        $lines = JournalFormSupport::extractLines($bag);

        self::assertCount(2, $lines);
        self::assertSame('debit',  $lines[0]['side']);
        self::assertSame('credit', $lines[1]['side']);
        self::assertSame('売上',   $lines[1]['memo']);
    }

    public function testExtractLinesReturnsEmptyForMissingKey(): void
    {
        self::assertSame([], JournalFormSupport::extractLines([]));
    }

    public function testExtractLinesNullifiesBlankSubAccount(): void
    {
        $bag = [
            'lines' => [
                ['side' => 'debit', 'account_title_id' => 'A1', 'sub_account_title_id' => '', 'amount' => '1', 'memo' => ''],
            ],
        ];
        $lines = JournalFormSupport::extractLines($bag);

        self::assertNull($lines[0]['sub_account_title_id']);
    }

    public function testNormalizeAmountPadsFractionalScale(): void
    {
        self::assertSame('1000.0000', JournalFormSupport::normalizeAmount('1000'));
        self::assertSame('1234.5600', JournalFormSupport::normalizeAmount('1234.56'));
    }

    public function testNormalizeAmountStripsCommaGrouping(): void
    {
        self::assertSame('1234567.0000', JournalFormSupport::normalizeAmount('1,234,567'));
    }

    public function testNormalizeAmountTruncatesExcessFractionalDigits(): void
    {
        self::assertSame('1.1234', JournalFormSupport::normalizeAmount('1.12345678'));
    }

    public function testNormalizeAmountReturnsZeroForEmptyInput(): void
    {
        self::assertSame('0.0000', JournalFormSupport::normalizeAmount(''));
        self::assertSame('0.0000', JournalFormSupport::normalizeAmount('   '));
    }

    public function testNormalizeAmountPassesThroughInvalidInputUnchanged(): void
    {
        // Invalid input is preserved so downstream validation can raise a
        // meaningful error — we don't silently rewrite garbage into '0'.
        self::assertSame('abc', JournalFormSupport::normalizeAmount('abc'));
    }
}
