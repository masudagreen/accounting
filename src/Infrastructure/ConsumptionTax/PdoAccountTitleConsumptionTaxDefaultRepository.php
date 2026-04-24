<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefaultRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoAccountTitleConsumptionTaxDefaultRepository implements AccountTitleConsumptionTaxDefaultRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByEntity(string $entityId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM account_title_consumption_tax_defaults WHERE entity_id = :e ORDER BY updated_at ASC',
        );
        $stmt->execute([':e' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        /** @var list<AccountTitleConsumptionTaxDefault> $out */
        $out = [];
        foreach ($rows as $r) {
            $hydrated = $this->hydrate($r);
            if ($hydrated !== null) {
                $out[] = $hydrated;
            }
        }
        return $out;
    }

    public function findByAccountTitle(string $entityId, string $accountTitleId): ?AccountTitleConsumptionTaxDefault
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM account_title_consumption_tax_defaults WHERE entity_id = :e AND account_title_id = :a LIMIT 1',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':a' => UlidGenerator::decode($accountTitleId),
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function save(AccountTitleConsumptionTaxDefault $row): void
    {
        $sql = <<<'SQL'
            INSERT INTO account_title_consumption_tax_defaults
              (id, entity_id, account_title_id, default_category_code, default_rate_code, created_at, updated_at)
            VALUES
              (:id, :e, :a, :cat, :rate, :ca, :ua)
            ON DUPLICATE KEY UPDATE
              default_category_code = VALUES(default_category_code),
              default_rate_code     = VALUES(default_rate_code),
              updated_at            = VALUES(updated_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'   => UlidGenerator::decode($row->id),
            ':e'    => UlidGenerator::decode($row->entityId),
            ':a'    => UlidGenerator::decode($row->accountTitleId),
            ':cat'  => $row->defaultCategoryCode->value,
            ':rate' => $row->defaultRateCode,
            ':ca'   => $row->createdAt->format('Y-m-d H:i:s.u'),
            ':ua'   => $row->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function saveAll(array $rows): void
    {
        $this->pdo->beginTransaction();
        try {
            foreach ($rows as $row) {
                $this->save($row);
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function delete(string $entityId, string $accountTitleId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM account_title_consumption_tax_defaults WHERE entity_id = :e AND account_title_id = :a',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':a' => UlidGenerator::decode($accountTitleId),
        ]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): ?AccountTitleConsumptionTaxDefault
    {
        $code = ConsumptionTaxCategoryCode::tryFrom((string) ($row['default_category_code'] ?? ''));
        if ($code === null) {
            return null;
        }
        return new AccountTitleConsumptionTaxDefault(
            id: self::encodeId($row['id'] ?? ''),
            entityId: self::encodeId($row['entity_id'] ?? ''),
            accountTitleId: self::encodeId($row['account_title_id'] ?? ''),
            defaultCategoryCode: $code,
            defaultRateCode: isset($row['default_rate_code']) && $row['default_rate_code'] !== null
                ? (string) $row['default_rate_code']
                : null,
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
