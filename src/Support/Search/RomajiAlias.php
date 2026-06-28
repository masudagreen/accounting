<?php

declare(strict_types=1);

namespace Rucaro\Support\Search;

/**
 * F-4: tiny per-kanji Hepburn romaji generator for the journal form's
 * account combobox.
 *
 * Goal: make a likely user-typed romaji (e.g. "shoumou" / "genkin" /
 * "tsuushin") appear *somewhere* in the alias string, so that the JS
 * combobox can do a substring match across the alias the same way it
 * matches across the visible name.
 *
 * The mapping is intentionally lossy and small — we only ship readings
 * for kanji that appear in account names from the master 勘定科目 list.
 * Unknown kanji contribute nothing rather than transliterating to its
 * default reading; that keeps the alias short and the per-row cost
 * predictable. ASCII letters / digits / punctuation are dropped, since
 * those segments are already covered by the regular code/name match.
 *
 * Hiragana / katakana are decoded inline via a small canonical Hepburn
 * table because they're cheap to map and a few account names rely on
 * them ("かいけい" → "kaikei", etc.).
 */
final class RomajiAlias
{
    /**
     * @return string lower-case ASCII alias, possibly empty
     */
    public static function for(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $kanjiMap = self::kanjiReadings();
        $idiomatics = self::idiomaticReadings();
        $kanaMap = self::kanaReadings();

        $out = '';
        $chars = mb_str_split($text, 1, 'UTF-8');
        $n = count($chars);
        $i = 0;
        while ($i < $n) {
            $ch = $chars[$i];

            // Drop ascii / punctuation / spaces — they don't help romaji
            // matching since the visible code/name already includes them.
            if (preg_match('/[\x{0021}-\x{007E}\s\x{3000}]/u', $ch) === 1) {
                ++$i;
                continue;
            }

            // Greedy multi-char idiomatic compound (熟字訓) match — longest
            // first. Lets us emit "uriage" for 売上, "tanaoroshi" for 棚卸,
            // "kashidaore" for 貸倒 etc., which per-kanji decomposition
            // can't produce because those are 熟字訓 readings.
            $matchedLen = 0;
            $matchedRom = '';
            $maxLook = min(self::MAX_IDIOM_LEN, $n - $i);
            for ($len = $maxLook; $len >= 2; --$len) {
                $key = implode('', array_slice($chars, $i, $len));
                if (isset($idiomatics[$key])) {
                    $matchedLen = $len;
                    $matchedRom = $idiomatics[$key];
                    break;
                }
            }
            if ($matchedLen > 0) {
                $out .= $matchedRom;
                $i += $matchedLen;
                continue;
            }

            $cp = mb_ord($ch, 'UTF-8');
            if ($cp === false) {
                ++$i;
                continue;
            }
            // Hiragana (3041-3096) and katakana (30A1-30FA) → hepburn.
            if (($cp >= 0x3041 && $cp <= 0x3096) || ($cp >= 0x30A1 && $cp <= 0x30FA)) {
                $out .= $kanaMap[$ch] ?? '';
                ++$i;
                continue;
            }
            // CJK kanji range — look up in the static accounting dictionary.
            if (isset($kanjiMap[$ch])) {
                $out .= $kanjiMap[$ch];
            }
            ++$i;
        }

        // Fold to lower-case ASCII; strip anything that slipped through.
        $out = strtolower($out);
        $out = preg_replace('/[^a-z]/', '', $out) ?? '';

        return self::collapseLongVowels($out);
    }

    /**
     * Long-vowel collapse used by both alias generation and the JS-side
     * query normaliser. Lets a single alias serve queries written with or
     * without the long-vowel marker — `shoumouhinhi` / `shomohinhi` /
     * `shomouhinhi` all collapse to the same `shomohinhi` for substring
     * matching.
     *
     * Only `ou` / `uu` / `oo` are collapsed; `ee`, `ii`, `aa` are left as
     * is because dropping them risks collisions with legitimate two-vowel
     * stems (e.g. 仕入 = `shiire` would corrupt to `shire`).
     */
    public static function collapseLongVowels(string $s): string
    {
        $s = (string) preg_replace('/ou+/', 'o', $s);
        $s = (string) preg_replace('/uu+/', 'u', $s);
        $s = (string) preg_replace('/oo+/', 'o', $s);

        return $s;
    }

