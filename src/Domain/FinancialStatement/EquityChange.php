<?php

declare(strict_types=1);

namespace App\Domain\FinancialStatement;

use App\Domain\Money\Money;

/**
 * 株主資本等変動計算書における1件の変動.
 *
 * amount の符号規則:
 *   正 = 純資産の増加方向 (例: 当期純利益, 新株発行)
 *   負 = 純資産の減少方向 (例: 配当, 自己株式取得)
 */
final readonly class EquityChange
{
    private function __construct(
        private EquityChangeType $type,
        private EquitySection $section,
        private Money $amount,
        private ?string $description,
    ) {
    }

    public static function of(
        EquityChangeType $type,
        EquitySection $section,
        Money $amount,
        ?string $description = null,
    ): self {
        return new self($type, $section, $amount, $description);
    }

    public function type(): EquityChangeType
    {
        return $this->type;
    }

    public function section(): EquitySection
    {
        return $this->section;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function description(): ?string
    {
        return $this->description;
    }
}
