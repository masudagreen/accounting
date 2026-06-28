<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ui\Journal;

use PDO;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefaultRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\SubAccountTitle\SubAccountTitleRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Search\RomajiAlias;

/**
 * Shared look-ups the Journal UI needs to populate select boxes: account
 * titles, fiscal terms, and a derived list of "year" choices for the
 * list-page filter.
 *
 * The class exists so each Journal UI controller can ask for exactly the
 * reference data it needs without reaching into PDO directly, and so the
 * fiscal_terms lookup stays in one place until we grow a proper
 * `FiscalTerm` domain module.
 */
final readonly class JournalUiContext
{
    public function __construct(
        private ListAccountTitlesUseCase $listAccountTitles,
        private \PDO $pdo,
        private ?SubAccountTitleRepositoryInterface $subAccountTitles = null,
        private ?AccountTitleConsumptionTaxDefaultRepositoryInterface $taxDefaults = null,
    ) {
    }

    /**
     * F-4: each title carries a `romaji` field (lowercase Hepburn alias)
     * so the JS combobox can do substring matching against romaji input
     * like "shoumou" → 消耗品費 in addition to code/name.
     *
     * @return list<array{id: string, code: string, name: string, romaji: string, category: string, normalSide: string}>
     */
    public function accountTitlesForEntity(string $entityId): array
    {
        $out = $this->listAccountTitles->execute(new ListAccountTitlesUseCaseInput(
            entityId: $entityId,
            page: 1,
            pageSize: 500,
            category: null,
            isActive: true,
            search: null,
        ));

        return array_map(
            static function (AccountTitle $a): array {
                // F-4 fix: ship the bare 「現金」 instead of 「現金 (cash)」.
                // The legacy English alias was a traceability hint from the
                // importer; the operator just wants the Japanese name. The
                // romaji is generated from the same trimmed value so the
                // alias mirror stays consistent.
                $displayName = self::stripAccountAliasSuffix($a->name);

                return [
                    'id' => $a->id,
                    'code' => $a->code,
                    'name' => $displayName,
                    'romaji' => RomajiAlias::for($displayName),
                    'category' => $a->category,
                    'normalSide' => $a->normalSide,
                ];
            },
            $out->items,
        );
    }

    /**
     * Strip the parenthesised English alias suffix the importer kept for
     * legacy traceability — "現金 (cash)" → "現金". Names without parens
     * are returned as-is.
     */
    public static function stripAccountAliasSuffix(string $name): string
    {
        $trimmed = preg_replace('/\s*\([^()]*\)\s*$/u', '', $name);

        return is_string($trimmed) && $trimmed !== '' ? $trimmed : $name;
    }

    /**
     * F-4: per-account tax-category defaults keyed by account_title_id so the
     * JS combobox can auto-set the tax-rate selector when an account is
     * picked. Missing mappings degrade to "exempt" (rate=0, kind=exempt) at
     * the JS layer — we only ship the explicit ones here.
     *
     * @return array<string, array{rate_percent: string, kind: string}>
     */
    public function consumptionTaxDefaultsForEntity(string $entityId): array
    {
        if ($this->taxDefaults === null) {
            return [];
        }
        try {
            $rows = $this->taxDefaults->findByEntity($entityId);
        } catch (\Throwable) {
            return [];
        }
        $out = [];
        foreach ($rows as $row) {
            $out[$row->accountTitleId] = self::mapTaxDefault($row->defaultCategoryCode, $row->defaultRateCode);
        }

        return $out;
    }

    /**
     * Translate the persistence-shape (category enum + rate code string)
     * into the form-level `{rate_percent, kind}` tuple the JS recompute
     * understands. Categories that aren't taxable always collapse to
     * `0|exempt`; taxable ones look at the rate code suffix to pick
     * standard 10% vs reduced 8%.
     *
     * @return array{rate_percent: string, kind: string}
     */
    private static function mapTaxDefault(ConsumptionTaxCategoryCode $category, ?string $rateCode): array
    {
        if (!$category->isTaxable()) {
            return match ($category) {
                ConsumptionTaxCategoryCode::NonTaxableSales,
                ConsumptionTaxCategoryCode::NonTaxablePurchase => ['rate_percent' => '0.00', 'kind' => 'nontax'],
                default => ['rate_percent' => '0.00', 'kind' => 'exempt'],
            };
        }
        $code = (string) $rateCode;
        // Rate codes follow the master-imported convention. The reduced 軽減
        // rate is encoded with "reduced" / "8" / "08"; everything else falls
        // back to standard 10%. The tax-config import only ships these two
        // active rates today, so a permissive substring match is fine.
        $lower = strtolower($code);
        if (str_contains($lower, 'reduced') || str_contains($lower, '_8') || $lower === '8' || $lower === '08' || str_contains($lower, 'r8')) {
            return ['rate_percent' => '8.00', 'kind' => 'reduced'];
        }

        return ['rate_percent' => '10.00', 'kind' => 'standard'];
    }

    /**
     * Active entities the given user owns, name-sorted, in the navbar shape
     * `{id, name}`. Lets every Journal UI page render the entity selector
     * (previously only Dashboard did, so the navbar collapsed to a hidden
     * input on Journal pages and the operator couldn't switch).
     *
     * @return list<array{id: string, name: string}>
     */
    public function entitiesForUser(string $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name
               FROM entities
              WHERE owner_user_id = :u
                AND is_active = 1
                AND deleted_at IS NULL
              ORDER BY name',
        );
        $stmt->execute([':u' => UlidGenerator::decode($userId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $idRaw = $r['id'] ?? null;
            if (!is_string($idRaw) || $idRaw === '') {
                continue;
            }
            $out[] = [
                'id' => strlen($idRaw) === 16 ? UlidGenerator::encode($idRaw) : $idRaw,
                'name' => (string) ($r['name'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * Load fiscal terms for an entity, newest period first.
     *
     * @return list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}>
     */
    public function fiscalTermsForEntity(string $entityId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, fiscal_period, start_date, end_date
               FROM fiscal_terms
              WHERE entity_id = :entity
              ORDER BY fiscal_period DESC',
        );
        $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $idRaw = $r['id'] ?? null;
            if (!is_string($idRaw) || $idRaw === '') {
                continue;
            }
            $out[] = [
                'id' => strlen($idRaw) === 16 ? UlidGenerator::encode($idRaw) : $idRaw,
                'fiscalPeriod' => (int) ($r['fiscal_period'] ?? 0),
                'startDate' => (string) ($r['start_date'] ?? ''),
                'endDate' => (string) ($r['end_date'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * Resolve a fiscal term id to its { start_date, end_date } bounds. Used
     * by controllers that need to default `journalDate` into a sensible
     * window for the operator.
     *
     * @return array{id: string, startDate: string, endDate: string}|null
     */
    public function findFiscalTerm(string $fiscalTermId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, start_date, end_date
               FROM fiscal_terms
              WHERE id = :id
              LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        $idRaw = $row['id'] ?? '';
        if (!is_string($idRaw) || $idRaw === '') {
            return null;
        }

        return [
            'id' => strlen($idRaw) === 16 ? UlidGenerator::encode($idRaw) : $idRaw,
            'startDate' => (string) ($row['start_date'] ?? ''),
            'endDate' => (string) ($row['end_date'] ?? ''),
        ];
    }

    /**
     * Sub-account titles grouped by their parent account_title_id, for the
     * Journal new/edit form's cascading dropdown. Returns a flat map so the
     * template can embed it once as JSON and the JS can filter on demand.
     *
     * Empty / missing repository wiring degrades gracefully to an empty map
     * — callers don't need to special-case the "no sub-accounts configured"
     * scenario.
     *
     * @return array<string, list<array{id: string, code: string, name: string}>>
     */
    public function subAccountTitlesGroupedByAccountForEntity(string $entityId): array
    {
        if ($this->subAccountTitles === null) {
            return [];
        }
        $rows = $this->subAccountTitles->listByEntity($entityId);

        /** @var array<string, list<array{id: string, code: string, name: string}>> $out */
        $out = [];
        foreach ($rows as $sub) {
            if (!$sub->isActive) {
                continue;
            }
            $parent = $sub->accountTitleId;
            $out[$parent] ??= [];
            $out[$parent][] = [
                'id' => $sub->id,
                'code' => $sub->code,
                'name' => $sub->name,
            ];
        }

        return $out;
    }

    /**
     * Display an amount as a thousands-separated JPY integer (no fractional
     * tail). The renewal app deals in JPY exclusively, so the legacy
     * `1000.0000` storage shape would only ever clutter the screen. Negative
     * values are wrapped in parentheses to match the report formatters
     * (see {@see \Rucaro\Http\Controller\Ui\Report\LedgerViewController::formatAmount()}).
     *
     * Falsy / non-numeric inputs degrade to "0" so template authors don't
     * need to special-case missing tax_amount columns.
     *
     * Note: this is the **display** formatter. The persistence layer keeps
     * its DECIMAL(18,4) shape; controllers should call this only when
     * preparing payloads for the form / list / show templates and the
     * `recent_journals` JSON.
     *
     * @psalm-param 500|float|null|string $value
     */
    public static function formatAmount(int|float|string|null $value): string
    {
        if ($value === null) {
            return '0';
        }
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return '0';
        }
        $str = trim((string) $value);
        if ($str === '' || !is_numeric($str)) {
            return '0';
        }
        $num = (float) $str;
        if ($num === 0.0) {
            return '0';
        }
        $rounded = (int) round(abs($num));
        $formatted = number_format($rounded, 0, '.', ',');

        return $num < 0 ? '('.$formatted.')' : $formatted;
    }

    /**
     * Display a tax-rate percent in its shortest accurate form. `'10.00'` is
     * shown as `'10'`, `'8.50'` as `'8.5'`, while `'1.25'` survives intact.
     * Drives the hidden inputs on the journal form (the JS recompute reads
     * them via `parseFloat`, which is happy with either form), and the
     * `recent_journals` JSON payload.
     */
    public static function formatTaxRatePercent(?string $value): string
    {
        if ($value === null) {
            return '0';
        }
        $str = trim($value);
        if ($str === '' || !is_numeric($str)) {
            return '0';
        }
        $num = (float) $str;
        if ($num === floor($num)) {
            return (string) (int) $num;
        }
        // Trim trailing zeros and a dangling decimal point for fractional
        // rates ("8.50" → "8.5", "8.500" → "8.5").
        $trimmed = rtrim(rtrim($str, '0'), '.');

        return $trimmed === '' ? '0' : $trimmed;
    }

    /**
     * Pick the "active" fiscal term to use when the session has none set:
     * prefer the term that contains today's date, otherwise fall back to the
     * most recent term.
     *
     * @param list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}> $terms
     */
    public static function defaultFiscalTermId(array $terms, \DateTimeImmutable $now): ?string
    {
        if ($terms === []) {
            return null;
        }
        $today = $now->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d');
        foreach ($terms as $t) {
            if ($t['startDate'] !== '' && $t['endDate'] !== ''
                && $today >= $t['startDate'] && $today <= $t['endDate']
            ) {
                return $t['id'];
            }
        }

        return $terms[0]['id'];
    }
}
