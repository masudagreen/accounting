<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;
use Rucaro\Domain\FixedAsset\DepreciationScheduleRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

final class PdoDepreciationScheduleRepository implements DepreciationScheduleRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(DepreciationScheduleEntry $entry): void
    {
        $sql = <<<'SQL'
            INSERT INTO fixed_asset_depreciation_schedules (
                id, fixed_asset_id, fiscal_term_id, period_number,
                period_start_date, period_end_date, months_in_service,
                opening_book_value, depreciation_amount, accumulated_depreciation, closing_book_value,
                is_posted, posted_journal_entry_id, generated_at
            ) VALUES (
                :id, :asset, :term, :period,
                :ps, :pe, :months,
                :ob, :dep, :acc, :cb,
                :posted, :pjid, :gen_at
            )
            ON DUPLICATE KEY UPDATE
                period_number = VALUES(period_number),
                period_start_date = VALUES(period_start_date),
                period_end_date = VALUES(period_end_date),
                months_in_service = VALUES(months_in_service),
                opening_book_value = VALUES(opening_book_value),
                depreciation_amount = VALUES(depreciation_amount),
                accumulated_depreciation = VALUES(accumulated_depreciation),
                closing_book_value = VALUES(closing_book_value),
                is_posted = VALUES(is_posted),
                posted_journal_entry_id = VALUES(posted_journal_entry_id),
                generated_at = VALUES(generated_at)
            SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'     => UlidGenerator::decode($entry->id),
            ':asset'  => UlidGenerator::decode($entry->fixedAssetId),
            ':term'   => UlidGenerator::decode($entry->fiscalTermId),
            ':period' => $entry->periodNumber,
            ':ps'     => $entry->periodStartDate->format('Y-m-d'),
            ':pe'     => $entry->periodEndDate->format('Y-m-d'),
            ':months' => $entry->monthsInService,
            ':ob'     => $entry->openingBookValue,
            ':dep'    => $entry->depreciationAmount,
            ':acc'    => $entry->accumulatedDepreciation,
            ':cb'     => $entry->closingBookValue,
            ':posted' => $entry->isPosted ? 1 : 0,
            ':pjid'   => $entry->postedJournalEntryId !== null ? UlidGenerator::decode($entry->postedJournalEntryId) : null,
            ':gen_at' => $entry->generatedAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function findByAssetAndFiscalTerm(string $fixedAssetId, string $fiscalTermId): ?DepreciationScheduleEntry
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM fixed_asset_depreciation_schedules WHERE fixed_asset_id = :a AND fiscal_term_id = :t LIMIT 1',
        );
        $stmt->execute([
            ':a' => UlidGenerator::decode($fixedAssetId),
            ':t' => UlidGenerator::decode($fiscalTermId),
        ]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findByAsset(string $fixedAssetId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM fixed_asset_depreciation_schedules WHERE fixed_asset_id = :a ORDER BY period_number ASC',
        );
        $stmt->execute([':a' => UlidGenerator::decode($fixedAssetId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findByEntityAndFiscalTerm(string $entityId, string $fiscalTermId): array
    {
        $stmt = $this->pdo->prepare(<<<'SQL'
            SELECT s.*
            FROM fixed_asset_depreciation_schedules s
            JOIN fixed_assets a ON a.id = s.fixed_asset_id
            WHERE a.entity_id = :e AND s.fiscal_term_id = :t
            ORDER BY a.asset_code ASC
            SQL);
        $stmt->execute([
            ':e' => UlidGenerator::decode($entityId),
            ':t' => UlidGenerator::decode($fiscalTermId),
        ]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'hydrate'], $rows);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): DepreciationScheduleEntry
    {
        return new DepreciationScheduleEntry(
            id: self::encodeId($row['id'] ?? ''),
            fixedAssetId: self::encodeId($row['fixed_asset_id'] ?? ''),
            fiscalTermId: self::encodeId($row['fiscal_term_id'] ?? ''),
            periodNumber: (int) ($row['period_number'] ?? 0),
            periodStartDate: self::parseDate((string) ($row['period_start_date'] ?? '')),
            periodEndDate: self::parseDate((string) ($row['period_end_date'] ?? '')),
            monthsInService: (int) ($row['months_in_service'] ?? 12),
            openingBookValue: (string) ($row['opening_book_value'] ?? '0.0000'),
            depreciationAmount: (string) ($row['depreciation_amount'] ?? '0.0000'),
            accumulatedDepreciation: (string) ($row['accumulated_depreciation'] ?? '0.0000'),
            closingBookValue: (string) ($row['closing_book_value'] ?? '0.0000'),
            isPosted: (int) ($row['is_posted'] ?? 0) === 1,
            postedJournalEntryId: self::encodeIdOrNull($row['posted_journal_entry_id'] ?? null),
            generatedAt: self::parseTimestamp($row['generated_at'] ?? null) ?? new DateTimeImmutable('now', new DateTimeZone('UTC')),
        );
    }

    private static function encodeId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function encodeIdOrNull(mixed $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        if (!is_string($raw) || $raw === '') {
            return null;
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function parseDate(string $raw): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return new DateTimeImmutable('1970-01-01', new DateTimeZone('UTC'));
        }
    }

    private static function parseTimestamp(mixed $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '' || !is_string($raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }
}
