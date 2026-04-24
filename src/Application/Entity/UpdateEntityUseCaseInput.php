<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

final readonly class UpdateEntityUseCaseInput
{
    public function __construct(
        public string $id,
        public string $name,
        public string $nationCode,
        public string $currencyCode,
        public string $fiscalStartMmDd,
        public bool $isActive,
        public bool $isCorporate,
    ) {
    }
}
