<?php

declare(strict_types=1);

namespace Rucaro\Domain\ConsumptionTax;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;

/**
 * Counter-party invoice-registration record.
 *
 * Under the 2023-10-01 インボイス制度, input-tax credits on purchases from
 * a non-registered counter-party are limited by a transition-measure
 * schedule (80% → 50% → 0). This aggregate tracks whether a given
 * counter-party is registered and for which dates.
 *
 * Invariants:
 *   - counterpartyName is non-empty;
 *   - when registrationNumber is set it must match /^T\d{13}$/;
 *   - registeredFrom <= registeredUntil when both present.
 */
final readonly class InvoiceRegistration
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $counterpartyName,
        public ?string $registrationNumber,
        public bool $isRegistered,
        public ?DateTimeImmutable $registeredFrom,
        public ?DateTimeImmutable $registeredUntil,
        public ?string $notes,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
        if (trim($counterpartyName) === '' || mb_strlen($counterpartyName) > 255) {
            throw ValidationException::withErrors([
                'counterpartyName' => ['counterpartyName must be 1..255 chars.'],
            ]);
        }
        if ($registrationNumber !== null && !preg_match('/^T\d{13}$/', $registrationNumber)) {
            throw ValidationException::withErrors([
                'registrationNumber' => ['registrationNumber must match /^T\\d{13}$/.'],
            ]);
        }
        if ($registeredFrom !== null && $registeredUntil !== null && $registeredUntil < $registeredFrom) {
            throw ValidationException::withErrors([
                'registeredUntil' => ['registeredUntil must be on or after registeredFrom.'],
            ]);
        }
    }

    public function isRegisteredOn(DateTimeImmutable $at): bool
    {
        if (!$this->isRegistered) {
            return false;
        }
        if ($this->registeredFrom !== null && $at < $this->registeredFrom) {
            return false;
        }
        if ($this->registeredUntil !== null && $at > $this->registeredUntil) {
            return false;
        }
        return true;
    }
}
