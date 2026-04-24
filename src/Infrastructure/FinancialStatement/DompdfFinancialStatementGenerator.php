<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementGeneratorInterface;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Section;
use Smarty\Smarty;

/**
 * Smarty + dompdf backed {@see FinancialStatementGeneratorInterface}
 * implementation.
 *
 * Responsibilities:
 *   1. Load the matching Smarty template (bs/pl/cs/layout) from
 *      `storage/templates/fs/`.
 *   2. Assign the {@see FinancialStatement} aggregate under `$fs` plus a
 *      pre-computed view model convenient for templates.
 *   3. Register the bundled IPAex Gothic font if the TTF is dropped under
 *      `storage/fonts/ipaexg.ttf`; otherwise emit a one-shot warning and
 *      fall back to dompdf's DejaVu family (Japanese glyphs render as
 *      tofu, which is acceptable for Phase 6.6 CI).
 *   4. Run dompdf to produce the final PDF bytes.
 *
 * Intentionally does not stream to disk; callers get the binary string so
 * they can drop it directly into an HTTP response body.
 */
final class DompdfFinancialStatementGenerator implements FinancialStatementGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(FinancialStatement $statement): string
    {
        $html = $this->renderHtml($statement);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        // @font-face で ipaexg.ttf を file:// で読むため、chroot にフォントディレクトリを追加
        $options->set('chroot', [$this->fontDir, dirname($this->templateDir)]);
        $options->set('defaultFont', $this->resolveDefaultFont());
        if (is_dir($this->fontDir)) {
            $options->set('fontDir', $this->fontDir);
            $options->set('fontCache', $this->fontDir);
        }

        $dompdf = new Dompdf($options);
        $this->registerJapaneseFont($dompdf);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        /** @var string $pdf */
        $pdf = $dompdf->output() ?? '';
        return $pdf;
    }

    /**
     * Exposed for tests — renders just the HTML so we can assert on it
     * without invoking dompdf.
     */
    public function renderHtml(FinancialStatement $statement): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'fs'              => $this->buildViewModel($statement),
            'title'           => $this->resolveTitle($statement->kind),
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
            'hasBs'           => $statement->hasBalanceSheet(),
            'hasPl'           => $statement->hasProfitAndLoss(),
            'hasCs'           => $statement->hasCashFlow(),
            'bsOrder'         => self::bsOrder(),
            'plOrder'         => self::plOrder(),
            'csOrder'         => self::csOrder(),
            'hasJgaap'        => self::hasJgaapSections($statement),
            'hasJgaapCs'      => self::hasJgaapCsSections($statement),
        ]);
        return (string) $smarty->fetch($this->resolveTemplateName($statement->kind));
    }

    private function resolveTitle(FinancialStatementKind $kind): string
    {
        return match ($kind) {
            FinancialStatementKind::BalanceSheet  => '貸借対照表 (Balance Sheet)',
            FinancialStatementKind::ProfitAndLoss => '損益計算書 (Profit & Loss)',
            FinancialStatementKind::CashFlow      => 'キャッシュフロー計算書 (Cash Flow)',
            FinancialStatementKind::All           => '決算書（BS / PL / CS）',
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(FinancialStatement $statement): array
    {
        $currency = $statement->currencyCode;
        return [
            'entityId'     => $statement->entityId,
            'fiscalTermId' => $statement->fiscalTermId,
            'kind'         => $statement->kind->value,
            'fromDate'     => $statement->fromDate->format('Y-m-d'),
            'toDate'       => $statement->toDate->format('Y-m-d'),
            'currencyCode' => $currency,
            'bs'           => self::sectionMap($statement->bs, $currency),
            'pl'           => self::sectionMap($statement->pl, $currency),
            'cs'           => self::sectionMap($statement->cs, $currency),
            'totals'       => self::formatTotals($statement->totals, $currency),
            'generatedAt'  => $statement->generatedAt->format('Y-m-d H:i:s'),
            'hasBs'        => $statement->hasBalanceSheet(),
            'hasPl'        => $statement->hasProfitAndLoss(),
            'hasCs'        => $statement->hasCashFlow(),
        ];
    }

    /**
     * @param array<string, Section> $sections
     * @return array<string, array<string, mixed>>
     */
    private static function sectionMap(array $sections, string $currency): array
    {
        $out = [];
        foreach ($sections as $code => $section) {
            $out[$code] = [
                'code'     => $section->code,
                'label'    => $section->label,
                'subtotal' => self::formatAmount($section->subtotal, $currency),
                'lines'    => array_map(
                    static fn ($line) => [
                        'label'      => $line->label,
                        'code'       => $line->accountTitleCode,
                        'amount'     => self::formatAmount($line->amount, $currency),
                        'depth'      => $line->depth,
                        'isSubtotal' => $line->isSubtotal,
                    ],
                    $section->lines,
                ),
            ];
        }
        return $out;
    }

    /**
     * 通貨に応じて金額を表示用文字列に整形。
     *
     * - JPY (円) は整数桁のみ、カンマ区切り（例: "1,600,000"）
     * - 他通貨は小数第 2 位まで（例: "1,234.56"）
     * - 負数は括弧表記（例: "(12,345)"）
     */
    private static function formatAmount(mixed $amount, string $currency): string
    {
        $raw = is_object($amount) && method_exists($amount, '__toString')
            ? (string) $amount
            : (string) $amount;
        if ($raw === '' || !is_numeric($raw)) {
            return '0';
        }
        $decimals = strtoupper($currency) === 'JPY' ? 0 : 2;
        $num = (float) $raw;
        $isNegative = $num < 0;
        $abs = abs($num);
        $formatted = number_format($abs, $decimals, '.', ',');
        return $isNegative ? '(' . $formatted . ')' : $formatted;
    }

    /**
     * @param array<string, mixed> $totals
     * @return array<string, string>
     */
    private static function formatTotals(array $totals, string $currency): array
    {
        $out = [];
        foreach ($totals as $key => $value) {
            $out[$key] = self::formatAmount($value, $currency);
        }
        return $out;
    }

    private function resolveTemplateName(FinancialStatementKind $kind): string
    {
        return match ($kind) {
            FinancialStatementKind::BalanceSheet  => 'bs.html.tpl',
            FinancialStatementKind::ProfitAndLoss => 'pl.html.tpl',
            FinancialStatementKind::CashFlow      => 'cs.html.tpl',
            FinancialStatementKind::All           => 'all.html.tpl',
        };
    }

    private function buildSmarty(): Smarty
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir($this->templateDir);
        $smarty->setCompileDir($this->compileDir);
        $smarty->escape_html = true;
        return $smarty;
    }

    private function registerJapaneseFont(Dompdf $dompdf): void
    {
        $ttf = $this->fontDir . DIRECTORY_SEPARATOR . 'ipaexg.ttf';
        if (!is_file($ttf)) {
            // Warn once per process; do not throw — CI containers don't
            // ship with the font and PDF smoke tests only need the %PDF
            // header.
            $this->logger->warning(
                'IPAex Gothic font not installed at {path}; Japanese glyphs will render as tofu.',
                ['path' => $ttf],
            );
            return;
        }
        try {
            $metrics = $dompdf->getFontMetrics();
            // IPAex は Regular 1 ウェイトしか提供しないため、bold / italic 要求時も
            // 同じ TTF を返すように 4 バリエーション全てを登録（化け防止の必須処理）。
            foreach ([
                ['weight' => 'normal', 'style' => 'normal'],
                ['weight' => 'bold',   'style' => 'normal'],
                ['weight' => 'normal', 'style' => 'italic'],
                ['weight' => 'bold',   'style' => 'italic'],
            ] as $variant) {
                $metrics->registerFont(
                    ['family' => 'ipaexg'] + $variant,
                    $ttf,
                );
            }
        } catch (\Throwable $e) {
            $this->logger->warning(
                'Failed to register IPAex font: {message}',
                ['message' => $e->getMessage()],
            );
        }
    }

    private function hasJapaneseFont(): bool
    {
        return is_file($this->fontDir . DIRECTORY_SEPARATOR . 'ipaexg.ttf');
    }

    private function resolveDefaultFont(): string
    {
        return $this->hasJapaneseFont() ? 'ipaexg' : 'dejavu sans';
    }

    /**
     * @return array<string, list<array{code: string, label: string}>>
     */
    private static function bsOrder(): array
    {
        return [
            'assetGroups' => [
                ['code' => 'current_asset',    'label' => '流動資産'],
                ['code' => 'noncurrent_asset', 'label' => '固定資産'],
                ['code' => 'deferred_asset',   'label' => '繰延資産'],
            ],
            'liabilityGroups' => [
                ['code' => 'current_liability',    'label' => '流動負債'],
                ['code' => 'noncurrent_liability', 'label' => '固定負債'],
            ],
            'equityGroups' => [
                ['code' => 'shareholders_equity',     'label' => '株主資本'],
                ['code' => 'valuation_adjustments',   'label' => '評価・換算差額等'],
                ['code' => 'stock_acquisition_rights','label' => '新株予約権'],
            ],
        ];
    }

    /**
     * @return list<array{code: string, label: string, isSubtotal: bool, isTotal: bool}>
     */
    private static function plOrder(): array
    {
        return [
            ['code' => 'operating_revenue',     'label' => '売上高',              'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'cost_of_sales',         'label' => '売上原価',            'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'gross_profit',          'label' => '売上総利益',          'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'sga',                   'label' => '販売費及び一般管理費', 'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'operating_income',      'label' => '営業利益',            'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'non_operating_revenue', 'label' => '営業外収益',          'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'non_operating_expense', 'label' => '営業外費用',          'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'ordinary_income',       'label' => '経常利益',            'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'extraordinary_gain',    'label' => '特別利益',            'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'extraordinary_loss',    'label' => '特別損失',            'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'pretax_income',         'label' => '税引前当期純利益',    'isSubtotal' => true,  'isTotal' => false],
            ['code' => 'income_tax',            'label' => '法人税、住民税及び事業税', 'isSubtotal' => false, 'isTotal' => false],
            ['code' => 'net_income',            'label' => '当期純利益',          'isSubtotal' => true,  'isTotal' => true],
        ];
    }

    private static function hasJgaapSections(FinancialStatement $statement): bool
    {
        return isset($statement->bs['asset']) || isset($statement->pl['operating_revenue']);
    }

    private static function hasJgaapCsSections(FinancialStatement $statement): bool
    {
        return isset($statement->cs['operating_cf_total'])
            || isset($statement->cs['operating_cf_subtotal']);
    }

    /**
     * @return list<array{code: string, label: string, isSubtotal: bool, isTotal: bool, indent: int}>
     */
    private static function csOrder(): array
    {
        return [
            ['code' => 'operating_cf',               'label' => 'I. 営業活動によるキャッシュフロー',      'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'operating_pretax_income',    'label' => '税引前当期純利益',                       'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'depreciation',               'label' => '減価償却費',                             'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'provision',                  'label' => '引当金繰入額',                           'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'wc_receivables',             'label' => '売上債権の増減額',                       'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'wc_inventory',               'label' => '棚卸資産の増減額',                       'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'wc_payables',                'label' => '仕入債務の増減額',                       'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'operating_cf_subtotal',      'label' => '小計',                                   'isSubtotal' => true,  'isTotal' => false, 'indent' => 0],
            ['code' => 'interest_received',          'label' => '利息の受取額',                           'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'interest_paid',              'label' => '利息の支払額',                           'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'tax_paid',                   'label' => '法人税等の支払額',                       'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'operating_cf_total',         'label' => '営業活動によるキャッシュフロー',          'isSubtotal' => false, 'isTotal' => true,  'indent' => 0],

            ['code' => 'investing_cf',               'label' => 'II. 投資活動によるキャッシュフロー',      'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'investing_ppe_purchase',     'label' => '有形固定資産の取得による支出',           'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_ppe_sale',         'label' => '有形固定資産の売却による収入',           'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_securities_purchase', 'label' => '投資有価証券の取得による支出',        'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_securities_sale',  'label' => '投資有価証券の売却による収入',           'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_loan_given',       'label' => '貸付による支出',                         'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_loan_received',    'label' => '貸付金の回収による収入',                 'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'investing_cf_total',         'label' => '投資活動によるキャッシュフロー',          'isSubtotal' => false, 'isTotal' => true,  'indent' => 0],

            ['code' => 'financing_cf',               'label' => 'III. 財務活動によるキャッシュフロー',     'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'financing_debt_proceeds',    'label' => '借入による収入',                         'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_debt_repayment',   'label' => '借入金の返済による支出',                 'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_equity_proceeds',  'label' => '株式の発行による収入',                   'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_dividends_paid',   'label' => '配当金の支払額',                         'isSubtotal' => false, 'isTotal' => false, 'indent' => 1],
            ['code' => 'financing_cf_total',         'label' => '財務活動によるキャッシュフロー',          'isSubtotal' => false, 'isTotal' => true,  'indent' => 0],

            ['code' => 'net_change_in_cash',         'label' => '現金及び現金同等物の増減額',             'isSubtotal' => true,  'isTotal' => false, 'indent' => 0],
            ['code' => 'beginning_cash',             'label' => '現金及び現金同等物の期首残高',           'isSubtotal' => false, 'isTotal' => false, 'indent' => 0],
            ['code' => 'ending_cash',                'label' => '現金及び現金同等物の期末残高',           'isSubtotal' => false, 'isTotal' => true,  'indent' => 0],
        ];
    }
}
