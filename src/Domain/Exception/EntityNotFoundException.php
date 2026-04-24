<?php

declare(strict_types=1);

namespace Rucaro\Domain\Exception;

/**
 * Thrown when a repository or service cannot find an aggregate / entity
 * the caller expected to exist.
 */
final class EntityNotFoundException extends DomainException
{
    private const DOMAIN_CODE = 'ENTITY_NOT_FOUND';

    public static function for(string $entityName, string $id): self
    {
        return new self(
            message: sprintf("%s with id '%s' was not found.", $entityName, $id),
            domainCode: self::DOMAIN_CODE,
            context: [
                'entity' => $entityName,
                'id'     => $id,
            ],
        );
    }
}
