<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Http\Controller\Ui\Journal;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Http\Controller\Ui\Journal\JournalUiContext;
use Rucaro\Tests\Support\Fake\InMemoryAccountTitleConsumptionTaxDefaultRepository;

/**
 * Targets the pure static defaulting logic in {@see JournalUiContext}. The
 * PDO-backed lookups are exercised indirectly by the integration tests
 * against the running container.
 */
#[CoversClass(JournalUiContext::class)]
final class JournalUiContextTest extends TestCase
{
    public function testDefaultFiscalTermIdReturnsNullWhenNoTerms(): void
    {
        $now = new \DateTimeImmutable('2025-06-01', new \DateTimeZone('UTC'));

        self::assertNull(JournalUiContext::defaultFiscalTermId([], $now));
    }

    public function testDefaultFiscalTermIdPrefersTermContainingNow(): void
    {
        $now = new \DateTimeImmutable('2025-06-01', new \DateTimeZone('UTC'));
        $terms = [
            ['id' => 'FT1', 'fiscalPeriod' => 1, 'startDate' => '2024-01-01', 'endDate' => '2024-12-31'],
            ['id' => 'FT2', 'fiscalPeriod' => 2, 'startDate' => '2025-01-01', 'endDate' => '2025-12-31'],
        ];

        self::assertSame('FT2', JournalUiContext::defaultFiscalTermId($terms, $now));
    }

    public function testDefaultFiscalTermIdFallsBackToFirstTermWhenNoneMatch(): void
    {
        $now = new \DateTimeImmutable('2030-06-01', new \DateTimeZone('UTC'));
        $terms = [
            ['id' => 'FT_LATEST', 'fiscalPeriod' => 3, 'startDate' => '2026-01-01', 'endDate' => '2026-12-31'],
            ['id' => 'FT_OLD',    'fiscalPeriod' => 2, 'startDate' => '2025-01-01', 'endDate' => '2025-12-31'],
        ];

        self::assertSame('FT_LATEST', JournalUiContext::defaultFiscalTermId($terms, $now));
    }

    /**
     * F-1: ``.0000`` padding must be gone. Amounts are presented as integers
     * with thousands separators so the operator can read them at a glance.
     */
    public function testFormatAmountReturnsThousandsSeparatedInteger(): void
    {
        self::assertSame('0', JournalUiContext::formatAmount(null));
        self::assertSame('0', JournalUiContext::formatAmount(''));
        self::assertSame('0', JournalUiContext::formatAmount('0'));
        self::assertSame('0', JournalUiContext::formatAmount('0.0000'));
        self::assertSame('100', JournalUiContext::formatAmount('100'));
        self::assertSame('100', JournalUiContext::formatAmount('100.0000'));
        self::assertSame('1,000', JournalUiContext::formatAmount('1000'));
        self::assertSame('1,000', JournalUiContext::formatAmount('1000.0000'));
        self::assertSame('1,234,567', JournalUiContext::formatAmount('1234567.0000'));
        // ints / floats accepted too
        self::assertSame('500', JournalUiContext::formatAmount(500));
        self::assertSame('500', JournalUiContext::formatAmount(500.0));
        // negative amounts use a parenthesised form, matching the report
        // formatters (LedgerViewController::formatAmount).
        self::assertSame('(100)', JournalUiContext::formatAmount('-100'));
        self::assertSame('(1,234)', JournalUiContext::formatAmount('-1234.0000'));
        // non-numeric junk degrades to "0" rather than throwing
        self::assertSame('0', JournalUiContext::formatAmount('abc'));
    }

