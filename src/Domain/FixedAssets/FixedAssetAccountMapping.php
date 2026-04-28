<?php

declare(strict_types=1);

namespace App\Domain\FixedAssets;

/**
 * 固定資産に紐づく勘定科目マッピング.
 *
 * 元実装では配賦区分 (販管費 / 製造原価 / 非営業 / 農業) があるが、
 * 本ドメイン層では単一の借方科目 (減価償却費) に集約する.
 * 将来の配賦対応は上位レイヤーで処理する.
 */
final readonly class FixedAssetAccountMapping
{
    public function __construct(
        private string $depreciationExpenseAccountTitleId,
        private string $accumulatedDepreciationAccountTitleId,
    ) {
    }

    /** 借方科目 ID: 減価償却費 */
    public function depreciationExpenseAccountTitleId(): string
    {
        return $this->depreciationExpenseAccountTitleId;
    }

    /** 貸方科目 ID: 減価償却累計額 */
    public function accumulatedDepreciationAccountTitleId(): string
    {
        return $this->accumulatedDepreciationAccountTitleId;
    }
}
