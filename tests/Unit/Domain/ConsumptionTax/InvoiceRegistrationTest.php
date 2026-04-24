<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\ConsumptionTax;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistration;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(InvoiceRegistration::class)]
final class InvoiceRegistrationTest extends TestCase
{
    public function testRegisteredFlagRespectsWindow(): void
    {
        $reg = $this->build(
            isRegistered: true,
            regFrom: new DateTimeImmutable('2023-10-01'),
            regUntil: new DateTimeImmutable('2024-12-31'),
        );
        self::assertTrue($reg->isRegisteredOn(new DateTimeImmutable('2023-11-01')));
        self::assertFalse($reg->isRegisteredOn(new DateTimeImmutable('2023-09-01')));
        self::assertFalse($reg->isRegisteredOn(new DateTimeImmutable('2025-01-15')));
    }

    public function testNotRegisteredAlwaysFalse(): void
    {
        $reg = $this->build(isRegistered: false);
        self::assertFalse($reg->isRegisteredOn(new DateTimeImmutable('2024-01-15')));
    }

    public function testRejectsInvalidRegistrationNumber(): void
    {
        $this->expectException(ValidationException::class);
        $this->build(regNumber: 'X1234567890123');
    }

    public function testAcceptsCanonicalRegistrationNumber(): void
    {
        $reg = $this->build(regNumber: 'T1234567890123');
        self::assertSame('T1234567890123', $reg->registrationNumber);
    }

    public function testRejectsEmptyCounterpartyName(): void
    {
        $this->expectException(ValidationException::class);
        $this->build(name: '   ');
    }

    public function testRejectsInvertedRegisteredWindow(): void
    {
        $this->expectException(ValidationException::class);
        $this->build(
            isRegistered: true,
            regFrom: new DateTimeImmutable('2025-01-01'),
            regUntil: new DateTimeImmutable('2024-01-01'),
        );
    }

    private function build(
        string $name = 'ACME商事',
        ?string $regNumber = null,
        bool $isRegistered = false,
        ?DateTimeImmutable $regFrom = null,
        ?DateTimeImmutable $regUntil = null,
    ): InvoiceRegistration {
        $now = new DateTimeImmutable('2024-04-01T00:00:00Z');
        return new InvoiceRegistration(
            id: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            counterpartyName: $name,
            registrationNumber: $regNumber,
            isRegistered: $isRegistered,
            registeredFrom: $regFrom,
            registeredUntil: $regUntil,
            notes: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
