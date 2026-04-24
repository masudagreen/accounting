<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatementNotes;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteCategory;
use Rucaro\Domain\FinancialStatementNotes\FsNotesPdfGeneratorInterface;
use Smarty\Smarty;

/**
 * Smarty + dompdf adapter for the 注記表 PDF port.
 *
 * Mirrors {@see \Rucaro\Infrastructure\Budget\DompdfBudgetGenerator} so
 * the chroot handling, compile directories, and IPAex font registration
 * stay uniform across Phase 6 ports.
 *
 * Layout: A4 portrait, one section per {@see FsNoteCategory} sorted by
 * {@see FsNoteCategory::displayOrder()}. Inside each section notes render
 * ordered by `sortOrder` then `label`.
 */
final class DompdfFsNotesGenerator implements FsNotesPdfGeneratorInterface
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $fontDir,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function render(array $notes, string $entityId, string $fiscalTermId): string
    {
        $html = $this->renderHtml($notes, $entityId, $fiscalTermId);

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
     * @param list<FinancialStatementNote> $notes
     */
    public function renderHtml(array $notes, string $entityId, string $fiscalTermId): string
    {
        $smarty = $this->buildSmarty();
        $smarty->assign([
            'title'           => '注記表',
            'entityId'        => $entityId,
            'fiscalTermId'    => $fiscalTermId,
            'sections'        => $this->buildSections($notes),
            'generatedAt'     => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            'defaultFont'     => $this->resolveDefaultFont(),
            'hasJapaneseFont' => $this->hasJapaneseFont(),
            'fontDir'         => $this->fontDir,
        ]);
        return (string) $smarty->fetch('notes.html.tpl');
    }

    /**
     * Group active notes by category, apply the display order contract, and
     * shape the payload the Smarty template expects.
     *
     * @param list<FinancialStatementNote> $notes
     * @return list<array<string, mixed>>
     */
    private function buildSections(array $notes): array
    {
        /** @var array<string, list<FinancialStatementNote>> $grouped */
        $grouped = [];
        foreach ($notes as $n) {
            if (!$n->isActive) {
                continue;
            }
            $key = $n->category->value;
            $grouped[$key] ??= [];
            $grouped[$key][] = $n;
        }

        $sections = [];
        foreach (FsNoteCategory::cases() as $cat) {
            $bucket = $grouped[$cat->value] ?? [];
            if ($bucket === []) {
                continue;
            }
            usort(
                $bucket,
                static fn (FinancialStatementNote $a, FinancialStatementNote $b): int =>
                    $a->sortOrder <=> $b->sortOrder ?: strcmp($a->label, $b->label),
            );
            $items = [];
            foreach ($bucket as $n) {
                $items[] = [
                    'label'        => $n->label,
                    'body'         => $n->body,
                    'templateCode' => $n->templateCode,
                ];
            }
            $sections[] = [
                'category'      => $cat->value,
                'categoryLabel' => $cat->jaLabel(),
                'displayOrder'  => $cat->displayOrder(),
                'items'         => $items,
            ];
        }
        usort(
            $sections,
            static fn (array $a, array $b): int => $a['displayOrder'] <=> $b['displayOrder'],
        );
        return $sections;
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
