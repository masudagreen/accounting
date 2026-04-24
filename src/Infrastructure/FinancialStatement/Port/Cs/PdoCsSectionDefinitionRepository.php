<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Port\Cs;

use PDO;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinition;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinitionRepositoryInterface;

/**
 * PDO-backed {@see CsSectionDefinitionRepositoryInterface}. Reads from
 * `fs_cs_section_definitions` and materialises {@see CsSectionDefinition} DTOs.
 */
final class PdoCsSectionDefinitionRepository implements CsSectionDefinitionRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findAll(): array
    {
        $sql = 'SELECT code, parent_code, label, sort_order,
                       is_subtotal, is_total, formula
                FROM fs_cs_section_definitions
                ORDER BY sort_order ASC, code ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $r) {
            $code = is_string($r['code'] ?? null) ? (string) $r['code'] : '';
            if ($code === '') {
                continue;
            }
            $parentRaw = $r['parent_code'] ?? null;
            $parentCode = is_string($parentRaw) && $parentRaw !== '' ? $parentRaw : null;
            $label = is_string($r['label'] ?? null) ? (string) $r['label'] : $code;
            $sortOrder = (int) ($r['sort_order'] ?? 0);
            $isSubtotal = self::toBool($r['is_subtotal'] ?? false);
            $isTotal = self::toBool($r['is_total'] ?? false);
            $formulaRaw = $r['formula'] ?? null;
            $formula = is_string($formulaRaw) && $formulaRaw !== '' ? $formulaRaw : null;

            $out[] = new CsSectionDefinition(
                code: $code,
                parentCode: $parentCode,
                label: $label,
                sortOrder: $sortOrder,
                isSubtotal: $isSubtotal,
                isTotal: $isTotal,
                formula: $formula,
            );
        }
        return $out;
    }

    private static function toBool(mixed $v): bool
    {
        if (is_bool($v)) {
            return $v;
        }
        if (is_int($v)) {
            return $v !== 0;
        }
        if (is_string($v)) {
            return $v !== '' && $v !== '0';
        }
        return (bool) $v;
    }
}
