<?php

declare(strict_types=1);

namespace App\Compare\Page;

use App\Compare\View\HtmlHelper;
use App\Compare\View\NavBuilder;
use App\Infrastructure\Legacy\LegacyJournalReader;
use PDO;

/**
 * 仕訳一覧 現新比較ページ.
 *
 * accountingLog の最新 50 件を表示する.
 * 各仕訳について「新ドメインで再構築できるか」「借方=貸方が成立するか」をマークする.
 */
final class JournalListPage
{
    private readonly LegacyJournalReader $reader;

    public function __construct(
        private readonly PDO $pdo,
    ) {
        $this->reader = new LegacyJournalReader();
    }

    public function render(int $idEntity, int $numFiscalPeriod): string
    {
        $rows = $this->loadLatestJournals($idEntity, $numFiscalPeriod, 50);

        $result = $this->reader->read($rows);
        $entries  = $result['entries'];
        $skipped  = $result['skipped'];
        $total    = count($entries) + $skipped;
        $okCount  = count($entries);

        $tableRows = '';

        // 変換成功したエントリを表示
        $entryIndex = 0;
        foreach ($rows as $raw) {
            $stampBook  = (int) ($raw['stampBook'] ?? 0);
            $dateStr    = $stampBook > 0
                ? (new \DateTimeImmutable('@' . $stampBook))->setTimezone(new \DateTimeZone('Asia/Tokyo'))->format('Y-m-d')
                : '-';

            // このrowが変換できたかをチェック
            $converted  = false;
            $balanced   = false;
            $debitSum   = 0;
            $creditSum  = 0;

            if ($entryIndex < count($entries)) {
                $entryStamp = $entries[$entryIndex]['date']->getTimestamp();
                if (abs($entryStamp - $stampBook) < 2) {
                    $converted = true;
                    $entry     = $entries[$entryIndex]['entry'];
                    foreach ($entry->debits() as $line) {
                        $debitSum += (int) $line->amount()->toString();
                    }
                    foreach ($entry->credits() as $line) {
                        $creditSum += (int) $line->amount()->toString();
                    }
                    $balanced = ($debitSum === $creditSum);
                    $entryIndex++;
                }
            }

            $convertBadge = $converted
                ? '<span class="badge-ok">OK</span>'
                : '<span class="badge-ng">NG</span>';
            $balanceBadge = $converted
                ? ($balanced ? '<span class="badge-ok">&#10003;</span>' : '<span class="badge-ng">&#10007;</span>')
                : '<span class="badge-info">-</span>';

            $rowClass = (! $converted || ! $balanced) ? ' class="diff-row"' : '';

            $tableRows .= <<<HTML
            <tr{$rowClass}>
              <td>{$dateStr}</td>
              <td class="num">{$convertBadge}</td>
              <td class="num">{$balanceBadge}</td>
              <td class="num">{$debitSum}</td>
              <td class="num">{$creditSum}</td>
            </tr>
HTML;
        }

        $entityTitle = HtmlHelper::e($this->getEntityTitle($idEntity));

        $content = <<<HTML
        <p class="meta">
          事業体: {$entityTitle} / 第{$numFiscalPeriod}期
          &nbsp;|&nbsp; 取得: <strong>{$total}</strong>件
          &nbsp;|&nbsp; 変換成功: <span class="badge-ok">{$okCount}</span>
          &nbsp;|&nbsp; スキップ: <span class="badge-ng">{$skipped}</span>
        </p>
        <table class="compare-table">
          <thead>
            <tr>
              <th>日付</th>
              <th>変換</th>
              <th>借貸一致</th>
              <th class="num">借方合計</th>
              <th class="num">貸方合計</th>
            </tr>
          </thead>
          <tbody>
            {$tableRows}
          </tbody>
        </table>
HTML;

        $nav = NavBuilder::build($idEntity, $numFiscalPeriod, 'journal-list');
        return HtmlHelper::layout('仕訳一覧 現新比較', $content, $nav);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadLatestJournals(int $idEntity, int $numFiscalPeriod, int $limit): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT stampBook, jsonVersion
             FROM accountingLog
             WHERE idEntity = :idEntity
               AND numFiscalPeriod = :numFiscalPeriod
               AND (flagRemove IS NULL OR flagRemove = 0)
             ORDER BY stampBook DESC
             LIMIT :lim',
        );
        $stmt->bindValue(':idEntity',        $idEntity,        PDO::PARAM_INT);
        $stmt->bindValue(':numFiscalPeriod', $numFiscalPeriod, PDO::PARAM_INT);
        $stmt->bindValue(':lim',             $limit,           PDO::PARAM_INT);
        $stmt->execute();

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    private function getEntityTitle(int $idEntity): string
    {
        $stmt = $this->pdo->prepare('SELECT strTitle FROM accountingEntity WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $idEntity]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? (string) ($row['strTitle'] ?? '') : '';
    }
}
