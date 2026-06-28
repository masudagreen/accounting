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
        self::assertSame('debit', $lines[0]['side']);
        self::assertSame('credit', $lines[1]['side']);
        self::assertSame('売上', $lines[1]['memo']);
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

    public function testExtractLinesParsesTaxFields(): void
    {
        $bag = [
            'lines' => [
                [
                    'side' => 'debit',
                    'account_title_id' => 'A1',
                    'amount' => '1100',
                    'memo' => '',
                    'tax_rate_percent' => '10.00',
                    'tax_amount' => '100.0000',
                    'is_tax_reduced' => '1',
                ],
            ],
        ];
        $lines = JournalFormSupport::extractLines($bag);

        self::assertSame('10.00', $lines[0]['tax_rate_percent']);
        self::assertSame('100.0000', $lines[0]['tax_amount']);
        self::assertTrue($lines[0]['is_tax_reduced']);
    }

    public function testExtractLinesDefaultsTaxFieldsWhenAbsent(): void
    {
        $bag = [
            'lines' => [
                ['side' => 'debit', 'account_title_id' => 'A1', 'amount' => '100', 'memo' => ''],
            ],
        ];
        $lines = JournalFormSupport::extractLines($bag);

        self::assertSame('0.00', $lines[0]['tax_rate_percent']);
        self::assertSame('0.0000', $lines[0]['tax_amount']);
        self::assertFalse($lines[0]['is_tax_reduced']);
    }

    public function testExtractLinesTreatsRowWithOnlyTaxFieldsAsEmpty(): void
    {
        // A row that has nothing but a tax checkbox unchecked should still be
        // considered a placeholder so we don't accidentally save a blank line.
        $bag = [
            'lines' => [
                ['side' => '', 'account_title_id' => '', 'amount' => '', 'memo' => '', 'tax_rate_percent' => '0.00', 'tax_amount' => '0.0000'],
            ],
        ];
        self::assertSame([], JournalFormSupport::extractLines($bag));
    }

    public function testNormalizeTaxRateAcceptsCommonInputs(): void
    {
        self::assertSame('0.00', JournalFormSupport::normalizeTaxRate(''));
        self::assertSame('0.00', JournalFormSupport::normalizeTaxRate('0'));
        self::assertSame('10.00', JournalFormSupport::normalizeTaxRate('10'));
        self::assertSame('10.00', JournalFormSupport::normalizeTaxRate('10.00'));
        self::assertSame('8.00', JournalFormSupport::normalizeTaxRate('8'));
        self::assertSame('8.50', JournalFormSupport::normalizeTaxRate('8.5'));
    }

    public function testNormalizeTaxRatePassesThroughInvalid(): void
    {
        // Invalid inputs are preserved so downstream validation can raise.
        self::assertSame('xx', JournalFormSupport::normalizeTaxRate('xx'));
    }

    public function testNormalizeTaxAmountUsesAmountSemantics(): void
    {
        self::assertSame('0.0000', JournalFormSupport::normalizeTaxAmount(''));
        self::assertSame('100.0000', JournalFormSupport::normalizeTaxAmount('100'));
        self::assertSame('100.5000', JournalFormSupport::normalizeTaxAmount('100.5'));
        self::assertSame('123456.7890', JournalFormSupport::normalizeTaxAmount('123,456.789'));
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

    public function testToLineInputCarriesEveryFieldThrough(): void
    {
        $input = JournalFormSupport::toLineInput([
            'side' => 'debit',
            'account_title_id' => 'A1',
            'sub_account_title_id' => 'S1',
            'amount' => '1100',
            'memo' => 'コーヒー',
            'tax_rate_percent' => '10',
            'tax_amount' => '100',
            'is_tax_reduced' => false,
        ]);

        self::assertSame('debit', $input->side);
        self::assertSame('A1', $input->accountTitleId);
        self::assertSame('S1', $input->subAccountTitleId);
        self::assertSame('1100.0000', $input->amount);
        self::assertSame('コーヒー', $input->memo);
        self::assertSame('10.00', $input->taxRatePercent);
        self::assertSame('100.0000', $input->taxAmount);
        self::assertFalse($input->isTaxReduced);
    }

    public function testToLineInputPreservesReducedTaxFlag(): void
    {
        $input = JournalFormSupport::toLineInput([
            'side' => 'debit',
            'account_title_id' => 'A1',
            'sub_account_title_id' => null,
            'amount' => '108',
            'memo' => '軽減対象',
            'tax_rate_percent' => '8',
            'tax_amount' => '8',
            'is_tax_reduced' => true,
        ]);

        self::assertSame('8.00', $input->taxRatePercent);
        self::assertTrue($input->isTaxReduced);
        self::assertNull($input->subAccountTitleId);
    }

    public function testToLineInputCollapsesEmptyTaxFieldsToZeroDefaults(): void
    {
        $input = JournalFormSupport::toLineInput([
            'side' => 'credit',
            'account_title_id' => 'A2',
            'sub_account_title_id' => null,
            'amount' => '1000',
            'memo' => '',
            'tax_rate_percent' => '',
            'tax_amount' => '',
            'is_tax_reduced' => false,
        ]);

        self::assertSame('0.00', $input->taxRatePercent);
        self::assertSame('0.0000', $input->taxAmount);
        self::assertFalse($input->isTaxReduced);
    }

    /* --------------------------------------------------------------------
     * F-4: per-line memo + line schema regressions for the
     *      independent two-table form. The pair-based extractors and the
     *      groupLinesIntoPairs helper introduced by F-2 are gone.
     * ------------------------------------------------------------------ */

    public function testExtractLinesPreservesPerLineMemo(): void
    {
        $bag = [
            'lines' => [
                ['side' => 'credit', 'account_title_id' => 'A_SALES', 'amount' => '1000', 'memo' => 'L1'],
                ['side' => 'debit',  'account_title_id' => 'A_CASH',  'amount' => '1000', 'memo' => 'L2'],
            ],
        ];
        $lines = JournalFormSupport::extractLines($bag);

        self::assertCount(2, $lines);
        self::assertSame('L1', $lines[0]['memo']);
        self::assertSame('L2', $lines[1]['memo']);
    }

    public function testExtractLinesAcceptsCompoundMixedSides(): void
    {
        // 3 lines: 1 credit + 2 debits (compound entry). Each row has
        // its own memo and the helper does not try to pair them.
        $bag = [
            'lines' => [
                ['side' => 'credit', 'account_title_id' => 'A_CASH',     'amount' => '1100', 'memo' => 'mc'],
                ['side' => 'debit',  'account_title_id' => 'A_SUPPLIES', 'amount' => '1000', 'memo' => 'd1', 'tax_rate_percent' => '10', 'tax_amount' => '90.9091'],
                ['side' => 'debit',  'account_title_id' => 'A_TAX',      'amount' => '100',  'memo' => 'd2'],
            ],
        ];
        $lines = JournalFormSupport::extractLines($bag);

        self::assertCount(3, $lines);
        self::assertSame('credit', $lines[0]['side']);
        self::assertSame('debit', $lines[1]['side']);
        self::assertSame('debit', $lines[2]['side']);
        self::assertSame('mc', $lines[0]['memo']);
        self::assertSame('A_SUPPLIES', $lines[1]['account_title_id']);
    }

    public function testSplitLinesBySideSegregatesByPosition(): void
    {
        // Out-of-order input still segregates correctly.
        $lines = [
            ['side' => 'debit',  'account_title_id' => 'A_SUPPLIES', 'sub_account_title_id' => null, 'amount' => '1000.0000', 'memo' => 'd1', 'tax_rate_percent' => '10.00', 'tax_amount' => '0.0000', 'is_tax_reduced' => false],
            ['side' => 'credit', 'account_title_id' => 'A_CASH',     'sub_account_title_id' => null, 'amount' => '1100.0000', 'memo' => 'c1', 'tax_rate_percent' => '0.00',  'tax_amount' => '0.0000', 'is_tax_reduced' => false],
            ['side' => 'debit',  'account_title_id' => 'A_TAX',      'sub_account_title_id' => null, 'amount' => '100.0000',  'memo' => 'd2', 'tax_rate_percent' => '0.00',  'tax_amount' => '0.0000', 'is_tax_reduced' => false],
        ];
        $split = JournalFormSupport::splitLinesBySide($lines);

        self::assertCount(1, $split['credit']);
        self::assertSame('A_CASH', $split['credit'][0]['account_title_id']);
        self::assertSame('c1', $split['credit'][0]['memo']);
        self::assertCount(2, $split['debit']);
        self::assertSame('A_SUPPLIES', $split['debit'][0]['account_title_id']);
        self::assertSame('d1', $split['debit'][0]['memo']);
        self::assertSame('A_TAX', $split['debit'][1]['account_title_id']);
    }

    public function testSplitLinesBySidePadsBlankWhenSideMissing(): void
    {
        $split = JournalFormSupport::splitLinesBySide([]);

        self::assertCount(1, $split['credit']);
        self::assertCount(1, $split['debit']);
        self::assertSame('', $split['credit'][0]['account_title_id']);
        self::assertSame('', $split['debit'][0]['amount']);
        self::assertSame('', $split['credit'][0]['memo']);
    }
}
