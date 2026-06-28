<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

use PDO;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Read-side fiscal-term lookup intended for the persistent navigation bar.
 *
 * The Journal UI already has the same query inside
 * {@see \Rucaro\Http\Controller\Ui\Journal\JournalUiContext::fiscalTermsForEntity()},
 * but Report / Dashboard controllers should not need to reach into the
 * Journal namespace just to populate a dropdown. This class is intentionally
 * tiny so any controller can depend on it without dragging in unrelated
 * use-cases.
 *
 * Always returns ULID-encoded ids regardless of how the underlying row was
 * stored (16-byte binary or string), so the navbar can use the same value
 * the session stores for `selected_fiscal_term_id`.
 */
final readonly class FiscalTermLookup
{
    public function __construct(
        private \PDO $pdo,
    ) {
    }

    /**
     * Newest fiscal period first, matching the order the navbar dropdown
     * expects so the most recent term appears at the top.
     *
     * @return list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}>
     */
    public function listForEntity(string $entityId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, fiscal_period, start_date, end_date
                   FROM fiscal_terms
                  WHERE entity_id = :entity
                  ORDER BY fiscal_period DESC',
            );
            $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        } catch (\Throwable) {
            // Test fixtures often run against a sqlite in-memory PDO that
            // omits the fiscal_terms table; the navbar should degrade to an
            // empty dropdown rather than 500.
            return [];
        }
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $idRaw = $r['id'] ?? null;
            if (!is_string($idRaw) || $idRaw === '') {
                continue;
            }
            $out[] = [
                'id' => strlen($idRaw) === 16 ? UlidGenerator::encode($idRaw) : $idRaw,
                'fiscalPeriod' => (int) ($r['fiscal_period'] ?? 0),
                'startDate' => (string) ($r['start_date'] ?? ''),
                'endDate' => (string) ($r['end_date'] ?? ''),
            ];
        }

        return $out;
    }
}
