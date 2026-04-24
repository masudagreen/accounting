<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use PDO;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategory;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoConsumptionTaxCategoryRepository implements ConsumptionTaxCategoryRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM consumption_tax_categories ORDER BY sort_order ASC, code ASC');
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        /** @var list<ConsumptionTaxCategory> $out */
        $out = [];
        foreach ($rows as $r) {
            $cat = $this->hydrate($r);
            if ($cat !== null) {
                $out[] = $cat;
            }
        }
        return $out;
    }

    public function findByCode(ConsumptionTaxCategoryCode $code): ?ConsumptionTaxCategory
    {
        $stmt = $this->pdo->prepare('SELECT * FROM consumption_tax_categories WHERE code = :c LIMIT 1');
        $stmt->execute([':c' => $code->value]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): ?ConsumptionTaxCategory
    {
        $code = ConsumptionTaxCategoryCode::tryFrom((string) ($row['code'] ?? ''));
        if ($code === null) {
            return null;
        }
        $rawId = $row['id'] ?? '';
        $id = is_string($rawId) && strlen($rawId) === 16 ? UlidGenerator::encode($rawId) : (string) $rawId;
        return new ConsumptionTaxCategory(
            id: $id,
            code: $code,
            label: (string) ($row['label'] ?? ''),
            side: (string) ($row['side'] ?? 'sales'),
            deductible: (int) ($row['deductible'] ?? 0) === 1,
            sortOrder: (int) ($row['sort_order'] ?? 0),
        );
    }
}
