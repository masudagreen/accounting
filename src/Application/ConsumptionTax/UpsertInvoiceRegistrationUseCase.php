<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistration;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistrationRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Create or update a single InvoiceRegistration. If `id` is null a new
 * record is minted; otherwise the existing row is replaced.
 */
final readonly class UpsertInvoiceRegistrationUseCase
{
    public function __construct(
        private InvoiceRegistrationRepositoryInterface $registrations,
        private UlidGenerator $ulids,
        private ClockInterface $clock,
    ) {
    }

    public function execute(
        ?string $id,
        string $entityId,
        string $counterpartyName,
        ?string $registrationNumber,
        bool $isRegistered,
        ?string $registeredFrom,
        ?string $registeredUntil,
        ?string $notes,
    ): InvoiceRegistration {
        $now = $this->clock->getCurrentTime();
        $existing = $id !== null ? $this->registrations->findById($id) : null;
        $createdAt = $existing?->createdAt ?? $now;
        $registration = new InvoiceRegistration(
            id: $id ?? $this->ulids->generate(),
            entityId: $entityId,
            counterpartyName: $counterpartyName,
            registrationNumber: $registrationNumber,
            isRegistered: $isRegistered,
            registeredFrom: self::parseDate($registeredFrom),
            registeredUntil: self::parseDate($registeredUntil),
            notes: $notes,
            createdAt: $createdAt,
            updatedAt: $now,
        );
        $this->registrations->save($registration);
        return $registration;
    }

    private static function parseDate(?string $iso): ?DateTimeImmutable
    {
        if ($iso === null || $iso === '') {
            return null;
        }
        return new DateTimeImmutable($iso, new DateTimeZone('UTC'));
    }
}
