<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mariadb;

use App\Domain\Journal\JournalEntry;
use App\Infrastructure\Legacy\LegacyJournalReader;
use App\Infrastructure\Persistence\JournalRepository;
use PDO;

/**
 * accountingLog テーブルを読む MariaDB 実装.
 *
 * 既存の LegacyJournalReader を内部で利用し、
 * SQL の実行と行変換を担当する.
 */
final class MariadbJournalRepository implements JournalRepository
{
    private readonly LegacyJournalReader $reader;

    public function __construct(
        private readonly PDO $pdo,
    ) {
        $this->reader = new LegacyJournalReader();
    }

    /**
     * {@inheritDoc}
     *
     * @return list<array{date: \DateTimeImmutable, entry: JournalEntry}>
     */
    public function findByEntityAndPeriod(int $idEntity, int $numFiscalPeriod): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT stampBook, jsonVersion
             FROM accountingLog
             WHERE idEntity = :idEntity
               AND numFiscalPeriod = :numFiscalPeriod
               AND (flagRemove IS NULL OR flagRemove = 0)
             ORDER BY stampBook ASC',
        );
        $stmt->execute([
            ':idEntity'        => $idEntity,
            ':numFiscalPeriod' => $numFiscalPeriod,
        ]);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = $this->reader->read($rows);

        return $result['entries'];
    }
}
