<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\FixedAssets\FixedAsset;

/**
 * 固定資産マスタの読み取りインターフェース.
 */
interface FixedAssetRepository
{
    /**
     * 指定事業体の固定資産一覧を返す.
     *
     * @return list<FixedAsset>
     */
    public function findByEntity(int $idEntity): array;

    /**
     * 指定 ID の固定資産を返す. 存在しない場合は null.
     */
    public function findById(string $id): ?FixedAsset;
}
