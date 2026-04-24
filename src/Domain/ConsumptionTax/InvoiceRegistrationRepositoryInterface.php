<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

interface InvoiceRegistrationRepositoryInterface
{
    /** @return list<InvoiceRegistration> */
    public function findByEntity(string $entityId): array;

    public function findById(string $id): ?InvoiceRegistration;

    public function findByRegistrationNumber(string $entityId, string $registrationNumber): ?InvoiceRegistration;

    public function save(InvoiceRegistration $registration): void;

    public function delete(string $id): void;
}
