<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

/**
 * Port for rendering 消費税申告書 イメージ PDFs.
 */
interface ConsumptionTaxReportGeneratorInterface
{
    public function render(ConsumptionTaxSettlement $settlement): string;
}
