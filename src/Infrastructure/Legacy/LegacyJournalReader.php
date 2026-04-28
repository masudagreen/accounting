<?php

declare(strict_types=1);

namespace App\Infrastructure\Legacy;

use App\Domain\Journal\JournalEntry;
use App\Domain\Journal\JournalLine;
use App\Domain\Money\Money;

/**
 * accountingLog テーブルの行を新ドメインの JournalEntry に変換するリーダー.
 *
 * jsonVersion の最新要素 (末尾) を読み取り、jsonDetail.varsDetail[] 配列から
 * 借方 (arrDebit) / 貸方 (arrCredit) の明細を展開する.
 *
 * flagRemove = 1 の行は除外すること (呼び出し側の SQL または本クラスの filter で制御).
 */
final class LegacyJournalReader
{
    /**
     * accountingLog 行の配列 (PDO::FETCH_ASSOC) を受け取り、
     * JournalEntry のリストを返す.
     *
     * 不整合な仕訳 (借方≠貸方, 明細0件等) は skip してカウントを返す.
     *
     * @param list<array<string, mixed>> $rows
     * @return array{entries: list<array{date: \DateTimeImmutable, entry: JournalEntry}>, skipped: int}
     */
    public function read(array $rows): array
    {
        $entries = [];
        $skipped = 0;

        foreach ($rows as $row) {
            $result = $this->convertRow($row);
            if ($result === null) {
                $skipped++;
                continue;
            }
            $entries[] = $result;
        }

        return ['entries' => $entries, 'skipped' => $skipped];
    }

    /**
     * @param array<string, mixed> $row
     * @return array{date: \DateTimeImmutable, entry: JournalEntry}|null
     */
    private function convertRow(array $row): ?array
    {
        $stampBook = isset($row['stampBook']) ? (int) $row['stampBook'] : 0;
        if ($stampBook <= 0) {
            return null;
        }

        $date = new \DateTimeImmutable('@' . $stampBook);

        $jsonVersionRaw = $row['jsonVersion'] ?? null;
        if (! is_string($jsonVersionRaw) || $jsonVersionRaw === '') {
            return null;
        }

        $versions = json_decode($jsonVersionRaw, true);
        if (! is_array($versions) || $versions === []) {
            return null;
        }

        // 最新バージョンは末尾要素
        $latest = end($versions);
        if (! is_array($latest)) {
            return null;
        }

        $detail = $latest['jsonDetail'] ?? null;
        if (! is_array($detail)) {
            return null;
        }

        $varsDetail = $detail['varsDetail'] ?? null;
        if (! is_array($varsDetail) || $varsDetail === []) {
            return null;
        }

        $debits = [];
        $credits = [];

        foreach ($varsDetail as $vd) {
            if (! is_array($vd)) {
                continue;
            }

            $debitLine = $this->extractLine($vd['arrDebit'] ?? null);
            if ($debitLine !== null) {
                $debits[] = $debitLine;
            }

            $creditLine = $this->extractLine($vd['arrCredit'] ?? null);
            if ($creditLine !== null) {
                $credits[] = $creditLine;
            }
        }

        if ($debits === [] || $credits === []) {
            return null;
        }

        // 借方合計と貸方合計が一致するか確認 (新ドメインは不一致を例外にする)
        // 本番DBのデータ不整合を吸収するため try/catch で skip
        try {
            $entry = JournalEntry::of($debits, $credits);
        } catch (\InvalidArgumentException) {
            return null;
        } catch (\App\Domain\Journal\UnbalancedJournalException) {
            return null;
        }

        return ['date' => $date, 'entry' => $entry];
    }

    /**
     * arrDebit / arrCredit の要素から JournalLine を生成する.
     *
     * @param mixed $arr
     */
    private function extractLine(mixed $arr): ?JournalLine
    {
        if (! is_array($arr)) {
            return null;
        }

        $accountTitleId = $arr['idAccountTitle'] ?? null;
        if (! is_string($accountTitleId) || $accountTitleId === '' || $accountTitleId === '0') {
            return null;
        }

        $numValue = $arr['numValue'] ?? null;
        $amount = $this->toMoney($numValue);
        if ($amount === null) {
            return null;
        }

        // 金額0は記録しない
        if ($amount->isZero()) {
            return null;
        }

        $departmentId = isset($arr['idDepartment']) && $arr['idDepartment'] !== '' && $arr['idDepartment'] !== 0 && $arr['idDepartment'] !== '0'
            ? (string) $arr['idDepartment']
            : null;

        $subAccountTitleId = isset($arr['idSubAccountTitle']) && $arr['idSubAccountTitle'] !== '' && $arr['idSubAccountTitle'] !== '0'
            ? (string) $arr['idSubAccountTitle']
            : null;

        try {
            return JournalLine::of($accountTitleId, $amount, $departmentId, $subAccountTitleId);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    private function toMoney(mixed $value): ?Money
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_int($value) || is_float($value)) {
            $intVal = (int) $value;
            if ($intVal < 0) {
                return null; // JournalLine は非負を要求
            }
            return Money::ofYen($intVal);
        }
        if (is_string($value)) {
            if (! is_numeric($value)) {
                return null;
            }
            $intVal = (int) $value;
            if ($intVal < 0) {
                return null;
            }
            return Money::ofYen($intVal);
        }
        return null;
    }
}
