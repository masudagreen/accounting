<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\AccountTitle\AccountTree;

/**
 * 勘定科目ツリーの読み取りインターフェース.
 */
interface AccountTreeRepository
{
    /**
     * BS + PL を結合した全科目ツリーを返す.
     *
     * 試算表・財務諸表の構築に使う.
     */
    public function loadCombinedTree(int $idEntity, int $numFiscalPeriod): AccountTree;
}
