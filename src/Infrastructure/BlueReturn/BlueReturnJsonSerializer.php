<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnForm;

/**
 * Serializes {@see BlueReturnForm} aggregates to the standard API
 * envelope.
 */
final class BlueReturnJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(BlueReturnForm $form): array
    {
        return [
            'id'           => $form->id,
            'entityId'     => $form->entityId,
            'fiscalTermId' => $form->fiscalTermId,
            'formType'     => $form->formType->value,
            'status'       => $form->status->value,
            'snapshot'     => $form->snapshot->toArray(),
            'finalizedAt'  => $form->finalizedAt?->format(DATE_ATOM),
            'createdBy'    => $form->createdBy,
            'createdAt'    => $form->createdAt->format(DATE_ATOM),
            'updatedAt'    => $form->updatedAt->format(DATE_ATOM),
            'deletedAt'    => $form->deletedAt?->format(DATE_ATOM),
        ];
    }

    /**
     * @param list<BlueReturnForm> $forms
     * @return list<array<string, mixed>>
     */
    public static function toArrayList(array $forms): array
    {
        return array_values(array_map([self::class, 'toArray'], $forms));
    }
}
