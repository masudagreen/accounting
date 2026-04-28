<?php

declare(strict_types=1);

namespace App\Compare\Page;

use App\Application\Service\FinancialStatementService;
use App\Compare\View\HtmlHelper;
use App\Compare\View\NavBuilder;
use App\Domain\TrialBalance\OpeningBalances;
use PDO;

/**
 * 貸借対照表 現新比較ページ.
 *
 * 資産合計・負債合計・純資産合計を新旧並列で表示する.
 * 旧値は accountingFSValueJpn.jsonJgaapFSBS の f1 キーから抽出する.
 * 不変条件 (資産 = 負債 + 純資産) をチェックする.
 */
final class BalanceSheetPage
{
    /**
     * BS 比較項目: [新ドメインキー => [label, 旧DB JSON サマリーキー]]
     *
     * @var array<string, array{label: string, legacyKey: string}>
     */
    private const array BS_ITEMS = [
        'totalAssets'      => ['label' => '資産合計',   'legacyKey' => 'assetsSum'],
        'totalLiabilities' => ['label' => '負債合計',   'legacyKey' => 'liabilitiesSum'],
        'totalEquity'      => ['label' => '純資産合計', 'legacyKey' => 'netAssetsSum'],
    ];

    public function __construct(
        private readonly PDO $pdo,
        private readonly FinancialStatementService $service,
    ) {
    }

    public function render(int $idEntity, int $numFiscalPeriod): string
    {
        // 新ドメイン計算
        $newBs = $this->service->buildBalanceSheet(
            idEntity: $idEntity,
            numFiscalPeriod: $numFiscalPeriod,
            opening: OpeningBalances::empty(),
        );

        // 旧値
        $legacyBs = $this->loadLegacyBs($idEntity, $numFiscalPeriod);

        // 不変条件: 資産 = 負債 + 純資産
        $assets      = (int) ($newBs['totalAssets'] ?? 0);
        $liabilities = (int) ($newBs['totalLiabilities'] ?? 0);
        $equity      = (int) ($newBs['totalEquity'] ?? 0);
        $invariantOk = ($assets === $liabilities + $equity);

        $tableRows = '';
        foreach (self::BS_ITEMS as $key => $def) {
            $label     = HtmlHelper::e($def['label']);
            $newValue  = (int) ($newBs[$key] ?? 0);
            $legacyKey = $def['legacyKey'];
            $oldValue  = isset($legacyBs[$legacyKey]) ? (int) $legacyBs[$legacyKey]['sumNext'] : null;

            if ($oldValue !== null) {
                $diff      = $newValue - $oldValue;
                $diffBadge = HtmlHelper::diffBadge($diff);
                $rowClass  = $diff !== 0 ? ' class="diff-row"' : '';
                $oldDisplay = HtmlHelper::money($oldValue);
            } else {
                $diffBadge  = '<span class="badge-info">-</span>';
                $rowClass   = '';
                $oldDisplay = '-';
            }

            $newDisplay = HtmlHelper::money($newValue);

            $tableRows .= <<<HTML
            <tr{$rowClass}>
              <td>{$label}</td>
              <td class="num">{$newDisplay}</td>
              <td class="num">{$oldDisplay}</td>
              <td class="num">{$diffBadge}</td>
            </tr>
HTML;
        }

        $invariantBadge = $invariantOk
            ? '<span class="badge-ok">資産 = 負債 + 純資産 ✓</span>'
            : '<span class="badge-ng">資産 ≠ 負債 + 純資産 ✗</span>';

        $entityTitle = HtmlHelper::e($this->getEntityTitle($idEntity));

        $content = <<<HTML
        <p class="meta">
          事業体: {$entityTitle} / 第{$numFiscalPeriod}期
          &nbsp;|&nbsp; 不変条件: {$invariantBadge}
        </p>
        <table class="compare-table">
          <thead>
            <tr>
              <th>項目</th>
              <th class="num">新ドメイン</th>
              <th class="num">旧DB (f1)</th>
              <th class="num">差異</th>
            </tr>
          </thead>
          <tbody>
            {$tableRows}
          </tbody>
        </table>
HTML;

        $nav = NavBuilder::build($idEntity, $numFiscalPeriod, 'balance-sheet');
        return HtmlHelper::layout('貸借対照表 現新比較', $content, $nav);
    }

    /**
     * accountingFSValueJpn.jsonJgaapFSBS の f1 を返す.
     *
     * @return array<string, array{sumPrev: int, sumNext: int}>
     */
    private function loadLegacyBs(int $idEntity, int $numFiscalPeriod): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT jsonJgaapFSBS
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

        $json = $row['jsonJgaapFSBS'] ?? null;
        if (! is_string($json) || $json === '') {
            return [];
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            return [];
        }

        $f1 = $decoded['f1'] ?? null;
        if (! is_array($f1)) {
            return [];
        }

        /** @var array<string, array{sumPrev: int, sumNext: int}> $f1 */
        return $f1;
    }

    private function getEntityTitle(int $idEntity): string
    {
        $stmt = $this->pdo->prepare('SELECT strTitle FROM accountingEntity WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $idEntity]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? (string) ($row['strTitle'] ?? '') : '';
    }
}
