<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * 製造原価報告書 (Cost Report / Cost of Manufacturing Statement).
 *
 * 構造:
 *   材料費 + 労務費 + 製造経費 = 当期製造費用 (grossProductCost)
 *   当期製造費用 + 期首仕掛品 - 期末仕掛品 - 他勘定振替 = 当期製品製造原価 (currentWorkInProcess)
 *
 * 当期製品製造原価は PL の売上原価に流れる.
 */
final readonly class CostReport
{
    public function __construct(
        private Money $materials,
        private Money $labor,
        private Money $manufacture,
        private Money $openingWorkInProcess,
        private Money $closingWorkInProcess,
        private Money $removeTransfer,
    ) {
    }

    public function materials(): Money
    {
        return $this->materials;
    }

    public function labor(): Money
    {
        return $this->labor;
    }

    public function manufacture(): Money
    {
        return $this->manufacture;
    }

    public function openingWorkInProcess(): Money
    {
        return $this->openingWorkInProcess;
    }

    public function closingWorkInProcess(): Money
    {
        return $this->closingWorkInProcess;
    }

    public function removeTransfer(): Money
    {
        return $this->removeTransfer;
    }

    /** 当期製造費用 = 材料費 + 労務費 + 製造経費. */
    public function grossProductCost(): Money
    {
        return $this->materials->plus($this->labor)->plus($this->manufacture);
    }

    /**
     * 当期製品製造原価 = 当期製造費用 + 期首仕掛品 - 期末仕掛品 - 他勘定振替.
     */
    public function currentWorkInProcess(): Money
    {
        return $this->grossProductCost()
            ->plus($this->openingWorkInProcess)
            ->minus($this->closingWorkInProcess)
            ->minus($this->removeTransfer);
    }
}
