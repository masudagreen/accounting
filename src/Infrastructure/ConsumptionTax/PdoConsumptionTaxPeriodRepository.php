<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriodRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\SimplifiedBusinessCategory;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoConsumptionTaxPeriodRepository implements ConsumptionTaxPeriodRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(ConsumptionTaxPeriod $period): void
    {
        $sql = <<<'SQL'
            INSERT INTO consumption_tax_periods
              (id, entity_id, fiscal_term_id, period_from, period_to, calculation_method,
               simplified_business_category, is_interim, settlement_status, settled_at,
               created_at, updated_at)
            VALUES
              (:id, :e, :ft, :pf, :pt, :m, :sbc, :ii, :ss, :sa, :ca, :ua)
            ON DUPLICATE KEY UPDATE
              fiscal_term_id                = VALUES(fiscal_term_id),
              period_to                     = VALUES(period_to),
              calculation_method            = VALUES(calculation_method),
              simplified_business_category  = VALUES(simplified_business_category),
              is_interim                    = VALUES(is_interim),
              settlement_status             = VALUES(settlement_status),
              settled_at                    = VALUES(settled_at),
              updated_at                    = VALUES(updated_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'  => UlidGenerator::decode($period->id),
            ':e'   => UlidGenerator::decode($period->entityId),
            ':ft'  => UlidGenerator::decode($period->fiscalTermId),
            ':pf'  => $period->periodFrom->format('Y-m-d'),
            ':pt'  => $period->periodTo->format('Y-m-d'),
            ':m'   => $period->calculationMethod->value,
            ':sbc' => $period->simplifiedBusinessCategory?->value,
            ':ii'  => $period->isInterim ? 1 : 0,
            ':ss'  => $period->settlementStatus,
            ':sa'  => $period->settledAt?->format('Y-m-d H:i:s.u'),
            ':ca'  => $period->createdAt->format('Y-m-d H:i:s.u'),
            ':ua'  => $period->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function findById(string $id): ?ConsumptionTaxPeriod
    {
        $stmt = $this->pdo->prepare('SELECT * FROM consumption_tax_periods WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByEntity(string $entityId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM consumption_tax_periods WHERE entity_id = :e ORDER BY period_from ASC',
        );
        $stmt->execute([':e' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM consumption_tax_periods WHERE id = :id');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): ConsumptionTaxPeriod
    {
        $method = ConsumptionTaxCalculationMethod::from((string) ($row['calculation_method'] ?? 'principle'));
        $sbcRaw = $row['simplified_business_category'] ?? null;
        $sbc = null;
        if ($sbcRaw !== null && $sbcRaw !== '') {
            $sbc = SimplifiedBusinessCategory::from((int) $sbcRaw);
        }
        return new ConsumptionTaxPeriod(
            id: self::encodeId($row['id'] ?? ''),
            entityId: self::encodeId($row['entity_id'] ?? ''),
            fiscalTermId: self::encodeId($row['fiscal_term_id'] ?? ''),
            periodFrom: self::parseDate((string) ($row['period_from'] ?? '1970-01-01')),
            periodTo: self::parseDate((string) ($row['period_to'] ?? '1970-01-01')),
            calculationMethod: $method,
            simplifiedBusinessCategory: $sbc,
            isInterim: (int) ($row['is_interim'] ?? 0) === 1,
            settlementStatus: (string) ($row['settlement_status'] ?? 'pending'),
            settledAt: self::parseTimestamp($row['settled_at'] ?? null),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? self::now(),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? self::now(),
        );
    }

    private static function encodeId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function parseDate(string $s): DateTimeImmutable
    {
        return new DateTimeImmutable($s, new DateTimeZone('UTC'));
    }

    private static function parseTimestamp(mixed $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '' || !is_string($raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
