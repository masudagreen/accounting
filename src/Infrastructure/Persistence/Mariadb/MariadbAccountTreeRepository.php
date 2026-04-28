<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mariadb;

use App\Domain\AccountTitle\AccountTree;
use App\Infrastructure\Legacy\LegacyAccountTreeReader;
use App\Infrastructure\Persistence\AccountTreeRepository;
use PDO;

/**
 * accountingFSJpn テーブルを読む MariaDB 実装.
 *
 * jsonJgaapAccountTitleBS + jsonJgaapAccountTitlePL の JSON を
 * LegacyAccountTreeReader に渡して AccountTree を構築する.
 */
final class MariadbAccountTreeRepository implements AccountTreeRepository
{
    private readonly LegacyAccountTreeReader $reader;

    public function __construct(
        private readonly PDO $pdo,
    ) {
        $this->reader = new LegacyAccountTreeReader();
    }

    public function loadCombinedTree(int $idEntity, int $numFiscalPeriod): AccountTree
    {
        $stmt = $this->pdo->prepare(
            'SELECT jsonJgaapAccountTitleBS, jsonJgaapAccountTitlePL
             FROM accountingFSJpn
             WHERE idEntity = :idEntity
               AND numFiscalPeriod = :numFiscalPeriod
             LIMIT 1',
        );
        $stmt->execute([
            ':idEntity'        => $idEntity,
            ':numFiscalPeriod' => $numFiscalPeriod,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false || $row === null) {
            // レコードなし → 空ツリー
            return AccountTree::of([]);
        }

        $bsJson = is_string($row['jsonJgaapAccountTitleBS']) ? $row['jsonJgaapAccountTitleBS'] : '[]';
        $plJson = is_string($row['jsonJgaapAccountTitlePL']) ? $row['jsonJgaapAccountTitlePL'] : '[]';

        return $this->reader->buildCombinedTreeFromJson($bsJson, $plJson);
    }
}
