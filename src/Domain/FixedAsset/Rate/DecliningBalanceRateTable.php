<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Rate;

/**
 * Static lookup table of Japanese declining-balance depreciation rates.
 *
 * Extracted from the legacy CSVs under
 * `back/tpl/templates/else/plugin/accounting/dat/`:
 *
 *   - depDecliningNew200.csv  => post-2012-04-01 (200% method)
 *   - depDecliningNew250.csv  => post-2007-04-01 (250% method) — same table
 *                                is reused by the 2016 reform for machinery
 *                                where the declining rule still applies.
 *
 * Each row carries three figures:
 *   - rate           : the per-period declining rate (償却率)
 *   - assuredRate    : the guarantee ratio (保証率)
 *   - updateRate     : the fallback "改定償却率" used once the computed
 *                      depreciation falls below the guarantee threshold —
 *                      at that point switch to straight-line on the remaining
 *                      book value.
 *
 * For old-regime assets (< 2007-04-01) see {@see OldDecliningBalanceRateTable}.
 */
final class DecliningBalanceRateTable
{
    /**
     * 200% declining-balance rates (post 2012-04-01).
     *
     * @var array<int, array{rate: float, updateRate: float, assuredRate: float}>
     */
    private const TABLE_200 = [
        2  => ['rate' => 1.000, 'updateRate' => 0.000, 'assuredRate' => 0.00000],
        3  => ['rate' => 0.667, 'updateRate' => 1.000, 'assuredRate' => 0.11089],
        4  => ['rate' => 0.500, 'updateRate' => 1.000, 'assuredRate' => 0.12499],
        5  => ['rate' => 0.400, 'updateRate' => 0.500, 'assuredRate' => 0.10800],
        6  => ['rate' => 0.333, 'updateRate' => 0.334, 'assuredRate' => 0.09911],
        7  => ['rate' => 0.286, 'updateRate' => 0.334, 'assuredRate' => 0.08680],
        8  => ['rate' => 0.250, 'updateRate' => 0.334, 'assuredRate' => 0.07909],
        9  => ['rate' => 0.222, 'updateRate' => 0.250, 'assuredRate' => 0.07126],
        10 => ['rate' => 0.200, 'updateRate' => 0.250, 'assuredRate' => 0.06552],
        11 => ['rate' => 0.182, 'updateRate' => 0.200, 'assuredRate' => 0.05992],
        12 => ['rate' => 0.167, 'updateRate' => 0.200, 'assuredRate' => 0.05566],
        13 => ['rate' => 0.154, 'updateRate' => 0.167, 'assuredRate' => 0.05180],
        14 => ['rate' => 0.143, 'updateRate' => 0.167, 'assuredRate' => 0.04854],
        15 => ['rate' => 0.133, 'updateRate' => 0.143, 'assuredRate' => 0.04565],
        16 => ['rate' => 0.125, 'updateRate' => 0.143, 'assuredRate' => 0.04294],
        17 => ['rate' => 0.118, 'updateRate' => 0.125, 'assuredRate' => 0.04038],
        18 => ['rate' => 0.111, 'updateRate' => 0.112, 'assuredRate' => 0.03884],
        19 => ['rate' => 0.105, 'updateRate' => 0.112, 'assuredRate' => 0.03693],
        20 => ['rate' => 0.100, 'updateRate' => 0.112, 'assuredRate' => 0.03486],
        25 => ['rate' => 0.080, 'updateRate' => 0.084, 'assuredRate' => 0.02841],
        30 => ['rate' => 0.067, 'updateRate' => 0.072, 'assuredRate' => 0.02366],
        40 => ['rate' => 0.050, 'updateRate' => 0.053, 'assuredRate' => 0.01791],
        50 => ['rate' => 0.040, 'updateRate' => 0.042, 'assuredRate' => 0.01440],
    ];

    /**
     * 250% declining-balance rates (2007-04-01 to 2012-03-31).
     *
     * @var array<int, array{rate: float, updateRate: float, assuredRate: float}>
     */
    private const TABLE_250 = [
        2  => ['rate' => 1.000, 'updateRate' => 0.000, 'assuredRate' => 0.00000],
        3  => ['rate' => 0.833, 'updateRate' => 1.000, 'assuredRate' => 0.02789],
        4  => ['rate' => 0.625, 'updateRate' => 1.000, 'assuredRate' => 0.05274],
        5  => ['rate' => 0.500, 'updateRate' => 1.000, 'assuredRate' => 0.06249],
        6  => ['rate' => 0.417, 'updateRate' => 0.500, 'assuredRate' => 0.05776],
        7  => ['rate' => 0.357, 'updateRate' => 0.500, 'assuredRate' => 0.05496],
        8  => ['rate' => 0.313, 'updateRate' => 0.334, 'assuredRate' => 0.05111],
        9  => ['rate' => 0.278, 'updateRate' => 0.334, 'assuredRate' => 0.04731],
        10 => ['rate' => 0.250, 'updateRate' => 0.334, 'assuredRate' => 0.04448],
        15 => ['rate' => 0.167, 'updateRate' => 0.200, 'assuredRate' => 0.02755],
        20 => ['rate' => 0.125, 'updateRate' => 0.143, 'assuredRate' => 0.02061],
        25 => ['rate' => 0.100, 'updateRate' => 0.112, 'assuredRate' => 0.01647],
        30 => ['rate' => 0.083, 'updateRate' => 0.084, 'assuredRate' => 0.01493],
    ];

    /**
     * Returns the rate bundle for a given useful life and method.
     *
     * @param 'declining_balance'|'declining_balance_2007'|'declining_balance_2012'|'declining_balance_2016' $method
     * @return array{rate: float, updateRate: float, assuredRate: float}|null
     */
    public static function lookup(string $method, int $usefulLifeYears): ?array
    {
        $table = $method === 'declining_balance_2007' ? self::TABLE_250 : self::TABLE_200;
        return $table[$usefulLifeYears] ?? null;
    }
}
