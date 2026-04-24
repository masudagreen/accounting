<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Multi;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadata;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadataRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * MySQL-backed {@see FiscalTermMetadataRepositoryInterface}.
 *
 * Resolves `fiscal_terms` rows by ULID (accepting both the canonical text
 * form and the raw BINARY(16) columns the schema uses) and stamps a
 * "第 N 期" label by reading `fiscal_period`.
 */
final class PdoFiscalTermMetadataRepository implements FiscalTermMetadataRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        // Bind each id as a positional parameter so PDO can emit a clean
        // `id IN (?, ?, …)` plan.
        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $sql = 'SELECT id, fiscal_period, start_date, end_date
                FROM fiscal_terms
                WHERE id IN (' . $placeholders . ')';
        $stmt = $this->pdo->prepare($sql);

        $binds = [];
        foreach ($ids as $id) {
            if (!UlidGenerator::isValid($id)) {
                // Skip invalid ids — the use case already ensures the HTTP
                // layer validated them, but stay defensive.
                continue;
            }
            $binds[] = UlidGenerator::decode($id);
        }
        if ($binds === []) {
            return [];
        }
        $stmt->execute($binds);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $tz = new DateTimeZone('UTC');
        $byBinary = [];
        foreach ($rows as $r) {
            $idRaw = $r['id'] ?? null;
            if (!is_string($idRaw) || $idRaw === '') {
                continue;
            }
            $period = (int) ($r['fiscal_period'] ?? 0);
            $startRaw = $r['start_date'] ?? null;
            $endRaw = $r['end_date'] ?? null;
            if (!is_string($startRaw) || !is_string($endRaw)) {
                continue;
            }
            try {
                $start = new DateTimeImmutable($startRaw, $tz);
                $end = new DateTimeImmutable($endRaw, $tz);
            } catch (\Exception) {
                continue;
            }
            $byBinary[$idRaw] = new FiscalTermMetadata(
                id: UlidGenerator::encode($idRaw),
                label: self::periodLabel($period),
                startDate: $start,
                endDate: $end,
            );
        }

        // Preserve caller's requested order.
        $out = [];
        foreach ($ids as $id) {
            if (!UlidGenerator::isValid($id)) {
                continue;
            }
            $binary = UlidGenerator::decode($id);
            if (isset($byBinary[$binary])) {
                $out[] = $byBinary[$binary];
            }
        }
        return $out;
    }

    private static function periodLabel(int $period): string
    {
        if ($period <= 0) {
            return '期';
        }
        return '第 ' . $period . ' 期';
    }
}
