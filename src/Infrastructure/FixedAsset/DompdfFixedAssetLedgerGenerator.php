<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FixedAsset;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Application\FixedAsset\GetFixedAssetLedgerOutput;
use Rucaro\Domain\FixedAsset\DepreciationScheduleEntry;
use Rucaro\Domain\FixedAsset\FixedAsset;
use Rucaro\Domain\FixedAsset\FixedAssetLedgerGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of {@see FixedAssetLedgerGeneratorInterface}.
 *
 * Mirrors {@see \Rucaro\Infrastructure\Ledger\DompdfLedgerGenerator} so the
 * font and chroot handling match the rest of the project.
 */
final class DompdfFixedAssetLedgerGenerator implements FixedAssetLedgerGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(GetFixedAssetLedgerOutput $ledger): string
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
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        /** @var string $pdf */
        $pdf = $dompdf->output() ?? '';
        return $pdf;
    }

    public function renderHtml(GetFixedAssetLedgerOutput $ledger): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'ledger'          => $this->buildViewModel($ledger),
            'title'           => '固定資産台帳 (Fixed Asset Ledger)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('asset_ledger.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(GetFixedAssetLedgerOutput $ledger): array
    {
        return [
            'entityId'     => $ledger->entityId,
            'fiscalTermId' => $ledger->fiscalTermId,
            'generatedAt'  => $ledger->generatedAt->format('Y-m-d H:i:s'),
            'books'        => array_map(
                fn (array $book): array => [
                    'asset'    => $this->assetView($book['asset']),
                    'schedule' => array_map(
                        fn (DepreciationScheduleEntry $e): array => $this->entryView($e),
                        $book['schedule'],
                    ),
                ],
                $ledger->books,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function assetView(FixedAsset $a): array
    {
        return [
            'id'               => $a->id,
            'assetCode'        => $a->assetCode,
            'assetName'        => $a->assetName,
            'categoryCode'     => $a->categoryCode,
            'acquisitionDate'  => $a->acquisitionDate->format('Y-m-d'),
            'serviceStartDate' => $a->serviceStartDate->format('Y-m-d'),
            'disposalDate'     => $a->disposalDate?->format('Y-m-d'),
            'acquisitionCost'  => self::fmt($a->acquisitionCost),
            'residualValue'    => self::fmt($a->residualValue),
            'usefulLifeYears'  => $a->usefulLifeYears,
            'method'           => $a->method->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function entryView(DepreciationScheduleEntry $e): array
    {
        return [
            'periodNumber'             => $e->periodNumber,
            'periodStartDate'          => $e->periodStartDate->format('Y-m-d'),
            'periodEndDate'            => $e->periodEndDate->format('Y-m-d'),
            'monthsInService'          => $e->monthsInService,
            'openingBookValue'         => self::fmt($e->openingBookValue),
            'depreciationAmount'       => self::fmt($e->depreciationAmount),
            'accumulatedDepreciation'  => self::fmt($e->accumulatedDepreciation),
            'closingBookValue'         => self::fmt($e->closingBookValue),
            'isPosted'                 => $e->isPosted,
        ];
    }

    private static function fmt(string $amount): string
    {
        if ($amount === '' || !is_numeric($amount)) {
            return '0';
        }
        $num = (float) $amount;
        $negative = $num < 0;
        $abs = abs($num);
        $formatted = number_format($abs, 0, '.', ',');
        return $negative ? '(' . $formatted . ')' : $formatted;
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
