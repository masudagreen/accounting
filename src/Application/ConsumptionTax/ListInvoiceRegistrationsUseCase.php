<?php

declare(strict_types=1);

namespace Rucaro\Application\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\InvoiceRegistration;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistrationRepositoryInterface;

final readonly class ListInvoiceRegistrationsUseCase
{
    public function __construct(
        private InvoiceRegistrationRepositoryInterface $registrations,
    ) {
    }

    /** @return list<InvoiceRegistration> */
    public function execute(string $entityId): array
    {
        return $this->registrations->findByEntity($entityId);
    }
}
