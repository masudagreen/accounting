<?php

declare(strict_types=1);

namespace App\Domain\FixedAssets;

use App\Domain\Depreciation\Average;
use App\Domain\Depreciation\DecliningBalance;
use App\Domain\Depreciation\DecliningMethod;
use App\Domain\Depreciation\DepreciationResult;
use App\Domain\Depreciation\LumpSumThreeYear;
use App\Domain\Depreciation\StraightLine;
use App\Domain\Depreciation\SumOfYears;
use App\Domain\Depreciation\Voluntary;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Money\Money;
use App\Domain\Money\RoundingMode;

/**
 * 固定資産の減価償却仕訳を生成する.
 *
 * 各 DepreciationMethodChoice に対して対応する計算クラスを呼び出し、
 * 償却額 > 0 であれば JournalEntry を返す.
 *
 * 仕訳内容:
 *   借方: 減価償却費 (depreciationExpenseAccountTitleId)
 *   貸方: 減価償却累計額 (accumulatedDepreciationAccountTitleId)
 */
final class FixedAssetJournalGenerator
{
    /**
     * @param int $yearIndex 級数法 (SumOfYears) でのみ使用する年度インデックス (1始まり).
     *                       他の方法では無視される.
     */
    public static function generate(
        FixedAsset $asset,
        FiscalPeriod $period,
        Money $previousAccumulated,
        RoundingMode $mode,
        ?Money $voluntaryAmount = null,
        int $yearIndex = 1,
    ): ?JournalEntry {
        $result = self::computeDepreciation(
            asset: $asset,
            period: $period,
            previousAccumulated: $previousAccumulated,
            mode: $mode,
            voluntaryAmount: $voluntaryAmount,
            yearIndex: $yearIndex,
        );

        if ($result->depreciation()->isZero()) {
            return null;
        }

        $mapping = $asset->accountMapping();
        $amount = $result->depreciation();

        return JournalEntry::of(
            debits: [
                JournalLine::of($mapping->depreciationExpenseAccountTitleId(), $amount),
            ],
            credits: [
                JournalLine::of($mapping->accumulatedDepreciationAccountTitleId(), $amount),
            ],
        );
    }

    private static function computeDepreciation(
        FixedAsset $asset,
        FiscalPeriod $period,
        Money $previousAccumulated,
        RoundingMode $mode,
        ?Money $voluntaryAmount,
        int $yearIndex,
    ): DepreciationResult {
        $acquisition = $asset->acquisition();

        return match ($asset->method()) {
            DepreciationMethodChoice::Straight => StraightLine::compute(
                $acquisition,
                $period,
                $previousAccumulated,
                $mode,
            ),
            DepreciationMethodChoice::Declining200 => DecliningBalance::compute(
                $acquisition,
                $period,
                $previousAccumulated,
                DecliningMethod::TwoHundredPercent,
                $mode,
            ),
            DepreciationMethodChoice::Declining250 => DecliningBalance::compute(
                $acquisition,
                $period,
                $previousAccumulated,
                DecliningMethod::TwoHundredFiftyPercent,
                $mode,
            ),
            DepreciationMethodChoice::SumOfYears => SumOfYears::compute(
                $acquisition,
                $period,
                $previousAccumulated,
                $yearIndex,
                $mode,
            ),
            DepreciationMethodChoice::Average => Average::compute(
                $acquisition,
                $period,
                $previousAccumulated,
                $mode,
            ),
            DepreciationMethodChoice::Voluntary => self::computeVoluntary(
                $asset,
                $period,
                $previousAccumulated,
                $voluntaryAmount,
            ),
            DepreciationMethodChoice::LumpSumThreeYear => LumpSumThreeYear::compute(
                $acquisition,
                $period,
                $previousAccumulated,
                $mode,
            ),
        };
    }

    private static function computeVoluntary(
        FixedAsset $asset,
        FiscalPeriod $period,
        Money $previousAccumulated,
        ?Money $voluntaryAmount,
    ): DepreciationResult {
        if ($voluntaryAmount === null) {
            throw new \InvalidArgumentException(
                'voluntaryAmount must be provided for Voluntary depreciation method',
            );
        }

        return Voluntary::compute(
            $asset->acquisition(),
            $period,
            $previousAccumulated,
            $voluntaryAmount,
        );
    }
}
