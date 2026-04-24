<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Port;

use PDO;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMapping;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\FsKind;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * PDO-backed {@see AccountTitleFsMappingRepositoryInterface}. Reads from
 * `account_title_fs_mappings` joined with `account_titles` purely for
 * deterministic ordering (code ascending).
 */
final class PdoAccountTitleFsMappingRepository implements AccountTitleFsMappingRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findAllByEntity(string $entityId): array
    {
        $sql = 'SELECT m.account_title_id, m.fs_kind, m.fs_section_code,
                       m.sort_order, m.display_label, m.sign,
                       a.code AS account_code
                FROM account_title_fs_mappings AS m
                INNER JOIN account_titles AS a ON a.id = m.account_title_id
                WHERE m.entity_id = :entity
                  AND a.deleted_at IS NULL
                ORDER BY m.fs_kind ASC, m.sort_order ASC, a.code ASC';
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
            $kindRaw = is_string($r['fs_kind'] ?? null) ? (string) $r['fs_kind'] : 'bs';
            try {
                $kind = FsKind::fromString($kindRaw);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $sectionCode = is_string($r['fs_section_code'] ?? null) ? (string) $r['fs_section_code'] : '';
            if ($sectionCode === '') {
                continue;
            }
            $sign = (int) ($r['sign'] ?? 1);
            if ($sign !== 1 && $sign !== -1) {
                $sign = 1;
            }
            $sortOrder = (int) ($r['sort_order'] ?? 0);
            $displayLabelRaw = $r['display_label'] ?? null;
            $displayLabel = is_string($displayLabelRaw) && $displayLabelRaw !== ''
                ? $displayLabelRaw
                : null;

            $out[] = new AccountTitleFsMapping(
                accountTitleId: strlen($accountId) === 16 ? UlidGenerator::encode($accountId) : $accountId,
                kind: $kind,
                sectionCode: $sectionCode,
                sign: $sign,
                sortOrder: $sortOrder,
                displayLabel: $displayLabel,
            );
        }
        return $out;
    }
}
