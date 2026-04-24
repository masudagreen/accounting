<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Domain\ConsumptionTax\TaxableTransactionQueryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Aggregate journal lines into {@see TaxableTransaction}s using the
 * per-entity `account_title_consumption_tax_defaults` mapping.
 *
 * SQL strategy:
 *   - join `journal_entry_lines` ⨯ `journal_entries` ⨯
 *     `account_title_consumption_tax_defaults` on (entity, account);
 *   - filter to posted / approved entries within the period;
 *   - use the line's `tax_rate_percent` / `tax_amount` / `is_tax_reduced`
 *     which were already filled at journaling time (migration 0003).
 *
 * Lines for accounts without a default mapping are skipped so the UI
 * can flag unmapped accounts.
 */
final class PdoTaxableTransactionQueryService implements TaxableTransactionQueryInterface
{
    private const QUERY_SQL = <<<'SQL'
        SELECT
            je.journal_date        AS journal_date,
            jel.side               AS side,
            jel.amount             AS amount,
            jel.tax_rate_percent   AS rate_percent,
            jel.tax_amount         AS tax_amount,
            jel.is_tax_reduced     AS is_reduced,
            atctd.default_category_code AS category_code
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.entry_id
        JOIN account_title_consumption_tax_defaults atctd
               ON atctd.entity_id = je.entity_id
              AND atctd.account_title_id = jel.account_title_id
        WHERE je.entity_id    = :entity
          AND je.journal_date >= :from
          AND je.journal_date <= :to
          AND je.deleted_at IS NULL
          AND je.status IN ('posted', 'approved')
        ORDER BY je.journal_date ASC
        SQL;

    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByPeriod(string $entityId, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $stmt = $this->pdo->prepare(self::QUERY_SQL);
        $stmt->execute([
            ':entity' => UlidGenerator::decode($entityId),
            ':from'   => $from->format('Y-m-d'),
            ':to'     => $to->format('Y-m-d'),
        ]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        /** @var list<TaxableTransaction> $out */
        $out = [];
        foreach ($rows as $r) {
            $code = ConsumptionTaxCategoryCode::tryFrom((string) ($r['category_code'] ?? ''));
            if ($code === null) {
                continue;
            }
            $amountExcluding = (string) ($r['amount'] ?? '0.0000');
            $tax = (string) ($r['tax_amount'] ?? '0.0000');
            $out[] = new TaxableTransaction(
                bookedOn: new DateTimeImmutable((string) ($r['journal_date'] ?? '1970-01-01'), new DateTimeZone('UTC')),
                categoryCode: $code,
                ratePercent: (string) ($r['rate_percent'] ?? '0.00'),
                isReduced: (int) ($r['is_reduced'] ?? 0) === 1,
                amountExcludingTax: $amountExcluding,
                taxAmount: $tax,
            );
        }
        return $out;
    }
}