    /**
     * F-1: tax rates render as integers when whole, otherwise drop trailing
     * zeros. The hidden inputs that drive the JS computation still need to
     * round-trip these values, so the formatter must be JS-parseFloat-safe.
     */
    public function testFormatTaxRatePercentDropsTrailingZeros(): void
    {
        self::assertSame('0', JournalUiContext::formatTaxRatePercent(null));
        self::assertSame('0', JournalUiContext::formatTaxRatePercent('0'));
        self::assertSame('0', JournalUiContext::formatTaxRatePercent('0.00'));
        self::assertSame('10', JournalUiContext::formatTaxRatePercent('10'));
        self::assertSame('10', JournalUiContext::formatTaxRatePercent('10.00'));
        self::assertSame('8', JournalUiContext::formatTaxRatePercent('8.00'));
        self::assertSame('8.5', JournalUiContext::formatTaxRatePercent('8.50'));
        self::assertSame('8.5', JournalUiContext::formatTaxRatePercent('8.5'));
        self::assertSame('1.25', JournalUiContext::formatTaxRatePercent('1.25'));
    }

    /**
     * F-4: every account title in the form payload carries a `romaji`
     * field so the JS combobox can substring-match against romaji input.
     * Empty strings are acceptable for unknown kanji — the JS just
     * silently ignores empty alias on the substring filter.
     */
    public function testAccountTitlesForEntityAttachesRomajiAlias(): void
    {
        $now = new \DateTimeImmutable('2026-05-01', new \DateTimeZone('UTC'));
        $repo = new class($now) implements AccountTitleRepositoryInterface {
            public function __construct(private \DateTimeImmutable $now)
            {
            }

            #[\Override]
            public function listByEntity(string $entityId, int $page, int $pageSize, ?string $category = null, ?bool $isActive = null, ?string $search = null): array
            {
                return [
                    new AccountTitle(
                        id: 'A1', entityId: $entityId, code: 'L0010', name: '通信費',
                        category: 'expense', normalSide: 'debit',
                        parentId: null, sortOrder: 0, isActive: true,
                        createdAt: $this->now, updatedAt: $this->now,
                    ),
                    new AccountTitle(
                        id: 'A2', entityId: $entityId, code: 'L0011', name: '消耗品費',
                        category: 'expense', normalSide: 'debit',
                        parentId: null, sortOrder: 0, isActive: true,
                        createdAt: $this->now, updatedAt: $this->now,
                    ),
                ];
            }

            #[\Override]
            public function countByEntity(string $entityId, ?string $category = null, ?bool $isActive = null, ?string $search = null): int
            {
                return 2;
            }

            #[\Override]
            public function findById(string $id): ?AccountTitle
            {
                return null;
            }

            #[\Override]
            public function findAllByEntity(string $entityId): array
            {
                return [];
            }

            #[\Override]
            public function save(AccountTitle $title): void
            {
            }

            #[\Override]
            public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
            {
            }

            #[\Override]
            public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool
            {
                return false;
            }
        };

        $ctx = new JournalUiContext(
            new ListAccountTitlesUseCase($repo),
            new \PDO('sqlite::memory:'),
        );

        $rows = $ctx->accountTitlesForEntity('E1');

        self::assertCount(2, $rows);
        self::assertArrayHasKey('romaji', $rows[0]);
        // Long-vowel collapse turns "tsuushinhi" → "tsushinhi" and
        // "shoumouhinhi" → "shomohinhi". JS-side normaliseRomaji applies
        // the same collapse to user input so both spellings still match.
        self::assertSame('tsushinhi', $rows[0]['romaji']);
        self::assertSame('shomohinhi', $rows[1]['romaji']);
    }

