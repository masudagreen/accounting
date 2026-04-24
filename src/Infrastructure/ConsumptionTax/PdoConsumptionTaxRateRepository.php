<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxRate;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxRateRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoConsumptionTaxRateRepository implements ConsumptionTaxRateRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM consumption_tax_rates ORDER BY sort_order ASC, effective_from ASC');
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    public function findByCode(string $code): ?ConsumptionTaxRate
    {
        $stmt = $this->pdo->prepare('SELECT * FROM consumption_tax_rates WHERE code = :c ORDER BY effective_from DESC LIMIT 1');
        $stmt->execute([':c' => $code]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findEffectiveOn(DateTimeImmutable $at): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM consumption_tax_rates
              WHERE effective_from <= :d AND (effective_until IS NULL OR effective_until >= :d)
              ORDER BY sort_order ASC, effective_from ASC',
        );
        $stmt->execute([':d' => $at->format('Y-m-d')]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): ConsumptionTaxRate
    {
        $rawId = $row['id'] ?? '';
        $id = is_string($rawId) && strlen($rawId) === 16 ? UlidGenerator::encode($rawId) : (string) $rawId;
        return new ConsumptionTaxRate(
            id: $id,
            code: (string) ($row['code'] ?? ''),
            label: (string) ($row['label'] ?? ''),
            ratePercent: (string) ($row['rate_percent'] ?? '0.00'),
            effectiveFrom: self::parseDate((string) ($row['effective_from'] ?? '1970-01-01')),
            effectiveUntil: isset($row['effective_until']) && $row['effective_until'] !== null
                ? self::parseDate((string) $row['effective_until'])
                : null,
            isTaxable: (int) ($row['is_taxable'] ?? 0) === 1,
            isReduced: (int) ($row['is_reduced'] ?? 0) === 1,
            sortOrder: (int) ($row['sort_order'] ?? 0),
        );
    }

    private static function parseDate(string $s): DateTimeImmutable
    {
        return new DateTimeImmutable($s, new DateTimeZone('UTC'));
    }
}