    /**
     * Longest compound key length checked at each position. Bumping this
     * costs O(MAX_IDIOM_LEN) extra string compares per character; keeping
     * it small keeps the per-row alias build cheap.
     */
    private const MAX_IDIOM_LEN = 4;

    /**
     * Static accounting-domain kanji → hepburn reading map. Compiled
     * once and cached on first call.
     *
     * Readings are chosen so common kanji compounds yield substrings
     * users actually type — e.g. 消耗品 → "shoumou"+"hin" so a search
     * for "shoumou" hits 消耗品費. Multiple-reading kanji use the
     * accounting reading first; we don't need exhaustive coverage.
     *
     * @return array<string, string>
     */
    private static function kanjiReadings(): array
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        // Hand-curated for the master 勘定科目 list; lossy by design.
        // Format: kanji => concatenated lowercase hepburn segment.
        // Multi-reading kanji default to the most common accounting reading;
        // the alias is for substring search so a slightly off reading still
        // works as long as the typed query lands somewhere in the alias.
        $cached = [
            // 現金預金・流動資産
            '現' => 'gen',
            '金' => 'kin',
            '預' => 'azukari',
            '貯' => 'cho',
            '蓄' => 'chiku',
            '当' => 'tou',
            '座' => 'za',
            '普' => 'fu',
            '通' => 'tsuu',
            '信' => 'shin',
            '定' => 'tei',
            '期' => 'ki',
            '別' => 'betsu',
            '段' => 'dan',
            // 売上・売掛
            '売' => 'uri',
            '掛' => 'kake',
            '買' => 'kai',
            '入' => 'iri',
            '出' => 'shutsu',
            '高' => 'kou',
            '上' => 'jou',
            '下' => 'ge',
            '受' => 'uke',
            '取' => 'tori',
            '払' => 'harai',
            '前' => 'mae',
            '渡' => 'watari',
            '返' => 'henkan',
            '値' => 'ne',
            '引' => 'biki',
            '割' => 'wari',
            '戻' => 'modoshi',
            // 棚卸資産
            '商' => 'shou',
            '品' => 'hin',
            '製' => 'sei',
            '材' => 'zai',
            '原' => 'gen',
            '仕' => 'shi',
            '蔵' => 'zou',
            // 固定資産
            '建' => 'tate',
            '物' => 'mono',
            '附' => 'fu',
            '属' => 'zoku',
            '設' => 'setsu',
            '備' => 'bi',
            '機' => 'ki',
            '械' => 'kai',
            '装' => 'sou',
            '置' => 'chi',
            '車' => 'sha',
            '両' => 'ryou',
            '運' => 'un',
            '搬' => 'pan',
            '具' => 'gu',
            '工' => 'kou',
            '器' => 'ki',
            '土' => 'to',
            '地' => 'chi',
            '構' => 'kou',
            '築' => 'chiku',
            '減' => 'gen',
            '価' => 'ka',
            '償' => 'shou',
            '却' => 'kyaku',
            '累' => 'rui',
            '計' => 'kei',
            '額' => 'gaku',
            // 投資・繰延
            '投' => 'tou',
            '資' => 'shi',
            '有' => 'yuu',
            '証' => 'shou',
            '券' => 'ken',
            '株' => 'kabu',
            '式' => 'shiki',
            '長' => 'chou',
            '貸' => 'kashi',
            '付' => 'tsuke',
            '保' => 'ho',
            '険' => 'ken',
            '繰' => 'kuri',
            '延' => 'en',
            '創' => 'sou',
            '立' => 'ritsu',
            '開' => 'kai',
            '業' => 'gyou',
            '研' => 'ken',
            '究' => 'kyuu',
            '発' => 'hatsu',
            '体' => 'tai',
            // 流動負債・固定負債
            '支' => 'shi',
            '形' => 'kei',
            '手' => 'te',
            '社' => 'sha',
            '債' => 'sai',
            '借' => 'kari',
            '未' => 'mi',
            '済' => 'sai',
            '仮' => 'kari',
            '消' => 'shou',
            '耗' => 'mou',
            '費' => 'hi',
            '税' => 'zei',
            '所' => 'sho',
            '得' => 'toku',
            '住' => 'juu',
            '民' => 'min',
            '事' => 'ji',
            // 純資産
            '純' => 'jun',
            '本' => 'hon',
            '剰' => 'jou',
            '余' => 'yo',
            '利' => 'ri',
            '益' => 'eki',
            '準' => 'jun',
            // 売上原価・販管費
            '販' => 'han',
            '管' => 'kan',
            '理' => 'ri',
            '給' => 'kyuu',
            '料' => 'ryou',
            '賞' => 'shou',
            '与' => 'yo',
            '退' => 'tai',
            '職' => 'shoku',
            '法' => 'hou',
            '福' => 'fuku',
            '厚' => 'kou',
            '生' => 'sei',
            '会' => 'kai',
            '議' => 'gi',
            '雑' => 'zatsu',
            '広' => 'kou',
            '告' => 'koku',
            '宣' => 'sen',
            '伝' => 'den',
            '接' => 'setsu',
            '待' => 'tai',
            '交' => 'kou',
            '際' => 'sai',
            '旅' => 'ryo',
            '水' => 'sui',
            '道' => 'dou',
            '光' => 'kou',
            '熱' => 'netsu',
            '修' => 'shuu',
            '繕' => 'zen',
            '租' => 'so',
            '公' => 'kou',
            '課' => 'ka',
            '寄' => 'ki',
            '新' => 'shin',
            '聞' => 'bun',
            '図' => 'to',
            '書' => 'sho',
            // 営業外
            '営' => 'ei',
            '外' => 'gai',
            '内' => 'nai',
            '為' => 'i',
            '替' => 'gae',
            '差' => 'sa',
            '損' => 'son',
            '配' => 'hai',
            // 特別損益・税金
            '特' => 'toku',
            '越' => 'koshi',
            // 補助・取引先
            '先' => 'saki',
            '名' => 'mei',
            '勘' => 'kan',
            '科' => 'ka',
            '目' => 'moku',
            // 件補助
            '部' => 'bu',
            '門' => 'mon',

            // F-4 fix: kanji used in account names but missing from the
            // original dictionary. Without these, alias for terms like
            // 短期借入金 / 棚卸資産 / 流動資産 collapsed to a substring users
            // would never type.
            '一' => 'ichi', '不' => 'fu',  '主' => 'shu',  '予' => 'yo',
            '人' => 'jin',  '他' => 'ta',  '代' => 'dai',  '作' => 'saku',
            '便' => 'bin',  '係' => 'kakari', '促' => 'soku', '倒' => 'tou',
            '元' => 'moto', '再' => 'sai', '処' => 'sho',  '分' => 'bun',
            '副' => 'fuku', '加' => 'ka',  '務' => 'mu',   '半' => 'han',
            '卸' => 'oroshi', '及' => 'kyuu', '収' => 'shuu', '口' => 'kuchi',
            '員' => 'in',   '固' => 'ko',  '報' => 'hou',  '失' => 'shitsu',
            '家' => 'ka',   '専' => 'sen', '小' => 'ko',   '少' => 'shou',
            '屑' => 'kuzu', '己' => 'ko',  '役' => 'yaku', '従' => 'juu',
            '性' => 'sei',  '拠' => 'kyo', '括' => 'katsu', '振' => 'furi',
            '採' => 'sai',  '控' => 'kou', '教' => 'kyou', '数' => 'suu',
            '整' => 'sei',  '敷' => 'shiki', '施' => 'shi', '更' => 'kou',
            '末' => 'matsu', '棚' => 'tana', '権' => 'ken',  '正' => 'sei',
            '注' => 'chuu', '流' => 'ryuu', '滞' => 'tai',  '燃' => 'nen',
            '産' => 'san',  '用' => 'you', '申' => 'shin', '留' => 'ryuu',
            '知' => 'chi',  '短' => 'tan', '破' => 'ha',   '積' => 'seki',
            '等' => 'tou',  '約' => 'yaku', '納' => 'nou',  '者' => 'sha',
            '育' => 'iku',  '自' => 'ji',  '荷' => 'ni',   '裏' => 'ura',
            '託' => 'taku', '評' => 'hyou', '試' => 'shi',  '話' => 'wa',
            '調' => 'chou', '諸' => 'sho', '譲' => 'jou',  '負' => 'fu',
            '賃' => 'chin', '込' => 'komi', '途' => 'to',   '造' => 'zou',
            '進' => 'shin', '郵' => 'yuu', '酬' => 'shuu', '量' => 'ryou',
            '関' => 'kan',  '除' => 'jo',  '電' => 'den',  '首' => 'shu',
            '験' => 'ken',  '方' => 'kata',
        ];

