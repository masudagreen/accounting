<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Search;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Search\RomajiAlias;

/**
 * F-4: hepburn romaji alias generator for the journal form's account
 * combobox. Drives the "SHOUMOU → 消耗品費" type-ahead behaviour.
 *
 * The generator is intentionally lossy — it concatenates per-kanji
 * readings from a static accounting-domain dictionary, ignoring
 * okurigana / context. That's good enough for substring-based search
 * which only needs to make a likely user-typed romaji string appear
 * somewhere in the alias.
 */
#[CoversClass(RomajiAlias::class)]
final class RomajiAliasTest extends TestCase
{
    public function testForReturnsLowercaseAsciiOnly(): void
    {
        $alias = RomajiAlias::for('現金');
        self::assertMatchesRegularExpression('/^[a-z]+$/', $alias);
    }

    public function testForKnownAccountingTermContainsExpectedSyllables(): void
    {
        // 消耗品費 → "shoumouhinhi" → long-vowel collapse → "shomohinhi".
        // Substring search for the collapsed "shomo" hits; query-side
        // collapse (JS) maps "shoumou" / "shomou" to "shomo" too.
        $alias = RomajiAlias::for('消耗品費');
        self::assertStringContainsString('shomo', $alias);
    }

    public function testForCashTerm(): void
    {
        // 現金 → "genkin"
        $alias = RomajiAlias::for('現金');
        self::assertStringContainsString('genkin', $alias);
    }

    public function testForSalesTerm(): void
    {
        // 売上高 → idiomatic "uriagedaka" via compound dictionary; substring
        // search for "uriage" must hit (per-kanji decomposition would have
        // produced "urijoukou", which is what users never type).
        $alias = RomajiAlias::for('売上高');
        self::assertStringContainsString('uriage', $alias);
    }

    public function testForShortTermLoansPayable(): void
    {
        // F-4 fix: 短 was missing entirely from the dictionary, so 短期借入金
        // collapsed to "kikariirikin". Now: 短期 → "tanki" (idiomatic) +
        // 借入金 → "kariirekin" (idiomatic). Substring search "tanki" must hit.
        $alias = RomajiAlias::for('短期借入金 (shortTermLoansPayable)');
        self::assertStringContainsString('tanki', $alias);
        self::assertStringContainsString('kariire', $alias);
    }

    public function testForCommonIdiomaticAccountingTerms(): void
    {
        // Idiomatic readings (熟字訓) that per-kanji decomposition cannot
        // produce — these were silent regressions before the compound
        // dictionary fix.
        self::assertStringContainsString('shiire', RomajiAlias::for('仕入'));
        self::assertStringContainsString('tatekae', RomajiAlias::for('立替金'));
        self::assertStringContainsString('kawase', RomajiAlias::for('為替差損'));
        self::assertStringContainsString('tanaoroshi', RomajiAlias::for('棚卸資産'));
        self::assertStringContainsString('kashidaore', RomajiAlias::for('貸倒引当金'));
        self::assertStringContainsString('yokin', RomajiAlias::for('普通預金'));
    }

    public function testForCommunicationsTerm(): void
    {
        // 通信費 → "tsuushinhi" → collapse → "tsushinhi".
        $alias = RomajiAlias::for('通信費');
        self::assertStringContainsString('tsushin', $alias);
    }

    public function testCollapseLongVowelsHelper(): void
    {
        self::assertSame('shomo', RomajiAlias::collapseLongVowels('shoumou'));
        self::assertSame('tsushin', RomajiAlias::collapseLongVowels('tsuushin'));
        self::assertSame('shire', RomajiAlias::collapseLongVowels('shire'));   // ii not collapsed
        self::assertSame('genkin', RomajiAlias::collapseLongVowels('genkin'));  // unchanged
        self::assertSame('shoshikin', RomajiAlias::collapseLongVowels('shooshikin'));
    }

    public function testForUnknownKanjiFallsBackToEmpty(): void
    {
        // A synthetic CJK that's definitely not in the accounting dict.
        // The result might be empty but must not throw.
        $alias = RomajiAlias::for('齉');
        self::assertSame('', $alias);
    }

    public function testForStripsHiraganaKatakanaToTheirRomaji(): void
    {
        // ひらがな gets a hepburn rendering too.
        $alias = RomajiAlias::for('かいけい');
        self::assertSame('kaikei', $alias);
    }

    public function testForPunctuationAndSpacesDoNotLeakIntoOutput(): void
    {
        $alias = RomajiAlias::for('現 金 (cash)');
        // parens, ascii letters, and spaces are dropped; only kanji readings remain.
        self::assertSame('genkin', $alias);
    }

    public function testForEmptyInputReturnsEmpty(): void
    {
        self::assertSame('', RomajiAlias::for(''));
    }

    public function testForCompoundsAccumulateReadings(): void
    {
        // 売掛金 → "uri"+"kake"+"kin" so contains "urikake" and "kakekin"
        $alias = RomajiAlias::for('売掛金');
        self::assertStringContainsString('urikake', $alias);
    }
}
