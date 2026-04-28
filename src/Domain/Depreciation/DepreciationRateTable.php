<?php

declare(strict_types=1);

namespace App\Domain\Depreciation;

use Brick\Math\BigDecimal;

/**
 * 耐用年数 → 償却率 のマッピング (定額法/定率法/旧定率法 共通インターフェース).
 *
 * 元実装は `back/tpl/templates/else/plugin/accounting/dat/dep*.csv` から読み込む.
 * 新ドメインでは表内の数値をハードコードした静的マップとしても提供し、
 * 起動時の I/O を不要にする.
 */
final class DepreciationRateTable
{
    /**
     * 平成19年4月1日以降取得の定額法 (depStraightNew.csv).
     *
     * 耐用年数 → 償却率.
     */
    public static function straightLineNew(int $usefulLifeYears): BigDecimal
    {
        $rates = [
            2  => '0.500',
            3  => '0.334',
            4  => '0.250',
            5  => '0.200',
            6  => '0.167',
            7  => '0.143',
            8  => '0.125',
            9  => '0.112',
            10 => '0.100',
            11 => '0.091',
            12 => '0.084',
            13 => '0.077',
            14 => '0.072',
            15 => '0.067',
            16 => '0.063',
            17 => '0.059',
            18 => '0.056',
            19 => '0.053',
            20 => '0.050',
            21 => '0.048',
            22 => '0.046',
            23 => '0.044',
            24 => '0.042',
            25 => '0.040',
            26 => '0.039',
            27 => '0.038',
            28 => '0.036',
            29 => '0.035',
            30 => '0.034',
            31 => '0.033',
            32 => '0.032',
            33 => '0.031',
            34 => '0.030',
            35 => '0.029',
            36 => '0.028',
            37 => '0.028',
            38 => '0.027',
            39 => '0.026',
            40 => '0.025',
            41 => '0.025',
            42 => '0.024',
            43 => '0.024',
            44 => '0.023',
            45 => '0.023',
            46 => '0.022',
            47 => '0.022',
            48 => '0.021',
            49 => '0.021',
            50 => '0.020',
        ];

        if (! isset($rates[$usefulLifeYears])) {
            throw new \DomainException(sprintf(
                'no straight-line rate for useful life %d years',
                $usefulLifeYears,
            ));
        }
        return BigDecimal::of($rates[$usefulLifeYears]);
    }

    /**
     * 200%定率法 (平成24年4月1日以降取得) 償却率/改定償却率/償却保証率.
     *
     * @return array{rate: BigDecimal, switchRate: ?BigDecimal, assuredRate: ?BigDecimal}
     */
    public static function declining200(int $usefulLifeYears): array
    {
        // [rate, switchRate, assuredRate]. 耐用2年は償却率1.000で1年で償却完了.
        $rates = [
            2  => ['1.000', null,    null],
            3  => ['0.667', '1.000', '0.11089'],
            4  => ['0.500', '1.000', '0.12499'],
            5  => ['0.400', '0.500', '0.10800'],
            6  => ['0.333', '0.334', '0.09911'],
            7  => ['0.286', '0.334', '0.08680'],
            8  => ['0.250', '0.334', '0.07909'],
            9  => ['0.222', '0.250', '0.07126'],
            10 => ['0.200', '0.250', '0.06552'],
            11 => ['0.182', '0.200', '0.05992'],
            12 => ['0.167', '0.200', '0.05566'],
            13 => ['0.154', '0.167', '0.05180'],
            14 => ['0.143', '0.167', '0.04854'],
            15 => ['0.133', '0.143', '0.04565'],
            16 => ['0.125', '0.143', '0.04294'],
            17 => ['0.118', '0.125', '0.04038'],
            18 => ['0.111', '0.112', '0.03884'],
            19 => ['0.105', '0.112', '0.03693'],
            20 => ['0.100', '0.112', '0.03486'],
        ];

        if (! isset($rates[$usefulLifeYears])) {
            throw new \DomainException(sprintf(
                'no 200%% declining rate for useful life %d years',
                $usefulLifeYears,
            ));
        }

        [$r, $s, $a] = $rates[$usefulLifeYears];
        return [
            'rate' => BigDecimal::of($r),
            'switchRate' => $s !== null ? BigDecimal::of($s) : null,
            'assuredRate' => $a !== null ? BigDecimal::of($a) : null,
        ];
    }

    /**
     * 250%定率法 (平成19年4月1日〜平成24年3月31日取得).
     *
     * @return array{rate: BigDecimal, switchRate: ?BigDecimal, assuredRate: ?BigDecimal}
     */
    public static function declining250(int $usefulLifeYears): array
    {
        $rates = [
            2  => ['1.000', null,    null],
            3  => ['0.833', '1.000', '0.02789'],
            4  => ['0.625', '1.000', '0.05274'],
            5  => ['0.500', '1.000', '0.06249'],
            6  => ['0.417', '0.500', '0.05776'],
            7  => ['0.357', '0.500', '0.05496'],
            8  => ['0.313', '0.334', '0.05111'],
            9  => ['0.278', '0.334', '0.04731'],
            10 => ['0.250', '0.334', '0.04448'],
            11 => ['0.227', '0.250', '0.04123'],
            12 => ['0.208', '0.250', '0.03870'],
            13 => ['0.192', '0.200', '0.03633'],
            14 => ['0.179', '0.200', '0.03457'],
            15 => ['0.167', '0.200', '0.03282'],
            16 => ['0.156', '0.167', '0.03097'],
            17 => ['0.147', '0.167', '0.02953'],
            18 => ['0.139', '0.143', '0.02814'],
            19 => ['0.132', '0.143', '0.02680'],
            20 => ['0.125', '0.143', '0.02565'],
        ];

        if (! isset($rates[$usefulLifeYears])) {
            throw new \DomainException(sprintf(
                'no 250%% declining rate for useful life %d years',
                $usefulLifeYears,
            ));
        }

        [$r, $s, $a] = $rates[$usefulLifeYears];
        return [
            'rate' => BigDecimal::of($r),
            'switchRate' => $s !== null ? BigDecimal::of($s) : null,
            'assuredRate' => $a !== null ? BigDecimal::of($a) : null,
        ];
    }
}
