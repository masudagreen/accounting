<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

/**
 * Running counters returned by each importer. Immutable on creation.
 *
 * We keep it dumb (no validation) because the CLI aggregates and logs the
 * totals; any structural invariant belongs in the domain, not the migration
 * bookkeeping layer.
 */
final class ImportReport
{
    public function __construct(
        public readonly string $stage,
        public readonly int $read,
        public readonly int $inserted,
        public readonly int $skipped,
        /** @var list<string> */
        public readonly array $notes = [],
    ) {
    }

    /**
     * @param list<string> $notes
     */
    public static function empty(string $stage, array $notes = []): self
    {
        return new self($stage, 0, 0, 0, $notes);
    }
}
