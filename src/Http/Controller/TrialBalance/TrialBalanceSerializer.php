<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\TrialBalance;

use DateTimeZone;
use Rucaro\Domain\TrialBalance\TrialBalance;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;

/**
 * Shared conversion from {@see TrialBalance} read model to the API response
 * shape defined in `docs/api/openapi.yaml` under `#/components/schemas/TrialBalance`.
 */
final class TrialBalanceSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(TrialBalance $tb): array
    {
        return [
            'entityId'     => $tb->entityId,
            'fiscalTermId' => $tb->fiscalTermId,
            'fromDate'     => $tb->fromDate->format('Y-m-d'),
            'asOf'         => $tb->toDate->format('Y-m-d'),
            'currencyCode' => $tb->currencyCode,
            'rows'         => array_map(
                static fn (TrialBalanceRow $r): array => [
                    'accountTitleId' => $r->accountTitleId,
                    'accountCode'    => $r->accountTitleCode,
                    'accountName'    => $r->accountTitleName,
                    'category'       => $r->accountCategory,
                    'normalSide'     => $r->normalSide,
                    'debitTotal'     => $r->debitTotal,
                    'creditTotal'    => $r->creditTotal,
                    'balance'        => $r->balance,
                    'lineCount'      => $r->lineCount,
                ],
                $tb->rows,
            ),
            'totals' => [
                'debit'  => $tb->debitTotal(),
                'credit' => $tb->creditTotal(),
            ],
            'generatedAt' => $tb->generatedAt
                ->setTimezone(new DateTimeZone('UTC'))
                ->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    /**
     * Build CSV (UTF-8 with BOM) representation for spreadsheet consumers.
     */
    public static function toCsv(TrialBalance $tb): string
    {
        $lines = [];
        $lines[] = self::csvRow([
            'account_code',
            'account_name',
            'category',
            'normal_side',
            'debit_total',
            'credit_total',
            'balance',
            'line_count',
        ]);
        foreach ($tb->rows as $r) {
            $lines[] = self::csvRow([
                $r->accountTitleCode,
                $r->accountTitleName,
                $r->accountCategory,
                $r->normalSide,
                $r->debitTotal,
                $r->creditTotal,
                $r->balance,
                (string) $r->lineCount,
            ]);
        }
        $lines[] = self::csvRow([
            '',
            '_total',
            '',
            '',
            $tb->debitTotal(),
            $tb->creditTotal(),
            '',
            '',
        ]);
        // UTF-8 BOM so Excel renders Japanese account names correctly.
        return "\xEF\xBB\xBF" . implode("\r\n", $lines) . "\r\n";
    }

    /**
     * @param list<string> $cells
     */
    private static function csvRow(array $cells): string
    {
        $escaped = [];
        foreach ($cells as $cell) {
            $escaped[] = self::csvCell($cell);
        }
        return implode(',', $escaped);
    }

    private static function csvCell(string $cell): string
    {
        if (str_contains($cell, ',') || str_contains($cell, '"') || str_contains($cell, "\n") || str_contains($cell, "\r")) {
            return '"' . str_replace('"', '""', $cell) . '"';
        }
        return $cell;
    }
}
