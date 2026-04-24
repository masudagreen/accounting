<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\StatementOfChangesInEquity;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\StatementOfChangesInEquity\SsChange;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSection;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquity;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquityPdfGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of the 株主資本等変動計算書 PDF port.
 *
 * Mirrors {@see \Rucaro\Infrastructure\Budget\DompdfBudgetGenerator}
 * and {@see \Rucaro\Infrastructure\FinancialStatement\DompdfFinancialStatementGenerator}
 * so chroot, compile, and Japanese-font registration behaviour stays
 * consistent across the Phase 6 ports.
 *
 * Layout: A4 landscape. One column per {@see SsSectionCode} plus a
 * trailing "合計" column; rows are {opening, change_type_1...,
 * total change, ending}.
 */
final class DompdfStatementOfChangesInEquityGenerator implements StatementOfChangesInEquityPdfGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(StatementOfChangesInEquity $statement): string
    {
        $html = $this->renderHtml($statement);

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

    /**
     * Render HTML only — used by tests so the template assertions
     * never depend on dompdf.
     */
    public function renderHtml(StatementOfChangesInEquity $statement): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'ss'              => $this->buildViewModel($statement),
            'title'           => '株主資本等変動計算書 (Statement of Changes in Equity)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('ss.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(StatementOfChangesInEquity $statement): array
    {
        $sectionsByCode = [];
        foreach ($statement->sections as $section) {
            $sectionsByCode[$section->sectionCode->value] = $section;
        }

        $columns = [];
        foreach (SsSectionCode::ordered() as $code) {
            $section = $sectionsByCode[$code->value] ?? null;
            if ($section === null) {
                continue;
            }
            $columns[] = [
                'code'           => $code->value,
                'label'          => $section->label,
                'openingBalance' => self::fmt($section->openingBalance),
                'endingBalance'  => self::fmt($section->endingBalance),
                'totalChange'    => self::fmt($section->totalChange()),
            ];
        }

        // Build row-major change grid: rows keyed by change_type_code,
        // one cell per column (blank string when that column has no
        // entry for the row).
        $changeTypes = $statement->changeTypesInUse();
        $rows = [];
        foreach ($changeTypes as $type) {
            $cells = [];
            foreach (SsSectionCode::ordered() as $code) {
                $section = $sectionsByCode[$code->value] ?? null;
                if ($section === null) {
                    continue;
                }
                $cells[] = [
                    'amount' => self::fmt(self::amountFor($section, $type)),
                    'source' => self::sourceFor($section, $type),
                ];
            }
            $rows[] = [
                'code'  => $type->value,
                'label' => $type->label(),
                'cells' => $cells,
            ];
        }

        $totals = $statement->totals();

        return [
            'entityId'     => $statement->entityId,
            'fiscalTermId' => $statement->fiscalTermId,
            'fromDate'     => $statement->fromDate->format('Y-m-d'),
            'toDate'       => $statement->toDate->format('Y-m-d'),
            'currencyCode' => $statement->currencyCode,
            'columns'      => $columns,
            'rows'         => $rows,
            'totals'       => [
                'opening'     => self::fmt($totals['opening']),
                'totalChange' => self::fmt($totals['totalChange']),
                'ending'      => self::fmt($totals['ending']),
            ],
            'generatedAt'  => $statement->generatedAt->format('Y-m-d H:i:s'),
        ];
    }

    private static function amountFor(SsSection $section, SsChangeType $type): string
    {
        $sum = '0.0000';
        foreach ($section->changes as $change) {
            if ($change->changeType === $type) {
                $sum = \Rucaro\Support\Decimal\Decimal::add($sum, $change->amount);
            }
        }
        return \Rucaro\Support\Decimal\Decimal::normalize($sum);
    }

    private static function sourceFor(SsSection $section, SsChangeType $type): string
    {
        foreach ($section->changes as $change) {
            if ($change->changeType === $type) {
                return $change->source;
            }
        }
        return SsChange::SOURCE_MANUAL;
    }

    private static function fmt(string $amount): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        $num = (float) $amount;
        $isNegative = $num < 0;
        $abs = abs($num);
        $formatted = number_format($abs, 0, '.', ',');
        return $isNegative ? '(' . $formatted . ')' : $formatted;
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
