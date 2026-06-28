<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Per-request lookups the navbar needs (entities the user owns,
 * fiscal terms for the selected entity).
 *
 * This lives outside the Journal namespace because every navbar-bearing
 * page needs the same data — Reports / Master / FixedAsset / CashPlan /
 * ConsumptionTax / Budget / SS adjustments. Centralising the queries
 * here lets {@see SmartyViewRenderer} auto-fill them so individual
 * controllers don't have to add wiring just to render the chrome.
 */
final readonly class NavbarLookup
{
    public function __construct(
        private \PDO $pdo,
    ) {
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    public function entitiesForUser(string $userId): array
    {
        if ($userId === '') {
            return [];
        }
        $stmt = $this->pdo->prepare(
            'SELECT id, name
               FROM entities
              WHERE owner_user_id = :u
                AND is_active = 1
                AND deleted_at IS NULL
              ORDER BY name',
        );
        $stmt->execute([':u' => UlidGenerator::decode($userId)]);
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
                'name' => (string) ($r['name'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @return list<array{id: string, fiscalPeriod: int, startDate: string, endDate: string}>
     */
    public function fiscalTermsForEntity(string $entityId): array
    {
        if ($entityId === '') {
            return [];
        }
        $stmt = $this->pdo->prepare(
            'SELECT id, fiscal_period, start_date, end_date
               FROM fiscal_terms
              WHERE entity_id = :e
              ORDER BY fiscal_period DESC',
        );
        $stmt->execute([':e' => UlidGenerator::decode($entityId)]);
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
