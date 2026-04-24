<?php

declare(strict_types=1);

namespace Rucaro\Application\FixedAsset;

use DateTimeZone;
use Rucaro\Domain\FixedAsset\DepreciationScheduleRepositoryInterface;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;
use Rucaro\Support\Clock\ClockInterface;

final readonly class GetFixedAssetLedgerUseCase
{
    public function __construct(
        private FixedAssetRepositoryInterface $assets,
        private DepreciationScheduleRepositoryInterface $schedules,
        private ClockInterface $clock,
    ) {
    }

    public function execute(GetFixedAssetLedgerInput $input): GetFixedAssetLedgerOutput
    {
        if ($input->fixedAssetId !== null) {
            $asset = $this->assets->findById($input->fixedAssetId);
            $assets = $asset === null ? [] : [$asset];
        } else {
            $assets = $this->assets->findByEntity($input->entityId, true);
        }

        $books = [];
        foreach ($assets as $asset) {
            $entries = $this->schedules->findByAsset($asset->id);
            usort($entries, static fn ($a, $b) => $a->periodNumber <=> $b->periodNumber);
            $books[] = ['asset' => $asset, 'schedule' => $entries];
        }

        return new GetFixedAssetLedgerOutput(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            books: $books,
            generatedAt: $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC')),
        );
    }
}
