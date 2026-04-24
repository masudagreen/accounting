<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\BreakEvenPoint;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointAnalysis;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointPdfGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of the Break-Even Point PDF port.
 *
 * Emits a plain summary table with sales / variable / fixed /
 * contribution-margin / break-even-sales / safety-margin. Bar charts
 * were in the legacy UI but are skipped here — dompdf has no native
 * chart support and adding an SVG pipeline is out of scope for this
 * wave.
 */
final class DompdfBreakEvenPointGenerator implements BreakEvenPointPdfGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(BreakEvenPointAnalysis $analysis): string
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
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        /** @var string $pdf */
        $pdf = $dompdf->output() ?? '';
        return $pdf;
    }

    public function renderHtml(BreakEvenPointAnalysis $analysis): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'analysis'        => $this->buildViewModel($analysis),
            'title'           => '損益分岐点分析 (Break-Even Point)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('break_even_point.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(BreakEvenPointAnalysis $a): array
    {
        return [
            'entityId'                => $a->entityId,
            'fiscalTermId'            => $a->fiscalTermId,
            'fromDate'                => $a->fromDate->format('Y-m-d'),
            'toDate'                  => $a->toDate->format('Y-m-d'),
            'currencyCode'            => $a->currencyCode,
            'sales'                   => self::fmtSigned($a->sales),
            'variableCosts'           => self::fmtSigned($a->variableCosts),
            'fixedCosts'              => self::fmtSigned($a->fixedCosts),
            'contributionMargin'      => self::fmtSigned($a->contributionMargin),
            'contributionMarginRate'  => self::fmtPct($a->contributionMarginRate),
            'bepSales'                => self::fmtSigned($a->bepSales),
            'bepRatio'                => self::fmtPct($a->bepRatio),
            'safetyMarginRatio'       => self::fmtPct($a->safetyMarginRatio),
            'operatingProfit'         => self::fmtSigned($a->operatingProfit),
            'isBelowBreakEven'        => $a->isBelowBreakEven(),
            'salesBreakdown'          => array_map(
                static fn (array $r): array => [
                    'code'   => $r['accountTitleCode'],
                    'name'   => $r['accountTitleName'],
                    'amount' => self::fmtSigned($r['amount']),
                ],
                $a->salesBreakdown,
            ),
            'variableBreakdown'       => array_map(
                static fn (array $r): array => [
                    'code'     => $r['accountTitleCode'],
                    'name'     => $r['accountTitleName'],
                    'costType' => $r['costType'],
                    'amount'   => self::fmtSigned($r['amount']),
                ],
                $a->variableBreakdown,
            ),
            'fixedBreakdown'          => array_map(
                static fn (array $r): array => [
                    'code'     => $r['accountTitleCode'],
                    'name'     => $r['accountTitleName'],
                    'costType' => $r['costType'],
                    'amount'   => self::fmtSigned($r['amount']),
                ],
                $a->fixedBreakdown,
            ),
            'generatedAt'             => $a->generatedAt->format('Y-m-d H:i:s'),
        ];
    }

    private static function fmtSigned(string $amount): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        $num = (float) $amount;
        $abs = number_format(abs($num), 0, '.', ',');
        return $num < 0 ? '(' . $abs . ')' : $abs;
    }

    /** Render a 0..1 decimal string as a percentage to 1 decimal place. */
    private static function fmtPct(string $ratio): string
    {
        if ($ratio === '' || !is_numeric($ratio)) {
            return '0.0%';
        }
        return number_format((float) $ratio * 100.0, 1, '.', '') . '%';
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
