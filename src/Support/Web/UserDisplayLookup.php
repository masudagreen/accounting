<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

use PDO;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Resolve a batch of user ULIDs to their display names so list / detail
 * views can render 「管理者」 instead of 「01KRA01T9KP1...」 in the 起票者
 * column.
 *
 * Tiny PDO-only adapter so any UI controller can pull this without
 * importing a User-bounded-context dependency.
 */
final readonly class UserDisplayLookup
{
    public function __construct(
        private \PDO $pdo,
    ) {
    }

    /**
     * @param list<string> $ids ULIDs
     *
     * @return array<string, string> id → display_name (missing rows omitted)
     */
    public function displayNamesByIds(array $ids): array
    {
        $unique = array_values(array_unique(array_filter($ids, static fn (string $i): bool => $i !== '')));
        if ($unique === []) {
            return [];
        }
        $placeholders = [];
        $params = [];
        foreach ($unique as $i => $id) {
            $key = ':u'.$i;
            $placeholders[] = $key;
            $params[$key] = UlidGenerator::decode($id);
        }
        $sql = 'SELECT id, display_name FROM users WHERE id IN ('.implode(',', $placeholders).')';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $idRaw = $r['id'] ?? null;
            if (!is_string($idRaw) || $idRaw === '') {
                continue;
            }
            $id = strlen($idRaw) === 16 ? UlidGenerator::encode($idRaw) : $idRaw;
            $out[$id] = (string) ($r['display_name'] ?? '');
        }

        return $out;
    }
}
