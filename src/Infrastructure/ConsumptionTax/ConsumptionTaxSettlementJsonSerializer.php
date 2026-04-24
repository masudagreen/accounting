<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefault;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategory;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxRate;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistration;

/**
 * Marshal the consumption-tax aggregates to the standard API envelope.
 */
final class ConsumptionTaxSettlementJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function settlementToArray(ConsumptionTaxSettlement $s): array
    {
        return [
            'period'                     => self::periodToArray($s->period),
            'method'                     => $s->method->value,
            'methodLabel'                => $s->method->label(),
            'taxableSales'               => $s->taxableSales,
            'nonTaxableSales'            => $s->nonTaxableSales,
            'exemptSales'                => $s->exemptSales,
            'untaxedSales'               => $s->untaxedSales,
            'totalSales'                 => $s->totalSales,
            'taxableSalesRatio'          => $s->taxableSalesRatio,
            'outputTax'                  => $s->outputTax,
            'deductibleInputTax'         => $s->deductibleInputTax,
            'adjustmentForNonRegistered' => $s->adjustmentForNonRegistered,
            'netTaxPayable'              => $s->netTaxPayable,
            'salesByRate'                => $s->salesByRate,
            'outputTaxByRate'            => $s->outputTaxByRate,
            'purchasesByRate'            => $s->purchasesByRate,
            'inputTaxByRate'             => $s->inputTaxByRate,
            'taxSplit'                   => $s->taxSplitNationalLocal(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function periodToArray(ConsumptionTaxPeriod $p): array
    {
        return [
            'id'                          => $p->id,
            'entityId'                    => $p->entityId,
            'fiscalTermId'                => $p->fiscalTermId,
            'periodFrom'                  => $p->periodFrom->format('Y-m-d'),
            'periodTo'                    => $p->periodTo->format('Y-m-d'),
            'calculationMethod'           => $p->calculationMethod->value,
            'simplifiedBusinessCategory'  => $p->simplifiedBusinessCategory?->value,
            'isInterim'                   => $p->isInterim,
            'settlementStatus'            => $p->settlementStatus,
            'settledAt'                   => $p->settledAt?->format(DATE_ATOM),
            'createdAt'                   => $p->createdAt->format(DATE_ATOM),
            'updatedAt'                   => $p->updatedAt->format(DATE_ATOM),
        ];
    }

    /**
     * @param list<ConsumptionTaxPeriod> $periods
     * @return list<array<string, mixed>>
     */
    public static function periodsToArrayList(array $periods): array
    {
        return array_values(array_map([self::class, 'periodToArray'], $periods));
    }

    /**
     * @return array<string, mixed>
     */
    public static function rateToArray(ConsumptionTaxRate $r): array
    {
        return [
            'id'             => $r->id,
            'code'           => $r->code,
            'label'          => $r->label,
            'ratePercent'    => $r->ratePercent,
            'effectiveFrom'  => $r->effectiveFrom->format('Y-m-d'),
            'effectiveUntil' => $r->effectiveUntil?->format('Y-m-d'),
            'isTaxable'      => $r->isTaxable,
            'isReduced'      => $r->isReduced,
            'sortOrder'      => $r->sortOrder,
        ];
    }

    /**
     * @param list<ConsumptionTaxRate> $rates
     * @return list<array<string, mixed>>
     */
    public static function ratesToArrayList(array $rates): array
    {
        return array_values(array_map([self::class, 'rateToArray'], $rates));
    }

    /**
     * @return array<string, mixed>
     */
    public static function categoryToArray(ConsumptionTaxCategory $c): array
    {
        return [
            'id'         => $c->id,
            'code'       => $c->code->value,
            'label'      => $c->label,
            'side'       => $c->side,
            'deductible' => $c->deductible,
            'sortOrder'  => $c->sortOrder,
        ];
    }

    /**
     * @param list<ConsumptionTaxCategory> $categories
     * @return list<array<string, mixed>>
     */
    public static function categoriesToArrayList(array $categories): array
    {
        return array_values(array_map([self::class, 'categoryToArray'], $categories));
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultToArray(AccountTitleConsumptionTaxDefault $d): array
    {
        return [
            'id'                  => $d->id,
            'entityId'            => $d->entityId,
            'accountTitleId'      => $d->accountTitleId,
            'defaultCategoryCode' => $d->defaultCategoryCode->value,
            'defaultRateCode'     => $d->defaultRateCode,
            'createdAt'           => $d->createdAt->format(DATE_ATOM),
            'updatedAt'           => $d->updatedAt->format(DATE_ATOM),
        ];
    }

    /**
     * @param list<AccountTitleConsumptionTaxDefault> $defaults
     * @return list<array<string, mixed>>
     */
    public static function defaultsToArrayList(array $defaults): array
    {
        return array_values(array_map([self::class, 'defaultToArray'], $defaults));
    }

    /**
     * @return array<string, mixed>
     */
    public static function invoiceRegistrationToArray(InvoiceRegistration $r): array
    {
        return [
            'id'                 => $r->id,
            'entityId'           => $r->entityId,
            'counterpartyName'   => $r->counterpartyName,
            'registrationNumber' => $r->registrationNumber,
            'isRegistered'       => $r->isRegistered,
            'registeredFrom'     => $r->registeredFrom?->format('Y-m-d'),
            'registeredUntil'    => $r->registeredUntil?->format('Y-m-d'),
            'notes'              => $r->notes,
            'createdAt'          => $r->createdAt->format(DATE_ATOM),
            'updatedAt'          => $r->updatedAt->format(DATE_ATOM),
        ];
    }

    /**
     * @param list<InvoiceRegistration> $regs
     * @return list<array<string, mixed>>
     */
    public static function invoiceRegistrationsToArrayList(array $regs): array
    {
        return array_values(array_map([self::class, 'invoiceRegistrationToArray'], $regs));
    }
}
