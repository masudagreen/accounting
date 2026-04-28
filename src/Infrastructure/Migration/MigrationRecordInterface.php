<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

interface MigrationRecordInterface
{
    /**
     * Returns all applied version strings in ascending order.
     *
     * @return list<string>
     */
    public function getAppliedVersions(): array;

    public function isApplied(string $version): bool;

    public function markApplied(string $version): void;

    public function markRolledBack(string $version): void;
}
