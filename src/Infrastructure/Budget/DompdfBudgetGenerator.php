<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Budget;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\Budget\Budget;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Budget\BudgetPdfGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of the 予算書 PDF port.
 *
 * Mirrors {@see \Rucaro\Infrastructure\CashPlan\DompdfCashPlanGenerator} so
 * chroot handling, compile directories, and Japanese-font registration
 * stay consistent across the Phase 6 ports.
 *
 * Layout: A4 landscape — 12 monthly columns + annual total.
 */
final class DompdfBudgetGenerator implements BudgetPdfGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(Budget $budget): string
    {
        $html = $this->renderHtml($budget);

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

    public function renderHtml(Budget $budget): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'budget'          => $this->buildViewModel($budget),
            'title'           => '予算書 (Budget)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('budget.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(Budget $budget): array
    {
        $months = range(1, BudgetLineItem::MONTHS);
        $items = [];
        foreach ($budget->lineItems as $li) {
            $cells = [];
            foreach ($months as $m) {
                $cells[] = self::fmt($li->monthlyAmounts[$m - 1]);
            }
            $items[] = [
                'accountTitleId'    => $li->accountTitleId,
                'subAccountTitleId' => $li->subAccountTitleId,
                'sortOrder'         => $li->sortOrder,
                'memo'              => $li->memo,
                'cells'             => $cells,
                'total'             => self::fmt($li->totalAmount()),
            ];
        }

        $monthlyTotals = [];
        foreach ($months as $m) {
            $monthlyTotals[] = self::fmt($budget->monthlyTotal($m));
        }

        return [
            'id'             => $budget->id,
            'entityId'       => $budget->entityId,
            'fiscalTermId'   => $budget->fiscalTermId,
            'name'           => $budget->name,
            'status'         => $budget->status->value,
            'notes'          => $budget->notes,
            'months'         => $months,
            'items'          => $items,
            'monthlyTotals'  => $monthlyTotals,
            'annualTotal'    => self::fmt($budget->annualTotal()),
            'generatedAt'    => $budget->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    private static function fmt(string $amount): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        return number_format((float) $amount, 0, '.', ',');
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
