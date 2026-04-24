<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Budget;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\Budget\BudgetVarianceAnalysis;
use Rucaro\Domain\Budget\BudgetVariancePdfGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of the 予実対比表 PDF port.
 *
 * Shares the same font / chroot strategy as
 * {@see DompdfBudgetGenerator}. Layout is A4 landscape so code /
 * name / budget / actual / variance / usage% fit on one row.
 */
final class DompdfBudgetVarianceGenerator implements BudgetVariancePdfGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(BudgetVarianceAnalysis $analysis): string
    {
        $html = $this->renderHtml($analysis);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('chroot', [$this->fontDir, dirname($this->templateDir)]);
        $options->set('defaultFont', $this->resolveDefaultFont());
        if (is_dir($this->fontDir)) {
            $options->set('fontDir', $this->fontDir);
            $options->set('fontCache', $this->fontDir);
        }

        $dompdf = new Dompdf($options);
        $this->registerJapaneseFont($dompdf);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        /** @var string $pdf */
        $pdf = $dompdf->output() ?? '';
        return $pdf;
    }

    public function renderHtml(BudgetVarianceAnalysis $analysis): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'analysis'        => $this->buildViewModel($analysis),
            'title'           => '予実対比表 (Budget Variance)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('variance.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(BudgetVarianceAnalysis $analysis): array
    {
        $rows = [];
        foreach ($analysis->rows as $r) {
            $rows[] = [
                'accountTitleId'   => $r->accountTitleId,
                'accountTitleCode' => $r->accountTitleCode,
                'accountTitleName' => $r->accountTitleName,
                'budget'           => self::fmt($r->budgetAmount),
                'actual'           => self::fmt($r->actualAmount),
                'variance'         => self::fmt($r->varianceAmount),
                'usage'            => $r->usageRatePercent ?? 'N/A',
                'isOverBudget'     => $r->isOverBudget(),
                'isUnderBudget'    => $r->isUnderBudget(),
            ];
        }
        return [
            'budgetId'     => $analysis->budgetId,
            'entityId'     => $analysis->entityId,
            'fiscalTermId' => $analysis->fiscalTermId,
            'budgetName'   => $analysis->budgetName,
            'status'       => $analysis->status->value,
            'periodFrom'   => $analysis->periodFrom->format('Y-m-d'),
            'periodTo'     => $analysis->periodTo->format('Y-m-d'),
            'currencyCode' => $analysis->currencyCode,
            'rows'         => $rows,
            'totals'       => [
                'budget'   => self::fmt($analysis->totalBudget()),
                'actual'   => self::fmt($analysis->totalActual()),
                'variance' => self::fmt($analysis->totalVariance()),
            ],
            'generatedAt'  => $analysis->generatedAt->format('Y-m-d H:i:s'),
        ];
    }

    private static function fmt(string $amount): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        $num = (float) $amount;
        $abs = number_format(abs($num), 0, '.', ',');
        return $num < 0 ? '(' . $abs . ')' : $abs;
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
            $this->logger->warning(
                'IPAex Gothic font not installed at {path}; Japanese glyphs will render as tofu.',
                ['path' => $ttf],
            );
            return;
        }
        try {
            $metrics = $dompdf->getFontMetrics();
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
}
