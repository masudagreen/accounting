<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Web;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Support\Web\NavViewModel;

#[CoversClass(NavViewModel::class)]
final class NavViewModelTest extends TestCase
{
    public function testBuildPayloadCopiesArgumentsThroughAndDefaultsToEmptyString(): void
    {
        $entities = [
            ['id' => 'E1', 'name' => '株式会社サンプル'],
        ];
        $terms = [
            ['id' => 'FT1', 'fiscalPeriod' => 20, 'startDate' => '2025-07-01', 'endDate' => '2026-06-30'],
        ];

        $payload = NavViewModel::buildPayload($entities, $terms, 'E1', 'FT1');

        self::assertSame($entities, $payload['entities']);
        self::assertSame($terms, $payload['nav_fiscal_terms']);
        self::assertSame('E1', $payload['selected_entity_id']);
        self::assertSame('FT1', $payload['selected_fiscal_term_id']);
    }

    public function testBuildPayloadCoercesNullSelectionsToEmptyString(): void
    {
        $payload = NavViewModel::buildPayload([], [], null, null);

        self::assertSame([], $payload['entities']);
        self::assertSame([], $payload['nav_fiscal_terms']);
        self::assertSame('', $payload['selected_entity_id']);
        self::assertSame('', $payload['selected_fiscal_term_id']);
    }

    public function testFiscalTermOptionLabelFormats(): void
    {
        $label = NavViewModel::fiscalTermOptionLabel([
            'fiscalPeriod' => 20,
            'startDate' => '2025-07-01',
            'endDate' => '2026-06-30',
        ]);

        self::assertSame('第 20 期 (2025/07/01 〜 2026/06/30)', $label);
    }

    public function testFiscalTermOptionLabelTolerantOfMissingDates(): void
    {
        $label = NavViewModel::fiscalTermOptionLabel([
            'fiscalPeriod' => 1,
            'startDate' => '',
            'endDate' => '',
        ]);

        self::assertSame('第 1 期 ( 〜 )', $label);
    }
}
