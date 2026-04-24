<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Multi;

use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodSectionRow;
use Rucaro\Support\Decimal\Decimal;

/**
 * Flatten an aggregated {@see MultiPeriodFinancialStatement} into per-section
 * comparison rows ({@see MultiPeriodSectionRow}).
 *
 * For each section code that appears in any period, we emit one row whose
 * `amounts` map is keyed by fiscalTermId → subtotal; missing periods are
 * reported as `0.0000`. Variance is `latest - previous` at scale 4;
 * `variancePercent` is the relative change in basis points of 100 with two
 * decimal places (so "+12.50" means +12.5%) — `null` when either the previous
 * period is missing or its subtotal is exactly zero.
 *
 * Stateless; expose static helpers so dompdf generator / JSON serializer /
 * Smarty templates can call in without extra wiring.
 */
final class MultiPeriodRowBuilder
{
    /**
     * Build comparison rows for the BS sections.
     *
     * @return list<MultiPeriodSectionRow>
     */
    public static function buildBs(MultiPeriodFinancialStatement $multi): array
    {
        return self::buildFor($multi, 'bs');
    }

    /**
     * Build comparison rows for the PL sections.
     *
     * @return list<MultiPeriodSectionRow>
     */
    public static function buildPl(MultiPeriodFinancialStatement $multi): array
    {
        return self::buildFor($multi, 'pl');
    }

    /**
     * Build comparison rows for the CS sections.
     *
     * @return list<MultiPeriodSectionRow>
     */
    public static function buildCs(MultiPeriodFinancialStatement $multi): array
    {
        return self::buildFor($multi, 'cs');
    }

    /**
     * @return list<MultiPeriodSectionRow>
     */
    private static function buildFor(MultiPeriodFinancialStatement $multi, string $which): array
    {
        /** @var array<string, array{code:string,label:string,sortOrder:int,isSubtotal:bool,isTotal:bool}> $codes */
        $codes = [];
        foreach ($multi->periods as $entry) {
            $sections = self::sectionsOf($entry, $which);
            foreach ($sections as $code => $section) {
                if (!isset($codes[$code])) {
                    $codes[$code] = [
                        'code'       => $section->code,
                        'label'      => $section->label,
                        'sortOrder'  => $section->sortOrder,
                        'isSubtotal' => $section->isSubtotal,
                        'isTotal'    => $section->isTotal,
                    ];
                }
            }
        }

        // Sort stable by sortOrder, then code so output is deterministic.
        $values = array_values($codes);
        usort($values, static function (array $a, array $b): int {
            if ($a['sortOrder'] !== $b['sortOrder']) {
                return $a['sortOrder'] <=> $b['sortOrder'];
            }
            return strcmp($a['code'], $b['code']);
        });

        $latest = $multi->latestPeriod();
        $previous = $multi->previousPeriod();

        $rows = [];
        foreach ($values as $info) {
            /** @var array<string, string> $amounts */
            $amounts = [];
            foreach ($multi->periods as $entry) {
                $sections = self::sectionsOf($entry, $which);
                $amounts[$entry->fiscalTermId] = isset($sections[$info['code']])
                    ? $sections[$info['code']]->subtotal
                    : '0.0000';
            }
            [$variance, $variancePercent] = self::variance(
                $amounts[$latest->fiscalTermId] ?? '0.0000',
                $previous === null
                    ? null
                    : ($amounts[$previous->fiscalTermId] ?? '0.0000'),
            );
            $rows[] = new MultiPeriodSectionRow(
                sectionCode: $info['code'],
                lineCode: $info['code'],
                label: $info['label'],
                amounts: $amounts,
                variance: $variance,
                variancePercent: $variancePercent,
                depth: 0,
                isSubtotal: $info['isSubtotal'],
                isTotal: $info['isTotal'],
            );
        }
        return $rows;
    }

    /**
     * @return array<string, \Rucaro\Domain\FinancialStatement\Section>
     */
    private static function sectionsOf(
        \Rucaro\Domain\FinancialStatement\Multi\MultiPeriodEntry $entry,
        string $which,
    ): array {
        $fs = $entry->statement;
        return match ($which) {
            'bs'    => $fs->bs,
            'pl'    => $fs->pl,
            'cs'    => $fs->cs,
            default => [],
        };
    }

    /**
     * Returns `[variance, variancePercent]` as a scale-4 decimal string pair,
     * or `[null, null]` when no previous period is available.
     *
     * @return array{0: string|null, 1: string|null}
     */
    public static function variance(string $latest, ?string $previous): array
    {
        if ($previous === null) {
            return [null, null];
        }
        // variance = latest - previous (use add with negated right-hand side).
        $negPrev = self::negate($previous);
        $variance = Decimal::normalize(Decimal::add($latest, $negPrev));

        // Percent: undefined when denominator is zero.
        if (Decimal::compare($previous, '0.0000') === 0) {
            return [$variance, null];
        }
        // (variance / previous) * 100 — compute in integer basis points to
        // avoid taking a hard dependency on bcmath division.
        $variBp = self::toInt4($variance);
        $prevBp = self::toInt4($previous);
        // Guard: toInt4 guaranteed non-zero for previous since compare != 0.
        $signedPct = (int) round(($variBp / $prevBp) * 1_000_000);
        $variancePercent = Decimal::normalize(self::fromInt4($signedPct));
        return [$variance, $variancePercent];
    }

    private static function negate(string $v): string
    {
        if ($v === '' || $v === '0' || $v === '0.0000') {
            return '0.0000';
        }
        if (str_starts_with($v, '-')) {
            return substr($v, 1);
        }
        return '-' . $v;
    }

    private static function toInt4(string $v): int
    {
        $normalized = Decimal::normalize($v);
        $negative = str_starts_with($normalized, '-');
        $abs = ltrim($normalized, '-');
        $dot = strpos($abs, '.');
        if ($dot === false) {
            $combined = $abs;
        } else {
            $combined = substr($abs, 0, $dot) . substr($abs, $dot + 1);
        }
        $n = (int) $combined;
        return $negative ? -$n : $n;
    }

    private static function fromInt4(int $n): string
    {
        $negative = $n < 0;
        $abs = (string) ($negative ? -$n : $n);
        $abs = str_pad($abs, 5, '0', STR_PAD_LEFT);
        $intPart = substr($abs, 0, strlen($abs) - 4);
        $fracPart = substr($abs, strlen($abs) - 4);
        $result = $intPart . '.' . $fracPart;
        return $negative ? '-' . $result : $result;
    }
}
