<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Port\Cs;

use PDO;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMapping;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsFlowCategory;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see AccountTitleCsMappingRepositoryInterface}. Reads from
 * `account_title_cs_mappings` joined with `account_titles` for deterministic
 * `code ASC` secondary ordering.
 */
final class PdoAccountTitleCsMappingRepository implements AccountTitleCsMappingRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findAllByEntity(string $entityId): array
    {
        $sql = 'SELECT m.account_title_id, m.cs_section_code, m.flow_category,
                       m.sign, m.is_working_capital, m.sort_order, m.display_label,
                       a.code AS account_code
                FROM account_title_cs_mappings AS m
                INNER JOIN account_titles AS a ON a.id = m.account_title_id
                WHERE m.entity_id = :entity
                  AND a.deleted_at IS NULL
                ORDER BY m.flow_category ASC, m.sort_order ASC, a.code ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':entity' => UlidGenerator::decode($entityId)]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $accountId = $r['account_title_id'] ?? '';
            if (!is_string($accountId) || $accountId === '') {
                continue;
            }
            $flowRaw = is_string($r['flow_category'] ?? null) ? (string) $r['flow_category'] : 'operating';
            try {
                $flow = CsFlowCategory::fromString($flowRaw);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $sectionCode = is_string($r['cs_section_code'] ?? null) ? (string) $r['cs_section_code'] : '';
            if ($sectionCode === '') {
                continue;
            }
            $sign = (int) ($r['sign'] ?? 1);
            if ($sign !== 1 && $sign !== -1) {
                $sign = 1;
            }
            $isWc = (int) ($r['is_working_capital'] ?? 0) === 1;
            $sortOrder = (int) ($r['sort_order'] ?? 0);
            $displayLabelRaw = $r['display_label'] ?? null;
            $displayLabel = is_string($displayLabelRaw) && $displayLabelRaw !== ''
                ? $displayLabelRaw
                : null;

            $out[] = new AccountTitleCsMapping(
                accountTitleId: strlen($accountId) === 16 ? UlidGenerator::encode($accountId) : $accountId,
                sectionCode: $sectionCode,
                flowCategory: $flow,
                sign: $sign,
                isWorkingCapital: $isWc,
                sortOrder: $sortOrder,
                displayLabel: $displayLabel,
            );
        }
        return $out;
    }
}
