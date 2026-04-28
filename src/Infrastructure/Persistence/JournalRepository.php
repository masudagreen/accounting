<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Journal\JournalEntry;

/**
 * 仕訳データの読み取りインターフェース.
 *
 * 実装は MariaDB / In-Memory / Stub 等を差し替え可能にする.
 */
interface JournalRepository
{
    /**
     * 指定事業体・会計期番号の仕訳一覧を返す.
     *
     * flagRemove = 1 の行は除外して返すこと.
     *
     * @return list<array{date: \DateTimeImmutable, entry: JournalEntry}>
     */
    public function findByEntityAndPeriod(int $idEntity, int $numFiscalPeriod): array;
}
