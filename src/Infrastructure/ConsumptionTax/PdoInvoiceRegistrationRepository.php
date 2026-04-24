<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistration;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistrationRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoInvoiceRegistrationRepository implements InvoiceRegistrationRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByEntity(string $entityId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM consumption_tax_invoice_registrations WHERE entity_id = :e ORDER BY counterparty_name ASC',
        );
        $stmt->execute([':e' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map([$this, 'hydrate'], $rows));
    }

    public function findById(string $id): ?InvoiceRegistration
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM consumption_tax_invoice_registrations WHERE id = :id LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByRegistrationNumber(string $entityId, string $registrationNumber): ?InvoiceRegistration
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM consumption_tax_invoice_registrations WHERE entity_id = :e AND registration_number = :n LIMIT 1',
        );
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':n' => $registrationNumber,
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function save(InvoiceRegistration $registration): void
    {
        $sql = <<<'SQL'
            INSERT INTO consumption_tax_invoice_registrations
              (id, entity_id, counterparty_name, registration_number, is_registered,
               registered_from, registered_until, notes, created_at, updated_at)
            VALUES
              (:id, :e, :n, :rn, :ir, :rf, :ru, :note, :ca, :ua)
            ON DUPLICATE KEY UPDATE
              counterparty_name   = VALUES(counterparty_name),
              registration_number = VALUES(registration_number),
              is_registered       = VALUES(is_registered),
              registered_from     = VALUES(registered_from),
              registered_until    = VALUES(registered_until),
              notes               = VALUES(notes),
              updated_at          = VALUES(updated_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'   => UlidGenerator::decode($registration->id),
            ':e'    => UlidGenerator::decode($registration->entityId),
            ':n'    => $registration->counterpartyName,
            ':rn'   => $registration->registrationNumber,
            ':ir'   => $registration->isRegistered ? 1 : 0,
            ':rf'   => $registration->registeredFrom?->format('Y-m-d'),
            ':ru'   => $registration->registeredUntil?->format('Y-m-d'),
            ':note' => $registration->notes,
            ':ca'   => $registration->createdAt->format('Y-m-d H:i:s.u'),
            ':ua'   => $registration->updatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM consumption_tax_invoice_registrations WHERE id = :id');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): InvoiceRegistration
    {
        return new InvoiceRegistration(
            id: self::encodeId($row['id'] ?? ''),
            entityId: self::encodeId($row['entity_id'] ?? ''),
            counterpartyName: (string) ($row['counterparty_name'] ?? ''),
            registrationNumber: isset($row['registration_number']) && $row['registration_number'] !== null
                ? (string) $row['registration_number']
                : null,
            isRegistered: (int) ($row['is_registered'] ?? 0) === 1,
            registeredFrom: self::parseDate($row['registered_from'] ?? null),
            registeredUntil: self::parseDate($row['registered_until'] ?? null),
            notes: isset($row['notes']) && $row['notes'] !== null ? (string) $row['notes'] : null,
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

    private static function parseDate(mixed $raw): ?DateTimeImmutable
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
