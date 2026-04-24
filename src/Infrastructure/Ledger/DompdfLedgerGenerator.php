<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Ledger;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\Ledger\Ledger;
use Rucaro\Domain\Ledger\LedgerBook;
use Rucaro\Domain\Ledger\LedgerEntry;
use Rucaro\Domain\Ledger\LedgerGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf backed {@see LedgerGeneratorInterface} implementation.
 *
 * Mirrors {@see \Rucaro\Infrastructure\FinancialStatement\DompdfFinancialStatementGenerator}:
 *   1. Loads Smarty templates from `storage/templates/ledger/`.
 *   2. Assigns a pre-formatted view model under `$ledger` plus font/meta flags.
 *   3. Registers `ipaexg.ttf` (all four weight/style variants) if the
 *      bundled font is installed; otherwise emits a one-shot warning and
 *      falls back to dompdf's DejaVu family.
 *   4. Runs dompdf and returns the raw PDF bytes.
 */
final class DompdfLedgerGenerator implements LedgerGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(Ledger $ledger): string
    {
        $html = $this->renderHtml($ledger);

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

    /**
     * Exposed for tests — renders just the HTML so we can assert on it
     * without invoking dompdf.
     */
    public function renderHtml(Ledger $ledger): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'ledger'          => $this->buildViewModel($ledger),
            'title'           => '総勘定元帳 (General Ledger)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('ledger.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(Ledger $ledger): array
    {
        $currency = $ledger->currencyCode;
        return [
            'entityId'     => $ledger->entityId,
            'fiscalTermId' => $ledger->fiscalTermId,
            'fromDate'     => $ledger->fromDate->format('Y-m-d'),
            'toDate'       => $ledger->toDate->format('Y-m-d'),
            'currencyCode' => $currency,
            'generatedAt'  => $ledger->generatedAt->format('Y-m-d H:i:s'),
            'books'        => array_map(
                static fn (LedgerBook $b): array => [
                    'accountTitleId'   => $b->accountTitleId,
                    'accountTitleCode' => $b->accountTitleCode,
                    'accountTitleName' => $b->accountTitleName,
                    'normalSide'       => $b->normalSide,
                    'openingBalance'   => self::formatAmount($b->openingBalance, $currency),
                    'debitTotal'       => self::formatAmount($b->debitTotal, $currency),
                    'creditTotal'      => self::formatAmount($b->creditTotal, $currency),
                    'closingBalance'   => self::formatAmount($b->closingBalance, $currency),
                    'entries'          => array_map(
                        static fn (LedgerEntry $e): array => [
                            'entryDate'          => $e->entryDate->format('Y-m-d'),
                            'summary'            => $e->summary,
                            'memo'               => $e->memo,
                            'counterAccountCode' => $e->counterAccountCode,
                            'counterAccountName' => $e->counterAccountName,
                            'debitAmount'        => self::formatAmountOrBlank($e->debitAmount, $currency),
                            'creditAmount'       => self::formatAmountOrBlank($e->creditAmount, $currency),
                            'runningBalance'     => self::formatAmount($e->runningBalance, $currency),
                        ],
                        $b->entries,
                    ),
                ],
                $ledger->books,
            ),
        ];
    }

    /**
     * JPY → integer with thousands separator and "(…)" on negatives.
     */
    private static function formatAmount(string $amount, string $currency): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        $decimals = strtoupper($currency) === 'JPY' ? 0 : 2;
        $num = (float) $amount;
        $isNegative = $num < 0;
        $abs = abs($num);
        $formatted = number_format($abs, $decimals, '.', ',');
        return $isNegative ? '(' . $formatted . ')' : $formatted;
    }

    /**
     * Same as {@see formatAmount()} but 0 becomes empty string — handy for
     * ledger debit/credit columns where a blank cell communicates "no movement".
     */
    private static function formatAmountOrBlank(string $amount, string $currency): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '';
        }
        if ((float) $amount === 0.0) {
            return '';
        }
        return self::formatAmount($amount, $currency);
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
