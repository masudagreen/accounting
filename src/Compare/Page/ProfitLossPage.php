<?php

declare(strict_types=1);

namespace App\Compare\Page;

use App\Application\Service\FinancialStatementService;
use App\Compare\View\HtmlHelper;
use App\Compare\View\NavBuilder;
use App\Domain\TrialBalance\OpeningBalances;
use PDO;

/**
 * 損益計算書 現新比較ページ.
 *
 * 7 項目を新旧並列で表示し差額を可視化する.
 * 旧値は accountingFSValueJpn.jsonJgaapFSPL の f1 キーから抽出する.
 */
final class ProfitLossPage
{
    /**
     * 比較する PL 項目の定義.
     *
     * [新ドメインキー => [label, 旧DB JSON キー]]
     *
     * @var array<string, array{label: string, legacyKey: string}>
     */
    private const array PL_ITEMS = [
        'sales'               => ['label' => '売上高',         'legacyKey' => 'salesSum'],
        'costOfSales'         => ['label' => '売上原価',       'legacyKey' => 'costOfSalesSum'],
        'grossProfit'         => ['label' => '売上総利益',     'legacyKey' => 'grossProfitNet'],
        'sellingAndAdmin'     => ['label' => '販管費',         'legacyKey' => 'sellingGeneralAndAdministrationExpensesSum'],
        'operatingIncome'     => ['label' => '営業利益',       'legacyKey' => 'operatingIncomeProfitOrLossNet'],
        'ordinaryIncome'      => ['label' => '経常利益',       'legacyKey' => 'ordinaryProfitNet'],
        'incomeBeforeTax'     => ['label' => '税引前当期純利益', 'legacyKey' => 'currentTermProfitOrLossPreNet'],
        'netIncome'           => ['label' => '当期純利益',     'legacyKey' => 'currentTermProfitOrLossNet'],
    ];

    public function __construct(
        private readonly PDO $pdo,
        private readonly FinancialStatementService $service,
    ) {
    }

    public function render(int $idEntity, int $numFiscalPeriod): string
    {
        // 新ドメイン計算
        $newPl = $this->service->buildProfitAndLoss(
            idEntity: $idEntity,
            numFiscalPeriod: $numFiscalPeriod,
            opening: OpeningBalances::empty(),
        );

        // 旧値: jsonJgaapFSPL の f1 を取得
        $legacyPl = $this->loadLegacyPl($idEntity, $numFiscalPeriod);

        $tableRows = '';
        foreach (self::PL_ITEMS as $key => $def) {
            $label    = HtmlHelper::e($def['label']);
            $newValue = (int) ($newPl[$key] ?? 0);
            $legacyKey = $def['legacyKey'];
            $oldValue  = isset($legacyPl[$legacyKey]) ? (int) $legacyPl[$legacyKey]['sumNext'] : null;

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

        $entityTitle = HtmlHelper::e($this->getEntityTitle($idEntity));

        $content = <<<HTML
        <p class="meta">事業体: {$entityTitle} / 第{$numFiscalPeriod}期</p>
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

        $nav = NavBuilder::build($idEntity, $numFiscalPeriod, 'profit-loss');
        return HtmlHelper::layout('損益計算書 現新比較', $content, $nav);
    }

    /**
     * accountingFSValueJpn.jsonJgaapFSPL の f1 を返す.
     *
     * @return array<string, array{sumPrev: int, sumNext: int}>
     */
    private function loadLegacyPl(int $idEntity, int $numFiscalPeriod): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT jsonJgaapFSPL
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

        $json = $row['jsonJgaapFSPL'] ?? null;
        if (! is_string($json) || $json === '') {
            return [];
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            return [];
        }

        // f1 が全期集計
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
