<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Web;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Web\FiscalTermLookup;

#[CoversClass(FiscalTermLookup::class)]
final class FiscalTermLookupTest extends TestCase
{
    public function testReturnsEmptyArrayWhenTableIsAbsent(): void
    {
        $pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
        $lookup = new FiscalTermLookup($pdo);

        // No fiscal_terms table created -- the lookup should swallow the SQL
        // error and return [] so the navbar can degrade gracefully.
        self::assertSame([], $lookup->listForEntity((new UlidGenerator())->generate()));
    }

    public function testReturnsRowsOrderedByFiscalPeriodDescAndKeepsEncodedIds(): void
    {
        // sqlite's BLOB binding is finicky compared to MariaDB; use TEXT
        // columns and seed both sides with the 26-char ULID itself so the
        // domain logic (decode → bind → fetch → encode) is still exercised
        // without us having to recreate MariaDB's binary equivalence rules.
        $pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec(
            'CREATE TABLE fiscal_terms (
                id TEXT PRIMARY KEY,
                entity_id TEXT NOT NULL,
                fiscal_period INTEGER NOT NULL,
                start_date TEXT NOT NULL,
                end_date TEXT NOT NULL
            )',
        );

        $ulid = new UlidGenerator();
        $entityUlid = $ulid->generate();

        // Insert period 18, 20, 19 — should come back as 20, 19, 18.
        $insert = $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date)
             VALUES (:id, :entity, :period, :start, :end)',
        );
        foreach ([
            [$ulid->generate(), 18, '2023-07-01', '2024-06-30'],
            [$ulid->generate(), 20, '2025-07-01', '2026-06-30'],
            [$ulid->generate(), 19, '2024-07-01', '2025-06-30'],
        ] as [$id, $period, $start, $end]) {
            // Store the raw 16-byte binary (matches what FiscalTermLookup
            // binds with `UlidGenerator::decode($entityId)`).
            $insert->execute([
                ':id' => UlidGenerator::decode($id),
                ':entity' => UlidGenerator::decode($entityUlid),
                ':period' => $period,
                ':start' => $start,
                ':end' => $end,
            ]);
        }

        $lookup = new FiscalTermLookup($pdo);
        $out = $lookup->listForEntity($entityUlid);

        self::assertCount(3, $out, 'expected 3 terms for the entity, got '.count($out));
        self::assertSame([20, 19, 18], array_column($out, 'fiscalPeriod'));
        self::assertSame('2025-07-01', $out[0]['startDate']);
        self::assertSame('2026-06-30', $out[0]['endDate']);
        // ids should round-trip into the 26-char ULID form because the rows
        // happened to be stored as 16-byte binary.
        foreach ($out as $row) {
            self::assertSame(26, strlen($row['id']), 'id '.$row['id'].' is not a 26-char ULID');
        }
    }

    public function testFiltersByEntityId(): void
    {
        $pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec(
            'CREATE TABLE fiscal_terms (
                id TEXT PRIMARY KEY,
                entity_id TEXT NOT NULL,
                fiscal_period INTEGER NOT NULL,
                start_date TEXT NOT NULL,
                end_date TEXT NOT NULL
            )',
        );

        $ulid = new UlidGenerator();
        $eA = $ulid->generate();
        $eB = $ulid->generate();
        $insert = $pdo->prepare(
            'INSERT INTO fiscal_terms (id, entity_id, fiscal_period, start_date, end_date)
             VALUES (:id, :entity, :period, :start, :end)',
        );
        foreach ([
            [$eA, 1, '2024-01-01', '2024-12-31'],
            [$eB, 1, '2024-01-01', '2024-12-31'],
        ] as [$entity, $period, $start, $end]) {
            $insert->execute([
                ':id' => UlidGenerator::decode($ulid->generate()),
                ':entity' => UlidGenerator::decode($entity),
                ':period' => $period,
                ':start' => $start,
                ':end' => $end,
            ]);
        }

        $lookup = new FiscalTermLookup($pdo);
        self::assertCount(1, $lookup->listForEntity($eA));
        self::assertCount(1, $lookup->listForEntity($eB));
    }
}
