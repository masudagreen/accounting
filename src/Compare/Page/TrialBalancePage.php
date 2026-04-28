<?php

declare(strict_types=1);

namespace App\Compare\Page;

use App\Application\Service\TrialBalanceService;
use App\Compare\View\HtmlHelper;
use App\Compare\View\NavBuilder;
use App\Domain\TrialBalance\OpeningBalances;
use PDO;

/**
 * 試算表 現新比較ページ.
 *
 * 左: 新ドメイン計算値
 * 右: 旧 DB 保存値 (accountingFSValueJpn の集計)
 * 差異がある行は赤背景で強調する.
 */
final class TrialBalancePage
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly TrialBalanceService $service,
    ) {
    }

    public function render(int $idEntity, int $numFiscalPeriod): string
    {
        // 新ドメイン計算
        $newResult = $this->service->build(
            idEntity: $idEntity,
            numFiscalPeriod: $numFiscalPeriod,
            opening: OpeningBalances::empty(),
        );
        $newRows = $newResult['rows'];

        // 旧値: accountingFSValueJpn から各科目の sumNext を取得
        $legacyValues = $this->loadLegacyValues($idEntity, $numFiscalPeriod);

        // 借方・貸方合計の不変条件チェック
        $totalNewDebit  = 0;
        $totalNewCredit = 0;
        foreach ($newRows as $row) {
            $totalNewDebit  += (int) ($row['periodDebits'] ?? 0);
            $totalNewCredit += (int) ($row['periodCredits'] ?? 0);
        }
        $invariantOk = ($totalNewDebit === $totalNewCredit);

        // テーブル行を構築
        $tableRows = '';
        foreach ($newRows as $id => $row) {
            $title      = HtmlHelper::e($row['title'] ?? $id);
            $newClosing = (int) ($row['closing'] ?? 0);
            $legacyVal  = $legacyValues[$id] ?? null;

            if ($legacyVal !== null) {
                $oldClosing = (int) $legacyVal;
                $diff       = $newClosing - $oldClosing;
                $diffBadge  = HtmlHelper::diffBadge($diff);
                $rowClass   = $diff !== 0 ? ' class="diff-row"' : '';
                $oldDisplay = HtmlHelper::money($oldClosing);
            } else {
                $oldClosing = null;
                $diff       = null;
                $diffBadge  = '<span class="badge-info">-</span>';
                $rowClass   = '';
                $oldDisplay = '-';
            }

            $newDisplay = HtmlHelper::money($newClosing);

            $tableRows .= <<<HTML
            <tr{$rowClass}>
              <td>{$title}</td>
              <td class="num">{$newDisplay}</td>
              <td class="num">{$oldDisplay}</td>
              <td class="num">{$diffBadge}</td>
            </tr>
HTML;
        }

        $invariantBadge = $invariantOk
            ? '<span class="badge-ok">借方合計 = 貸方合計 ✓</span>'
            : '<span class="badge-ng">借方合計 ≠ 貸方合計 ✗</span>';

        $entityTitle = HtmlHelper::e($this->getEntityTitle($idEntity));

        $content = <<<HTML
        <p class="meta">
          事業体: {$entityTitle} / 第{$numFiscalPeriod}期
          &nbsp;|&nbsp; 不変条件: {$invariantBadge}
          &nbsp;|&nbsp; 借方合計: <strong>{$totalNewDebit}</strong>
          &nbsp;|&nbsp; 貸方合計: <strong>{$totalNewCredit}</strong>
        </p>
        <table class="compare-table">
          <thead>
            <tr>
              <th>科目名</th>
              <th class="num">新ドメイン (残高)</th>
              <th class="num">旧DB (sumNext)</th>
              <th class="num">差異</th>
            </tr>
          </thead>
          <tbody>
            {$tableRows}
          </tbody>
        </table>
HTML;

        $nav = NavBuilder::build($idEntity, $numFiscalPeriod, 'trial-balance');
        return HtmlHelper::layout('試算表 現新比較', $content, $nav);
    }

    /**
     * accountingFSValueJpn から各科目 ID → sumNext のマップを返す.
     *
     * @return array<string, int>
     */
    private function loadLegacyValues(int $idEntity, int $numFiscalPeriod): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT jsonJgaapAccountTitlePL, jsonJgaapAccountTitleBS
             FROM accountingFSValueJpn
             WHERE idEntity = :idEntity AND numFiscalPeriod = :numFiscalPeriod
             LIMIT 1',
        );
        $stmt->execute([
            ':idEntity'        => $idEntity,
            ':numFiscalPeriod' => $numFiscalPeriod,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! is_array($row)) {
            return [];
        }

        $result = [];
        foreach (['jsonJgaapAccountTitlePL', 'jsonJgaapAccountTitleBS'] as $col) {
            $json = $row[$col] ?? null;
            if (! is_string($json) || $json === '') {
                continue;
            }
            $this->extractAccountValues($json, $result);
        }
        return $result;
    }

    /**
     * 科目ツリー JSON を再帰的に走査して各科目の sumNext を抽出する.
     *
     * @param array<string, int> $result (参照渡しで蓄積)
     */
    private function extractAccountValues(string $json, array &$result): void
    {
        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            return;
        }
        $this->walkNodes(array_values($decoded), $result);
    }

    /**
     * @param list<mixed>        $nodes
     * @param array<string, int> $result
     */
    private function walkNodes(array $nodes, array &$result): void
    {
        foreach ($nodes as $node) {
            if (! is_array($node)) {
                continue;
            }

            $vars = is_array($node['vars'] ?? null) ? $node['vars'] : [];
            $id   = $vars['idTarget'] ?? null;
            if (is_string($id) && $id !== '') {
                // sumNext を取得 (存在しない場合は 0)
                $sumNext = (int) ($vars['sumNext'] ?? 0);
                $result[$id] = $sumNext;
            }

            // 子ノードを再帰処理
            if (is_array($node['child'] ?? null)) {
                $this->walkNodes(array_values($node['child']), $result);
            }
        }
    }

    private function getEntityTitle(int $idEntity): string
    {
        $stmt = $this->pdo->prepare('SELECT strTitle FROM accountingEntity WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $idEntity]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? (string) ($row['strTitle'] ?? '') : '';
    }
}