        // Multi-char compounds previously living in this table never fired
        // because the for() loop only did single-char lookups. They moved
        // to idiomaticReadings() so the new greedy match picks them up.
        return $cached;
    }

    /**
     * Multi-character compound (熟字訓 / common accounting term) readings.
     * Greedy longest-match-first inside {@see for()} ensures users typing
     * idiomatic readings ("uriage" / "tanaoroshi" / "kawase" / "kariire")
     * land on the right account even though per-kanji decomposition would
     * yield a different substring.
     *
     * Keys must be ≤ {@see MAX_IDIOM_LEN} characters.
     *
     * @return array<string, string>
     */
    private static function idiomaticReadings(): array
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $cached = [
            // legacy entries that used to live in the kanji dictionary but
            // never fired (the loop only did single-char lookups).
            '出資' => 'shusshi',
            '保険' => 'hoken',
            '消耗' => 'shoumou',
            '消耗品' => 'shoumouhin',
            '雑損' => 'zasson',
            '雑益' => 'zatsueki',
            '受取' => 'uketori',
            '支払' => 'shiharai',
            '利息' => 'risoku',
            '前期' => 'zenki',
            '繰越' => 'kurikoshi',
            '損益' => 'soneki',
            '純利' => 'junri',
            '取引' => 'torihiki',
            '会費' => 'kaihi',
            '交通' => 'koutsuu',
            '通信' => 'tsuushin',
            '所属' => 'shozoku',
            '仕掛' => 'shikake',
            '貯蔵' => 'chozou',

            // New: idiomatic readings (熟字訓) and high-frequency compounds
            // that per-kanji decomposition cannot reproduce.
            '売上' => 'uriage',
            '売上高' => 'uriagedaka',
            '売掛' => 'urikake',
            '売掛金' => 'urikakekin',
            '買掛' => 'kaikake',
            '買掛金' => 'kaikakekin',
            '仕入' => 'shiire',
            '仕入高' => 'shiiredaka',
            '立替' => 'tatekae',
            '前払' => 'maebarai',
            '前受' => 'maeuke',
            '短期' => 'tanki',
            '長期' => 'chouki',
            '振替' => 'furikae',
            '貸倒' => 'kashidaore',
            '棚卸' => 'tanaoroshi',
            '棚卸資産' => 'tanaoroshishisan',
            '引当' => 'hikiate',
            '引当金' => 'hikiatekin',
            '為替' => 'kawase',
            '借入' => 'kariire',
            '借入金' => 'kariirekin',
            '借方' => 'karikata',
            '貸方' => 'kashikata',
            '役員' => 'yakuin',
            '従業員' => 'juugyouin',
            '退職' => 'taishoku',
            '退職金' => 'taishokukin',
            '家賃' => 'yachin',
            '敷金' => 'shikikin',
            '賃借' => 'chinshaku',
            '賃借料' => 'chinshakuryou',
            '燃料' => 'nenryou',
            '諸経費' => 'shokeihi',
            '諸口' => 'shoguchi',
            '仮払' => 'karibarai',
            '仮受' => 'kariuke',
            '当座' => 'touza',
            '預金' => 'yokin',
            '当座預金' => 'touzayokin',
            '普通預金' => 'futsuuyokin',
            '定期預金' => 'teikiyokin',
            '小口' => 'koguchi',
            '小口現金' => 'koguchigenkin',
            '勘定' => 'kanjou',
            '科目' => 'kamoku',
            '勘定科目' => 'kanjoukamoku',
            '減価償却' => 'genkashoukyaku',
            '減価' => 'genka',
            '人件費' => 'jinkenhi',
            '通勤' => 'tsuukin',
            '事業' => 'jigyou',
            '法人税' => 'houjinzei',
            '消費税' => 'shouhizei',
            '所得税' => 'shotokuzei',
            '不動産' => 'fudousan',
        ];

        return $cached;
    }

    /**
     * Hepburn-style romanization for hiragana/katakana. Covers everything
     * that's likely to appear in account titles. Yo'on ("ya/yu/yo")
     * combinations are not handled separately — the small-kana mapping
     * collapses ゃ → "ya" etc., which is good enough for substring search.
     *
     * @return array<string, string>
     */
    private static function kanaReadings(): array
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $base = [
            // gojuuon
            'あ' => 'a', 'い' => 'i', 'う' => 'u', 'え' => 'e', 'お' => 'o',
            'か' => 'ka', 'き' => 'ki', 'く' => 'ku', 'け' => 'ke', 'こ' => 'ko',
            'さ' => 'sa', 'し' => 'shi', 'す' => 'su', 'せ' => 'se', 'そ' => 'so',
            'た' => 'ta', 'ち' => 'chi', 'つ' => 'tsu', 'て' => 'te', 'と' => 'to',
            'な' => 'na', 'に' => 'ni', 'ぬ' => 'nu', 'ね' => 'ne', 'の' => 'no',
            'は' => 'ha', 'ひ' => 'hi', 'ふ' => 'fu', 'へ' => 'he', 'ほ' => 'ho',
            'ま' => 'ma', 'み' => 'mi', 'む' => 'mu', 'め' => 'me', 'も' => 'mo',
            'や' => 'ya', 'ゆ' => 'yu', 'よ' => 'yo',
            'ら' => 'ra', 'り' => 'ri', 'る' => 'ru', 'れ' => 're', 'ろ' => 'ro',
            'わ' => 'wa', 'ゐ' => 'i', 'ゑ' => 'e', 'を' => 'wo', 'ん' => 'n',
            // dakuten
            'が' => 'ga', 'ぎ' => 'gi', 'ぐ' => 'gu', 'げ' => 'ge', 'ご' => 'go',
            'ざ' => 'za', 'じ' => 'ji', 'ず' => 'zu', 'ぜ' => 'ze', 'ぞ' => 'zo',
            'だ' => 'da', 'ぢ' => 'ji', 'づ' => 'zu', 'で' => 'de', 'ど' => 'do',
            'ば' => 'ba', 'び' => 'bi', 'ぶ' => 'bu', 'べ' => 'be', 'ぼ' => 'bo',
            // handakuten
            'ぱ' => 'pa', 'ぴ' => 'pi', 'ぷ' => 'pu', 'ぺ' => 'pe', 'ぽ' => 'po',
            // sokuon / chouon — collapse to nothing (sokuon would be context-dependent)
            'っ' => '',
            // small kana → assume base reading; substring search is forgiving.
            'ゃ' => 'ya', 'ゅ' => 'yu', 'ょ' => 'yo',
            'ぁ' => 'a', 'ぃ' => 'i', 'ぅ' => 'u', 'ぇ' => 'e', 'ぉ' => 'o',
        ];

        $map = [];
        foreach ($base as $hira => $rom) {
            $map[$hira] = $rom;
            // Auto-derive katakana counterpart by codepoint shift (+0x60).
            $cp = mb_ord($hira, 'UTF-8');
            if ($cp !== false) {
                $kata = mb_chr($cp + 0x60, 'UTF-8');
                if ($kata !== false) {
                    $map[$kata] = $rom;
                }
            }
        }
        // Extra katakana not derived above
        $map['ヴ'] = 'vu';
        $map['ー'] = ''; // chouon: drop, substring search doesn't need it.
        $cached = $map;

        return $cached;
    }
}
