<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\BlueReturn;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\BlueReturn\BlueReturnForm;
use Rucaro\Domain\BlueReturn\BlueReturnPdfGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf implementation of the 青色申告決算書 4-page PDF.
 *
 * Mirrors {@see \Rucaro\Infrastructure\Budget\DompdfBudgetGenerator}
 * for chroot / IPAex-font / compile-dir plumbing.
 */
final class DompdfBlueReturnGenerator implements BlueReturnPdfGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(BlueReturnForm $form): string
    {
        $html = $this->renderHtml($form);

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

    public function renderHtml(BlueReturnForm $form): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'form'            => $this->buildViewModel($form),
            'title'           => '青色申告決算書 (Blue Return)',
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('layout.html.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewModel(BlueReturnForm $form): array
    {
        $snap = $form->snapshot;
        return [
            'id'             => $form->id,
            'entityId'       => $form->entityId,
            'fiscalTermId'   => $form->fiscalTermId,
            'formType'       => $form->formType->value,
            'status'         => $form->status->value,
            'finalizedAt'    => $form->finalizedAt?->format('Y-m-d H:i:s'),
            'generatedAt'    => $form->updatedAt->format('Y-m-d H:i:s'),
            'page1'          => $snap->page1Pl,
            'page2'          => $snap->page2Monthly,
            'page3'          => $snap->page3Breakdown,
            'page4'          => $snap->page4Bs,
        ];
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
