<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\ConsumptionTax;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxReportGeneratorInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxSettlement;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of the
 * {@see ConsumptionTaxReportGeneratorInterface}.
 *
 * Layout mirrors 旧 ConsumptionTaxSheet / 2012_消費税申告書:
 *   - 標準 10% の課税売上・税額
 *   - 軽減  8% の課税売上・税額
 *   - 旧税率 8% / 5% / 3%（存在する場合）
 *   - 非課税・免税・不課税の内訳
 *   - 課税売上割合 / 控除対象仕入税額 / 非登録事業者調整
 *   - 国税・地方消費税の内訳
 */
final class DompdfConsumptionTaxReportGenerator implements ConsumptionTaxReportGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(ConsumptionTaxSettlement $settlement): string
    {
        $html = $this->renderHtml($settlement);

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

    public function renderHtml(ConsumptionTaxSettlement $settlement): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'report'          => $this->buildViewModel($settlement),
            'title'           => '消費税申告書イメージ (Consumption Tax Settlement)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('settlement.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(ConsumptionTaxSettlement $s): array
    {
        $salesRows = [];
        $purchaseRows = [];
        foreach (self::rateLabels() as $code => $label) {
            $salesAmount = $s->salesByRate[$code] ?? null;
            $salesTax   = $s->outputTaxByRate[$code] ?? null;
            if ($salesAmount !== null || $salesTax !== null) {
                $salesRows[] = [
                    'rateCode' => $code,
                    'label'    => $label,
                    'base'     => self::fmt($salesAmount ?? '0'),
                    'tax'      => self::fmt($salesTax ?? '0'),
                ];
            }
            $pAmount = $s->purchasesByRate[$code] ?? null;
            $pTax   = $s->inputTaxByRate[$code] ?? null;
            if ($pAmount !== null || $pTax !== null) {
                $purchaseRows[] = [
                    'rateCode' => $code,
                    'label'    => $label,
                    'base'     => self::fmt($pAmount ?? '0'),
                    'tax'      => self::fmt($pTax ?? '0'),
                ];
            }
        }
        $split = $s->taxSplitNationalLocal();
        return [
            'period' => [
                'id'          => $s->period->id,
                'entityId'    => $s->period->entityId,
                'fiscalTerm'  => $s->period->fiscalTermId,
                'from'        => $s->period->periodFrom->format('Y-m-d'),
                'to'          => $s->period->periodTo->format('Y-m-d'),
                'method'      => $s->method->label(),
                'isInterim'   => $s->period->isInterim,
                'status'      => $s->period->settlementStatus,
                'simplifiedBusinessCategory' => $s->period->simplifiedBusinessCategory?->label(),
            ],
            'salesRows'         => $salesRows,
            'purchaseRows'      => $purchaseRows,
            'totalSales'        => self::fmt($s->totalSales),
            'taxableSales'      => self::fmt($s->taxableSales),
            'nonTaxableSales'   => self::fmt($s->nonTaxableSales),
            'exemptSales'       => self::fmt($s->exemptSales),
            'untaxedSales'      => self::fmt($s->untaxedSales),
            'taxableSalesRatio' => self::fmtRatio($s->taxableSalesRatio),
            'outputTax'         => self::fmt($s->outputTax),
            'deductibleInputTax' => self::fmt($s->deductibleInputTax),
            'adjustmentForNonRegistered' => self::fmt($s->adjustmentForNonRegistered),
            'netTaxPayable'     => self::fmtSigned($s->netTaxPayable),
            'taxSplitNational'  => self::fmtSigned($split['national']),
            'taxSplitLocal'     => self::fmtSigned($split['local']),
            'generatedAt'       => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function rateLabels(): array
    {
        return [
            'standard_10' => '標準 10%',
            'reduced_8'   => '軽減 8%',
            'old_8'       => '旧税率 8%',
            'old_5'       => '旧税率 5%',
            'old_3'       => '旧税率 3%',
            'exempt'      => '免税',
            'untaxed'     => '不課税',
        ];
    }

    private static function fmt(string $amount): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        return number_format((float) $amount, 0, '.', ',');
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

    private static function fmtRatio(string $ratio): string
    {
        if ($ratio === '' || !is_numeric($ratio)) {
            return '0.00%';
        }
        return number_format(((float) $ratio) * 100, 2, '.', '') . '%';
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
