<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassificationRepositoryInterface;

final class InMemoryCvpClassificationRepository implements AccountTitleCvpClassificationRepositoryInterface
{
    /** @var array<string, AccountTitleCvpClassification> */
    private array $byKey = [];

    public function findAllByEntity(string $entityId): array
    {
        $out = [];
        foreach ($this->byKey as $c) {
            if ($c->entityId === $entityId) {
                $out[] = $c;
            }
        }
        return array_values($out);
    }

    public function findByAccountTitle(string $entityId, string $accountTitleId): ?AccountTitleCvpClassification
    {
        return $this->byKey[self::key($entityId, $accountTitleId)] ?? null;
    }

    public function save(AccountTitleCvpClassification $classification): void
    {
        $this->byKey[self::key($classification->entityId, $classification->accountTitleId)] = $classification;
    }

    public function saveMany(array $classifications): void
    {
        foreach ($classifications as $c) {
            $this->save($c);
        }
    }

    public function delete(string $entityId, string $accountTitleId): void
    {
        unset($this->byKey[self::key($entityId, $accountTitleId)]);
    }

    private static function key(string $entityId, string $accountTitleId): string
    {
        return $entityId . '|' . $accountTitleId;
    }
}
