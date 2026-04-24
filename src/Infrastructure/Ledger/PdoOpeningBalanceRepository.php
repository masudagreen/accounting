<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Ledger;

use PDO;
use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Decimal\Decimal;

/**
 * PDO-backed {@see OpeningBalanceRepositoryInterface}.
 *
 * Reads from the `opening_balances` table created by migration 0010.
 * Returns 0 when no row exists for the requested tuple, mirroring the
 * legacy ledger behaviour where accounts without a prior term simply
 * started at zero.
 *
 * TODO (Wave 6-D+): a "close-fiscal-term" workflow should populate this
 * table with the closing balance of each balance-sheet account. Until
 * then the container wires {@see ZeroOpeningBalanceRepository} by default
 * because the table is intentionally seeded empty.
 */
final class PdoOpeningBalanceRepository implements OpeningBalanceRepositoryInterface
{
    private const SQL = <<<SQL
        SELECT amount
        FROM opening_balances
        WHERE entity_id = :entity
          AND fiscal_term_id = :term
          AND account_title_id = :account
        LIMIT 1
        SQL;

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findOpeningBalance(
        string $entityId,
        string $fiscalTermId,
        string $accountTitleId,
    ): string {
        $stmt = $this->pdo->prepare(self::SQL);
        $stmt->execute([
            ':entity'  => UlidGenerator::decode($entityId),
            ':term'    => UlidGenerator::decode($fiscalTermId),
            ':account' => UlidGenerator::decode($accountTitleId),
        ]);
        /** @var string|false $raw */
        $raw = $stmt->fetchColumn();
        if ($raw === false || !is_string($raw) || $raw === '') {
            return Decimal::normalize('0');
        }
        return Decimal::normalize($raw);
    }
}
