<?php

declare(strict_types=1);

namespace App\Domain\AccountTitle;

/**
 * 製造原価報告書 (CR) における科目の所属セクション.
 *
 *  - materialsCost[Sum]                         → Materials (材料費)
 *  - laborCost[Sum]                             → Labor (労務費)
 *  - manufactureCost[Sum]                       → Manufacture (製造経費)
 *  - workInProcessOpeningInventoryWrap[Sum]     → OpeningWorkInProcess
 *  - workInProcessClosingInventoryWrap[Sum]     → ClosingWorkInProcess
 *  - workInProcessRemoveWrap[Sum]               → RemoveTransfer
 *
 * 計算ノード:
 *  grossProductCostNet (総製造費用) = Materials + Labor + Manufacture
 *  currentWorkInProcessNet (当期製品製造原価)
 *      = 総製造費用 + 期首仕掛品 - 期末仕掛品 - 他勘定振替
 */
enum CrSection: string
{
    case Materials = 'materials';
    case Labor = 'labor';
    case Manufacture = 'manufacture';
    case OpeningWorkInProcess = 'openingWorkInProcess';
    case ClosingWorkInProcess = 'closingWorkInProcess';
    case RemoveTransfer = 'removeTransfer';
}
