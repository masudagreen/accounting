<?php

declare(strict_types=1);

namespace App\Compare\Page;

use App\Compare\View\HtmlHelper;
use PDO;

/**
 * ホームページ: 事業体・期セレクタと各ページへのリンク一覧.
 */
final class HomePage
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function render(): string
    {
        $entities = $this->loadEntities();

        $rows = '';
        foreach ($entities as $entity) {
            $idEntity  = (int) $entity['id'];
            $title     = HtmlHelper::e($entity['strTitle'] ?? '');
            $maxPeriod = (int) ($entity['numFiscalPeriod'] ?? 1);

            $periodOptions = '';
            for ($p = 1; $p <= $maxPeriod; $p++) {
                $periodOptions .= sprintf(
                    '<a href="%s">第%s期</a> ',
                    HtmlHelper::e("/compare/?page=trial-balance&entity={$idEntity}&period={$p}"),
                    HtmlHelper::e((string) $p),
                );
            }

            $rows .= <<<HTML
            <tr>
              <td>{$title}</td>
              <td>{$periodOptions}</td>
              <td>
                <a href="{$idEntity}期セレクタ">
                  <a href="/compare/?page=trial-balance&entity={$idEntity}&period={$maxPeriod}">試算表</a> |
                  <a href="/compare/?page=profit-loss&entity={$idEntity}&period={$maxPeriod}">PL</a> |
                  <a href="/compare/?page=balance-sheet&entity={$idEntity}&period={$maxPeriod}">BS</a> |
                  <a href="/compare/?page=journal-list&entity={$idEntity}&period={$maxPeriod}">仕訳</a>
              </td>
            </tr>
HTML;
        }

        $content = <<<HTML
        <p>事業体を選択して比較したいページを開いてください。</p>
        <table class="home-table">
          <thead>
            <tr>
              <th>事業体</th>
              <th>期を選択</th>
              <th>最新期のページ</th>
            </tr>
          </thead>
          <tbody>
            {$rows}
          </tbody>
        </table>
HTML;

        return HtmlHelper::layout('比較ホーム', $content);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadEntities(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT e.id, e.strTitle, e.numFiscalPeriod
             FROM accountingEntity e
             ORDER BY e.id ASC',
        );
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