    /**
     * F-4: the form ships per-account tax defaults so the JS combobox can
     * auto-set the tax-rate selector when an account is picked. Map the
     * persistence-shape (category enum + rate code) into the
     * `{rate_percent, kind}` pairs the JS recompute consumes.
     */
    public function testConsumptionTaxDefaultsForEntityMapsTaxableAndExempt(): void
    {
        $now = new \DateTimeImmutable('2026-05-01', new \DateTimeZone('UTC'));
        $defaults = new InMemoryAccountTitleConsumptionTaxDefaultRepository();
        $defaults->save(new AccountTitleConsumptionTaxDefault(
            id: 'D1', entityId: 'E1', accountTitleId: 'A_SUPPLIES',
            defaultCategoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
            defaultRateCode: 'standard',
            createdAt: $now, updatedAt: $now,
        ));
        $defaults->save(new AccountTitleConsumptionTaxDefault(
            id: 'D2', entityId: 'E1', accountTitleId: 'A_FOOD',
            defaultCategoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
            defaultRateCode: 'reduced_8',
            createdAt: $now, updatedAt: $now,
        ));
        $defaults->save(new AccountTitleConsumptionTaxDefault(
            id: 'D3', entityId: 'E1', accountTitleId: 'A_SALES_NONTAX',
            defaultCategoryCode: ConsumptionTaxCategoryCode::NonTaxableSales,
            defaultRateCode: null,
            createdAt: $now, updatedAt: $now,
        ));
        $defaults->save(new AccountTitleConsumptionTaxDefault(
            id: 'D4', entityId: 'E1', accountTitleId: 'A_EXEMPT',
            defaultCategoryCode: ConsumptionTaxCategoryCode::ExemptSales,
            defaultRateCode: null,
            createdAt: $now, updatedAt: $now,
        ));

        $repo = new class implements AccountTitleRepositoryInterface {
            #[\Override]
            public function listByEntity(string $entityId, int $page, int $pageSize, ?string $category = null, ?bool $isActive = null, ?string $search = null): array
            {
                return [];
            }

            #[\Override]
            public function countByEntity(string $entityId, ?string $category = null, ?bool $isActive = null, ?string $search = null): int
            {
                return 0;
            }

            #[\Override]
            public function findById(string $id): ?AccountTitle
            {
                return null;
            }

            #[\Override]
            public function findAllByEntity(string $entityId): array
            {
                return [];
            }

            #[\Override]
            public function save(AccountTitle $title): void
            {
            }

            #[\Override]
            public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
            {
            }

            #[\Override]
            public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool
            {
                return false;
            }
        };

        $ctx = new JournalUiContext(
            new ListAccountTitlesUseCase($repo),
            new \PDO('sqlite::memory:'),
            null,
            $defaults,
        );

        $map = $ctx->consumptionTaxDefaultsForEntity('E1');

        self::assertSame(['rate_percent' => '10.00', 'kind' => 'standard'], $map['A_SUPPLIES']);
        self::assertSame(['rate_percent' => '8.00',  'kind' => 'reduced'], $map['A_FOOD']);
        self::assertSame(['rate_percent' => '0.00',  'kind' => 'nontax'], $map['A_SALES_NONTAX']);
        self::assertSame(['rate_percent' => '0.00',  'kind' => 'exempt'], $map['A_EXEMPT']);
    }

    public function testConsumptionTaxDefaultsForEntityIsEmptyWhenRepoMissing(): void
    {
        $repo = new class implements AccountTitleRepositoryInterface {
            #[\Override]
            public function listByEntity(string $entityId, int $page, int $pageSize, ?string $category = null, ?bool $isActive = null, ?string $search = null): array
            {
                return [];
            }

            #[\Override]
            public function countByEntity(string $entityId, ?string $category = null, ?bool $isActive = null, ?string $search = null): int
            {
                return 0;
            }

            #[\Override]
            public function findById(string $id): ?AccountTitle
            {
                return null;
            }

            #[\Override]
            public function findAllByEntity(string $entityId): array
            {
                return [];
            }

            #[\Override]
            public function save(AccountTitle $title): void
            {
            }

            #[\Override]
            public function softDelete(string $id, \DateTimeImmutable $deletedAt): void
            {
            }

            #[\Override]
            public function existsByCode(string $entityId, string $code, ?string $excludeId = null): bool
            {
                return false;
            }
        };
        $ctx = new JournalUiContext(
            new ListAccountTitlesUseCase($repo),
            new \PDO('sqlite::memory:'),
        );

        self::assertSame([], $ctx->consumptionTaxDefaultsForEntity('E1'));
    }
}
