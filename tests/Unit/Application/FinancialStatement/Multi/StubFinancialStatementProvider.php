<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Multi;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\FinancialStatement\Multi\FinancialStatementProviderInterface;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Section;

/**
 * Deterministic stub that returns a pre-seeded {@see FinancialStatement} per
 * `fiscalTermId` — sufficient for exercising the multi-period use case
 * without wiring a full trial-balance fixture per period.
 *
 * Each seeded BS/PL/CS section is a single line with the configured subtotal;
 * that keeps row comparison assertions easy to read.
 */
final class StubFinancialStatementProvider implements FinancialStatementProviderInterface
{
    /**
     * @var array<string, array{
     *   bs: array<string, string>,
     *   pl: array<string, string>,
     *   cs: array<string, string>,
     *   totals: array<string, string>,
     * }>
     */
    private array $byTerm = [];

    /**
     * @param array<string, string> $bs
     * @param array<string, string> $pl
     * @param array<string, string> $cs
     * @param array<string, string> $totals
     */
    public function seed(
        string $fiscalTermId,
        array $bs = [],
        array $pl = [],
        array $cs = [],
        array $totals = [],
    ): void {
        $this->byTerm[$fiscalTermId] = [
            'bs'     => $bs,
            'pl'     => $pl,
            'cs'     => $cs,
            'totals' => $totals,
        ];
    }

    public function provide(GenerateFinancialStatementUseCaseInput $input): FinancialStatement
    {
        $data = $this->byTerm[$input->fiscalTermId] ?? [
            'bs' => [], 'pl' => [], 'cs' => [], 'totals' => [],
        ];

        return new FinancialStatement(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            kind: $input->kind,
            fromDate: $input->fromDate,
            toDate: $input->asOf,
            currencyCode: $input->currencyCode,
            bs: self::sections($data['bs']),
            pl: self::sections($data['pl']),
            cs: self::sections($data['cs']),
            totals: $data['totals'],
            generatedAt: new DateTimeImmutable('2026-04-21T00:00:00Z', new DateTimeZone('UTC')),
        );
    }

    /**
     * @param array<string, string> $map
     * @return array<string, Section>
     */
    private static function sections(array $map): array
    {
        $out = [];
        $i = 0;
        foreach ($map as $code => $subtotal) {
            $out[$code] = new Section(
                code: $code,
                label: $code,
                lines: [
                    FinancialStatementLine::subtotal($code, $subtotal),
                ],
                subtotal: $subtotal,
                parentCode: null,
                sortOrder: ++$i * 10,
                isSubtotal: false,
                isTotal: false,
            );
        }
        return $out;
    }
}
