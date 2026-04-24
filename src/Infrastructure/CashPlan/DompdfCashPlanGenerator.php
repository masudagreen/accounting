<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\CashPlan;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\CashPlan\CashPlan;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\CashPlan\CashPlanPdfGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of the Cash Plan PDF port.
 *
 * Mirrors {@see \Rucaro\Infrastructure\FixedAsset\DompdfFixedAssetLedgerGenerator}
 * so chroot, compile directory and Japanese-font handling all stay consistent.
 *
 * The layout is A4 landscape: 12 monthly columns + totals; legacy 旧
 * Jpn_CashPlanOutput did the same.
 */
final class DompdfCashPlanGenerator implements CashPlanPdfGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(CashPlan $plan): string
    {
        $html = $this->renderHtml($plan);

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

    public function renderHtml(CashPlan $plan): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'plan'            => $this->buildViewModel($plan),
            'title'           => '資金繰り表 (Cash Plan)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('plan.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(CashPlan $plan): array
    {
        $months = range(1, CashPlanEntry::MONTHS);
        $entries = [];
        foreach ($plan->entries as $e) {
            $cells = [];
            foreach ($months as $m) {
                $cells[] = self::fmt($e->monthlyAmounts[$m - 1]);
            }
            $entries[] = [
                'category' => $e->category->value,
                'group'    => $e->category->group(),
                'isInflow' => $e->category->isInflow(),
                'label'    => $e->label,
                'cells'    => $cells,
                'total'    => self::fmt($e->total()),
                'memo'     => $e->memo,
            ];
        }

        $deltas = [];
        $closings = [];
        foreach ($months as $m) {
            $deltas[] = self::fmtSigned($plan->monthlyDelta($m));
            $closings[] = self::fmtSigned($plan->closingBalance($m));
        }
        return [
            'id'             => $plan->id,
            'entityId'       => $plan->entityId,
            'fiscalTermId'   => $plan->fiscalTermId,
            'name'           => $plan->name,
            'openingBalance' => self::fmtSigned($plan->openingBalance),
            'currencyCode'   => $plan->currencyCode,
            'notes'          => $plan->notes,
            'months'         => $months,
            'entries'        => $entries,
            'monthlyDeltas'  => $deltas,
            'closingBalances' => $closings,
            'generatedAt'    => $plan->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    /** Unsigned formatter — amounts only. */
    private static function fmt(string $amount): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        return number_format((float) $amount, 0, '.', ',');
    }

    /** Signed formatter with parenthesis for negatives. */
    private static function fmtSigned(string $amount): string
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
