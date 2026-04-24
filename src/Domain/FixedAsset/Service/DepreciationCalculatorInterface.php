<?php

declare(strict_types=1);

namespace Rucaro\Domain\FixedAsset\Service;

interface DepreciationCalculatorInterface
{
    public function calculate(DepreciationCalculationRequest $request): DepreciationCalculationResult;
}
