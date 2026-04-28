<?php

declare(strict_types=1);

namespace App\Domain\FixedAssets;

use App\Domain\Depreciation\Acquisition;

/**
 * 固定資産マスタ.
 *
 * 元実装の `accountingLogFixedAssetsJpn` に対応する値オブジェクト.
 * 減価償却計算は FixedAssetJournalGenerator に委譲する.
 *
 * 不変条件:
 *  - id / name は非空文字
 */
final readonly class FixedAsset
{
    public function __construct(
        private string $id,
        private string $name,
        private Acquisition $acquisition,
        private DepreciationMethodChoice $method,
        private FixedAssetAccountMapping $accountMapping,
    ) {
        if ($id === '') {
            throw new \InvalidArgumentException('id must not be empty');
        }
        if ($name === '') {
            throw new \InvalidArgumentException('name must not be empty');
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function acquisition(): Acquisition
    {
        return $this->acquisition;
    }

    public function method(): DepreciationMethodChoice
    {
        return $this->method;
    }

    public function accountMapping(): FixedAssetAccountMapping
    {
        return $this->accountMapping;
    }
}
