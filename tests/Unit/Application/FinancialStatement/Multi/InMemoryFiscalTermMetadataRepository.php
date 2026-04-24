<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\FinancialStatement\Multi;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadata;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadataRepositoryInterface;

/**
 * In-memory {@see FiscalTermMetadataRepositoryInterface} for unit tests.
 */
final class InMemoryFiscalTermMetadataRepository implements FiscalTermMetadataRepositoryInterface
{
    /** @var array<string, FiscalTermMetadata> */
    private array $byId = [];

    public function seed(string $id, int $period, string $from, string $to): void
    {
        $tz = new DateTimeZone('UTC');
        $this->byId[$id] = new FiscalTermMetadata(
            id: $id,
            label: '第 ' . $period . ' 期',
            startDate: new DateTimeImmutable($from, $tz),
            endDate: new DateTimeImmutable($to, $tz),
        );
    }

    public function findByIds(array $ids): array
    {
        $out = [];
        foreach ($ids as $id) {
            if (isset($this->byId[$id])) {
                $out[] = $this->byId[$id];
            }
        }
        return $out;
    }
}
