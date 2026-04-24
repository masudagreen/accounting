<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatement\Multi;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodFinancialStatement;
use Rucaro\Domain\FinancialStatement\Multi\MultiPeriodSectionRow;
use Smarty\Smarty;

/**
 * Smarty + dompdf renderer for the multi-period FS (Wave 6-I).
 *
 * Mirrors {@see \Rucaro\Infrastructure\FinancialStatement\DompdfFinancialStatementGenerator}
 * for template resolution, dompdf options and IPAex font registration. The
 * only substantive difference is `setPaper('A4', 'landscape')` (wide enough
 * to fit up to 5 period columns + variance + variance%).
 */
final class DompdfMultiPeriodFinancialStatementGenerator implements MultiPeriodFinancialStatementGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(MultiPeriodFinancialStatement $statement): string
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

    public function renderHtml(MultiPeriodFinancialStatement $statement): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'multi'           => $this->buildViewModel($statement),
            'title'           => $this->resolveTitle($statement->kind),
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
            'hasBs'           => $statement->kind->includesBalanceSheet(),
            'hasPl'           => $statement->kind->includesProfitAndLoss(),
            'hasCs'           => $statement->kind->includesCashFlow(),
        ]);
        return (string) $smarty->fetch($this->resolveTemplateName($statement->kind));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(MultiPeriodFinancialStatement $multi): array
    {
        $columns = [];
        foreach ($multi->periods as $entry) {
            $columns[] = [
                'fiscalTermId' => $entry->fiscalTermId,
                'label'        => $entry->fiscalTermLabel,
                'fromDate'     => $entry->fromDate->format('Y-m-d'),
                'toDate'       => $entry->toDate->format('Y-m-d'),
            ];
        }

        return [
            'entityId'    => $multi->entityId,
            'kind'        => $multi->kind->value,
            'columns'     => $columns,
            'showVariance'=> $multi->periodCount() >= 2,
            'bsRows'      => $this->formatRows(MultiPeriodRowBuilder::buildBs($multi)),
            'plRows'      => $this->formatRows(MultiPeriodRowBuilder::buildPl($multi)),
            'csRows'      => $this->formatRows(MultiPeriodRowBuilder::buildCs($multi)),
            'generatedAt' => $multi->generatedAt->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param list<MultiPeriodSectionRow> $rows
     * @return list<array<string, mixed>>
     */
    private function formatRows(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $formattedAmounts = [];
            foreach ($row->amounts as $termId => $amount) {
                $formattedAmounts[$termId] = self::formatAmount($amount);
            }
            $out[] = [
                'sectionCode'     => $row->sectionCode,
                'label'           => $row->label,
                'amounts'         => $formattedAmounts,
                'variance'        => $row->variance === null ? null : self::formatAmount($row->variance),
                'variancePercent' => $row->variancePercent === null
                    ? null
                    : self::formatPercent($row->variancePercent),
                'isSubtotal'      => $row->isSubtotal,
                'isTotal'         => $row->isTotal,
            ];
        }
        return $out;
    }

    private static function formatAmount(string $raw): string
    {
        if ($raw === '' || !is_numeric($raw)) {
            return '0';
        }
        $num = (float) $raw;
        $isNegative = $num < 0;
        $abs = abs($num);
        $formatted = number_format($abs, 0, '.', ',');
        return $isNegative ? '(' . $formatted . ')' : $formatted;
    }

    private static function formatPercent(string $raw): string
    {
        if ($raw === '' || !is_numeric($raw)) {
            return '-';
        }
        $num = (float) $raw;
        return number_format($num, 2, '.', ',') . '%';
    }

    private function resolveTitle(FinancialStatementKind $kind): string
    {
        return match ($kind) {
            FinancialStatementKind::BalanceSheet  => '複数期比較 貸借対照表 (Multi-Period BS)',
            FinancialStatementKind::ProfitAndLoss => '複数期比較 損益計算書 (Multi-Period PL)',
            FinancialStatementKind::CashFlow      => '複数期比較 キャッシュフロー計算書 (Multi-Period CS)',
            FinancialStatementKind::All           => '複数期比較 決算書 (BS / PL / CS)',
        };
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
